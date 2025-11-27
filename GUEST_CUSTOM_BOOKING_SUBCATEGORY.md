# Guest Custom Booking - Subcategory Feature

## Overview
Extended subcategory functionality to guest bookings (from homepage) when they select "Other" custom service, enabling the same smart technician assignment as admin quick bookings.

## What Changed

### 1. Guest Booking Form (index.php)
- Added hidden input field to capture subcategory selection
- JavaScript updates hidden field when user selects category from dropdown
- Subcategory is sent with form submission

### 2. Guest Booking Processing (process-guest-booking.php)
- Captures `sb_subcategory` from form
- Saves subcategory to database for both custom and regular services
- Ensures `sb_subcategory` column exists in database

### 3. Admin Assign Technician (admin/admin-assign-technician.php)
- Already detects guest custom bookings via `sb_custom_service` field
- Shows subcategory selection buttons automatically
- Filters technicians based on subcategory

## Complete Flow

### Step 1: Guest Books Custom Service
```
Homepage → Booking Form
1. Guest enters phone, name, address
2. Selects "Service Category" from dropdown (e.g., "Appliance Setup")
3. Selects "Specific Service" → Chooses "Other" for custom service
4. Enters custom service description
5. Submits form
```

### Step 2: Data Saved
```sql
INSERT INTO tms_service_booking (
    sb_user_id = 456,
    sb_service_id = NULL,  -- NULL for custom service
    sb_custom_service = "Solar Panel Installation",
    sb_subcategory = "Appliance Setup",  -- From dropdown
    sb_description = "CUSTOM SERVICE: Solar Panel Installation\n\nNeed 5kW system",
    sb_status = "Pending"
)
```

### Step 3: Admin Receives Booking
```
Admin Dashboard → Notifications
- New Booking from Guest
- Custom Service: Solar Panel Installation
- Category: Appliance Setup
```

### Step 4: Admin Assigns Technician
```
Admin → Assign Technician Page

✅ CUSTOM BOOKING DETECTED
- Shows subcategory selection buttons
- "Appliance Setup" is pre-selected (from guest's choice)
- Technician list filtered by "Appliance Setup" skills
- Admin can change subcategory if needed
- Assigns appropriate technician
```

## Technical Implementation

### Hidden Field in Form
```html
<input type="hidden" name="sb_subcategory" id="guestSubcategoryHidden" value="">
```

### JavaScript Update
```javascript
subcategorySelect.addEventListener('change', function() {
    var subcategory = this.value;
    
    // Update hidden field
    var hiddenSubcategory = document.getElementById('guestSubcategoryHidden');
    if(hiddenSubcategory) {
        hiddenSubcategory.value = subcategory;
    }
    
    // Load services...
});
```

### Database Insert (Custom Service)
```php
if($is_other_service) {
    $query_booking = "INSERT INTO tms_service_booking 
                     (sb_user_id, sb_service_id, sb_booking_date, sb_booking_time, 
                      sb_address, sb_pincode, sb_phone, sb_description, sb_status, 
                      sb_total_price, sb_custom_service, sb_subcategory) 
                     VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_booking->bind_param('isssssssdss', 
        $customer_id, $sb_booking_date, $sb_booking_time, $sb_address, 
        $customer_pincode, $customer_phone, $sb_description, $sb_status, 
        $sb_total_price, $other_service_name, $sb_subcategory);
}
```

### Admin Detection Logic
```php
// Already in admin-assign-technician.php
$is_custom_service_booking = !empty($booking_data->sb_custom_service) 
                          || empty($booking_data->sb_service_id) 
                          || (isset($booking_data->s_name) && $booking_data->s_name === 'Custom Service Request');

// Guest custom bookings have sb_custom_service set, so they're detected!
```

## Benefits

### For Guests
✅ **Easy categorization** - Select from dropdown while booking
✅ **Faster processing** - Admin gets pre-categorized booking
✅ **Better matching** - Right technician assigned based on category
✅ **No extra steps** - Category selection is part of normal flow

### For Admins
✅ **Pre-filtered technicians** - List already filtered by guest's category
✅ **Can override** - Change category if guest selected wrong one
✅ **Consistent workflow** - Same process as admin quick bookings
✅ **Faster assignment** - No manual categorization needed

