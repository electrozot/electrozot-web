# Notification System Troubleshooting Guide

## Common Issues and Solutions

### 1. No Sound Playing

**Symptoms:**
- Console shows: "Error playing sound: NotAllowedError"
- No audio when new booking arrives

**Cause:**
Browser security policy requires user interaction before playing audio.

**Solution:**
- Click anywhere on the page first
- The system will automatically initialize audio
- After first interaction, sounds will work normally

**Automatic Fallback:**
- If custom sound fails, Web Audio API beep plays automatically
- No action needed - system handles it

### 2. Browser Notification Not Showing

**Symptoms:**
- No desktop notification appears
- Console shows notification permission status

**Solution:**
1. Check browser notification permission:
   - Chrome: Click lock icon in address bar → Notifications → Allow
   - Firefox: Click shield icon → Permissions → Notifications → Allow
   - Edge: Click lock icon → Permissions → Notifications → Allow

2. System will automatically request permission on page load
3. Click "Allow" when prompted

### 3. Missing Icon Error (404)

**Symptoms:**
- Console shows: "GET http://localhost/vendor/img/icons/icon-192x192.png 404"

**Solution:**
- This is now fixed - system uses `vendor/img/logo.png` instead
- If logo.png is missing, notification still works (just without icon)
- Add your logo to `admin/vendor/img/logo.png` for branded notifications

### 4. Custom Sound Not Loading

**Symptoms:**
- Console shows: "Custom sound not available, will use Web API"

**Solution:**
- This is normal if sound files are not present
- System automatically uses Web Audio API beep
- To add custom sounds:
  1. Create directory: `admin/vendor/sounds/`
  2. Add files: `arived.mp3` (dashboard) and `notification.mp3` (notifications page)
  3. Refresh page

### 5. Marquee Not Updating

**Symptoms:**
- Marquee shows "Loading recent notifications..."
- No booking information displayed

**Solution:**
1. Check if `get-recent-notifications.php` exists
2. Verify database connection in `vendor/inc/config.php`
3. Check browser console for AJAX errors
4. Ensure you're logged in as admin

### 6. Notifications Page Shows Errors

**Symptoms:**
- PHP errors on notifications page
- Database connection issues

**Solution:**
1. Verify database tables exist:
   - `tms_service_booking`
   - `tms_user`
   - `tms_service`
   - `tms_technician`
2. Check database credentials in config
3. Ensure proper session handling

## Testing the System

### Test Audio Notifications
1. Open admin dashboard
2. Click anywhere on the page (initializes audio)
3. Open browser console (F12)
4. Look for: "✅ Audio system ready"
5. Create a test booking from user panel
6. Should hear sound and see notification

### Test Browser Notifications
1. Allow notifications when prompted
2. Create a test booking
3. Desktop notification should appear
4. Click notification to focus window

### Test Marquee
1. Open admin dashboard
2. Wait 2-3 seconds for marquee to load
3. Should show recent booking activities
4. Hover over marquee - it should pause
5. Move mouse away - it should resume

### Test Notifications Page
1. Click "Notifications" in sidebar
2. Should see all bookings with filters
3. Try filtering by status
4. Try searching for bookings
5. Check pagination if many bookings

## Console Messages Reference

### Success Messages
- ✅ Audio system ready
- ✅ Custom notification sound loaded
- 🔊 Custom sound played
- 🔊 Web API beep played
- 🔔 Browser notification shown
- ✅ Notification system active

### Warning Messages
- ⚠️ Custom sound failed (fallback activated)
- ⚠️ Audio context not supported
- ⚠️ Browser notification failed
- ℹ️ Custom sound not available, will use Web API

### Error Messages
- ❌ Error playing sound
- ❌ Check notifications error
- ❌ AJAX Error

## Browser Compatibility

### Fully Supported
- Chrome 80+
- Firefox 75+
- Edge 80+
- Safari 13+

### Partial Support
- Older browsers: No audio, but visual notifications work
- Mobile browsers: May have audio restrictions

## Performance Tips

1. **Reduce Check Interval**: If server load is high
   - Dashboard: Change from 10s to 30s
   - Notifications page: Change from 15s to 30s

2. **Disable Auto-Reload**: If you don't want page refresh
   - Comment out the `location.reload()` line
   - Notifications will still show, page won't refresh

3. **Optimize Database**: Add indexes
   ```sql
   CREATE INDEX idx_booking_status ON tms_service_booking(sb_status);
   CREATE INDEX idx_booking_created ON tms_service_booking(sb_created_at);
   ```

## Getting Help

If issues persist:
1. Check browser console for errors
2. Verify all files are uploaded correctly
3. Test database connection
4. Clear browser cache and cookies
5. Try different browser

## Debug Mode

Enable detailed logging:
```javascript
// Add to console
localStorage.setItem('debug', 'true');
// Reload page
```

This will show additional debug information in console.
