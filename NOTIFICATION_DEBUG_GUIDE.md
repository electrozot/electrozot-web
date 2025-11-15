# Notification System Debug Guide

## Quick Test Steps

### Step 1: Open Browser Console
1. Open admin dashboard
2. Press **F12** to open Developer Tools
3. Go to **Console** tab
4. You should see:
   ```
   ✅ Real-time notification system activated
   🔔 Checking for new bookings every 10 seconds
   ```

### Step 2: Check First Poll
Wait 2 seconds, you should see:
```
🔍 Checking for new bookings...
📊 Response received: {success: true, new_count: 0, ...}
✅ No new bookings (Count: 0)
```

### Step 3: Create Test Booking
1. Open **another browser/tab** (or incognito)
2. Go to homepage
3. Create a new booking
4. Go back to admin dashboard console

### Step 4: Watch for Notification
Within 10 seconds, you should see:
```
🔍 Checking for new bookings...
📊 Response received: {success: true, new_count: 1, ...}
🔔 NEW BOOKINGS DETECTED: 1
📋 Booking details: [...]
🔊 Sound played
📱 Toast notification shown
🔴 Badge updated
🔔 Browser notification sent
🔄 Reloading page to show new bookings...
```

---

## Common Issues & Fixes

### Issue 1: No Console Messages
**Problem:** Console is empty, no messages at all

**Fix:**
1. Refresh the page (Ctrl+F5)
2. Check if jQuery is loaded: Type `$` in console
3. If undefined, jQuery not loaded - check network tab

### Issue 2: "check-new-bookings.php not found"
**Problem:** 404 error in console

**Fix:**
1. Verify file exists: `admin/check-new-bookings.php`
2. Check file permissions (should be 644)
3. Try accessing directly: `http://yoursite/admin/check-new-bookings.php`

### Issue 3: "Query preparation failed"
**Problem:** Database error in response

**Fix:**
1. Check if `sb_created_at` column exists
2. Run this SQL:
   ```sql
   ALTER TABLE tms_service_booking 
   ADD COLUMN IF NOT EXISTS sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
   ```
3. Refresh page

### Issue 4: Sound Not Playing
**Problem:** No sound when notification appears

**Possible Causes:**
- Browser muted
- System volume off
- Audio context blocked (need user interaction first)

**Fix:**
1. Check browser volume (not muted)
2. Check system volume
3. Click anywhere on page first (some browsers require interaction)
4. Check console for audio errors

### Issue 5: No Toast Notification
**Problem:** Sound plays but no visual notification

**Fix:**
1. Check console for JavaScript errors
2. Verify jQuery is loaded
3. Check if toast is hidden behind something (z-index issue)
4. Try: `$('.notification-toast').length` in console

### Issue 6: Badge Not Updating
**Problem:** Bell icon doesn't show red badge

**Fix:**
1. Check if bell icon exists: `$('#notificationBadge').length`
2. Manually show badge: `$('#notificationBadge').text('1').show()`
3. Check CSS display property

---

## Manual Testing

### Test Sound Manually
Open console and run:
```javascript
const audioContext = new (window.AudioContext || window.webkitAudioContext)();
const oscillator = audioContext.createOscillator();
const gainNode = audioContext.createGain();
oscillator.connect(gainNode);
gainNode.connect(audioContext.destination);
oscillator.frequency.value = 800;
oscillator.type = 'sine';
gainNode.gain.setValueAtTime(0, audioContext.currentTime);
gainNode.gain.linearRampToValueAtTime(0.3, audioContext.currentTime + 0.1);
oscillator.start(audioContext.currentTime);
oscillator.stop(audioContext.currentTime + 0.5);
```

### Test AJAX Endpoint
Open console and run:
```javascript
$.get('check-new-bookings.php', function(data) {
    console.log('Response:', data);
});
```

### Force Notification
Open console and run:
```javascript
showNotification([{
    id: 123,
    customer: 'Test Customer',
    phone: '1234567890',
    service: 'Test Service'
}]);
```

---

## Check Database

### Verify Column Exists
Run this SQL:
```sql
SHOW COLUMNS FROM tms_service_booking LIKE 'sb_created_at';
```

Should return one row. If empty, run:
```sql
ALTER TABLE tms_service_booking 
ADD COLUMN sb_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

### Check Recent Bookings
```sql
SELECT sb_id, sb_created_at, sb_status 
FROM tms_service_booking 
ORDER BY sb_created_at DESC 
LIMIT 5;
```

### Update Existing Bookings
If `sb_created_at` is NULL for existing bookings:
```sql
UPDATE tms_service_booking 
SET sb_created_at = NOW() 
WHERE sb_created_at IS NULL;
```

---

## Expected Console Output

### On Page Load:
```
✅ Real-time notification system activated
🔔 Checking for new bookings every 10 seconds
Notification permission: granted
```

### Every 10 Seconds:
```
🔍 Checking for new bookings...
📊 Response received: {success: true, new_count: 0, has_new: false, ...}
✅ No new bookings (Count: 0)
```

### When New Booking:
```
🔍 Checking for new bookings...
📊 Response received: {success: true, new_count: 1, has_new: true, bookings: [...]}
🔔 NEW BOOKINGS DETECTED: 1
📋 Booking details: [{id: 123, customer: "John Doe", ...}]
🔊 Sound played
📱 Toast notification shown
🔴 Badge updated
🔔 Browser notification sent
🔄 Reloading page to show new bookings...
```

---

## Still Not Working?

### 1. Clear Everything
```javascript
// In console:
sessionStorage.clear();
localStorage.clear();
location.reload(true);
```

### 2. Check Session
The system uses PHP sessions. If sessions aren't working:
- Check `session_start()` is called
- Check session cookie in browser
- Try different browser

### 3. Test in Incognito
Open admin dashboard in incognito mode to rule out:
- Cache issues
- Extension conflicts
- Cookie problems

### 4. Check Network Tab
1. Open DevTools → Network tab
2. Filter: XHR
3. Look for `check-new-bookings.php` requests
4. Check response (should be JSON)

### 5. Enable All Logs
Add to top of `check-new-bookings.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## Success Indicators

You'll know it's working when:
- ✅ Console shows polling every 10 seconds
- ✅ Creating booking triggers notification within 10 seconds
- ✅ Sound plays (dual beep)
- ✅ Toast appears top-right
- ✅ Badge shows on bell icon
- ✅ Page reloads after 3 seconds
- ✅ New booking appears in table

---

## Contact Support

If still not working after all checks:
1. Copy full console output
2. Copy response from `check-new-bookings.php`
3. Check PHP error logs
4. Verify database structure
