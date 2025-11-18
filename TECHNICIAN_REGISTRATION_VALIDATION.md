# Technician Registration - Duplicate Prevention

## Overview
Prevents duplicate technician registrations by checking mobile number and Aadhaar number.

## Validation Checks

### 1. Already Registered as EZ Technician
**Checks:** Mobile number OR Aadhaar number
**Condition:** Already approved and active technician

**Message:**
```
⚠️ Registration Not Allowed: You are already registered as an EZ Technician 
with this mobile number/Aadhaar number. Your EZ ID is: EZ0001. 
Please use the login page to access your account. If you forgot your password, 
please contact admin for assistance.
```

**What it checks:**
- Mobile number matches existing technician
- OR Aadhaar number matches existing technician
- AND technician is approved (not guest)
- AND status is 'Available' or 'Booked'

### 2. Registration Already Pending
**Checks:** Mobile number OR Email OR Aadhaar number
**Condition:** Registration submitted but not yet approved

**Message:**
```
⏳ Registration Already Submitted: Your registration request (submitted on 18 Nov 2024) 
is currently pending admin approval. Please wait for confirmation. 
You will be notified once your account is approved.
```

**What it checks:**
- Mobile number, email, or Aadhaar matches pending registration
- AND is_guest = 1 (guest technician)
- AND status = 'Pending'

### 3. Too Many Attempts (IP-based)
**Checks:** Registration attempts from same IP
**Condition:** More than 3 attempts in 24 hours

**Message:**
```
Maximum 3 registration requests allowed per 24 hours. Please try again later.
```

### 4. Data Already Used Recently
**Checks:** Phone/Email/Aadhaar used in last 24 hours
**Condition:** Same data used for registration attempt

**Message:**
```
This phone number, email, or Aadhaar was already used for registration 
in the last 24 hours.
```

## Validation Flow

```
User submits registration
    ↓
Check 1: Already EZ Technician?
    ↓ NO
Check 2: Registration pending?
    ↓ NO
Check 3: Too many IP attempts?
    ↓ NO
Check 4: Data used recently?
    ↓ NO
Proceed with registration
```

## Database Queries

### Check Existing Technician
```sql
SELECT t_id, t_ez_id, t_name, t_phone 
FROM tms_technician 
WHERE (t_phone = ? OR t_aadhar = ?) 
AND (t_is_guest = 0 OR t_status IN ('Available', 'Booked'))
```

### Check Pending Registration
```sql
SELECT t_id, t_registered_at 
FROM tms_technician 
WHERE (t_phone = ? OR t_email = ? OR t_aadhar = ?) 
AND t_is_guest = 1 
AND t_status = 'Pending'
```

## Error Display

### Visual Format:
- ⚠️ Icon for "Already Registered"
- ⏳ Icon for "Pending Approval"
- Red alert box
- Bold text for important info (EZ ID, dates)
- Clear action steps

### User Experience:
1. **Already Registered:** Directs to login page
2. **Pending:** Tells them to wait
3. **Too Many Attempts:** Tells them to wait 24 hours
4. **Data Used:** Prevents spam

## Benefits

✅ **Prevents Duplicates** - No multiple accounts with same mobile/Aadhaar
✅ **Clear Messages** - Users know exactly what's wrong
✅ **Shows EZ ID** - Helps users remember their existing account
✅ **Shows Date** - Users know when they registered
✅ **Spam Prevention** - IP and time-based limits
✅ **Better UX** - Helpful guidance instead of generic errors

## Example Scenarios

### Scenario 1: Existing Technician Tries to Register
```
User: Enters mobile 9876543210
System: Checks database
Result: Found EZ0123 with this mobile
Message: "You are already registered... EZ ID: EZ0123"
Action: User goes to login page
```

### Scenario 2: User Registers Twice
```
User: Submits registration on Nov 15
User: Tries again on Nov 16
System: Checks pending registrations
Result: Found pending from Nov 15
Message: "Registration submitted on 15 Nov 2024... pending approval"
Action: User waits for admin approval
```

### Scenario 3: Spam Attempt
```
User: Tries to register 4 times in 1 hour
System: Checks IP attempts
Result: 3 attempts already made
Message: "Maximum 3 attempts... try again later"
Action: User must wait 24 hours
```

## Technical Details

### Match Detection:
- Checks mobile number first
- If mobile matches, shows "mobile number"
- If Aadhaar matches, shows "Aadhaar number"
- Helps user identify which data is duplicate

### Date Formatting:
- Shows registration date in readable format
- Example: "18 Nov 2024" instead of "2024-11-18"
- Helps users remember when they registered

### HTML Formatting:
- Uses `<strong>` tags for EZ ID
- Makes important info stand out
- Better visual hierarchy
