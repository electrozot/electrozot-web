# Setup Guide: One-Booking-Per-Technician Rule

## Quick Setup (3 Steps)

### Step 1: Run Database Updates

Execute the SQL script to add necessary columns:

```bash
# Option A: Via phpMyAdmin
1. Open phpMyAdmin
2. Select your database
3. Go to SQL tab
4. Copy and paste contents of: DATABASE FILE/add_technician_engagement_columns.sql
5. Click "Go"

# Option B: Via MySQL command line
mysql -u your_username -p your_database < "DATABASE FILE/add_technician_engagement_columns.sql"
```

### Step 2: Verify Installation

Visit the test page to verify everything is working:

```
http://your-domain/admin/test-technician-engagement.php
```

You should see:
- ✅ All technicians listed
- ✅ Their engagement status (Available/Engaged)
- ✅ Current booking assignments
- ✅ Statistics

### Step 3: Test the System

1. **Test Fresh Assignment:**
   - Go to a pending booking
   - Click "Assign Technician"
   - Notice only available technicians are shown
   - Assign a technician
   - Check test page - technician should now show as "Engaged"

2. **Test Double Assignment Prevention:**
   - Try to assign the same technician to another booking
   - System should show error message
   - Technician should NOT appear in available list

3. **Test Completion:**
   - Have technician complete the booking
   - Check test page - technician should now show as "Available"
   - Technician should now appear for new assignments

---

## Files Added/Modified

### New Files Created:

1. **`admin/check-technician-availability.php`**
   - Core availability checking functions
   - AJAX API endpoints
   - Engagement validation logic

2. **`admin/test-technician-engagement.php`**
   - Visual dashboard for monitoring
   - Real-time engagement status
   - Statistics and reporting

3. **`DATABASE FILE/add_technician_engagement_columns.sql`**
   - Database schema updates
   - Initialization queries
   - Maintenance queries

4. **`TECHNICIAN_ONE_BOOKING_RULE.md`**
   - Complete documentation
   - User flows and examples
   - Troubleshooting guide

5. **`SETUP_ONE_BOOKING_RULE.md`** (this file)
   - Quick setup instructions

### Modified Files:

1. **`admin/admin-assign-technician.php`**
   - Added availability checking
   - Prevents double assignments
   - Shows only available technicians

2. **`tech/complete-booking.php`**
   - Auto-frees technician on completion
   - Auto-frees technician on rejection
   - Updates all status fields

---

## Configuration

No configuration needed! The system works automatically once database is updated.

### Optional: Customize Error Messages

Edit `admin/admin-assign-technician.php` around line 35:

```php
$err = "Technician is currently engaged with Booking #" . 
       $new_tech_engagement['booking_id'] . 
       " (Status: " . $new_tech_engagement['booking_status'] . "). " .
       "Please wait until they complete or reject that booking.";
```

---

## Testing Checklist

- [ ] Database columns added successfully
- [ ] Test page loads without errors
- [ ] Can see all technicians and their status
- [ ] Can assign available technician to booking
- [ ] Assigned technician shows as "Engaged"
- [ ] Cannot assign engaged technician to another booking
- [ ] Technician becomes available after completing booking
- [ ] Technician becomes available after rejecting booking
- [ ] Reassignment works correctly
- [ ] Change technician works correctly

---

## API Usage (Optional)

If you want to integrate with other systems:

### Check Technician Engagement

```javascript
fetch('admin/check-technician-availability.php?action=check_engagement&technician_id=5')
  .then(response => response.json())
  .then(data => {
    if(data.is_engaged) {
      console.log('Technician is engaged with booking:', data.booking_id);
    } else {
      console.log('Technician is available');
    }
  });
```

### Get Available Technicians

```javascript
fetch('admin/check-technician-availability.php?action=get_available&category=Electrical')
  .then(response => response.json())
  .then(technicians => {
    console.log('Available technicians:', technicians);
  });
```

### Get All Technicians Summary

```javascript
fetch('admin/check-technician-availability.php?action=get_summary')
  .then(response => response.json())
  .then(summary => {
    console.log('Technician summary:', summary);
  });
```

---

## Maintenance

### Weekly Check (Recommended)

Run this query to ensure no inconsistencies:

```sql
-- Check for orphaned engaged status
SELECT t.t_id, t.t_name, t.t_status, t.t_current_booking_id
FROM tms_technician t
WHERE t.t_status = 'Booked'
AND t.t_id NOT IN (
    SELECT DISTINCT sb_technician_id
    FROM tms_service_booking
    WHERE sb_status NOT IN ('Completed', 'Rejected', 'Cancelled', 'Not Done')
    AND sb_technician_id IS NOT NULL
);
```

If any results, run the fix query from `add_technician_engagement_columns.sql`.

### Monthly Report

Visit `admin/test-technician-engagement.php` to review:
- Technician utilization
- Average engagement time
- Booking distribution

---

## Troubleshooting

### Problem: Technician stuck as "Engaged"

**Solution:**
```sql
-- Replace 5 with the technician ID
UPDATE tms_technician 
SET t_status = 'Available', 
    t_is_available = 1, 
    t_current_booking_id = NULL 
WHERE t_id = 5;
```

### Problem: No technicians showing as available

**Check:**
1. Are all technicians actually engaged? (Check test page)
2. Do technicians match the service category?
3. Run the maintenance query to fix orphaned statuses

### Problem: Error when assigning technician

**Check:**
1. Is `check-technician-availability.php` included?
2. Are database columns added?
3. Check PHP error logs

---

## Support

For issues or questions:
1. Check `TECHNICIAN_ONE_BOOKING_RULE.md` for detailed documentation
2. Review the test page for current system state
3. Run verification queries from SQL script
4. Check PHP error logs

---

## Success Indicators

✅ System is working correctly if:
- Only available technicians appear in assignment dropdown
- Engaged technicians show error when trying to assign
- Technicians automatically become available after completion/rejection
- Test page shows accurate real-time status
- No orphaned engaged statuses in database

---

## Next Steps

After setup is complete:
1. Train admin staff on the new system
2. Inform technicians about the one-booking rule
3. Monitor the test page regularly
4. Set up weekly maintenance checks
5. Review booking distribution monthly

**The system is now ready to use!** 🎉
