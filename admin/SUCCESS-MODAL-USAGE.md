# Success Modal with Auto-Redirect

## Overview
A reusable success modal component that displays a green checkmark animation and automatically redirects users after successful operations.

## Features
- ✅ Large animated green checkmark
- ⏱️ Countdown timer (3 seconds default)
- 🎨 Beautiful animations (fade in, slide up, scale)
- 🔄 Automatic redirect
- 📱 Responsive design

## How to Use

### 1. Include the Modal in Your Page
Add this line before the closing `</body>` tag:
```php
<!-- Success Modal -->
<?php include("vendor/inc/success-modal.php");?>
```

### 2. Redirect with Success Parameters
Instead of using `$_SESSION['success']`, redirect with URL parameters:

```php
// Example: After successful technician assignment
$success_message = "Technician assigned successfully to Booking #" . $sb_id;
$redirect_url = "admin-manage-service-booking.php";
header("Location: current-page.php?success=1&message=" . urlencode($success_message) . "&redirect=" . urlencode($redirect_url));
exit();
```

### 3. URL Parameters
- `success=1` - Triggers the modal
- `message=Your message here` - The success message to display (URL encoded)
- `redirect=target-page.php` - Where to redirect after 3 seconds (URL encoded)

## JavaScript API

You can also trigger the modal programmatically:

```javascript
showSuccessModal(
    "Operation completed successfully!", 
    "redirect-page.php", 
    3000  // delay in milliseconds (optional, default 3000)
);
```

## Examples

### Example 1: Technician Assignment
```php
$success_message = "Technician assigned successfully to Booking #123";
$redirect_url = "admin-manage-service-booking.php";
header("Location: admin-assign-technician.php?success=1&message=" . urlencode($success_message) . "&redirect=" . urlencode($redirect_url));
exit();
```

### Example 2: Booking Cancellation
```php
$success_message = "Booking #456 has been cancelled successfully!";
$redirect_url = "admin-manage-service-booking.php";
header("Location: admin-cancel-service-booking.php?success=1&message=" . urlencode($success_message) . "&redirect=" . urlencode($redirect_url));
exit();
```

### Example 3: Add Technician
```php
$success_message = "Technician added successfully with 5 skills!";
$redirect_url = "admin-manage-technician.php";
header("Location: admin-add-technician.php?success=1&message=" . urlencode($success_message) . "&redirect=" . urlencode($redirect_url));
exit();
```

## Pages Already Updated
- ✅ admin-assign-technician.php
- ✅ admin-rejected-bookings.php
- ✅ admin-add-technician.php
- ✅ admin-manage-technician.php
- ✅ admin-manage-service-booking.php

## Customization

### Change Redirect Delay
Modify the delay parameter (in milliseconds):
```javascript
showSuccessModal("Success!", "page.php", 5000); // 5 seconds
```

### Change Animation
Edit the CSS in `vendor/inc/success-modal.php`:
- `.success-modal-overlay` - Background overlay
- `.success-modal-box` - Modal container
- `.success-checkmark` - Green circle with checkmark
- Animations: `fadeIn`, `slideUp`, `scaleIn`, `checkPop`

## Browser Compatibility
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers

## Notes
- The modal automatically prevents form resubmission
- URL parameters are cleaned after redirect
- Works with all Bootstrap-based admin pages
- No additional JavaScript libraries required
