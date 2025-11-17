<?php
session_start();
include('config.php');

// Ensure columns exist
try {
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_is_available TINYINT(1) DEFAULT 1");
    $mysqli->query("ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_booking_id INT DEFAULT NULL");
} catch(Exception $e) {}

// CRITICAL: Availability check - technician must be available AND have NO current booking
$availability_check = "(t_is_available = 1 OR t_status = 'Available') AND (t_current_booking_id IS NULL OR t_current_booking_id = 0)";

// NEW: Get service gadget name for skill-based matching
$service_gadget_name = isset($_POST['service_gadget_name']) ? $_POST['service_gadget_name'] : '';

if(!empty($service_gadget_name)) {
    // SKILL-BASED FILTERING: Get technicians who have this specific skill checked
    $options = '<option value="">-- Select Technician --</option>';
    
    // Query to find technicians with matching skill from tms_technician_skills table
    $query = "SELECT DISTINCT t.t_id, t.t_name, t.t_id_no, t.t_category, t.t_current_booking_id, t.t_experience
              FROM tms_technician t
              INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
              WHERE ts.skill_name = ? 
              AND $availability_check
              ORDER BY t.t_experience DESC, t.t_name ASC";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $service_gadget_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $count = $result->num_rows;
    
    if($count > 0) {
        $options .= '<optgroup label="✅ Qualified & Available (' . $count . ' technicians)">';
        while($tech = $result->fetch_object()) {
            $exp_text = $tech->t_experience ? ' - ' . $tech->t_experience . ' yrs exp' : '';
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (' . $tech->t_category . $exp_text . ' - ✓ Has Skill)</option>';
        }
        $options .= '</optgroup>';
    } else {
        // No technicians with this skill - show warning
        $options .= '<option disabled>❌ No technicians have skill: ' . htmlspecialchars($service_gadget_name) . '</option>';
        
        // Fallback: Show available technicians from same category
        $category = isset($_POST['category']) ? $_POST['category'] : '';
        if(!empty($category)) {
            $fallback_query = "SELECT t_id, t_name, t_id_no, t_category, t_current_booking_id 
                              FROM tms_technician 
                              WHERE t_category = ? 
                              AND $availability_check
                              ORDER BY t_name";
            $stmt2 = $mysqli->prepare($fallback_query);
            $stmt2->bind_param('s', $category);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            if($result2->num_rows > 0) {
                $options .= '<optgroup label="⚠️ Fallback: Available ' . htmlspecialchars($category) . ' (' . $result2->num_rows . ')">';
                while($tech = $result2->fetch_object()) {
                    $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (⚠️ No skill marked - Manual assign)</option>';
                }
                $options .= '</optgroup>';
            }
        }
    }
    
    echo $options;
    
} else if(isset($_POST['category']) && isset($_POST['service_name'])) {
    // Legacy: Reassignment with service name and category (for backward compatibility)
    $category = $_POST['category'];
    $service_name = $_POST['service_name'];
    
    $options = '<option value="">-- Select Technician --</option>';
    
    // Try skill-based first
    $query = "SELECT DISTINCT t.t_id, t.t_name, t.t_id_no, t.t_category, t.t_current_booking_id
              FROM tms_technician t
              INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
              WHERE ts.skill_name LIKE ? 
              AND $availability_check
              ORDER BY t.t_name";
    $stmt = $mysqli->prepare($query);
    $search_term = '%' . $service_name . '%';
    $stmt->bind_param('s', $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $count = $result->num_rows;
    
    if($count > 0) {
        $options .= '<optgroup label="✅ Has Skill: ' . htmlspecialchars($service_name) . ' (' . $count . ')">';
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (✓ Qualified)</option>';
        }
        $options .= '</optgroup>';
    } else {
        $options .= '<option disabled>❌ No technicians with this skill</option>';
    }
    
    echo $options;
    
} else if(isset($_POST['category'])) {
    // Category-based filtering (fallback)
    $category = $_POST['category'];
    
    $options = '<option value="">-- Select Technician --</option>';
    
    $query = "SELECT t_id, t_name, t_id_no, t_category, t_current_booking_id 
              FROM tms_technician 
              WHERE t_category = ? 
              AND $availability_check
              ORDER BY t_name";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $count = $result->num_rows;
    
    if($count > 0) {
        $options .= '<optgroup label="✅ Available ' . htmlspecialchars($category) . ' (' . $count . ')">';
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (✓ Free)</option>';
        }
        $options .= '</optgroup>';
    } else {
        $options .= '<option disabled>❌ No available ' . htmlspecialchars($category) . ' technicians</option>';
    }
    
    echo $options;
    
} else {
    // Get all available technicians (no filters)
    $query = "SELECT t_id, t_name, t_id_no, t_category, t_current_booking_id 
              FROM tms_technician 
              WHERE $availability_check
              ORDER BY t_category, t_name";
    $result = $mysqli->query($query);
    
    $options = '<option value="">-- Select Technician --</option>';
    
    if($result->num_rows > 0) {
        $options .= '<optgroup label="✅ All Available Technicians (' . $result->num_rows . ')">';
        while($tech = $result->fetch_object()) {
            $options .= '<option value="' . $tech->t_id . '">' . htmlspecialchars($tech->t_name) . ' (' . $tech->t_category . ' - ✓ Free)</option>';
        }
        $options .= '</optgroup>';
    } else {
        $options .= '<option disabled>❌ No available technicians</option>';
    }
    
    echo $options;
}
?>
