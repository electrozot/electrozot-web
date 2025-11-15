# Unique Constraints for Technicians

## Overview
EZ ID and Mobile Number are now enforced as UNIQUE for all technicians to prevent duplicates.

---

## What's Unique?

### 1. EZ ID (t_ez_id)
- **Must be unique** across all technicians
- Example: EZ0001, EZ0002, EZ0023
- Cannot have two technicians with the same EZ ID

### 2. Mobile Number (t_phone)
- **Must be unique** across all technicians
- Example: 9876543210
- Cannot have two technicians with the same mobile number

---

## How It Works

### Database Level
- Unique constraints added to database table
- Database will reject duplicate entries automatically

### Application Level
- Add Technician form checks for duplicates before inserting
- Shows clear error message if duplicate found
- Prevents form submission with duplicate values

---

## Setup Instructions

### Run Setup Script
1. Navigate to: `admin/setup-unique-constraints.php`
2. Click "Run Setup"
3. Review results
4. Fix any duplicate values if found

### What the Setup Does
1. Adds UNIQUE constraint to `t_ez_id` column
2. Adds UNIQUE constraint to `t_phone` column
3. Checks for existing duplicates
4. Reports any issues found

---

## Error Messages

### When Adding Technician

**EZ ID Duplicate:**
```
❌ EZ ID already exists! Please use a unique EZ ID.
```

**Mobile Number Duplicate:**
```
❌ Mobile Number already exists! Please use a unique mobile number.
```

---

## Fixing Duplicates

### If Duplicates Found

1. **View Duplicates:**
   - Run `setup-unique-constraints.php`
   - Check the warnings section

2. **Fix Manually:**
   - Go to Manage Technicians
   - Find duplicate entries
   - Update one of them with a new EZ ID or Mobile Number
   - Or delete the duplicate entry

3. **Re-run Setup:**
   - After fixing, run setup again
   - Verify no duplicates remain

---

## Benefits

✅ **Data Integrity** - No duplicate technicians
✅ **Login Security** - Each mobile number is unique for login
✅ **Easy Identification** - Each EZ ID is unique
✅ **Prevents Errors** - Catches duplicates before saving
✅ **Clear Messages** - User-friendly error messages

---

## Technical Details

### Database Constraints
```sql
ALTER TABLE tms_technician ADD UNIQUE KEY unique_ez_id (t_ez_id);
ALTER TABLE tms_technician ADD UNIQUE KEY unique_phone (t_phone);
```

### Validation Code
- Checks database before INSERT
- Uses prepared statements for security
- Returns specific error messages
- Prevents duplicate submissions

---

## Testing

### Test Cases
1. ✅ Add technician with unique EZ ID and phone - SUCCESS
2. ✅ Try to add technician with duplicate EZ ID - BLOCKED
3. ✅ Try to add technician with duplicate phone - BLOCKED
4. ✅ Update existing technician - WORKS
5. ✅ Database constraint prevents duplicates - WORKS

---

## Maintenance

### Regular Checks
- Run setup script monthly to verify constraints
- Check for any duplicate entries
- Review error logs for duplicate attempts

### Best Practices
- Always use format: EZ0001, EZ0002, etc.
- Verify mobile number is 10 digits
- Check existing technicians before adding new ones

---

**Status:** ✅ ACTIVE
**Last Updated:** November 15, 2025