### For Technicians
✅ **Better job fit** - Receive bookings matching their skills
✅ **Clear expectations** - Know service type before accepting
✅ **Higher success rate** - Work on services they're trained for

## Booking Types Comparison

| Booking Type | Subcategory Source | Detection Method | Admin Experience |
|--------------|-------------------|------------------|------------------|
| **Admin Quick Booking** | Admin selects buttons | `sb_custom_service` set, `sb_service_id` NULL | Buttons shown, pre-selected |
| **User Custom Booking** | User selects buttons | Service name = "Custom Service Request" | Buttons shown, pre-selected |
| **Guest Custom Booking** | Guest selects dropdown | `sb_custom_service` set, `sb_service_id` NULL | Buttons shown, pre-selected |
| **Guest Regular Service** | Guest selects dropdown | Normal service_id | Regular skill matching |

## Database Schema

### Columns Used
```sql
sb_service_id INT NULL              -- NULL for custom services
sb_custom_service VARCHAR(255)      -- Custom service name (e.g., "Solar Panel")
sb_subcategory VARCHAR(100)         -- Category (e.g., "Appliance Setup")
sb_description TEXT                 -- Full description with "CUSTOM SERVICE:" prefix
```

### Example Records

#### Guest Custom Booking
```
sb_id: 1234
sb_service_id: NULL
sb_custom_service: "Solar Panel Installation"
sb_subcategory: "Appliance Setup"
sb_description: "CUSTOM SERVICE: Solar Panel Installation\n\nNeed 5kW system"
```

#### Guest Regular Booking
```
sb_id: 1235
sb_service_id: 15
sb_custom_service: NULL
sb_subcategory: "Wiring & Fixtures"
sb_description: "Need fan installation in bedroom"
```

## Edge Cases Handled

### Case 1: Guest Selects "Other" Without Category
- Subcategory will be empty string
- Admin sees yellow warning box
- Prompted to select subcategory
- Shows all available technicians

### Case 2: Guest Changes Category After Selecting Service
- Hidden field updates with new category
- Correct category saved to database
- Admin sees latest selection

### Case 3: Old Guest Bookings (Before This Feature)
- No subcategory saved (NULL)
- Admin sees yellow warning box
- Can select subcategory manually
- Works same as before

## Testing Scenarios

### Scenario 1: Guest Books Custom Service with Category
1. Guest selects "Appliance Setup" category
2. Selects "Other" service
3. Enters "Solar Panel Installation"
4. Submits booking
5. ✅ Admin sees booking with "Appliance Setup" pre-selected
6. ✅ Technicians filtered by "Appliance Setup"

### Scenario 2: Guest Books Regular Service with Category
1. Guest selects "Wiring & Fixtures" category
2. Selects "Fan Installation" service
3. Submits booking
4. ✅ Admin sees regular booking
5. ✅ Technicians filtered by "Fan Installation" skill

### Scenario 3: Admin Changes Guest's Category
1. Guest selected "Major Appliances"
2. Admin thinks "Appliance Setup" is better
3. Admin clicks "Appliance Setup" button
4. ✅ Page reloads with new filter
5. ✅ Different technicians shown
6. ✅ Admin assigns appropriate technician

## Files Modified

1. **index.php**
   - Added hidden input field for subcategory
   - Updated JavaScript to populate hidden field
   - Subcategory captured from dropdown selection

2. **process-guest-booking.php**
   - Captures `sb_subcategory` from POST
   - Saves subcategory for both custom and regular bookings
   - Updated INSERT queries to include subcategory

3. **admin/admin-assign-technician.php**
   - Already detects guest custom bookings
   - Shows subcategory buttons automatically
   - No changes needed (already working!)

## Summary

Guest custom bookings now have the **same smart assignment workflow** as admin quick bookings:

1. **Guest** selects category from dropdown
2. **System** saves category with booking
3. **Admin** sees category and filtered technicians
4. **Admin** can change category if needed
5. **Technician** receives well-matched booking

This creates a **unified, efficient workflow** across all booking types (admin, user, guest) with intelligent technician matching based on subcategories.
