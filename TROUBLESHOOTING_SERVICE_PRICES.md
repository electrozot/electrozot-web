# 🔧 Service Prices - Troubleshooting Guide

## Common Issues & Solutions

---

## 🚨 Admin Issues

### Issue 1: Service Prices Menu Not Showing

**Symptoms:**
- Can't see "Service Prices" in Services menu
- Menu only shows "Add Service" and "Manage All"

**Solutions:**
1. **Clear browser cache**
   ```
   Ctrl + Shift + Delete (Windows)
   Cmd + Shift + Delete (Mac)
   ```

2. **Hard refresh the page**
   ```
   Ctrl + F5 (Windows)
   Cmd + Shift + R (Mac)
   ```

3. **Check file exists**
   - Verify `admin/admin-service-prices.php` exists
   - Check file permissions (should be readable)

4. **Check sidebar modification**
   - Open `admin/vendor/inc/sidebar.php`
   - Look for "Service Prices" menu item
   - Should be under Services dropdown

**Still not working?**
- Logout and login again
- Try different browser
- Contact system administrator

---

### Issue 2: Page Loads But No Services Show

**Symptoms:**
- Service Prices page opens
- Statistics show 0 services
- No service list appears

**Solutions:**
1. **Check database connection**
   - Verify database is running
   - Check connection credentials

2. **Verify services exist**
   ```sql
   SELECT COUNT(*) FROM tms_service;
   ```
   Should return 43 (or your service count)

3. **Check for errors**
   - Open browser console (F12)
   - Look for JavaScript errors
   - Check PHP error logs

4. **Run setup script**
   - Visit `admin/setup-service-prices.php`
   - Check for any errors in setup log

**Still not working?**
- Check database table structure
- Verify tms_service table exists
- Contact database administrator

---

### Issue 3: Prices Not Saving

**Symptoms:**
- Enter prices and click Update
- Success message appears
- Prices revert to empty on refresh

**Solutions:**
1. **Check database column**
   ```sql
   SHOW COLUMNS FROM tms_service LIKE 's_admin_price';
   ```
   Should show DECIMAL(10,2) column

2. **Add column if missing**
   ```sql
   ALTER TABLE tms_service 
   ADD COLUMN s_admin_price DECIMAL(10,2) DEFAULT NULL;
   ```

3. **Check permissions**
   - Verify database user has UPDATE permission
   - Check file write permissions

4. **Test with one service**
   - Set price for just one service
   - Check if it saves
   - If yes, try bulk update again

**Still not working?**
- Check PHP error logs
- Verify database connection
- Contact system administrator

---

### Issue 4: Statistics Not Updating

**Symptoms:**
- Set prices but statistics don't change
- Numbers seem incorrect

**Solutions:**
1. **Refresh the page**
   - Hard refresh (Ctrl + F5)
   - Statistics calculate on page load

2. **Check database values**
   ```sql
   SELECT 
     COUNT(*) as total,
     COUNT(s_admin_price) as priced
   FROM tms_service;
   ```

3. **Clear cache**
   - Browser cache
   - Server cache if applicable

**Still not working?**
- Check query in admin-service-prices.php
- Verify database data integrity

---

### Issue 5: Bulk Update Takes Too Long

**Symptoms:**
- Click Update All Prices
- Page hangs or times out
- No response for long time

**Solutions:**
1. **Increase PHP timeout**
   ```php
   set_time_limit(300); // 5 minutes
   ```

2. **Update in batches**
   - Update 10-15 services at a time
   - Save between batches

3. **Check server resources**
   - CPU usage
   - Memory usage
   - Database connections

4. **Optimize database**
   ```sql
   OPTIMIZE TABLE tms_service;
   OPTIMIZE TABLE tms_service_booking;
   ```

**Still not working?**
- Contact hosting provider
- Check server specifications
- Consider upgrading hosting plan

---

## 👨‍🔧 Technician Issues

### Issue 6: Service Prices Button Not Showing

**Symptoms:**
- Can't see Service Prices button in dashboard
- Quick bar missing the button

**Solutions:**
1. **Clear browser cache**
   - Ctrl + Shift + Delete
   - Clear all cached data

2. **Hard refresh**
   - Ctrl + F5 (Windows)
   - Cmd + Shift + R (Mac)

3. **Check file modification**
   - Verify `tech/includes/nav.php` updated
   - Look for Service Prices button code

4. **Try different device**
   - Check on mobile
   - Check on different browser

**Still not working?**
- Logout and login again
- Contact system administrator

---

### Issue 7: Prices Not Displaying

**Symptoms:**
- Service Prices page opens
- No prices show
- All services show "Flexible"

**Solutions:**
1. **Check if admin set prices**
   - Admin may not have set prices yet
   - Contact admin to set prices

