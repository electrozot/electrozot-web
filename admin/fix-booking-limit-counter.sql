-- Fix Booking Limit Counter System
-- This script ensures the booking limit columns exist and recalculates current bookings

-- Step 1: Add columns if they don't exist
ALTER TABLE tms_technician 
ADD COLUMN IF NOT EXISTS t_booking_limit INT DEFAULT 1 COMMENT 'Maximum concurrent bookings allowed',
ADD COLUMN IF NOT EXISTS t_current_bookings INT DEFAULT 0 COMMENT 'Current active bookings count';

-- Step 2: Set default booking limit for existing technicians (if NULL or 0)
UPDATE tms_technician 
SET t_booking_limit = 1 
WHERE t_booking_limit IS NULL OR t_booking_limit = 0;

-- Step 3: Recalculate current bookings for all technicians based on actual active bookings
UPDATE tms_technician t
SET t_current_bookings = (
    SELECT COUNT(*) 
    FROM tms_service_booking sb 
    WHERE sb.sb_technician_id = t.t_id 
    AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
);

-- Step 4: Verify the counts
SELECT 
    t.t_id,
    t.t_name,
    t.t_booking_limit,
    t.t_current_bookings,
    (t.t_booking_limit - t.t_current_bookings) as available_slots,
    (SELECT COUNT(*) 
     FROM tms_service_booking sb 
     WHERE sb.sb_technician_id = t.t_id 
     AND sb.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
    ) as actual_active_bookings
FROM tms_technician t
ORDER BY t.t_name;

-- Step 5: Create index for better performance
CREATE INDEX IF NOT EXISTS idx_booking_technician_status 
ON tms_service_booking(sb_technician_id, sb_status);

-- Step 6: Add trigger to auto-update counter (optional but recommended)
DELIMITER $$

DROP TRIGGER IF EXISTS trg_booking_status_update$$

CREATE TRIGGER trg_booking_status_update
AFTER UPDATE ON tms_service_booking
FOR EACH ROW
BEGIN
    -- When booking is completed, cancelled, or rejected
    IF NEW.sb_status IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician') 
       AND OLD.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician') THEN
        
        -- Decrement old technician's count
        IF OLD.sb_technician_id IS NOT NULL THEN
            UPDATE tms_technician 
            SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
            WHERE t_id = OLD.sb_technician_id;
        END IF;
        
    -- When technician is assigned to an active booking
    ELSEIF NEW.sb_status NOT IN ('Completed', 'Cancelled', 'Rejected', 'Rejected by Technician')
           AND (OLD.sb_technician_id IS NULL OR OLD.sb_technician_id != NEW.sb_technician_id) THEN
        
        -- Decrement old technician's count (if reassigning)
        IF OLD.sb_technician_id IS NOT NULL AND OLD.sb_technician_id != NEW.sb_technician_id THEN
            UPDATE tms_technician 
            SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
            WHERE t_id = OLD.sb_technician_id;
        END IF;
        
        -- Increment new technician's count
        IF NEW.sb_technician_id IS NOT NULL THEN
            UPDATE tms_technician 
            SET t_current_bookings = t_current_bookings + 1
            WHERE t_id = NEW.sb_technician_id;
        END IF;
    END IF;
END$$

DELIMITER ;

-- Success message
SELECT 'Booking limit counter system fixed successfully!' as message;
