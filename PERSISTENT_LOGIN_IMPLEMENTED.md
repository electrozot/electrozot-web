# Persistent Login Implementation

## Overview
Implemented persistent login sessions that last for **30 days** across all user types (Admin, Technician, and User). Users will remain logged in on the same device/browser until they explicitly click logout.

## Changes Made

### 1. Created Session Configuration Files
- `admin/vendor/inc/session-config.php` - For admin and technician sessions
- `usr/vendor/inc/session-config.php` - For user sessions

These files configure:
- Session lifetime: 30 days (2,592,000 seconds)
- Cookie lifetime: 30 days
- HTTPOnly flag: Enabled (prevents JavaScript access)
- SameSite: Lax (CSRF protection)
- Secure session regeneration on login

### 2. Updated Login Files

#### Admin Login (`admin/index.php`)
- Includes session-config.php before session_start()
- Regenerates session ID on successful login for security

#### User Login (`usr/index.php`)
- Includes session-config.php before session_start()
- Regenerates session ID on successful login for security

#### Technician Login (`tech/process-login.php`)
- Includes session-config.php before session_start()
- Regenerates session ID on successful login for security

## How It Works

1. **Before Login**: Session configuration is loaded, setting 30-day cookie parameters
2. **On Login**: 
   - User credentials are verified
   - Session is started with 30-day lifetime
   - Session ID is regenerated for security
   - User is redirected to dashboard
3. **After Login**: 
   - Session cookie persists for 30 days
   - User stays logged in across browser sessions
   - No need to log in again unless:
     - User clicks logout
     - 30 days pass without activity
     - Browser cookies are cleared

## Security Features

✅ **Session Regeneration**: New session ID on each login prevents session fixation attacks
✅ **HTTPOnly Cookies**: Session cookies cannot be accessed via JavaScript
✅ **SameSite Protection**: Lax setting provides CSRF protection
✅ **Secure Flag Ready**: Can be enabled for HTTPS environments

## User Experience

### Before:
- Users had to log in frequently
- Sessions expired after browser close
- Frustrating repeated logins

### After:
- Login once, stay logged in for 30 days
- Works across browser sessions
- Only logout when user wants to
- Seamless experience on same device

## Testing

To test persistent login:
1. Log in as any user type (Admin/Technician/User)
2. Close the browser completely
3. Reopen browser and navigate to the dashboard
4. User should still be logged in
5. Session persists for 30 days or until logout

## Notes

- Session lifetime: 30 days (configurable in session-config.php)
- Works per device/browser (cookies are device-specific)
- Logging out clears the session immediately
- Clearing browser cookies will require re-login
