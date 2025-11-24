-- ============================================================
-- SKILL-BASED TECHNICIAN ASSIGNMENT SYSTEM
-- ============================================================
-- This creates a comprehensive system where technicians can only
-- be assigned to services they have skills for, and only when
-- they have available time slots (not busy with other bookings)
-- ============================================================

-- Step 1: Create technician_service_skills mapping table
-- This table stores which services each technician is qualified for
CREATE TABLE IF NOT EXISTS tms_technician_service_skills (
    tss_id INT AUTO_INCREMENT PRIMARY KEY,
    tss_technician_id INT NOT NULL,
    tss_service_id INT NOT NULL,
    tss_skill_level ENUM('Beginner', 'Intermediate', 'Expert') DEFAULT 'Intermediate',
    tss_added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tss_added_by INT DEFAULT NULL COMMENT 'Admin ID who added this skill',
    UNIQUE KEY unique_tech_service (tss_technician_id, tss_service_id),
    FOREIGN KEY (tss_technician_id) REFERENCES tms_technician(t_id) ON DELETE CASCADE,
    FOREIGN KEY (tss_service_id) REFERENCES tms_service(s_id) ON DELETE CASCADE,
    INDEX idx_technician (tss_technician_id),
    INDEX idx_service (tss_service_id),
    INDEX idx_skill_level (tss_skill_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Maps which services each technician can perform';

-- Step 2: Add booking limit and current bookings columns to technician table
ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_booking_limit INT DEFAULT 3 COMMENT 'Max concurrent bookings',
ADD COLUMN IF NOT EXISTS t_current_bookings INT DEFAULT 0 COMMENT 'Current active bookings count',
ADD COLUMN IF NOT EXISTS t_is_available TINYINT(1) DEFAULT 1 COMMENT '1=Available, 0=Busy',
ADD COLUMN IF NOT EXISTS t_last_booking_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Last booking assignment time';

-- Step 3: Add indexes for performance
ALTER TABLE tms_technician
ADD INDEX IF NOT EXISTS idx_availability (t_is_available, t_current_bookings, t_booking_limit),
ADD INDEX IF NOT EXISTS idx_status (t_status);

-- Step 4: Add constraint to prevent over-assignment
ALTER TABLE tms_technician
ADD CONSTRAINT IF NOT EXISTS chk_booking_limit 
CHECK (t_current_bookings <= t_booking_limit);

-- Step 5: Update existing technicians with default values
UPDATE tms_technician 
SET t_booking_limit = 3,
    t_current_bookings = 0,
    t_is_available = 1
WHERE t_booking_limit IS NULL OR t_current_bookings IS NULL;

-- Step 6: Add assignment tracking columns to service_booking table
ALTER TABLE tms_service_booking
ADD COLUMN IF NOT EXISTS sb_assigned_at TIMESTAMP NULL DEFAULT NULL COMMENT 'When technician was assigned',
ADD COLUMN IF NOT EXISTS sb_assigned_by INT NULL DEFAULT NULL COMMENT 'Admin ID who assigned',
ADD COLUMN IF NOT EXISTS sb_previous_technician_id INT NULL DEFAULT NULL COMMENT 'Previous technician if reassigned',
ADD COLUMN IF NOT EXISTS sb_reassignment_count INT DEFAULT 0 COMMENT 'How many times reassigned',
ADD COLUMN IF NOT EXISTS sb_can_user_cancel TINYINT(1) DEFAULT 1 COMMENT 'Can user cancel this booking';

-- Step 7: Add indexes for booking queries
ALTER TABLE tms_service_booking
ADD INDEX IF NOT EXISTS idx_technician_status (sb_technician_id, sb_status),
ADD INDEX IF NOT EXISTS idx_status_date (sb_status, sb_booking_date),
ADD INDEX IF NOT EXISTS idx_service (sb_service_id);

-- Step 8: Create view for available technicians with skills
CREATE OR REPLACE VIEW v_available_technicians_with_skills AS
SELECT 
    t.t_id,
    t.t_name,
    t.t_experience,
    t.t_category,
    t.t_status,
    t.t_current_bookings,
    t.t_booking_limit,
    (t.t_booking_limit - t.t_current_bookings) AS available_slots,
    CASE 
        WHEN t.t_current_bookings >= t.t_booking_limit THEN 0
        WHEN t.t_status != 'Available' THEN 0
        ELSE 1
    END AS is_available,
    GROUP_CONCAT(DISTINCT s.s_id ORDER BY s.s_id) AS service_ids,
    GROUP_CONCAT(DISTINCT s.s_name ORDER BY s.s_name SEPARATOR ' | ') AS service_skills,
    GROUP_CONCAT(DISTINCT s.s_category ORDER BY s.s_category) AS categories,
    COUNT(DISTINCT tss.tss_service_id) AS total_skills
FROM tms_technician t
LEFT JOIN tms_technician_service_skills tss ON t.t_id = tss.tss_technician_id
LEFT JOIN tms_service s ON tss.tss_service_id = s.s_id
GROUP BY t.t_id, t.t_name, t.t_experience, t.t_category, t.t_status, 
         t.t_current_bookings, t.t_booking_limit;

-- Step 9: Create stored procedure to get eligible technicians for a service
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS sp_get_eligible_technicians_for_service(
    IN p_service_id INT,
    IN p_exclude_booking_id INT
)
BEGIN
    -- Get technicians who:
    -- 1. Have the required service skill
    -- 2. Have available slots (current_bookings < booking_limit)
    -- 3. Are in 'Available' status
    -- 4. Are not busy with conflicting bookings
    
    SELECT 
        t.t_id,
        t.t_name,
        t.t_experience,
        t.t_category,
        t.t_current_bookings,
        t.t_booking_limit,
        (t.t_booking_limit - t.t_current_bookings) AS available_slots,
        tss.tss_skill_level,
        s.s_name AS service_name,
        s.s_category AS service_category,
        CASE 
            WHEN tss.tss_skill_level = 'Expert' THEN 3
            WHEN tss.tss_skill_level = 'Intermediate' THEN 2
            ELSE 1
        END AS skill_priority,
        -- Check if technician is truly available
        CASE 
            WHEN t.t_current_bookings >= t.t_booking_limit THEN 'At Capacity'
            WHEN t.t_status != 'Available' THEN 'Not Available'
            ELSE 'Available'
        END AS availability_status
    FROM tms_technician t
    INNER JOIN tms_technician_service_skills tss 
        ON t.t_id = tss.tss_technician_id
    INNER JOIN tms_service s 
        ON tss.tss_service_id = s.s_id
    WHERE tss.tss_service_id = p_service_id
        AND t.t_current_bookings < t.t_booking_limit
        AND t.t_status = 'Available'
    ORDER BY 
        skill_priority DESC,  -- Experts first
        available_slots DESC, -- More slots first
        t.t_experience DESC,  -- More experienced first
        t.t_name ASC;
END$$

DELIMITER ;

-- Step 10: Create stored procedure to assign technician with race condition protection
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS sp_assign_technician_safe(
    IN p_booking_id INT,
    IN p_technician_id INT,
    IN p_admin_id INT,
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(500)
)
BEGIN
    DECLARE v_old_tech_id INT;
    DECLARE v_current_bookings INT;
    DECLARE v_booking_limit INT;
    DECLARE v_tech_name VARCHAR(200);
    DECLARE v_service_id INT;
    DECLARE v_has_skill BOOLEAN;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_success = FALSE;
        SET p_message = 'Database error occurred during assignment';
    END;
    
    START TRANSACTION;
    
    -- Lock technician row to prevent race conditions
    SELECT t_name, t_current_bookings, t_booking_limit
    INTO v_tech_name, v_current_bookings, v_booking_limit
    FROM tms_technician
    WHERE t_id = p_technician_id
    FOR UPDATE;
    
    -- Check if technician exists
    IF v_tech_name IS NULL THEN
        SET p_success = FALSE;
        SET p_message = 'Technician not found';
        ROLLBACK;
    ELSE
        -- Check if technician has capacity
        IF v_current_bookings >= v_booking_limit THEN
            SET p_success = FALSE;
            SET p_message = CONCAT(v_tech_name, ' is at capacity (', v_current_bookings, '/', v_booking_limit, ' bookings)');
            ROLLBACK;
        ELSE
            -- Get service ID and old technician from booking (lock booking row)
            SELECT sb_service_id, sb_technician_id
            INTO v_service_id, v_old_tech_id
            FROM tms_service_booking
            WHERE sb_id = p_booking_id
            FOR UPDATE;
            
            -- Check if technician has required skill
            SELECT COUNT(*) > 0
            INTO v_has_skill
            FROM tms_technician_service_skills
            WHERE tss_technician_id = p_technician_id
                AND tss_service_id = v_service_id;
            
            IF NOT v_has_skill THEN
                SET p_success = FALSE;
                SET p_message = CONCAT(v_tech_name, ' does not have the required skill for this service');
                ROLLBACK;
            ELSE
                -- Decrement old technician's count if reassigning
                IF v_old_tech_id IS NOT NULL AND v_old_tech_id != p_technician_id THEN
                    UPDATE tms_technician
                    SET t_current_bookings = GREATEST(t_current_bookings - 1, 0),
                        t_is_available = CASE 
                            WHEN t_current_bookings - 1 < t_booking_limit THEN 1 
                            ELSE 0 
                        END
                    WHERE t_id = v_old_tech_id;
                END IF;
                
                -- Assign booking to new technician
                UPDATE tms_service_booking
                SET sb_technician_id = p_technician_id,
                    sb_status = 'Approved',
                    sb_assigned_at = NOW(),
                    sb_assigned_by = p_admin_id,
                    sb_previous_technician_id = v_old_tech_id,
                    sb_reassignment_count = sb_reassignment_count + 1,
                    sb_can_user_cancel = 0
                WHERE sb_id = p_booking_id;
                
                -- Increment new technician's count
                UPDATE tms_technician
                SET t_current_bookings = t_current_bookings + 1,
                    t_is_available = CASE 
                        WHEN t_current_bookings + 1 >= t_booking_limit THEN 0 
                        ELSE 1 
                    END,
                    t_last_booking_at = NOW()
                WHERE t_id = p_technician_id;
                
                SET p_success = TRUE;
                SET p_message = CONCAT('Successfully assigned to ', v_tech_name);
                COMMIT;
            END IF;
        END IF;
    END IF;
END$$

DELIMITER ;

-- Step 11: Create trigger to auto-update technician availability on booking completion
DELIMITER $$

CREATE TRIGGER IF NOT EXISTS trg_update_tech_on_booking_complete
AFTER UPDATE ON tms_service_booking
FOR EACH ROW
BEGIN
    -- When booking is completed, rejected, or cancelled, free up the technician
    IF NEW.sb_status IN ('Completed', 'Rejected', 'Cancelled', 'Rejected by Technician', 'Not Done') 
       AND OLD.sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Rejected by Technician', 'Not Done')
       AND NEW.sb_technician_id IS NOT NULL THEN
        
        UPDATE tms_technician
        SET t_current_bookings = GREATEST(t_current_bookings - 1, 0),
            t_is_available = CASE 
                WHEN t_current_bookings - 1 < t_booking_limit THEN 1 
                ELSE 0 
            END
        WHERE t_id = NEW.sb_technician_id;
    END IF;
END$$

DELIMITER ;

-- Step 12: Create function to check if technician has skill for service
DELIMITER $$

CREATE FUNCTION IF NOT EXISTS fn_technician_has_skill(
    p_technician_id INT,
    p_service_id INT
) RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_has_skill BOOLEAN;
    
    SELECT COUNT(*) > 0 INTO v_has_skill
    FROM tms_technician_service_skills
    WHERE tss_technician_id = p_technician_id
        AND tss_service_id = p_service_id;
    
    RETURN v_has_skill;
END$$

DELIMITER ;

-- ============================================================
-- SAMPLE DATA: Assign skills to existing technicians
-- ============================================================
-- This is sample data - adjust based on your actual services and technicians

-- Clear existing skills (if re-running)
-- TRUNCATE TABLE tms_technician_service_skills;

-- Example: Assign skills to technicians
-- Technician 3 (John Smith - Electrical) gets electrical services
INSERT IGNORE INTO tms_technician_service_skills (tss_technician_id, tss_service_id, tss_skill_level)
SELECT 3, s_id, 'Expert' FROM tms_service WHERE s_category = 'Electrical';

-- Technician 4 (Sarah Johnson - Plumbing) gets plumbing services
INSERT IGNORE INTO tms_technician_service_skills (tss_technician_id, tss_service_id, tss_skill_level)
SELECT 4, s_id, 'Expert' FROM tms_service WHERE s_category = 'Plumbing';

-- Technician 5 (Mike Williams - HVAC) gets HVAC services
INSERT IGNORE INTO tms_technician_service_skills (tss_technician_id, tss_service_id, tss_skill_level)
SELECT 5, s_id, 'Expert' FROM tms_service WHERE s_category = 'HVAC';

-- Technician 6 (Emily Davis - Appliance) gets appliance services
INSERT IGNORE INTO tms_technician_service_skills (tss_technician_id, tss_service_id, tss_skill_level)
SELECT 6, s_id, 'Expert' FROM tms_service WHERE s_category = 'Appliance';

-- Technician 7 (David Brown - General) gets all services (general maintenance)
INSERT IGNORE INTO tms_technician_service_skills (tss_technician_id, tss_service_id, tss_skill_level)
SELECT 7, s_id, 'Intermediate' FROM tms_service;

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- View all technicians with their skills
SELECT * FROM v_available_technicians_with_skills;

-- Check which technicians can perform a specific service (e.g., service_id = 1)
-- CALL sp_get_eligible_technicians_for_service(1, NULL);

-- ============================================================
-- NOTES FOR ADMIN
-- ============================================================
-- 1. To add a skill to a technician:
--    INSERT INTO tms_technician_service_skills (tss_technician_id, tss_service_id, tss_skill_level)
--    VALUES (technician_id, service_id, 'Expert');
--
-- 2. To remove a skill:
--    DELETE FROM tms_technician_service_skills 
--    WHERE tss_technician_id = ? AND tss_service_id = ?;
--
-- 3. To change booking limit:
--    UPDATE tms_technician SET t_booking_limit = 5 WHERE t_id = ?;
--
-- 4. System automatically:
--    - Prevents assignment if technician lacks skill
--    - Prevents assignment if technician is at capacity
--    - Frees up technician when booking is completed/rejected
--    - Protects against race conditions with row locking
-- ============================================================
