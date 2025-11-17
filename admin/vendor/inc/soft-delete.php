<?php
/**
 * Soft Delete Helper Functions
 * Use these functions to move items to recycle bin instead of permanent deletion
 */

// Ensure the deleted_items table exists
function ensureDeletedItemsTable($mysqli) {
    $create_table = "CREATE TABLE IF NOT EXISTS tms_deleted_items (
        di_id INT AUTO_INCREMENT PRIMARY KEY,
        di_item_type VARCHAR(50) NOT NULL,
        di_item_id INT NOT NULL,
        di_item_data TEXT NOT NULL,
        di_deleted_by INT NOT NULL,
        di_deleted_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        di_reason TEXT,
        INDEX(di_item_type),
        INDEX(di_deleted_date)
    )";
    return $mysqli->query($create_table);
}

function softDeleteTechnician($mysqli, $t_id, $admin_id, $reason = '') {
    // Ensure table exists
    ensureDeletedItemsTable($mysqli);
    // Get technician data
    $query = "SELECT * FROM tms_technician WHERE t_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $t_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tech = $result->fetch_assoc();
    
    if($tech) {
        // Save to recycle bin
        $insert_query = "INSERT INTO tms_deleted_items (di_item_type, di_item_id, di_item_data, di_deleted_by, di_reason) 
                        VALUES ('technician', ?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);
        $data_json = json_encode($tech);
        $insert_stmt->bind_param('isis', $t_id, $data_json, $admin_id, $reason);
        
        if($insert_stmt->execute()) {
            // Delete from main table
            $delete_query = "DELETE FROM tms_technician WHERE t_id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param('i', $t_id);
            return $delete_stmt->execute();
        }
    }
    return false;
}

function softDeleteBooking($mysqli, $sb_id, $admin_id, $reason = '') {
    // Ensure table exists
    ensureDeletedItemsTable($mysqli);
    
    // Get booking data
    $query = "SELECT * FROM tms_service_booking WHERE sb_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $sb_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    
    if($booking) {
        // Save to recycle bin
        $insert_query = "INSERT INTO tms_deleted_items (di_item_type, di_item_id, di_item_data, di_deleted_by, di_reason) 
                        VALUES ('booking', ?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);
        
        if(!$insert_stmt) {
            error_log("Prepare failed: " . $mysqli->error);
            return false;
        }
        
        $data_json = json_encode($booking);
        $insert_stmt->bind_param('isis', $sb_id, $data_json, $admin_id, $reason);
        
        if($insert_stmt->execute()) {
            // Delete from main table
            $delete_query = "DELETE FROM tms_service_booking WHERE sb_id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param('i', $sb_id);
            return $delete_stmt->execute();
        } else {
            error_log("Insert failed: " . $insert_stmt->error);
        }
    }
    return false;
}

function softDeleteUser($mysqli, $u_id, $admin_id, $reason = '') {
    // Ensure table exists
    ensureDeletedItemsTable($mysqli);
    
    // Get user data
    $query = "SELECT * FROM tms_user WHERE u_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $u_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if($user) {
        // Save to recycle bin
        $insert_query = "INSERT INTO tms_deleted_items (di_item_type, di_item_id, di_item_data, di_deleted_by, di_reason) 
                        VALUES ('user', ?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);
        $data_json = json_encode($user);
        $insert_stmt->bind_param('isis', $u_id, $data_json, $admin_id, $reason);
        
        if($insert_stmt->execute()) {
            // Delete from main table
            $delete_query = "DELETE FROM tms_user WHERE u_id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param('i', $u_id);
            return $delete_stmt->execute();
        }
    }
    return false;
}

function softDeleteService($mysqli, $s_id, $admin_id, $reason = '') {
    // Ensure table exists
    ensureDeletedItemsTable($mysqli);
    
    // Get service data
    $query = "SELECT * FROM tms_service WHERE s_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $s_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    
    if($service) {
        // Save to recycle bin
        $insert_query = "INSERT INTO tms_deleted_items (di_item_type, di_item_id, di_item_data, di_deleted_by, di_reason) 
                        VALUES ('service', ?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);
        $data_json = json_encode($service);
        $insert_stmt->bind_param('isis', $s_id, $data_json, $admin_id, $reason);
        
        if($insert_stmt->execute()) {
            // Delete from main table
            $delete_query = "DELETE FROM tms_service WHERE s_id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param('i', $s_id);
            return $delete_stmt->execute();
        }
    }
    return false;
}
?>
