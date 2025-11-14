# ✅ ElectroZot Database Testing - Execution Checklist

## Pre-Execution Checklist

- [ ] XAMPP/WAMP is running
- [ ] MySQL service is active
- [ ] phpMyAdmin is accessible
- [ ] Database `electrozot_db` exists
- [ ] Database connection works (check vendor/inc/config.php)
- [ ] **BACKUP CREATED** (very important!)

---

## Step 1: Insert Sample Users ✅

- [ ] Open phpMyAdmin
- [ ] Select `electrozot_db` database
- [ ] Click "SQL" tab
- [ ] Open file: `sample_data_insert.sql`
- [ ] Copy lines 15-40 (INSERT SAMPLE USERS section)
- [ ] Paste into SQL tab
- [ ] Click "Go"
- [ ] Verify: "25 rows inserted" message appears
- [ ] Check: `SELECT COUNT(*) FROM tms_user;` should return 26

**Expected Result:** 26 total users (1 existing + 25 new)

---

## Step 2: Insert Sample Technicians ✅

- [ ] In phpMyAdmin SQL tab
- [ ] Open file: `sample_data_insert.sql`
- [ ] Copy the "INSERT SAMPLE TECHNICIANS" section
- [ ] Paste into SQL tab
- [ ] Click "Go"
- [ ] Verify: "25 rows inserted" message appears
- [ ] Check: `SELECT COUNT(*) FROM tms_technician;` should return 30

**Expected Result:** 30 total technicians (5 existing + 25 new)

---

## Step 3: Insert Sample Bookings ✅

- [ ] In phpMyAdmin SQL tab
- [ ] Open file: `insert_bookings.sql` (NOT sample_data_insert.sql)
- [ ] Copy entire file content
- [ ] Paste into SQL tab
- [ ] Click "Go"
- [ ] Verify: "30 rows inserted" message appears
- [ ] Check: `SELECT COUNT(*) FROM tms_service_booking;` should return 30

**Expected Result:** 30 total bookings (all new)

---

## Step 4: Verify Data Insertion ✅

Run these queries in phpMyAdmin:

### Query 1: Total Counts
```sql
SELECT 
    (SELECT COUNT(*) FROM tms_user) as users,
    (SELECT COUNT(*) FROM tms_technician) as technicians,
    (SELECT COUNT(*) FROM tms_service_booking) as bookings;
```
- [ ] Users: 26 ✓
- [ ] Technicians: 30 ✓
- [ ] Bookings: 30 ✓

### Query 2: Booking Status Distribution
```sql
SELECT sb_status, COUNT(*) as count 
FROM tms_service_booking 
GROUP BY sb_status;
```
- [ ] Pending: 5 ✓
- [ ] Assigned: 5 ✓
- [ ] In Progress: 5 ✓
- [ ] Completed: 5 ✓
- [ ] Rejected: 5 ✓
- [ ] Cancelled: 5 ✓

### Query 3: Technician Distribution
```sql
SELECT t_category, COUNT(*) as count 
FROM tms_technician 
GROUP BY t_category;
```
- [ ] Electrical: 6 ✓
- [ ] Plumbing: 6 ✓
- [ ] HVAC: 6 ✓
- [ ] Appliance: 6 ✓
- [ ] General: 6 ✓

---

## Step 5: Run Database Tests ✅

### Option A: SQL Tests
- [ ] Open file: `test_database_logic.sql`
- [ ] Copy entire content
- [ ] Paste into phpMyAdmin SQL tab
- [ ] Click "Go"
- [ ] Review all test results
- [ ] Verify no errors in Test 9 (Data Integrity)

### Option B: Web Interface
- [ ] Open browser
- [ ] Navigate to: `http://localhost/electrozot/test-database.php`
- [ ] Wait for page to load
- [ ] Check all sections show green checkmarks
- [ ] Verify counts match expected values

