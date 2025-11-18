# Duplicate Technician Registration Prevention

## Overview
This system prevents technicians from registering multiple times using the same mobile number or Aadhaar number.

## Key Features

### 1. Guest Registration Prevention (tech/register.php)
- **Checks before allowing registration:**
  - If mobile number OR Aadhaar number already exists for an approved EZ Technician
  - Shows message: "You are already registered as an EZ Technician (EZ ID: XXX). Please login with your existing credentials."
  - Prevents duplicate pending registrations

### 2. Admin Add Technician Prevention (admin/admin-add-technician.php)
- **Checks before adding new technician:**
  - If mobile number OR Aadhaar number already exists for any technician
  - Shows message: "This Mobile Number or Aadhaar Number is already registered to EZ Technician: [Name] (EZ ID: XXX)"
  - Ensures each technician has unique mobile and Aadhaar

### 3. Guest Approval Prevention (admin/admin-guest-technicians.php)
- **Checks before approving guest technician:**
  - If mobile number OR Aadhaar number already exists for another approved technician
  - Shows message: "This technician is already registered as EZ Technician: [Name] (EZ ID: XXX). Cannot approve duplicate registration."
  - Prevents converting guest to regular if already registered

## Validation Rules

### Unique Identifiers
1. **Mobile Number** - Must be unique across all technicians (10 digits)
2. **Aadhaar Number** - Must be unique across all technicians (12 digits)
3. **EZ ID** - Must be unique (auto-generated)

### Registration Flow
```
Guest Registration → Pending Approval → Admin Approval → Regular EZ Technician
```

### Duplicate Prevention Points
1. **At Guest Registration** - Checks if mobile/Aadhaar exists for approved technicians
2. **At Admin Add** - Checks if mobile/Aadhaar exists for any technician
3. **At Guest Approval** - Checks if mobile/Aadhaar exists for other approved technicians

## Error Messages

### For Guest Registration
```
"You are already registered as an EZ Technician (EZ ID: EZ0001). 
Please login with your existing credentials. If you forgot your password, contact admin."
```

### For Admin Add Technician
```
"This Mobile Number or Aadhaar Number is already registered to EZ Technician: 
John Doe (EZ ID: EZ0001). Each technician must have unique mobile number and Aadhaar number."
```

### For Guest Approval
```
"This technician is already registered as EZ Technician: John Doe (EZ ID: EZ0001). 
Cannot approve duplicate registration."
```

## Database Checks

### Query Logic
```sql
-- Check for existing approved technician
SELECT t_id, t_name, t_ez_id 
FROM tms_technician 
WHERE (t_phone = ? OR t_aadhar = ?) 
AND (t_is_guest = 0 OR t_status IN ('Available', 'Booked'))
```

## Benefits
1. ✅ Prevents duplicate technician accounts
2. ✅ Maintains data integrity
3. ✅ Clear error messages for users
4. ✅ Protects against accidental re-registration
5. ✅ Ensures unique mobile and Aadhaar per technician
