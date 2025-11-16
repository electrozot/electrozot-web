# Complete Button Troubleshooting Guide

## Test Files Created

1. **test-complete-button.php** - Full diagnostic test
   - Shows login status
   - Lists all active bookings
   - Tests complete button links
   - Verifies file exists

2. **complete-test-simple.php** - Simple PHP test
   - Tests if PHP is working
   - Tests URL parameters
   - No database or session dependencies

## How to Troubleshoot

### Step 1: Test if you're logged in
Visit: `tech/test-complete-button.php`

This will show:
- ✅ If you're logged in as technician
- ✅ Your active bookings
- ✅ Working complete buttons to test

### Step 2: Test basic PHP
Visit: `tech/complete-test-simple.php`

This will confirm PHP is working properly.

### Step 3: Test with debug mode
Visit: `tech/complete-booking.php?id=BOOKING_ID&action=done&debug=1`

Replace BOOKING_ID with an actual booking ID from your dashboard.

## Common Issues & Solutions

### Issue 1: Button does nothing
**Cause**: JavaScript error or link not working
**Solution**: Check browser console for errors (F12)

### Issue 2: Redirects to dashboard immediately
**Cause**: Booking not found or already completed
**Solution**: Check the error message in dashboard

### Issue 3: Blank page
**Cause**: PHP error
**Solution**: Enable error display or check error logs

### Issue 4: "Booking not found"
**Possible causes**:
- Booking doesn't exist
- Booking not assigned to you
- Wrong technician logged in

## Quick Fix

If complete-booking.php still doesn't work, use the backup:
`tech/complete-booking-simple.php`

This is a simpler version without fancy features.

## Manual Test

1. Login as technician
2. Go to dashboard
3. Find an active booking (status: Pending, Approved, In Progress)
4. Click "Done" button
5. Should see the complete form

If it redirects back to dashboard, check for error message at top of page.