2. **Verify database column**
   ```sql
   SELECT s_name, s_admin_price 
   FROM tms_service 
   LIMIT 5;
   ```

3. **Refresh the page**
   - Hard refresh (Ctrl + F5)

**Still not working?**
- Check with admin if prices are set
- Verify database connection

---

### Issue 8: Price Field Not Locked During Completion

**Symptoms:**
- Admin set a price
- But field is still editable
- Should be locked but isn't

**Solutions:**
1. **Verify admin price is set**
   - Check Service Prices page
   - Confirm price shows with 🔒 badge

2. **Refresh booking details**
   - Go back to bookings list
   - Re-open the booking
   - Try completion again

3. **Check booking service**
   - Verify correct service selected
   - Check if service has admin price

4. **Clear session**
   - Logout
   - Clear browser cache
   - Login again

**Still not working?**
- Contact system administrator
- Report the specific booking ID

---

### Issue 9: Can't Enter Price for Flexible Service

**Symptoms:**
- Service should be flexible
- But price field is locked
- Shows as admin-set but shouldn't

**Solutions:**
1. **Verify service is flexible**
   - Check Service Prices page
   - Should show ✏️ Flexible badge

2. **Check with admin**
   - Admin may have set price recently
   - Verify current price status

3. **Refresh the page**
   - Hard refresh
   - Try again

**Still not working?**
- Contact admin to verify price status
- Report the specific service

---

## 💰 Booking & Pricing Issues

### Issue 10: Booking Shows Wrong Price

**Symptoms:**
- Admin set price ₹500
- Booking shows different amount
- Price doesn't match

**Solutions:**
1. **Check booking status**
   - Completed bookings don't update
   - Only Pending/In Progress update

2. **Check when price was set**
   - If booking created before price set
   - May need manual update

3. **Verify service**
   - Confirm correct service selected
   - Check if service has admin price

4. **Refresh booking details**
   - Close and reopen booking
   - Check if price updates

**Still not working?**
- Contact admin to manually update
- Report booking ID

---

### Issue 11: New Booking Not Using Admin Price

**Symptoms:**
- Admin set price ₹500
- New booking shows ₹0 or different price
- Should use admin price

**Solutions:**
1. **Verify price is saved**
   - Check Service Prices page
   - Confirm price shows correctly

2. **Check service selection**
   - Verify correct service chosen
   - Not "Other" or custom service

3. **Clear cache and retry**
   - Clear browser cache
   - Create booking again

4. **Check database**
   ```sql
   SELECT s_admin_price 
   FROM tms_service 
   WHERE s_id = [service_id];
   ```

**Still not working?**
- Check process-guest-booking.php
- Verify admin-quick-booking.php
- Contact developer

---

### Issue 12: Price Updates Not Applying to Existing Bookings

**Symptoms:**
- Changed price from ₹500 to ₹600
- Pending bookings still show ₹500
- Should update automatically

**Solutions:**
1. **Check booking status**
   - Only Pending/In Progress update
   - Completed/Cancelled don't update

2. **Wait a moment**
   - Updates may take a few seconds
   - Refresh the page

3. **Manual update if needed**
   ```sql
   UPDATE tms_service_booking sb
   INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
   SET sb.sb_total_price = s.s_admin_price
   WHERE s.s_admin_price IS NOT NULL 
     AND sb.sb_status IN ('Pending', 'In Progress');
   ```

**Still not working?**
- Check database triggers
- Verify update query in code
- Contact developer

---

## 🗄️ Database Issues

### Issue 13: Column s_admin_price Doesn't Exist

**Symptoms:**
- Error: Unknown column 's_admin_price'
- Database errors in logs
- Features not working

**Solutions:**
1. **Run migration script**
   ```sql
   ALTER TABLE tms_service 
   ADD COLUMN s_admin_price DECIMAL(10,2) DEFAULT NULL;
   ```

2. **Use setup script**
   - Visit `admin/setup-service-prices.php`
   - Follow setup instructions

3. **Verify column added**
   ```sql
   SHOW COLUMNS FROM tms_service;
   ```
   Should show s_admin_price column

**Still not working?**
- Check database permissions
- Contact database administrator

---

### Issue 14: Data Type Mismatch

**Symptoms:**
- Prices save incorrectly
- Decimal places missing
- Numbers rounded unexpectedly

**Solutions:**
1. **Check column type**
   ```sql
   SHOW COLUMNS FROM tms_service LIKE 's_admin_price';
   ```
   Should be DECIMAL(10,2)

2. **Fix column type**
   ```sql
   ALTER TABLE tms_service 
   MODIFY COLUMN s_admin_price DECIMAL(10,2);
   ```

3. **Re-enter prices**
   - Go to Service Prices page
   - Re-enter affected prices
   - Save again

