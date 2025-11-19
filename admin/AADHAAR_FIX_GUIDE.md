# Aadhaar Number Display Fix for ID Card

## Problem
Aadhaar number was not showing on the technician ID card.

## Solution Implemented

### 1. Database Setup
Run the setup script to add the Aadhaar column to your database:

**Option A: Using PHP Script (Recommended)**
- Navigate to: `admin/setup-aadhaar-column.php`
- This will automatically add the required columns

**Option B: Using SQL File**
- Import the SQL file: `admin/add-aadhaar-column.sql`
- Or run it manually in phpMyAdmin

### 2. ID Card Updated
The ID card generation file (`admin-generate-id-card.php`) has been updated to display:
- Mobile Number
- Aadhaar Number (12 digits)
- Address (if available)

### 3. What Was Changed

#### Database Changes:
- Added `t_aadhar` column (VARCHAR 12) to store Aadhaar numbers
- Added `t_addr` column (VARCHAR 500) for address
- Added `t_email` column (VARCHAR 200) for email

#### ID Card Display:
The ID card now shows Aadhaar number between Mobile and Address fields.

### 4. How to Use

1. **First Time Setup:**
   - Visit: `http://yourdomain.com/admin/setup-aadhaar-column.php`
   - This adds the Aadhaar column to your database

2. **Add New Technicians:**
   - The "Add Technician" form already has Aadhaar field
   - Enter 12-digit Aadhaar number
   - System validates it's exactly 12 digits

3. **Update Existing Technicians:**
   - Go to "Manage Technicians"
   - Edit each technician
   - Add their Aadhaar number
   - Save changes

4. **Generate ID Card:**
   - Go to "Generate ID Card"
   - Enter technician's mobile number
   - Aadhaar will now appear on the ID card

### 5. Validation
- Aadhaar must be exactly 12 digits
- Only numbers are allowed
- Each Aadhaar can only be used once (unique)

### 6. ID Card Layout
The ID card now displays in this order:
1. Name
2. Employee ID (EZ ID)
3. Email (if available)
4. Category
5. Specialization (if available)
6. Experience (if available)
7. Service Area (if available)
8. **Mobile Number** ← NEW
9. **Aadhaar Number** ← NEW
10. Address (if available)

## Testing
1. Run setup script
2. Add/Edit a technician with Aadhaar number
3. Generate ID card
4. Verify Aadhaar appears on the card

## Notes
- Aadhaar field is required when adding new technicians
- Existing technicians need to be updated manually
- The field is validated for exactly 12 digits
- Duplicate Aadhaar numbers are prevented
