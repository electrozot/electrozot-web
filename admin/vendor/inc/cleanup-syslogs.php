<?php
/**
 * System Logs Auto-Cleanup Function
 * Keeps only the 100 most recent system log entries
 * Automatically deletes older entries
 */

function cleanup_system_logs($mysqli) {
    // Count total system logs
    $count_query = "SELECT COUNT(*) as total FROM tms_syslogs";
    $count_result = $mysqli->query($count_query);
    
    if($count_result) {
        $count_row = $count_result->fetch_assoc();
        
        // If more than 100 logs exist, delete the oldest ones
        if($count_row['total'] > 100) {
            // Delete oldest logs, keeping only 100 most recent
            $delete_old = "DELETE FROM tms_syslogs 
                          WHERE log_id NOT IN (
                              SELECT log_id FROM (
                                  SELECT log_id FROM tms_syslogs 
                                  ORDER BY u_logintime DESC 
                                  LIMIT 100
                              ) AS recent_logs
                          )";
            $mysqli->query($delete_old);
            
            return true; // Cleanup performed
        }
    }
    
    return false; // No cleanup needed
}

// Auto-execute if included directly
if(isset($mysqli)) {
    cleanup_system_logs($mysqli);
}
?>