**Still not working?**
- Check database version
- Verify DECIMAL support
- Contact database administrator

---

## 🌐 Browser Issues

### Issue 15: Page Layout Broken

**Symptoms:**
- Page looks weird
- Buttons misaligned
- Text overlapping

**Solutions:**
1. **Clear browser cache**
   - Ctrl + Shift + Delete
   - Clear cached images and files

2. **Hard refresh**
   - Ctrl + F5 (Windows)
   - Cmd + Shift + R (Mac)

3. **Try different browser**
   - Chrome, Firefox, Safari, Edge
   - Check if issue persists

4. **Check CSS files**
   - Verify Bootstrap CSS loaded
   - Check custom CSS files

**Still not working?**
- Disable browser extensions
- Try incognito/private mode
- Contact support

---

### Issue 16: JavaScript Not Working

**Symptoms:**
- Buttons don't respond
- No success messages
- Forms don't submit

**Solutions:**
1. **Check browser console**
   - Press F12
   - Look for JavaScript errors
   - Note error messages

2. **Enable JavaScript**
   - Check browser settings
   - Ensure JavaScript enabled

3. **Check jQuery loaded**
   - Open console
   - Type: `jQuery.fn.jquery`
   - Should show version number

4. **Clear cache**
   - Clear browser cache
   - Hard refresh page

**Still not working?**
- Disable ad blockers
- Check firewall settings
- Try different browser

---

## 📱 Mobile Issues

### Issue 17: Mobile View Not Working

**Symptoms:**
- Page doesn't fit screen
- Buttons too small
- Hard to navigate

**Solutions:**
1. **Use landscape mode**
   - Rotate device
   - Better for data entry

2. **Zoom in/out**
   - Pinch to zoom
   - Adjust to comfortable size

3. **Use desktop site**
   - Request desktop version
   - In browser settings

4. **Update browser**
   - Update to latest version
   - Try different mobile browser

**Still not working?**
- Use desktop/laptop instead
- Contact support for mobile optimization

---

## 🔐 Permission Issues

### Issue 18: Access Denied

**Symptoms:**
- Can't access Service Prices page
- "Permission denied" error
- Redirected to login

**Solutions:**
1. **Verify login**
   - Ensure logged in as admin/technician
   - Check session not expired

2. **Check user role**
   - Admin: Should have full access
   - Technician: View-only access

3. **Re-login**
   - Logout completely
   - Clear cookies
   - Login again

4. **Check permissions**
   - Verify user account active
   - Check role assignments

**Still not working?**
- Contact system administrator
- Verify account status

---

## 📞 Getting Help

### Before Contacting Support

Gather this information:
- [ ] What were you trying to do?
- [ ] What happened instead?
- [ ] Error messages (exact text)
- [ ] Screenshots if possible
- [ ] Browser and version
- [ ] Device type (desktop/mobile)
- [ ] When did it start?
- [ ] Does it happen every time?

### Contact Information

**System Administrator:**
- Email: [admin email]
- Phone: [admin phone]

**Technical Support:**
- Email: [support email]
- Phone: [support phone]

**Emergency Issues:**
- Critical bugs
- Data loss
- Security concerns

---

## 🛠️ Developer Debug Commands

### Check Database
```sql
-- Verify column exists
SHOW COLUMNS FROM tms_service LIKE 's_admin_price';

-- Check prices set
SELECT COUNT(*) FROM tms_service WHERE s_admin_price IS NOT NULL;

-- View sample data
SELECT s_id, s_name, s_price, s_admin_price FROM tms_service LIMIT 10;

-- Check bookings
SELECT sb_id, sb_service_id, sb_total_price, sb_status 
FROM tms_service_booking 
WHERE sb_service_id IN (SELECT s_id FROM tms_service WHERE s_admin_price IS NOT NULL)
LIMIT 10;
```

### Check Files
```bash
# Verify files exist
ls -la admin/admin-service-prices.php
ls -la tech/service-prices.php
ls -la admin/setup-service-prices.php

# Check file permissions
chmod 644 admin/admin-service-prices.php
chmod 644 tech/service-prices.php
```

### Check PHP Errors
```php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check PHP version
echo phpversion(); // Should be 7.0+

// Check MySQL connection
mysqli_connect_errno();
```

---

## ✅ Prevention Tips

### For Admins
- Set prices during low-traffic hours
- Test with one service first
- Keep backup of prices
- Document price changes
- Train staff properly

### For Technicians
- Check prices before quoting
- Verify price status before completion
- Report issues immediately
- Keep app updated
- Clear cache regularly

### For Everyone
- Use supported browsers
- Keep software updated
- Report bugs promptly
- Follow best practices
- Read documentation

---

**Last Updated:** November 2025  
**Version:** 1.0

**Need more help? Contact your system administrator!**
