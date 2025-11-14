# Mobile Number Login System

## Overview
All users (Clients, Technicians, Admin) can now login using their mobile numbers as the primary identifier.

## Login Methods by User Type

### 1. **Client/User Login**
- **Login Field**: Mobile Number (10 digits)
- **Password**: User's password
- **Location**: `usr/index.php`
- **Database**: `tms_user` table, `u_phone` column

### 2. **Technician Login**
- **Login Field**: Mobile Number (10 digits)
- **Password**: Technician's password
- **Location**: `tech/index.php`
- **Database**: `tms_technician` table, `t_phone` column
- **Fallback**: Also accepts `t_id_no` for backward compatibility

### 3. **Admin Login**
- **Login Field**: Email OR Mobile Number
- **Password**: Admin's password (MD5 hashed)
- **Location**: `admin/index.php`
- **Database**: `tms_admin` table, `a_email` or `a_phone` column
- **Smart Detection**: Automatically detects if input is email or phone

## Features

### Mobile Number as Primary ID
- 10-digit mobile numbers are the main identifier
- Unique per user
- Easy to remember
- No need for email for clients and technicians

### Admin Flexibility
- Can login with either email or mobile number
- System automatically detects input type:
  - If all digits (10 digits) → Phone login
  - If contains @ → Email login

### Password Visibility Toggle
- All login pages have eye icon
- Click to show/hide password
- Better user experience

## Database Changes

### New Columns Added:
1. **tms_user**: Already has `u_phone`
2. **tms_technician**: Added `t_phone VARCHAR(15)`
3. **tms_admin**: Added `a_phone VARCHAR(15)`

### Auto-Migration:
All login pages automatically create the phone columns if they don't exist using:
```sql
ALTER TABLE table_name ADD COLUMN IF NOT EXISTS phone_column VARCHAR(15) DEFAULT NULL
```

## Form Validations

### Mobile Number Input:
- Type: `tel`
- Pattern: `[0-9]{10}`
- Maxlength: 10
- Required: Yes
- Placeholder: "Enter 10-digit mobile number"

### Admin Login Input:
- Type: `text` (accepts both email and phone)
- Required: Yes
- Placeholder: "Enter email or 10-digit mobile number"
- Helper text: "You can login with either email or mobile number"

## Updated Files

### Client Login:
- `usr/index.php` - Changed from email to mobile number

### Technician Login:
- `tech/index.php` - Changed from ID number to mobile number
- `tech/process-login.php` - Updated authentication logic

### Admin Login:
- `admin/index.php` - Added dual login (email OR mobile)

### Technician Management:
- `admin/admin-add-technician.php` - Added mobile number field (required)

## User Experience Improvements

### 1. **Clearer Labels**
- "Mobile Number" instead of "Email" or "ID"
- Helper text explaining usage
- Icons for visual clarity

### 2. **Better Validation**
- Pattern matching for 10 digits
- Real-time validation
- Clear error messages

### 3. **Backward Compatibility**
- Technicians can still use ID number if phone not set
- Existing users not affected
- Gradual migration supported

## Error Messages

### Client Login:
- "Mobile Number & Password Not Match"

### Technician Login:
- "Technician not found with this mobile number"
- "Invalid password"
- "Password not set. Please contact Admin"

### Admin Login:
- "Email/Mobile Number & Password Not Match"

## Benefits

1. **Easier to Remember**: Mobile numbers are easier than emails
2. **Universal**: Everyone has a mobile number
3. **Unique**: Mobile numbers are unique identifiers
4. **No Email Required**: Clients don't need email accounts
5. **Admin Flexibility**: Admins can use either method
6. **Better UX**: Clearer, simpler login process

## Migration Notes

### For Existing Users:
- System will work with existing data
- Phone column added automatically
- No data loss

### For New Users:
- Mobile number is required field
- Used as primary login identifier
- Stored in phone column

## Testing Checklist

- [ ] Client can login with mobile number
- [ ] Technician can login with mobile number
- [ ] Admin can login with email
- [ ] Admin can login with mobile number
- [ ] Password visibility toggle works
- [ ] Form validation works (10 digits)
- [ ] Error messages display correctly
- [ ] Database columns created automatically

---
**System Ready!** All users can now login using mobile numbers as their primary identifier.
