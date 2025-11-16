# Complete Booking Form Troubleshooting Guide

## Issue: Form Not Submitting

### Quick Fixes to Try:

#### 1. Check Browser Console
Open browser developer tools (F12) and check the Console tab for errors:
- Look for JavaScript errors
- Check if validation is blocking submission
- See console.log messages showing form state

#### 2. Test with Simple Form
Visit: `tech/test-complete-form.php`
- This is a simplified version without camera features
- If this works, the issue is with the camera/preview JavaScript
- If this doesn't work, the issue is with PHP/server configuration

#### 3. Enable Debug Mode
Add `&debug=1` to the URL:
```
tech/complete-booking.php?id=123&action=done&debug=1
```
This will show:
- POST data status
- File upload status
- Error codes
- Form values

#### 4. Check File Upload Limits
Edit `php.ini` or `.htaccess`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

#### 5. Check Folder Permissions
Ensure these folders exist and are writable:
```
uploads/service_images/
uploads/bill_images/
```

Windows: Right-click → Properties → Security → Edit → Add write permissions
Linux: `chmod 777 uploads/service_images uploads/bill_images`

---

## Common Issues & Solutions

### Issue 1: "Please upload service image" Alert

**Cause:** File input is empty or not properly set

**Solutions:**
1. Make sure you actually selected/captured an image
2. Check if camera capture is working (try gallery upload instead)
3. Check browser console for JavaScript errors
4. Try the test form: `tech/test-complete-form.php`

### Issue 2: Form Submits But Shows Error

**Cause:** PHP validation failing or file upload error

**Solutions:**
1. Enable debug mode: Add `&debug=1` to URL
2. Check error message carefully
3. Common file upload errors:
   - Error 1: File too large (increase upload_max_filesize)
   - Error 2: File too large (increase post_max_size)
   - Error 3: Partial upload (network issue, try again)
   - Error 4: No file uploaded (select file first)
   - Error 6: Missing temp folder (check PHP configuration)
   - Error 7: Failed to write (check folder permissions)

### Issue 3: Camera Not Working

**Cause:** Browser doesn't support camera or permissions denied

**Solutions:**
1. Use "Gallery" button instead of "Use Camera"
2. Grant camera permissions in browser
3. Try different browser (Chrome/Firefox work best)
4. On mobile: Make sure app has camera permission

### Issue 4: Nothing Happens When Clicking Submit

**Cause:** JavaScript validation blocking or form not found

**Solutions:**
1. Open browser console (F12) and check for errors
2. Look for console.log messages
3. Check if form ID is correct: `completeForm`
4. Disable JavaScript temporarily to test PHP validation

### Issue 5: "No rows updated" Error

**Cause:** Booking doesn't exist or doesn't belong to technician

**Solutions:**
1. Check booking ID in URL
2. Verify technician is assigned to this booking
3. Check if booking status is already "Completed" or "Not Done"
4. Run this query to check:
```sql
SELECT sb_id, sb_technician_id, sb_status 
FROM tms_service_booking 
WHERE sb_id = [booking_id];
```

---

## Debugging Steps

### Step 1: Check Browser Console
1. Open page
2. Press F12
3. Go to Console tab
4. Try to submit form
5. Look for errors or console.log messages

### Step 2: Check Network Tab
1. Open page
2. Press F12
3. Go to Network tab
4. Try to submit form
5. Look for POST request
6. Check request payload (should have images and amount)
7. Check response (should redirect or show error)

### Step 3: Check PHP Error Log
Location varies by server:
- XAMPP: `xampp/apache/logs/error.log`
- WAMP: `wamp/logs/php_error.log`
- Linux: `/var/log/apache2/error.log`

Look for:
- PHP errors
- File upload errors
- Database errors

### Step 4: Test Database Connection
Run this query:
```sql
SELECT sb_id, sb_technician_id, sb_status 
FROM tms_service_booking 
WHERE sb_technician_id = [your_tech_id] 
AND sb_status NOT IN ('Completed', 'Not Done')
LIMIT 5;
```

Should show bookings you can complete.

---

## Quick Test Checklist

- [ ] Browser console shows no JavaScript errors
- [ ] Can select/capture service image
- [ ] Can select/capture bill image
- [ ] Can enter amount
- [ ] Submit button is clickable (not disabled)
- [ ] Form has `enctype="multipart/form-data"`
- [ ] Upload folders exist and are writable
- [ ] PHP upload limits are sufficient
- [ ] Booking exists and belongs to technician
- [ ] Booking status is not already "Completed" or "Not Done"

---

## Testing URLs

### Test Simple Form:
```
tech/test-complete-form.php
```

### Test with Debug Mode:
```
tech/complete-booking.php?id=[booking_id]&action=done&debug=1
```

### Force Complete (bypass status check):
```
tech/complete-booking.php?id=[booking_id]&action=done&force=1
```

---

## What Was Changed

### PHP Changes:
1. Better error messages for file uploads
2. Debug mode output
3. Improved validation messages
4. Console logging for debugging

### JavaScript Changes:
1. Better form validation
2. Console logging for debugging
3. More detailed error messages
4. Checks for null/undefined values

---

## If Nothing Works

### Last Resort Solutions:

#### 1. Use Simple Test Form
Use `tech/test-complete-form.php` instead - it's simpler and more reliable.

#### 2. Disable JavaScript Validation
Comment out the form validation JavaScript temporarily:
```javascript
// completeForm.addEventListener('submit', function(e) {
//     ... validation code ...
// });
```

#### 3. Check Server Configuration
- Ensure PHP file uploads are enabled
- Check upload_max_filesize and post_max_size
- Verify folder permissions
- Check PHP error log

#### 4. Contact Support
Provide:
- Browser console errors
- PHP error log
- Debug mode output
- Network tab screenshot

---

## Success Indicators

Form is working correctly if:
- ✅ Can select/capture images
- ✅ Images show preview
- ✅ Can enter amount
- ✅ Submit button shows "Submitting..." when clicked
- ✅ Redirects to dashboard with success message
- ✅ Booking status changes to "Completed"
- ✅ Technician becomes available

---

## Additional Help

### Check These Files:
- `tech/complete-booking.php` - Main form file
- `tech/test-complete-form.php` - Simple test form
- PHP error log - Server errors
- Browser console - JavaScript errors

### Useful SQL Queries:

**Check booking status:**
```sql
SELECT * FROM tms_service_booking WHERE sb_id = [booking_id];
```

**Check technician status:**
```sql
SELECT * FROM tms_technician WHERE t_id = [tech_id];
```

**Check recent completions:**
```sql
SELECT * FROM tms_service_booking 
WHERE sb_status = 'Completed' 
ORDER BY sb_completed_at DESC 
LIMIT 5;
```

---

## Contact Information

If you still have issues after trying all the above:
1. Check browser console for errors
2. Enable debug mode
3. Try the test form
4. Check PHP error log
5. Verify database connection
6. Contact your developer with all the above information

**Remember:** The test form (`tech/test-complete-form.php`) is your best friend for debugging!
