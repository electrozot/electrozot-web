<?php
/**
 * AUTO-FIX: Technician Slots
 * This file automatically syncs technician data when included
 * Include this at the top of admin-dashboard.php or admin-manage-technicians.php
 * 
 * Updated: Now runs every 30 seconds instead of once per session for real-time updates
 */

// Run if not synced yet OR if last sync was more than 30 seconds ago
$should_sync = false;

if (!isset($_SESSION['technician_slots_synced'])) {
    $should_sync = true;
} elseif (isset($_SESSION['technician_slots_synced_time'])) {
    $time_since_sync = time() - $_SESSION['technician_slots_synced_time'];
    if ($time_since_sync > 30) { // Re-sync every 30 seconds
        $should_sync = true;
    }
}

if ($should_sync) {
    // Use the centralized auto-update script
    include_once('auto-update-technician-status.php');
    
    // Mark as synced
    $_SESSION['technician_slots_synced'] = true;
    $_SESSION['technician_slots_synced_time'] = time();
}
?>
