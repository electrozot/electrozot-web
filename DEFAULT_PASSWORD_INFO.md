# Default Password for Quick Booking Users

## When Admin Creates Quick Booking

When an admin creates a quick booking for a **new customer** (phone number not in system), the system automatically creates a user account with:

### Default Credentials:
- **Username/Login:** Customer's phone number (10 digits)
- **Default Password:** `electrozot123`

### Example:
If admin creates booking for phone: `7559606925`
- Customer can login with:
  - Phone: `7559606925`
  - Password: `electrozot123`

## Important Notes:

1. **Password is hashed** - The password is securely stored using PHP's `password_hash()` function
2. **Customer should change password** - After first login, customer should change their password from profile settings
3. **Only for new customers** - If customer already exists in system, their existing password is used
4. **Guest bookings** - Guest bookings create accounts with empty password, customer must register/reset password to login

## How Customers Can Access Their Account:

### Option 1: Login with Default Password
1. Go to customer login page
2. Enter phone number: `7559606925`
3. Enter password: `electrozot123`
4. Login successful ✅

### Option 2: Reset Password (if forgotten)
1. Click "Forgot Password" on login page
2. Enter phone number
3. Follow password reset process

## Security Recommendation:

⚠️ **For Production:** Consider implementing one of these:
1. Send SMS with random password when account is created
2. Send password reset link via SMS
3. Require customer to set password on first login
4. Use OTP-based login instead of password

## Files Involved:
- `admin/admin-quick-booking.php` - Line 51: Sets default password
- `usr/index.php` - Login page where customers enter credentials
- `usr/usr-forgot-password.php` - Password reset functionality
