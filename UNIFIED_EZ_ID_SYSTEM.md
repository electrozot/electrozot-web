# Unified EZ ID Generation System

## Overview
Both admin-added technicians and guest technicians (after approval) use the SAME EZ ID generation system to ensure uniqueness.

## How It Works

### 1. Admin Adds Technician (admin-add-technician.php)
- Admin clicks "Auto Generate" button
- Calls `api-generate-ez-id.php`
- Gets next unique EZ ID (e.g., EZ0001, EZ0002, etc.)
- EZ ID is filled in the form
- Admin submits form with generated EZ ID

### 2. Guest Technician Approval (admin-guest-technicians.php)
- Guest registers and waits for approval
- Admin opens guest approval page
- **EZ ID is AUTO-GENERATED on page load** for each pending guest
- Admin can click "Generate" button to regenerate if needed
- Admin approves with the generated EZ ID
- Guest becomes regular EZ Technician with unique ID

## EZ ID Generation Logic (api-generate-ez-id.php)

### Formula: EZ + 4-digit sequential number
- EZ0001, EZ0002, EZ0003, ..., EZ9999

### Process:
1. Query database for highest numeric EZ ID
2. Extract number and increment by 1
3. Format with leading zeros (4 digits)
4. Check if ID exists in database
5. If exists, increment and check again
6. Return unique EZ ID

## Key Features

✅ **Same System** - Both paths use identical generation logic
✅ **Truly Unique** - Numeric sorting prevents duplicates
✅ **Auto-Generated** - Guest approval auto-fills EZ ID
✅ **Manual Override** - Admin can regenerate if needed
✅ **Validation** - Checks for duplicates before approval
✅ **Sequential** - IDs follow proper numeric order

## Summary

**Admin Add Technician:** Manual click → Generate EZ ID → Submit
**Guest Approval:** Auto-generate on load → Admin approves → Same unique EZ ID system

Both use the same `api-generate-ez-id.php` ensuring all EZ IDs are unique!
