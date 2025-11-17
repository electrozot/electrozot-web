# Technician Unique ID & Mobile Number System

## Overview
Every technician in the system has:
1. **Unique EZ ID** - Auto-generated company identification (EZ0001, EZ0002, etc.)
2. **Unique Mobile Number** - 10-digit phone number (no duplicates allowed)

## Features Implemented

### 1. Auto-Generated EZ ID
- **Format**: `EZ` + 4-digit number with leading zeros
- **Examples**: EZ0001, EZ0002, EZ0003, ..., EZ9999
- **Auto-increment**: System automatically generates the next available ID
- **Validation**: Checks for duplicates before assignment

### 2. Unique Mobile Number Validation
- **Format**: Exactly 10 digits (Indian mobile format)
- **Validation**: 
  - Must be exactly 10 digits
  - No duplicates allowed in the system
  - Only numeric characters accepted
- **Login**: Technicians use their mobile number to login

## Files Modified/Created

### Modified Files:
1. **admin/admin-add-technician.php**
   - Added auto-generate button for EZ ID
   - Enhanced mobile number validation
   - Duplicate checking for both EZ ID and mobile number
   - Auto-generates EZ ID on page load

### New Files:
1. **admin/api-generate-ez-id.php**
   - API endpoint to generate next available EZ ID
   - Handles race conditions
   - Returns JSON response

2. **admin/admin-technician-list.php**
   - Lists all technicians with their unique IDs
   - Shows EZ ID and mobile numbers
   - Validation summary display

3. **TECHNICIAN_UNIQUE_ID_SYSTEM.md** (this file)
   - Complete documentation

## How It Works

### Adding a New Technician

1. **Navigate to Add Technician Page**
   ```
   Admin Panel → Technicians → Add Technician
   ```

2. **EZ ID Auto-Generation**
   - EZ ID is automatically generated when page loads
   - Click "Auto Generate" button to get next available ID
   - System checks database for highest existing EZ ID
   - Increments by 1 and formats with leading zeros

3. **Mobile Number Entry**
   - Enter exactly 10 digits
   - System validates format (only numbers)
   - Checks for duplicates before saving
   - Shows error if number already exists

4. **Validation on Submit**
   - EZ ID uniqueness check
   - Mobile number uniqueness check
   - Both must be unique to proceed

### EZ ID Generation Logic

```php
// Get last EZ ID from database
SELECT t_ez_id FROM tms_technician 
WHERE t_ez_id LIKE 'EZ%' 
ORDER BY t_ez_id DESC LIMIT 1

// Extract number (e.g., "EZ0001" → 1)
// Increment by 1
// Format as EZ0002
```

### Mobile Number Validation

```php
// Check format (exactly 10 digits)
if(!preg_match('/^[0-9]{10}$/', $t_phone)) {
    $err = "Phone number must be exactly 10 digits";
}

// Check for duplicates
SELECT t_id FROM tms_technician WHERE t_phone = ?
```

## Database Schema

### tms_technician Table Columns:
- `t_id` - Primary key (auto-increment)
- `t_ez_id` - Unique EZ ID (VARCHAR(20), UNIQUE)
- `t_phone` - Mobile number (VARCHAR(15), UNIQUE)
- `t_id_no` - ID number (same as t_ez_id)
- `t_name` - Technician name
- `t_category` - Service category
- `t_status` - Availability status
- `t_service_pincode` - Service area pincode

## Usage Examples

### Example 1: First Technician
```
EZ ID: EZ0001
Mobile: 9876543210
Name: John Doe
```

### Example 2: Second Technician
```
EZ ID: EZ0002
Mobile: 9876543211
Name: Jane Smith
```

### Example 3: After 99 Technicians
```
EZ ID: EZ0100
Mobile: 9876543310
Name: Mike Johnson
```

## Error Messages

### Duplicate EZ ID
```
"EZ ID already exists! Please use a unique EZ ID."
```

### Duplicate Mobile Number
```
"Mobile Number already exists! Please use a unique mobile number."
```

### Invalid Mobile Format
```
"Phone number must be exactly 10 digits"
```

## Testing

### Test Auto-Generation
1. Go to: `admin/admin-add-technician.php`
2. Page loads → EZ ID automatically populated
3. Click "Auto Generate" → Gets next available ID
4. Verify format: EZ0001, EZ0002, etc.

### Test Duplicate Prevention
1. Try to add technician with existing EZ ID → Error shown
2. Try to add technician with existing mobile → Error shown
3. Both validations work independently

### Test Mobile Format
1. Enter less than 10 digits → Validation error
2. Enter more than 10 digits → Input limited to 10
3. Enter letters → Only numbers accepted

## View All Technicians

Navigate to: `admin/admin-technician-list.php`

This page shows:
- All technicians in a table
- EZ ID for each technician
- Mobile number for each technician
- Validation summary
- Quick actions (View, Edit)

## API Endpoints

### Generate Next EZ ID
```
Endpoint: admin/api-generate-ez-id.php
Method: GET
Response: JSON

Success Response:
{
    "success": true,
    "ez_id": "EZ0001",
    "message": "EZ ID generated successfully"
}

Error Response:
{
    "success": false,
    "message": "Error generating EZ ID: [error details]"
}
```

## Security Features

1. **Session Validation**: All pages check admin login
2. **SQL Injection Prevention**: Prepared statements used
3. **Duplicate Prevention**: Database-level checks
4. **Input Sanitization**: All inputs validated and sanitized

## Capacity

- **Maximum Technicians**: 9,999 (EZ0001 to EZ9999)
- **Can be extended**: Change format to EZ00001 for 99,999 capacity

## Future Enhancements

1. **Bulk Import**: Import multiple technicians from CSV
2. **QR Code**: Generate QR code for each EZ ID
3. **ID Card**: Print technician ID cards with EZ ID
4. **SMS Notification**: Send EZ ID to technician via SMS
5. **Mobile App**: Technician mobile app using EZ ID

## Troubleshooting

### EZ ID Not Generating
1. Check database connection
2. Verify tms_technician table exists
3. Check browser console for errors
4. Verify admin is logged in

### Duplicate Error Despite Unique Values
1. Check database for existing records
2. Clear browser cache
3. Verify case sensitivity (EZ0001 vs ez0001)

### Mobile Number Validation Not Working
1. Ensure exactly 10 digits entered
2. No spaces or special characters
3. Check database column type (VARCHAR)

## Support

For issues or questions:
1. Check browser console for JavaScript errors
2. Check server error logs for PHP errors
3. Verify database schema matches requirements
4. Test with the technician list page to see existing data

---

**Last Updated**: November 17, 2025
**Status**: ✅ Fully Implemented and Tested
**Version**: 1.0
