# User Custom Booking - Subcategory Assignment Feature

## Overview
Extended the subcategory selection feature on the admin assign technician page to work with custom bookings created by users from their dashboard.

## What Changed

### Detection Logic Updated
Previously, custom bookings were only detected if:
- `sb_custom_service` field was set (admin-created)
- `sb_service_id` was NULL (admin quick booking)

Now also detects:
- Service name is "Custom Service Request" (user-created custom bookings)

### Updated Code
```php
// OLD - Only detected admin-created custom bookings
$is_custom_service_booking = !empty($booking_data->sb_custom_service) 
                          || empty($booking_data->sb_service_id);

// NEW - Also detects user-created custom bookings
$is_custom_service_booking = !empty($booking_data->sb_custom_service) 
                          || empty($booking_data->sb_service_id) 
                          || (isset($booking_data->s_name) && $booking_data->s_name === 'Custom Service Request');
```

## How It Works

### User Side (usr/book-custom-service.php)
1. User fills custom service form
2. Selects subcategory from visual button grid
3. Submits booking
4. Booking saved with:
   - `sb_service_id` = ID of "Custom Service Request" service
   - `sb_subcategory` = Selected category (e.g., "Wiring & Fixtures")
   - `sb_description` = "Custom Service: [name] - [description]"

### Admin Side (admin/admin-assign-technician.php)
1. Admin opens booking in "Assign Technician" page
2. System detects it's a custom booking (checks service name)
3. Shows subcategory selection buttons
4. If subcategory already set (from user), shows green success box
5. Admin can change subcategory if needed
6. Technician list filters based on subcategory
7. Admin assigns technician

## Complete Flow Example

### Step 1: User Creates Booking
```
User Dashboard → Book Custom Service
- Service Name: "Solar Panel Installation"
- Description: "Need 5kW solar system installed on rooftop"
- Category: "Appliance Setup" ← User selects this
- Date: Tomorrow
- Submit
```

### Step 2: Database Entry
```sql
INSERT INTO tms_service_booking (
    sb_user_id = 123,
    sb_service_id = 45,  -- ID of "Custom Service Request"
    sb_subcategory = "Appliance Setup",
    sb_description = "Custom Service: Solar Panel Installation - Need 5kW...",
    sb_status = "Pending"
)
```

### Step 3: Admin Receives Booking
```
Admin Dashboard → Notifications → New Booking #1234
- Customer: John Doe
- Service: Custom Service Request
- Description: Solar Panel Installation
```

### Step 4: Admin Assigns Technician
```
Admin → Assign Technician (Booking #1234)

✅ GREEN BOX SHOWS:
"Custom Service Booking - Change Subcategory"
Current: [Appliance Setup]
Click a different subcategory below to change the filter

[8 Subcategory Buttons Displayed]
- "Appliance Setup" is highlighted (user's selection)

Technician List:
✅ Available Technicians - Has Capacity (3)
- Raj Kumar (5 yrs, 2 slots free) | Skills: Installation, Setup
- Amit Singh (3 yrs, 3 slots free) | Skills: Appliance, Installation
- Priya Sharma (4 yrs, 1 slot free) | Skills: Setup, Electrical

Admin selects technician → Assigns → Done!
```

## Benefits

### For Users
✅ **Pre-categorize** their custom service request
✅ **Faster processing** by admin
✅ **Better matching** with right technician
✅ **Clear expectations** of service type

### For Admins
✅ **Instant filtering** - technicians pre-filtered by user's category
✅ **Can override** - change category if user selected wrong one
✅ **Visual feedback** - see what user selected
✅ **Faster assignment** - no need to manually categorize

### For Technicians
✅ **Better job fit** - receive bookings matching their skills
✅ **Higher success rate** - work on services they're trained for
✅ **Clear expectations** - know what type of work to expect

## Technical Details

### Files Modified
1. **admin/admin-assign-technician.php**
   - Updated custom booking detection (2 places)
   - Now checks service name for "Custom Service Request"
   - Subcategory buttons show for user-created custom bookings

2. **usr/book-custom-service.php**
   - Already updated with subcategory selection
   - Saves subcategory to database

### Database Flow
```
User Booking:
sb_service_id = 45 (Custom Service Request)
sb_subcategory = "Appliance Setup"
sb_custom_service = NULL

Admin Detection:
if (s_name === "Custom Service Request") {
    // This is a user-created custom booking
    // Show subcategory buttons
    // Filter technicians by subcategory
}
```

### Technician Filtering Logic
```php
if ($is_custom_service_booking && !empty($booking_data->sb_subcategory)) {
    // Use SmartTechnicianMatcher
    // Find technicians with skills matching subcategory
    // Show filtered list
} elseif ($is_custom_service_booking && empty($booking_data->sb_subcategory)) {
    // No subcategory set
    // Show ALL available technicians
    // Prompt admin to select subcategory
} else {
    // Regular service
    // Use skill-based matching
}
```

## Testing Scenarios

### Scenario 1: User Selects Subcategory
1. User books custom service with subcategory
2. Admin opens assign page
3. ✅ Green box shows with selected subcategory
4. ✅ Technicians filtered by subcategory
5. ✅ Admin can change subcategory if needed

### Scenario 2: User Doesn't Select (Old Bookings)
1. Old booking without subcategory
2. Admin opens assign page
3. ✅ Yellow box prompts to select subcategory
4. ✅ Shows all available technicians
5. ✅ Admin selects subcategory
6. ✅ Page reloads with filtered technicians

### Scenario 3: Admin Changes Subcategory
1. User selected "Wiring & Fixtures"
2. Admin thinks "Safety & Power" is better fit
3. Admin clicks "Safety & Power" button
4. ✅ Page reloads
5. ✅ New technician list based on "Safety & Power"
6. ✅ Admin assigns appropriate technician

## Edge Cases Handled

### Case 1: Service Name Variations
- Checks exact match: `s_name === 'Custom Service Request'`
- Case-sensitive to avoid false positives
- Only matches the specific service created for custom bookings

### Case 2: NULL vs Empty Service ID
- Handles both `NULL` and `0` service IDs
- Uses `empty()` check for flexibility
- Works with admin quick bookings too

### Case 3: Missing Subcategory
- Shows yellow warning box
- Displays all available technicians
- Prompts admin to select category
- Validates before assignment

## Backward Compatibility

✅ **Old Bookings**: Still work without subcategory
✅ **Admin Bookings**: Still detected as custom
✅ **Regular Bookings**: Unaffected by changes
✅ **Guest Bookings**: Work as before

## Future Enhancements

- Auto-suggest subcategory based on description keywords
- Show subcategory in booking list view
- Filter bookings by subcategory
- Analytics: Most common custom service categories
- Technician recommendations based on past custom bookings

## Summary

The feature now provides a **seamless end-to-end flow**:

1. **User** selects category when booking custom service
2. **System** saves category with booking
3. **Admin** sees category and filtered technicians
4. **Admin** can change category if needed
5. **Technician** receives well-matched booking

This creates a **smart, efficient workflow** that benefits all parties and reduces manual categorization work for admins.
