# "Other" Service Option in Booking Forms

## Overview
Added an "Other" option to all service dropdowns, allowing customers to book services that are not listed in the predefined service catalog.

## Features Implemented

### 1. **"Other" Option in Service Dropdown**
- Appears at the bottom of every service category
- Label: "Other (Service not listed)" or "Other - Specify your service"
- Always available regardless of category selected

### 2. **Custom Service Input Field**
- Appears when "Other" is selected
- Required field with validation
- Allows customers to describe their specific service need
- Placeholder: "Enter the service you need"

### 3. **Database Handling**
- New column: `sb_custom_service` in `tms_service_booking` table
- Stores custom service name when "Other" is selected
- Service ID set to 0 for custom services
- Price set to 0 (to be determined by admin)

### 4. **Description Enhancement**
- Custom service name prepended to description as "CUSTOM SERVICE: [name]"
- Helps admin quickly identify custom service requests
- Original description preserved below

## Files Modified

### 1. **admin/get-services-by-subcategory.php**
- Added "Other" option to all service API responses
- Always included regardless of available services
- Returns special ID: 'other'

### 2. **index.php** (Main Booking Form)
- Added hidden input field for custom service name
- JavaScript to show/hide custom input based on selection
- Form validation for custom service input
- Event listeners for service dropdown changes

### 3. **process-guest-booking.php** (Booking Processing)
- Handles "Other" service selection
- Validates custom service name input
- Creates booking with service_id = 0
- Stores custom service name in database
- Sets price to 0 for admin review

## How It Works

### User Flow:

1. **Select Service Category**
   ```
   User selects: "Wiring & Fixtures"
   ```

2. **Service Dropdown Loads**
   ```
   - Home Wiring
   - Switch/Socket Installation
   - Light Fixture Installation
   - Other (Service not listed)  ← Always at bottom
   ```

3. **Select "Other"**
   ```
   Custom input field appears:
   "Specify Your Service *"
   [Enter the service you need]
   ```

4. **Enter Custom Service**
   ```
   User types: "Solar panel installation"
   ```

5. **Submit Booking**
   ```
   Booking created with:
   - service_id: 0
   - sb_custom_service: "Solar panel installation"
   - sb_description: "CUSTOM SERVICE: Solar panel installation\n\n[original description]"
   - sb_total_price: 0
   ```

### Admin View:

When admin views the booking:
- Service shows as "Custom Service" or blank
- Custom service name visible in `sb_custom_service` field
- Description starts with "CUSTOM SERVICE: [name]"
- Price is 0 (admin can update)
- Admin can assign appropriate technician
- Admin can set final price

## Database Schema

### New Column Added:
```sql
ALTER TABLE tms_service_booking 
ADD COLUMN IF NOT EXISTS sb_custom_service VARCHAR(255) DEFAULT NULL;
```

### Booking Record Example:
```
sb_id: 123
sb_user_id: 45
sb_service_id: 0  ← Indicates custom service
sb_custom_service: "Solar panel installation"
sb_description: "CUSTOM SERVICE: Solar panel installation\n\nNeed 3 panels installed on roof"
sb_total_price: 0  ← To be set by admin
sb_status: Pending
```

## JavaScript Logic

### Show/Hide Custom Input:
```javascript
serviceSelect.addEventListener('change', function() {
    var selectedValue = this.value;
    
    if(selectedValue === 'other') {
        // Show custom input
        otherServiceDiv.style.display = 'block';
        otherServiceInput.setAttribute('required', 'required');
        otherServiceInput.focus();
    } else {
        // Hide custom input
        otherServiceDiv.style.display = 'none';
        otherServiceInput.removeAttribute('required');
        otherServiceInput.value = '';
    }
});
```

## Validation Rules

### Frontend Validation:
1. Service category must be selected
2. Either a service OR "Other" must be selected
3. If "Other" selected, custom service name is required
4. Custom service name cannot be empty

### Backend Validation:
1. Checks if service_id is 'other'
2. Validates custom service name is not empty
3. Trims whitespace from custom service name
4. Ensures all required fields are present

## Admin Features Needed

To fully support this feature, admin panel should have:

### 1. **View Custom Services**
- Display custom service name prominently
- Show "CUSTOM SERVICE" badge
- Highlight bookings with service_id = 0

### 2. **Set Price**
- Allow admin to set price for custom services
- Update sb_total_price field
- Notify customer of final price

### 3. **Convert to Regular Service** (Optional)
- If custom service is common, admin can:
  - Create new service in catalog
  - Link booking to new service
  - Update service_id

### 4. **Reports**
- Track most requested custom services
- Identify gaps in service catalog
- Add popular custom services to catalog

## Example Use Cases

### Use Case 1: Uncommon Service
```
Customer needs: "Smart doorbell installation"
Not in catalog → Selects "Other"
Types: "Smart doorbell installation with WiFi setup"
Admin reviews → Assigns technician → Sets price
```

### Use Case 2: Specialized Repair
```
Customer needs: "Vintage radio repair"
Not in catalog → Selects "Other"
Types: "1960s transistor radio repair"
Admin reviews → Finds specialist → Sets price
```

### Use Case 3: Combined Service
```
Customer needs: "Complete home automation"
Not in catalog → Selects "Other"
Types: "Smart lights, thermostat, and security system installation"
Admin reviews → Creates custom package → Sets price
```

## Benefits

### For Customers:
✅ Can book any service, even if not listed
✅ No need to call for unlisted services
✅ Describe exact needs in their own words
✅ Faster booking process

### For Business:
✅ Capture all service requests
✅ Identify new service opportunities
✅ Expand service catalog based on demand
✅ No lost bookings due to limited catalog
✅ Better customer satisfaction

### For Admin:
✅ See all booking requests in one place
✅ Flexibility to price custom services
✅ Data on customer needs
✅ Opportunity to add new services

## Testing

### Test Case 1: Select Other Service
1. Go to booking form
2. Select any category
3. Select "Other (Service not listed)"
4. Verify custom input appears
5. Enter custom service name
6. Submit booking
7. Verify booking created with service_id = 0

### Test Case 2: Validation
1. Select "Other"
2. Leave custom input empty
3. Try to submit
4. Verify validation error
5. Enter custom service
6. Verify submission succeeds

### Test Case 3: Switch Back
1. Select "Other"
2. Custom input appears
3. Select different service
4. Verify custom input hides
5. Verify custom input not required

## Future Enhancements

1. **Auto-suggest**: Show similar services as user types
2. **Image Upload**: Allow customers to upload photos
3. **Price Estimation**: AI-based price estimation for custom services
4. **Quick Add**: Admin can quickly add custom service to catalog
5. **Service Templates**: Pre-fill common custom service descriptions
6. **Customer History**: Track customer's custom service requests

## Troubleshooting

### Custom Input Not Showing
- Check JavaScript console for errors
- Verify element IDs match: `otherServiceDiv`, `otherServiceInput`
- Ensure JavaScript is loaded after DOM

### Booking Fails with "Other"
- Check if `sb_custom_service` column exists
- Verify backend validation logic
- Check error logs for SQL errors

### Price Shows as 0
- This is expected for custom services
- Admin should update price manually
- Consider adding admin notification

## Support

For issues:
1. Check browser console for JavaScript errors
2. Verify database column exists
3. Check server error logs
4. Test with different browsers

---

**Last Updated**: November 17, 2025
**Status**: ✅ Fully Implemented
**Version**: 1.0
