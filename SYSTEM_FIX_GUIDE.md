# 🔧 Complete System Fix Guide

## Quick Fix - Run This First!

**Step 1:** Visit this URL in your browser:
```
http://your-domain.com/fix-all-logics.php
```

This will automatically:
- ✅ Create all missing database columns
- ✅ Create upload folders
- ✅ Set proper permissions
- ✅ Standardize booking statuses
- ✅ Reset technician availability
- ✅ Verify database integrity
- ✅ Test all critical queries

---

## What Was Fixed

### 1. **Database Structure**
- Added `sb_service_image` - Stores service photo path
- Added `sb_bill_image` - Stores bill photo path
- Added `sb_amount_charged` - Stores charged amount
- Added `sb_completed_at` - Completion timestamp
- Added `sb_not_done_reason` - Reason for not completing
- Added `sb_not_done_at` - Not done timestamp
- Added `t_is_available` - Technician availability flag
- Added `t_current_booking_id` - Current booking tracking

### 2. **Upload System**
- Created `uploads/` folder
- Created `uploads/service_images/` folder
- Created `uploads/bill_images/` folder
- Set 777 permissions on all folders

### 3. **Booking Statuses**
Standardized to:
- `Pending` - New booking
- `In Progress` - Being worked on
- `Completed` - Successfully completed
- `Not Done` - Could not complete
- `Rejected` - Rejected by technician
- `Cancelled` - Cancelled by admin

### 4. **Technician Availability**
- All technicians reset to "Available"
- Cleared current booking assignments
- Auto-updates when booking completed/not done

---

## Testing Checklist

### Technician Side:
- [ ] Login to technician dashboard
- [ ] See list of bookings
- [ ] Click "Done" on a booking
- [ ] Upload service image (camera or gallery)
- [ ] Upload bill image (camera or gallery)
- [ ] Enter amount charged
- [ ] Click "Complete Service"
- [ ] Verify redirect to dashboard
- [ ] See success message
- [ ] Booking moves to bottom (completed section)
- [ ] Try "Not Done" on another booking
- [ ] Enter reason
- [ ] Submit
- [ ] Verify redirect and message

### Admin Side:
- [ ] Login to admin dashboard
- [ ] See "Rejected / Not Done" card
- [ ] Click on card
- [ ] See list of rejected bookings
- [ ] See technician name
- [ ] See reason provided
- [ ] See timestamp
- [ ] Click "Reassign" button
- [ ] Select new technician
- [ ] Confirm reassignment

---

## File Structure

```
project/
├── fix-all-logics.php          # Main fix script (RUN THIS FIRST)
├── tech/
│   ├── dashboard.php            # Technician dashboard
│   ├── complete-booking.php     # Complete/reject booking
│   ├── check-system.php         # System diagnostics
│   ├── debug-complete.php       # Debug completion system
│   └── test-complete.php        # Test completion
├── admin/
│   ├── admin-dashboard.php      # Admin dashboard
│   ├── admin-rejected-bookings.php  # Rejected bookings page
│   └── get-available-technicians.php # Available techs
└── uploads/
    ├── service_images/          # Service photos
    └── bill_images/             # Bill photos
```

---

## Common Issues & Solutions

### Issue 1: "Service image is required"
**Cause:** Image not uploaded or upload failed
**Fix:** 
- Check if folders exist: `uploads/service_images/`
- Check folder permissions: `chmod 777 uploads/service_images/`
- Try uploading a smaller image
- Check PHP upload limits in php.ini

### Issue 2: "No rows updated"
**Cause:** Booking doesn't exist or doesn't belong to technician
**Fix:**
- Verify booking ID is correct
- Check if booking is assigned to this technician
- Run `fix-all-logics.php` to check database integrity

### Issue 3: "Status already set"
**Cause:** Booking already completed or marked not done
**Fix:**
- This is intentional - status is permanent
- Admin can reassign if needed
- Use `?force=1` parameter for testing only

### Issue 4: Bookings not showing
**Cause:** Database query issue or missing columns
**Fix:**
- Run `fix-all-logics.php`
- Visit `tech/check-system.php` for diagnostics
- Check browser console for JavaScript errors

### Issue 5: Images not displaying
**Cause:** Wrong path or permissions
**Fix:**
- Check if images exist in `uploads/service_images/`
- Verify folder permissions (777)
- Check image paths in database

### Issue 6: Admin can't see rejected bookings
**Cause:** Status mismatch or query issue
**Fix:**
- Run `fix-all-logics.php` to standardize statuses
- Check if bookings have status "Not Done" or "Rejected"
- Visit `admin/admin-rejected-bookings.php` directly

---

## Database Queries for Manual Fix

If automatic fix doesn't work, run these manually:

```sql
-- Add booking columns
ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_service_image VARCHAR(255) DEFAULT NULL;
ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_bill_image VARCHAR(255) DEFAULT NULL;
ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_amount_charged DECIMAL(10,2) DEFAULT NULL;
ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_completed_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_not_done_reason TEXT DEFAULT NULL;
ALTER TABLE tms_service_booking ADD COLUMN IF NOT EXISTS sb_not_done_at TIMESTAMP NULL DEFAULT NULL;

-- Add technician columns
ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_is_available TINYINT(1) DEFAULT 1;
ALTER TABLE tms_technician ADD COLUMN IF NOT EXISTS t_current_booking_id INT DEFAULT NULL;

-- Reset technician availability
UPDATE tms_technician SET t_is_available = 1, t_current_booking_id = NULL;

-- Standardize statuses
UPDATE tms_service_booking SET sb_status = 'Pending' WHERE sb_status IN ('New', 'Assigned');
UPDATE tms_service_booking SET sb_status = 'In Progress' WHERE sb_status = 'Processing';
```

---

## Support & Diagnostics

### Diagnostic Tools:
1. **System Check:** `tech/check-system.php`
   - Shows all database columns
   - Shows technician status
   - Shows booking counts
   - Shows folder permissions

2. **Debug Complete:** `tech/debug-complete.php`
   - Tests completion system
   - Shows recent bookings
   - Provides test form

3. **Test Complete:** `tech/test-complete.php`
   - Quick test interface
   - Shows available bookings
   - One-click testing

### Log Files:
- Check PHP error log for detailed errors
- Check browser console for JavaScript errors
- Check network tab for AJAX failures

---

## Success Indicators

✅ **System is working when:**
- Technician can see bookings on dashboard
- Can upload images using camera or gallery
- Status changes to "Completed" after submission
- Success message appears and auto-hides
- Completed bookings move to bottom
- Admin sees rejected bookings in separate page
- Admin dashboard shows correct count
- Technician becomes available after completion

---

## Contact & Next Steps

After running `fix-all-logics.php`:
1. Clear browser cache
2. Logout and login again
3. Test technician completion flow
4. Test admin rejected bookings view
5. Verify all counts are correct

If issues persist:
- Run `tech/check-system.php` for diagnostics
- Check specific error messages
- Verify database structure manually
- Check file permissions