**Expected Results:**
- [ ] All tests pass ✓
- [ ] No orphaned bookings ✓
- [ ] No category mismatches ✓
- [ ] All integrity checks pass ✓

---

## Step 6: Test Admin Dashboard ✅

- [ ] Open browser
- [ ] Navigate to: `http://localhost/electrozot/admin/`
- [ ] Login with: `mohit@electrozot.in` / `mohit123`
- [ ] Dashboard loads successfully
- [ ] See "Rejected/Cancelled Bookings" section
- [ ] Section shows "(10)" in header
- [ ] 10 bookings are listed
- [ ] Each booking has a [Reassign] button

**Expected Result:** Rejected bookings section visible with 10 bookings

---

## Step 7: Test Reassignment Feature ✅

- [ ] Click [Reassign] button on any rejected booking
- [ ] Modal opens
- [ ] Modal shows booking details
- [ ] Modal shows list of available technicians
- [ ] Technicians are filtered by service category
- [ ] Select a technician
- [ ] Click "Reassign Booking"
- [ ] Success message appears
- [ ] Booking disappears from rejected list (or status changes)
- [ ] Verify in database: `SELECT * FROM tms_service_booking WHERE sb_id = [booking_id];`

**Expected Result:** Booking status changes to "Assigned" and has new technician_id

---

## Step 8: Data Integrity Verification ✅

Run these verification queries:

### Check 1: No Orphaned Bookings
```sql
SELECT COUNT(*) as orphaned FROM tms_service_booking sb
LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
WHERE u.u_id IS NULL;
```
- [ ] Result: 0 ✓

### Check 2: No Category Mismatches
```sql
SELECT COUNT(*) as mismatches FROM tms_service_booking sb
INNER JOIN tms_service s ON sb.sb_service_id = s.s_id
INNER JOIN tms_technician t ON sb.sb_technician_id = t.t_id
WHERE s.s_category != t.t_category;
```
- [ ] Result: 0 ✓

### Check 3: All Assigned Bookings Have Technicians
```sql
SELECT COUNT(*) as invalid FROM tms_service_booking
WHERE sb_status IN ('Assigned', 'In Progress', 'Completed')
AND sb_technician_id IS NULL;
```
- [ ] Result: 0 ✓

### Check 4: Revenue Calculation
```sql
SELECT SUM(sb_total_price) as total_revenue,
       AVG(sb_total_price) as avg_booking
FROM tms_service_booking;
```
- [ ] Total Revenue: ~৳4,200 ✓
- [ ] Average Booking: ~৳140 ✓

---

## Step 9: Test Booking Flow ✅

### Create New Booking (Optional)
- [ ] Go to user booking page
- [ ] Fill in booking form
- [ ] Submit booking
- [ ] Verify booking appears in database
- [ ] Status should be "Pending"

### Assign Booking (Admin)
- [ ] Login to admin dashboard
- [ ] Find pending booking
- [ ] Assign technician
- [ ] Verify status changes to "Assigned"

### Accept Booking (Technician)
- [ ] Login as technician (if implemented)
- [ ] See assigned booking
- [ ] Accept booking
- [ ] Verify status changes to "In Progress"

---

## Step 10: Performance Check ✅

- [ ] All pages load in < 2 seconds
- [ ] No PHP errors in browser console
- [ ] No MySQL errors in phpMyAdmin
- [ ] Database queries execute quickly (< 100ms)
- [ ] Admin dashboard responsive
- [ ] Modals open/close smoothly

---

## Final Verification Checklist ✅

### Database
- [ ] 26+ users in database
- [ ] 30+ technicians in database
- [ ] 30+ bookings in database
- [ ] All foreign keys valid
- [ ] No orphaned records
- [ ] No data integrity issues

### Admin Dashboard
- [ ] Dashboard loads successfully
- [ ] Statistics show correct counts
- [ ] Rejected bookings section visible
- [ ] Reassignment modal works
- [ ] Success messages appear
- [ ] No JavaScript errors

