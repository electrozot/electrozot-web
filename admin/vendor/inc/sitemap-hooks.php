<?php
/**
 * Sitemap Auto-Update Hooks for ElectroZot
 * Automatically regenerates sitemap when blog posts are modified
 */

/**
 * Regenerate sitemap function
 * Can be called from anywhere in the admin system
 */
function regenerate_sitemap() {
    // Include the sitemap generator
    $generator_path = __DIR__ . '/../../../generate-sitemap.php';
    
    if (file_exists($generator_path)) {
        // Capture output to prevent it from displaying
        ob_start();
        include($generator_path);
        ob_end_clean();
        
        return true;
    }
    
    return false;
}

/**
 * Hook function to call after blog operations
 * Call this function after adding, updating, or deleting blog posts
 */
function update_sitemap_after_blog_change($operation = 'update', $blog_id = null) {
    try {
        $success = regenerate_sitemap();
        
        if ($success) {
            // Log the sitemap update (optional)
            error_log("ElectroZot: Sitemap regenerated after blog {$operation}" . ($blog_id ? " (ID: {$blog_id})" : ""));
            return true;
        } else {
            error_log("ElectroZot: Failed to regenerate sitemap after blog {$operation}");
            return false;
        }
    } catch (Exception $e) {
        error_log("ElectroZot: Sitemap regeneration error: " . $e->getMessage());
        return false;
    }
}

/**
 * Schedule sitemap regeneration (for cron jobs)
 * Add this to your server's cron tab:
 * 0 2 * * * /usr/bin/php /path/to/your/site/generate-sitemap.php >/dev/null 2>&1
 */
function schedule_sitemap_regeneration() {
    // This function provides instructions for cron setup
    $cron_command = "0 2 * * * /usr/bin/php " . realpath(__DIR__ . '/../../../generate-sitemap.php') . " >/dev/null 2>&1";
    
    return [
        'command' => $cron_command,
        'description' => 'Regenerates sitemap daily at 2:00 AM',
        'setup_instructions' => [
            '1. Open your server\'s crontab: crontab -e',
            '2. Add this line: ' . $cron_command,
            '3. Save and exit',
            '4. Verify with: crontab -l'
        ]
    ];
}

/**
 * Manual sitemap update trigger
 * Can be called via AJAX or direct URL
 */
if (isset($_GET['action']) && $_GET['action'] === 'regenerate_sitemap') {
    // Security check - only allow from admin area
    session_start();
    if (!isset($_SESSION['a_id'])) {
        http_response_code(403);
        die('Unauthorized');
    }
    
    $success = regenerate_sitemap();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Sitemap regenerated successfully!' : 'Failed to regenerate sitemap',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}
?>