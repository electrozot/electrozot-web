# 🔒 Automatic Technician Locking System - Testing Guide

## Overview
This system automatically locks technician accounts at **7:00 AM every day** if they haven't paid their Electrozot commission from the previous day.

## How It Works

### 1. **Daily Process (7:00 AM)**
- Cron job runs: `cron-lock-unpaid-technicians.php`
- Checks all technicians who completed jobs **yesterday**
- Calculates commission due (20% of revenue)
- Checks if commission was paid
- **Locks accounts** if payment is missing

### 2. **Technician Experience**
- Account gets locked automatically
- Cannot receive new bookings
- Sees message: *"Unpaid Electrozot charges for [date]. Amount Due: ₹XXX. Please complete payment and contact Electrozot Admin to unlock your account."*
- Must pay commission and call admin

### 3. **Admin Process**
- Admin receives payment from technician
- Admin goes to: **Admin Panel → Locked Technicians**
- Admin clicks **Unlock** button
- Technician can work again

---

## Testing Methods

### Method 1: Full Automated Test Suite (Recommended)
**Best for comprehensive testing**

1. Open browser and go to:
   ```
   http://localhost/electrozot/admin/test-auto-lock-system.php
   ```

2. The test will automatically:
   - ✅ Check database tables
   - ✅ Create test bookings from yesterday
   - ✅ Simulate some payments (some technicians pay, some don't)
   - ✅ Run the auto-lock logic
   - ✅ Verify correct technicians are locked
   - ✅ Test admin unlock functionality
   - ✅ Show detailed results

3. Review the results:
   - Green ✅ = Test passed
   - Red ❌ = Test failed
   - Yellow ⚠️ = Warning

4. Clean up test data:
   - Click **"Clean Up Test Data"** button at the bottom
   - This removes all test bookings and unlocks test technicians

**Time Required:** 2-3 minutes

---

### Method 2: Quick Manual Test
**Best for quick verification**

1. Open browser and go to:
   ```
   http://localhost/electrozot/admin/quick-test-lock.php
   ```

2. This runs the actual cron job script immediately

3. Check results:
   - See how many technicians were locked
   - View the log output

4. Verify locked accounts:
   ```
   http://localhost/electrozot/admin/admin-unlock-technician.php
   ```

**Time Required:** 1 minute

---

### Method 3: Command Line Test
**Best for developers**

1. Open Command Prompt (Windows) or Terminal

2. Navigate to admin folder:
   ```cmd
   cd C:\xampp\htdocs\electrozot\admin
   ```

3. Run the cron job:
   ```cmd
   php cron-lock-unpaid-technicians.php
   ```

4. Check output in console

5. View log file:
   ```cmd
   type cron-last-run.log
   ```

**Time Required:** 30 seconds

---

### Method 4: Real-World Simulation
**Best for production-like testing**

#### Step 1: Create Real Scenario
1. Go to admin panel
2. Create a test booking for yesterday:
   - Service: Any service
   - Technician: Select a test technician
   - Status: Mark as **Completed**
   - Amount: ₹1000 (commission will be ₹200)
   - Completed Date: **Yesterday's date**

#### Step 2: Don't Pay Commission
- Skip the commission payment step
- This technician now has unpaid commission

#### Step 3: Run the Lock Job
- Use Method 2 (Quick Test) or Method 3 (Command Line)
- Or wait until 7:00 AM tomorrow (if cron is set up)

#### Step 4: Verify Lock
1. Go to: `admin-unlock-technician.php`
2. You should see the technician listed as locked
3. Lock reason should show: "Unpaid Electrozot charges for [date]. Amount Due: ₹200"

#### Step 5: Test Unlock
1. Click **Unlock** button
2. Confirm the unlock
3. Technician should be unlocked immediately

**Time Required:** 5-10 minutes

---

## What to Check During Testing

### ✅ Expected Behavior

1. **Technicians with unpaid commission:**
   - ✅ Account should be LOCKED
   - ✅ Lock reason should show correct amount
   - ✅ Locked timestamp should be recorded
   - ✅ Should appear in "Locked Technicians" page

2. **Technicians who paid commission:**
   - ✅ Account should remain UNLOCKED
   - ✅ Should NOT appear in locked list

3. **Admin unlock:**
   - ✅ Should unlock immediately
   - ✅ Lock reason should be cleared
   - ✅ Technician can receive bookings again

4. **System logs:**
   - ✅ Check `admin/cron-last-run.log` for execution history
   - ✅ Check `admin/cron-errors.log` for any errors
   - ✅ Check `tms_system_logs` table in database

### ❌ Issues to Watch For

1. **No technicians locked when they should be:**
   - Check if bookings exist from yesterday
   - Check if commission_payments table has entries
   - Check database connection

2. **Wrong technicians locked:**
   - Verify commission calculation (20%)
   - Check payment records in database

3. **Cron job not running:**
   - Check Task Scheduler (Windows)
   - Check PHP path is correct
   - Check file permissions

---

## Database Verification

### Check Locked Technicians
```sql
SELECT t_id, t_name, t_phone, account_locked, lock_reason, locked_at 
FROM tms_technician 
WHERE account_locked = 1;
```

### Check Yesterday's Completed Jobs
```sql
SELECT sb_id, sb_technician_id, sb_bill_amount, sb_completed_at 
FROM tms_service_booking 
WHERE DATE(sb_completed_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
  AND sb_status = 'Completed';
```

### Check Commission Payments
```sql
SELECT cp_id, cp_technician_id, cp_amount, cp_date 
FROM tms_commission_payments 
WHERE cp_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY);
```

### Check System Logs
```sql
SELECT * FROM tms_system_logs 
WHERE log_type = 'account_lock' 
ORDER BY log_date DESC 
LIMIT 10;
```

---

## Setting Up Automatic Execution (Windows)

### Task Scheduler Setup

1. **Open Task Scheduler:**
   - Press `Win + R`
   - Type: `taskschd.msc`
   - Press Enter

2. **Create New Task:**
   - Click "Create Basic Task"
   - Name: `Electrozot Auto-Lock Technicians`
   - Description: `Automatically lock technicians with unpaid commission`

3. **Set Trigger:**
   - Trigger: **Daily**
   - Start time: **7:00 AM**
   - Recur every: **1 day**

4. **Set Action:**
   - Action: **Start a program**
   - Program/script: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\electrozot\admin\cron-lock-unpaid-technicians.php`
   - Start in: `C:\xampp\htdocs\electrozot\admin`

5. **Additional Settings:**
   - ✅ Run whether user is logged on or not
   - ✅ Run with highest privileges
   - ✅ Configure for: Windows 10

6. **Save and Test:**
   - Right-click the task
   - Click "Run"
   - Check if it executes successfully

---

## Troubleshooting

### Issue: Cron job not locking anyone

**Solution:**
1. Check if there are completed bookings from yesterday
2. Verify commission_payments table exists
3. Run the test suite to see detailed output

### Issue: Wrong technicians getting locked

**Solution:**
1. Check commission calculation (should be 20%)
2. Verify payment records in database
3. Check if payment dates match

### Issue: Task Scheduler not running

**Solution:**
1. Verify PHP path: `C:\xampp\php\php.exe`
2. Check file path is correct
3. Run task manually to see errors
4. Check Task Scheduler history

### Issue: Database errors

**Solution:**
1. Check database connection in `config.php`
2. Verify all required tables exist
3. Run `setup-commission-system.php`

---

## Log Files

### cron-last-run.log
- Location: `admin/cron-last-run.log`
- Contains: Execution timestamps and results
- Example:
  ```
  2024-12-07 07:00:01 - Cron job started
  2024-12-07 07:00:02 - Tables checked/created
  2024-12-07 07:00:03 - SUCCESS: Locked 3 technicians for date 2024-12-06
  ```

### cron-errors.log
- Location: `admin/cron-errors.log`
- Contains: PHP errors and warnings
- Check this if cron job fails

---

## Quick Reference

| Action | URL/Command |
|--------|-------------|
| Full Test Suite | `http://localhost/electrozot/admin/test-auto-lock-system.php` |
| Quick Test | `http://localhost/electrozot/admin/quick-test-lock.php` |
| View Locked Technicians | `http://localhost/electrozot/admin/admin-unlock-technician.php` |
| Run Cron Manually | `php admin/cron-lock-unpaid-technicians.php` |
| Check Logs | `type admin\cron-last-run.log` |

---

## Success Criteria

✅ **System is working correctly if:**

1. Technicians with unpaid commission from yesterday are locked at 7 AM
2. Technicians who paid are NOT locked
3. Lock message shows correct commission amount
4. Admin can unlock accounts
5. Logs are being written correctly
6. No errors in error log

---

## Support

If you encounter issues:

1. Run the full test suite first
2. Check log files for errors
3. Verify database tables exist
4. Check Task Scheduler configuration
5. Review this guide for troubleshooting steps

---

**Last Updated:** December 7, 2024
**System Version:** 1.0
**Status:** Ready for Testing ✅