### Testing Interface
- [ ] test-database.php loads
- [ ] All tests show green
- [ ] No red error messages
- [ ] Counts match expected values
- [ ] Revenue calculations correct

### Documentation
- [ ] All SQL files present
- [ ] README files readable
- [ ] Instructions clear
- [ ] Visual guide helpful

---

## Troubleshooting Checklist ✅

If something doesn't work:

### Issue: Foreign Key Error
- [ ] Check users inserted before bookings
- [ ] Check technicians inserted before bookings
- [ ] Use `insert_bookings.sql` not `sample_data_insert.sql`

### Issue: Duplicate Entry Error
- [ ] Check if data already exists
- [ ] Run cleanup queries
- [ ] Try again with fresh database

### Issue: No Rejected Bookings in Dashboard
- [ ] Verify bookings inserted: `SELECT COUNT(*) FROM tms_service_booking WHERE sb_status='Rejected'`
- [ ] Check admin dashboard query
- [ ] Clear browser cache
- [ ] Check PHP errors

### Issue: Modal Not Opening
- [ ] Check JavaScript console for errors
- [ ] Verify Bootstrap JS loaded
- [ ] Check jQuery loaded
- [ ] Test in different browser

### Issue: Category Mismatch
- [ ] This might be intentional for testing
- [ ] Check service category matches technician category
- [ ] Review booking assignment logic

---

## Success Criteria ✅

You're done when ALL of these are true:

- [x] ✅ Sample data inserted without errors
- [x] ✅ All 60+ tests pass
- [x] ✅ Admin dashboard shows rejected bookings
- [x] ✅ Reassignment modal works
- [x] ✅ No data integrity issues
- [x] ✅ All foreign keys valid
- [x] ✅ Revenue calculations correct
- [x] ✅ test-database.php shows all green
- [x] ✅ No PHP/MySQL errors
- [x] ✅ Performance acceptable

---

## Post-Testing Actions ✅

After successful testing:

- [ ] Document any issues found
- [ ] Note any improvements needed
- [ ] Plan next testing phase
- [ ] Consider load testing with more data
- [ ] Test edge cases
- [ ] Test error handling
- [ ] Test security measures

---

## Optional: Load Testing ✅

If you want to test with more data:

- [ ] Modify sample data scripts to insert 100+ records
- [ ] Test query performance with large dataset
- [ ] Check page load times
- [ ] Verify pagination works
- [ ] Test search functionality
- [ ] Monitor database size

---

## Cleanup (If Needed) ✅

To remove sample data:

```sql
-- Delete all bookings
DELETE FROM tms_service_booking WHERE sb_id > 0;

-- Delete sample users (keep original)
DELETE FROM tms_user WHERE u_id > 13;

-- Delete sample technicians (keep original 5)
DELETE FROM tms_technician WHERE t_id > 7;

-- Reset auto-increment
ALTER TABLE tms_service_booking AUTO_INCREMENT = 1;
ALTER TABLE tms_user AUTO_INCREMENT = 14;
ALTER TABLE tms_technician AUTO_INCREMENT = 8;
```

- [ ] Backup before cleanup
- [ ] Run cleanup queries
- [ ] Verify data removed
- [ ] Test with fresh data

---

## Notes & Observations

Use this space to note any issues or observations:

```
Date: _______________
Tester: _______________

Issues Found:
1. 
2. 
3. 

Improvements Needed:
1. 
2. 
3. 

Performance Notes:
- Page load time: _______
- Query execution time: _______
- Database size: _______

Additional Comments:




```

---

## Sign-Off ✅

- [ ] All tests completed successfully
- [ ] All issues documented
- [ ] Database ready for development
- [ ] Team notified of results

**Tested By:** _______________
**Date:** _______________
**Status:** ☐ PASS  ☐ FAIL  ☐ NEEDS REVIEW

---

**🎉 Congratulations! Your database is fully tested and ready to use!**
