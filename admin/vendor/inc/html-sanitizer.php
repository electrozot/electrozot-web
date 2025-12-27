<?php
/**
 * HTML Sanitizer for Blog Content
 * Safely sanitizes HTML content while preserving formatting
 */

function sanitize_blog_content($html) {
    // Define allowed HTML tags and attributes
    $allowed_tags = [
        'p' => [],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        'h1' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'h5' => [],
        'h6' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'a' => ['href', 'title', 'target'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'style'],
        'blockquote' => [],
        'table' => ['class'],
        'thead' => [],
        'tbody' => [],
        'tr' => [],
        'th' => [],
        'td' => [],
        'div' => ['class', 'style'],
        'span' => ['class', 'style'],
        'pre' => [],
        'code' => ['class']
    ];
    
    // Convert allowed tags array to string format for strip_tags
    $allowed_tags_string = '<' . implode('><', array_keys($allowed_tags)) . '>';
    
    // First pass: Remove disallowed tags
    $html = strip_tags($html, $allowed_tags_string);
    
    // Second pass: Clean up attributes (basic implementation)
    // For production, consider using HTML Purifier library for more robust sanitization
    
    // Remove potentially dangerous attributes
    $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/', '', $html); // Remove onclick, onload, etc.
    $html = preg_replace('/\s*javascript\s*:/i', '', $html); // Remove javascript: URLs
    $html = preg_replace('/\s*vbscript\s*:/i', '', $html); // Remove vbscript: URLs
    
    return $html;
}

function get_blog_excerpt($content, $length = 160) {
    // Strip all HTML tags and get plain text excerpt
    $plain_text = strip_tags($content);
    
    // Remove extra whitespace
    $plain_text = preg_replace('/\s+/', ' ', trim($plain_text));
    
    // Truncate to specified length
    if (strlen($plain_text) > $length) {
        $plain_text = substr($plain_text, 0, $length);
        // Try to break at word boundary
        $last_space = strrpos($plain_text, ' ');
        if ($last_space !== false && $last_space > $length * 0.8) {
            $plain_text = substr($plain_text, 0, $last_space);
        }
        $plain_text .= '...';
    }
    
    return $plain_text;
}
?>