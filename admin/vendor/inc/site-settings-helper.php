<?php
/**
 * Site Settings Helper Functions
 * Use these functions to retrieve contact information anywhere in your application
 */

/**
 * Get a single setting value by key
 * @param mysqli $mysqli Database connection
 * @param string $key Setting key
 * @param string $default Default value if setting not found
 * @return string Setting value
 */
function get_setting($mysqli, $key, $default = '') {
    static $cache = [];
    
    // Check cache first
    if(isset($cache[$key])) {
        return $cache[$key];
    }
    
    $query = "SELECT setting_value FROM tms_site_settings WHERE setting_key = ? LIMIT 1";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        $cache[$key] = $row['setting_value'];
        return $row['setting_value'];
    }
    
    return $default;
}

/**
 * Get all settings as an associative array
 * @param mysqli $mysqli Database connection
 * @return array All settings [key => value]
 */
function get_all_settings($mysqli) {
    static $all_settings = null;
    
    if($all_settings !== null) {
        return $all_settings;
    }
    
    $all_settings = [];
    $query = "SELECT setting_key, setting_value FROM tms_site_settings";
    $result = $mysqli->query($query);
    
    while($row = $result->fetch_assoc()) {
        $all_settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $all_settings;
}

/**
 * Get settings by group
 * @param mysqli $mysqli Database connection
 * @param string $group Setting group (contact, social, general)
 * @return array Settings in the group
 */
function get_settings_by_group($mysqli, $group) {
    $query = "SELECT setting_key, setting_value, setting_label FROM tms_site_settings WHERE setting_group = ? ORDER BY display_order";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $group);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $settings = [];
    while($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row;
    }
    
    return $settings;
}

/**
 * Quick access functions for common settings
 */
function get_primary_phone($mysqli) {
    return get_setting($mysqli, 'contact_phone_1', '7559606925');
}

function get_secondary_phone($mysqli) {
    return get_setting($mysqli, 'contact_phone_2', '');
}

function get_whatsapp($mysqli) {
    return get_setting($mysqli, 'contact_whatsapp', '7559606925');
}

function get_primary_email($mysqli) {
    return get_setting($mysqli, 'contact_email_1', 'info@electrozot.com');
}

function get_secondary_email($mysqli) {
    return get_setting($mysqli, 'contact_email_2', '');
}

function get_instagram($mysqli) {
    return get_setting($mysqli, 'contact_instagram', '@electrozot');
}

function get_facebook($mysqli) {
    return get_setting($mysqli, 'contact_facebook', '');
}

function get_twitter($mysqli) {
    return get_setting($mysqli, 'contact_twitter', '');
}

function get_business_address($mysqli) {
    return get_setting($mysqli, 'contact_address', 'Himachal Pradesh, India');
}

function get_business_name($mysqli) {
    return get_setting($mysqli, 'business_name', 'ElectroZot');
}

function get_business_tagline($mysqli) {
    return get_setting($mysqli, 'business_tagline', 'Professional Technician Services');
}

/**
 * Format phone number for display
 * @param string $phone Phone number
 * @return string Formatted phone number
 */
function format_phone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if(strlen($phone) == 10) {
        return substr($phone, 0, 5) . ' ' . substr($phone, 5);
    }
    return $phone;
}

/**
 * Generate WhatsApp link
 * @param mysqli $mysqli Database connection
 * @param string $message Optional pre-filled message
 * @return string WhatsApp URL
 */
function get_whatsapp_link($mysqli, $message = '') {
    $phone = get_whatsapp($mysqli);
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Add country code if not present (assuming India +91)
    if(strlen($phone) == 10) {
        $phone = '91' . $phone;
    }
    
    $url = "https://wa.me/" . $phone;
    if(!empty($message)) {
        $url .= "?text=" . urlencode($message);
    }
    
    return $url;
}

/**
 * Generate phone call link
 * @param mysqli $mysqli Database connection
 * @param string $which Which phone (primary or secondary)
 * @return string Tel URL
 */
function get_phone_link($mysqli, $which = 'primary') {
    $phone = ($which == 'secondary') ? get_secondary_phone($mysqli) : get_primary_phone($mysqli);
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return "tel:" . $phone;
}

/**
 * Generate email link
 * @param mysqli $mysqli Database connection
 * @param string $which Which email (primary or secondary)
 * @param string $subject Optional email subject
 * @return string Mailto URL
 */
function get_email_link($mysqli, $which = 'primary', $subject = '') {
    $email = ($which == 'secondary') ? get_secondary_email($mysqli) : get_primary_email($mysqli);
    $url = "mailto:" . $email;
    if(!empty($subject)) {
        $url .= "?subject=" . urlencode($subject);
    }
    return $url;
}

/**
 * Check if table exists
 * @param mysqli $mysqli Database connection
 * @return bool True if settings table exists
 */
function settings_table_exists($mysqli) {
    $result = $mysqli->query("SHOW TABLES LIKE 'tms_site_settings'");
    return $result->num_rows > 0;
}
?>
