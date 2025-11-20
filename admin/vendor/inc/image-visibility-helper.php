<?php
/**
 * Image Visibility Helper
 * 
 * Controls when service images are visible based on:
 * - Customer view: Hide after 31 days
 * - Admin view: Hide after 40 days
 * - Images are physically deleted after 40 days by cron job
 */

/**
 * Check if image should be visible to user
 * 
 * @param string $completed_date - Service completion date (Y-m-d H:i:s)
 * @param string $user_role - 'customer' or 'admin'
 * @return bool - True if image should be visible
 */
function isImageVisible($completed_date, $user_role = 'customer') {
    if (empty($completed_date)) {
        return false;
    }
    
    $completion_timestamp = strtotime($completed_date);
    $current_timestamp = time();
    $days_since_completion = floor(($current_timestamp - $completion_timestamp) / (60 * 60 * 24));
    
    // Customer view: hide after 31 days
    if ($user_role === 'customer') {
        return $days_since_completion <= 31;
    }
    
    // Admin view: hide after 40 days
    if ($user_role === 'admin') {
        return $days_since_completion <= 40;
    }
    
    return false;
}

/**
 * Get days remaining until image is hidden
 * 
 * @param string $completed_date - Service completion date (Y-m-d H:i:s)
 * @param string $user_role - 'customer' or 'admin'
 * @return int - Days remaining (0 if already hidden)
 */
function getDaysRemainingForImage($completed_date, $user_role = 'customer') {
    if (empty($completed_date)) {
        return 0;
    }
    
    $completion_timestamp = strtotime($completed_date);
    $current_timestamp = time();
    $days_since_completion = floor(($current_timestamp - $completion_timestamp) / (60 * 60 * 24));
    
    $max_days = ($user_role === 'customer') ? 31 : 40;
    $days_remaining = $max_days - $days_since_completion;
    
    return max(0, $days_remaining);
}

/**
 * Check if image file exists and is visible
 * 
 * @param string $image_path - Relative path to image
 * @param string $completed_date - Service completion date
 * @param string $user_role - 'customer' or 'admin'
 * @return bool - True if image exists and should be visible
 */
function shouldDisplayImage($image_path, $completed_date, $user_role = 'customer') {
    // Check if image should be visible based on date
    if (!isImageVisible($completed_date, $user_role)) {
        return false;
    }
    
    // Check if file exists
    if (empty($image_path)) {
        return false;
    }
    
    return true;
}
?>
