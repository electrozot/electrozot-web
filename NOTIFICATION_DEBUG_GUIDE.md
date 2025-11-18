# Notification System - Debugging Guide

## Changes Made

### 1. Added Debug Logging
- Console logs now show notification responses
- Logs when new notifications are detected
- Logs sound playback attempts

### 2. Created Test Page
**File:** `admin/test-notifications.php`

**Features:**
- Create test notifications
- View all notifications
- Test API endpoint
- Test sound playback

## How to Debug

### Step 1: Open Test Page
1. Login as admin
2. Go to: `admin/test-notifications.php`
3. Click "Create Test Notification"
4. Check if notification appears in table

### Step 2: Test API
1. On test page, click "Test API" button
2. Check JSON response
3. Should show: `{"success":true,"notifications":[...],"count":X}`

### Step 3: Test Sound
1. Click "Test Sound" button
2. Should hear beep sound
3. If no sound, check browser volume

### Step 4: Check Browser Console
1. Press F12 to open Developer Tools
2. Go to Console tab
3. Look for messages:
   - "Notification response: {...}"
   - "New notification detected! Playing sound..."
4. Check for errors

### Step 5: Check Permissions
1. Look for notification permission prompt
2. Click "Allow" if asked
3. Check browser settings if blocked

## Common Issues

### Issue 1: No Notifications Created
**Check:** Database table exists
**Solution:** API creates table automatically

### Issue 2: API Returns Empty
**Check:** Technician rejected/completed booking
**Solution:** Have technician perform action

### Issue 3: No Sound
**Check:** Browser autoplay policy
**Solution:** User must interact with page first

### Issue 4: Badge Not Updating
**Check:** Element ID matches
**Solution:** Already fixed in code

## Testing Flow

1. Create test notification → Should appear in table
2. Go to dashboard → Badge should show count
3. Wait 5 seconds → Console should log check
4. Create another → Sound should play

## Files Modified

- `admin/vendor/inc/nav.php` - Added debug logs
- `admin/test-notifications.php` - New test page
