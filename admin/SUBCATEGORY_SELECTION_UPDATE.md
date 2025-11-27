# Subcategory Selection for Custom Services - Always Visible

## What Changed

The subcategory selection buttons now **ALWAYS appear** on the assign technician page for custom service bookings, regardless of whether a subcategory is already set.

## Features

### 1. Always Visible Buttons
- Previously: Buttons only showed when `sb_subcategory` was empty
- Now: Buttons always show for custom bookings, allowing easy changes

### 2. Visual Feedback
- **No subcategory set**: Yellow/amber alert box with warning icon
- **Subcategory already set**: Green alert box with checkmark, showing current selection
- **Active button**: Selected subcategory button is highlighted with gradient background

### 3. Dynamic Updates
- Click any subcategory button to change the filter
- Page automatically reloads to show technicians matching the new subcategory
- Selected subcategory is pre-highlighted when page loads

### 4. AJAX Integration
- Subcategory changes are saved immediately via AJAX
- No need to submit the full form to update subcategory
- Smooth transition with visual feedback

## How It Works

### On Page Load
1. If custom booking has a subcategory, the corresponding button is pre-selected (highlighted)
2. Green success message shows current subcategory
3. Technician list is filtered based on current subcategory

### When Clicking a Button
1. Button gets highlighted with active state
2. AJAX request updates `sb_subcategory` in database
3. Success message shows selected subcategory
4. Page reloads after 800ms to refresh technician list

### Technician Filtering
- **With subcategory**: Shows only technicians with skills matching that subcategory
- **Without subcategory**: Shows all available technicians (prompts to select one)

## Files Modified

1. **admin/admin-assign-technician.php**
   - Changed condition from `if(empty($booking_data->sb_subcategory))` to `if(true)`
   - Updated alert box to show different colors/messages based on subcategory status
   - Added JavaScript to handle button clicks and pre-selection
   - Added AJAX call to update subcategory

2. **admin/admin-edit-custom-booking.php**
   - Added AJAX handler at the top to process subcategory updates
   - Returns JSON response for success/failure

## Usage

### For Admins
1. Open any custom service booking in "Assign Technician" page
2. See the subcategory selection buttons at the top
3. Click any button to filter technicians by that subcategory
4. Page reloads automatically with filtered results
5. Assign technician from the filtered list

### Subcategory Options
- **Wiring & Fixtures** (Electrical)
- **Safety & Power** (Electrical)
- **Major Appliances** (Appliance)
- **Other Gadgets** (Appliance)
- **Appliance Setup** (Installation)
- **Tech & Security** (Installation)
- **Routine Care** (Maintenance)
- **Fixtures & Taps** (Plumbing)

## Benefits

✅ **Flexibility**: Change subcategory anytime during assignment
✅ **Better Matching**: Easily try different subcategories to find available technicians
✅ **Visual Clarity**: Clear indication of current selection
✅ **No Extra Steps**: No need to go to edit page to change subcategory
✅ **Instant Feedback**: See filtered technicians immediately after selection

## Technical Details

### Database Update
```sql
UPDATE tms_service_booking 
SET sb_subcategory = ? 
WHERE sb_id = ?
```

### JavaScript Event Handler
```javascript
$('.subcategory-select-btn').on('click', function() {
    const subcategory = $(this).data('subcategory');
    // Update via AJAX
    // Reload page to refresh technician list
});
```

### Pre-selection Logic
```javascript
// On page load, highlight current subcategory
const currentSubcategory = '<?php echo $booking_data->sb_subcategory; ?>';
$('.subcategory-select-btn').each(function() {
    if($(this).data('subcategory') === currentSubcategory) {
        $(this).addClass('active');
    }
});
```
