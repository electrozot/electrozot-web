# PWA Dashboard Button Fix - Permanent Sessions

## Problem
The Dashboard button in the footer was not persisting after closing and reopening the browser in production PWA. It worked fine on localhost but disappeared in production after browser restart.

## Root Cause
The issue was caused by:
1. **Session Configuration**: Regular session settings were not PWA-compatible
2. **Cookie Settings**: PWA apps need different cookie parameters for persistence
3. **Environment Detection**: Production PWA environments need longer session lifetimes
4. **SameSite Policy**: PWA contexts require specific cookie policies

## Solution Applied

### 1. Updated Session Management
- Modified `tech/process-login.php` to use PWA session configuration
- Updated `tech/dashboard.php` to include PWA session fix
- Enhanced `tech/includes/checklogin.php` with PWA-compatible validation

### 2. Enhanced PWA Session Fix (`tech/pwa-session-fix.php`)
- **Environment Detection**: Automatically detects production vs localhost
- **Permanent Lifetime**: 10 years for production PWA, 15 hours for localhost
- **Cookie Security**: Proper HTTPS and SameSite settings
- **Session Validation**: Enhanced validation for PWA contexts

### 3. Updated Footer Authentication (`vendor/inc/footer.php`)
- Now uses PWA-compatible session validation
- Properly detects technician login status in PWA context

### 4. Database Configuration (`config.php`)
- Auto-detects environment (localhost vs production)
- Uses appropriate database settings automatically

### 5. Main Page Integration (`index.php`)
- Includes PWA session fix for proper authentication detection

## Key Features of the Fix

### Environment-Aware Configuration
```php
$is_pwa = $_SERVER['HTTP_HOST'] !== 'localhost'; // Production environment
$lifetime = $is_pwa ? 315360000 : 54000; // 10 years vs 15 hours
```

### PWA-Compatible Cookies
```php
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'domain' => '',
    'secure' => $is_https,
    'httponly' => true,
    'samesite' => $samesite
]);
```

### Automatic Permanent Sessions in Production
```php
$is_production_pwa = $_SERVER['HTTP_HOST'] !== 'localhost';
if($remember_me || $is_production_pwa) {
    // Cookie will last 10 years (effectively permanent until logout)
    $cookie_lifetime = time() + 315360000; // 10 years
    setcookie(session_name(), session_id(), $cookie_lifetime, '/', '', $is_https, true);
}
```

## Testing

### Test File Created
- `tech/test-pwa-session.php` - Comprehensive testing page

### Test Steps
1. **Login Test**: Login as technician
2. **Persistence Test**: Close browser completely, reopen
3. **Dashboard Test**: Check if Dashboard button appears on home page
4. **Expected Result**: Session persists, Dashboard button visible

## Files Modified

1. `tech/process-login.php` - Updated to use PWA session fix
2. `tech/dashboard.php` - Added PWA session configuration
3. `tech/includes/checklogin.php` - Enhanced with PWA validation
4. `vendor/inc/footer.php` - Updated authentication check
5. `index.php` - Added PWA session support
6. `config.php` - Auto-environment detection
7. `tech/pwa-session-fix.php` - Enhanced PWA compatibility
8. `tech/test-pwa-session.php` - Created for testing

## Expected Behavior

### Localhost (Development)
- Sessions last 15 hours
- Regular cookie settings
- Dashboard button works normally
- "Remember me" checkbox extends session to 10 years

### Production PWA
- Sessions are **PERMANENT** (10 years) until logout
- Automatic permanent sessions (no need to check "remember me")
- PWA-compatible cookie settings
- Dashboard button persists after browser restart
- Proper HTTPS and security settings
- Only logout will clear the session

## Verification

After deployment:
1. Login as technician in production PWA
2. Close browser completely
3. Reopen browser and visit home page
4. Dashboard button should be visible and functional
5. Session will persist until manual logout
6. Use `tech/test-pwa-session.php` to verify session details

## Security Notes

- Sessions are properly secured with httponly cookies
- HTTPS detection for secure cookies
- Session regeneration on login
- Account validation on each request
- Proper SameSite policies for CSRF protection
- **Permanent sessions only in production PWA** - localhost still uses 15-hour sessions for security

The fix ensures that technician authentication persists **permanently** in PWA environments until manual logout, while maintaining security and compatibility across different deployment scenarios.