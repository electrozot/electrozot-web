# Apply Consistent Navigation to All User Pages

## Overview
This guide shows how to apply the consistent header and footer navigation from user-dashboard.php to all user pages.

## Files Created
1. `usr/vendor/inc/user-header-styles.php` - Common CSS styles
2. `usr/vendor/inc/user-header.php` - Top navigation bar
3. `usr/vendor/inc/user-footer.php` - Bottom navigation bar

## How to Apply to Any Page

### Step 1: Add styles in the `<head>` section
```php
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Your Page Title - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <?php include('vendor/inc/user-header-styles.php'); ?>
    <!-- Add any page-specific styles here -->
</head>
```

### Step 2: Add header after `<body>` tag
```php
<body>
    <?php include('vendor/inc/user-header.php'); ?>
    
    <!-- Your page content here -->
    
</body>
```

### Step 3: Add footer before closing `</body>` tag
```php
    <!-- Your page content -->
    
    <?php include('vendor/inc/user-footer.php'); ?>
</body>
```

## Complete Example Template

```php
<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

// Get user info (required for header)
$query = "SELECT * FROM tms_user WHERE u_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $aid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_object();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Page Title - Electrozot</title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <?php include('vendor/inc/user-header-styles.php'); ?>
    <style>
        /* Add page-specific styles here */
        .content {
            padding: 15px;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/user-header.php'); ?>
    
    <div class="content">
        <!-- Your page content here -->
        <h1>Your Page Content</h1>
    </div>
    
    <?php include('vendor/inc/user-footer.php'); ?>
</body>
</html>
```

## Pages That Need Updating
- user-manage-booking.php
- user-track-booking.php
- user-view-profile.php
- user-update-profile.php
- user-give-feedback.php
- user-change-pwd.php
- book-service-step1.php
- book-service-step2.php
- book-service-step3.php
- book-custom-service.php
- user-booking-details.php
- And any other user-facing pages

## Benefits
- Consistent look and feel across all pages
- Easy to update navigation in one place
- Automatic active state detection in footer
- Responsive design built-in
- Smaller, rounded bottom navigation
