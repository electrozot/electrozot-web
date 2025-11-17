# Hierarchical Service System Implementation Guide

## Overview
The service booking system has been restructured to use a hierarchical approach:
**Category → Subcategory → Service (with optional Gadget Name)**

This makes it easier for admins to manage services and for customers to find and book the right service.

---

## Database Changes

### 1. Run the SQL Migration
Execute this file first to set up the structure:
```
DATABASE FILE/add_hierarchical_service_structure.sql
```

This will:
- Add `s_subcategory` column to `tms_service` table
- Add `s_gadget_name` column to `tms_service` table
- Create `tms_service_categories` reference table
- Insert predefined category-subcategory mappings

### 2. Database Structure

**Categories and their Subcategories:**

1. **Basic Electrical Work**
   - Wiring & Fixtures
   - Safety & Power

2. **Electronic Repair**
   - Major Appliances
   - Small Gadgets

3. **Installation & Setup**
   - Appliance Setup
   - Tech & Security

4. **Servicing & Maintenance**
   - Routine Care

5. **Plumbing Work**
   - Fixtures & Taps

---

## Admin Panel Changes

### 1. Add New Service (admin-add-service.php)
**New Flow:**
1. Select **Category** (e.g., "Electronic Repair")
2. Select **Subcategory** (e.g., "Major Appliances") - auto-populated based on category
3. Enter **Service Name** (e.g., "AC Repair")
4. Enter **Gadget/Device Name** (optional) (e.g., "Split AC")
5. Fill other details (description, price, duration, status)

**Features:**
- Cascading dropdowns (subcategory loads based on category)
- Gadget name field for specific device identification
- All existing fields remain functional

### 2. Quick Booking (admin-quick-booking.php)
**New Flow:**
1. Enter customer phone (auto-fills registered customer details)
2. Select **Service Category**
3. Select **Service Subcategory** (loads based on category)
4. Select **Service** (loads only services from selected subcategory)
5. Service price displays automatically

**Benefits:**
- Easier to find services (filtered by subcategory)
- Faster booking process
- Reduced errors in service selection

---

## Guest Booking Changes (index.php)

### Guest Booking Form
**New Flow:**
1. Customer enters phone and name
2. Select **Service Category** (e.g., "Installation & Setup")
3. Select **Service Type** (Subcategory) (e.g., "Appliance Setup")
4. Select **Service** (shows only relevant services like "Washing Machine Installation")

**User Experience:**
- Cleaner, more organized service selection
- Customers can easily navigate to their needed service
- Reduces confusion with long service lists

---

## New Files Created

### 1. AJAX Endpoints
- `admin/get-subcategories.php` - Returns subcategories for a category
- `admin/get-services-by-subcategory.php` - Returns services for a subcategory
- `admin/vendor/inc/check-customer.php` - Checks if customer exists by phone

### 2. Database Migration
- `DATABASE FILE/add_hierarchical_service_structure.sql` - Sets up the new structure

---

## How to Migrate Existing Services

### Option 1: Manual Update via phpMyAdmin
```sql
-- Example: Update existing AC services
UPDATE tms_service 
SET s_category = 'Electronic Repair',
    s_subcategory = 'Major Appliances',
    s_gadget_name = 'Split AC'
WHERE s_name LIKE '%Split AC%';

UPDATE tms_service 
SET s_category = 'Electronic Repair',
    s_subcategory = 'Major Appliances',
    s_gadget_name = 'Window AC'
WHERE s_name LIKE '%Window AC%';
```

### Option 2: Delete Old Services and Re-add
1. Go to Admin Panel → Manage Services
2. Delete old services (they go to Recycle Bin)
3. Add new services using the new hierarchical form

---

## Testing Checklist

### Admin Panel
- [ ] Add new service with category → subcategory → gadget name
- [ ] Verify cascading dropdowns work correctly
- [ ] Create quick booking with new service selection flow
- [ ] Verify service price displays correctly

### Guest Booking
- [ ] Test category selection
- [ ] Verify subcategory loads based on category
- [ ] Verify services load based on subcategory
- [ ] Submit a test booking

### Database
- [ ] Verify `s_subcategory` column exists in `tms_service`
- [ ] Verify `s_gadget_name` column exists in `tms_service`
- [ ] Verify `tms_service_categories` table exists with data

---

## Benefits of This System

### For Admins:
✅ Better organization of services
✅ Easier to manage large service catalogs
✅ Clear categorization reduces confusion
✅ Gadget names help identify specific services

### For Customers:
✅ Easier to find the right service
✅ Logical flow from general to specific
✅ Reduced scrolling through long lists
✅ Better user experience

### For Business:
✅ Scalable structure for adding more services
✅ Better analytics (services by category/subcategory)
✅ Professional appearance
✅ Reduced booking errors

---

## Troubleshooting

### Issue: Subcategories not loading
**Solution:** Check browser console for JavaScript errors. Ensure jQuery is loaded.

### Issue: Services not appearing
**Solution:** 
1. Verify services have `s_subcategory` set in database
2. Check service status is 'Active'
3. Verify AJAX endpoint `get-services-by-subcategory.php` is accessible

### Issue: Old services not showing
**Solution:** Update old services to include subcategory:
```sql
UPDATE tms_service 
SET s_subcategory = 'Wiring & Fixtures' 
WHERE s_category = 'Basic Electrical Work' 
AND s_subcategory IS NULL;
```

---

## Next Steps

1. **Run the SQL migration** to add new columns and tables
2. **Test the admin add service form** to ensure cascading works
3. **Update existing services** with subcategories (if any)
4. **Test quick booking** with the new flow
5. **Test guest booking** on the homepage
6. **Add new services** using the hierarchical structure

---

## Support

If you encounter any issues:
1. Check browser console for JavaScript errors
2. Check PHP error logs for backend issues
3. Verify database structure matches the migration
4. Ensure all new files are uploaded to the server

---

**Implementation Date:** November 2024
**Version:** 2.0
**Status:** Ready for Production
