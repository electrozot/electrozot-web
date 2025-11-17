# 🚀 Hierarchical Services - Quick Start Guide

## What Changed?

### Before:
- Admin adds service → Select from flat list of categories
- Guest books → Select from long list of all services
- Hard to find specific services

### After:
- Admin adds service → **Category → Subcategory → Service Name → Gadget Name**
- Guest books → **Category → Service Type → Specific Service**
- Easy navigation and organized structure

---

## 📋 Setup (One-Time)

### Step 1: Run Setup Script
Open in browser: `http://yoursite.com/setup-hierarchical-services.php`

This will:
- ✅ Add new database columns
- ✅ Create category reference table
- ✅ Insert predefined categories

### Step 2: Test Admin Panel
1. Go to **Admin Panel → Add Service**
2. Select a category (e.g., "Electronic Repair")
3. Subcategory dropdown auto-populates (e.g., "Major Appliances")
4. Fill service details
5. Save

### Step 3: Test Booking
1. Go to homepage booking form
2. Select category → subcategory → service
3. Submit test booking

---

## 📊 Category Structure

```
Basic Electrical Work
├── Wiring & Fixtures
└── Safety & Power

Electronic Repair
├── Major Appliances
└── Small Gadgets

Installation & Setup
├── Appliance Setup
└── Tech & Security

Servicing & Maintenance
└── Routine Care

Plumbing Work
└── Fixtures & Taps
```

---

## 🎯 How to Add a New Service

### Admin Panel → Add Service

1. **Category**: Select "Electronic Repair"
2. **Subcategory**: Auto-shows "Major Appliances" or "Small Gadgets"
3. **Service Name**: "AC Repair"
4. **Gadget Name**: "Split AC" (optional)
5. **Price**: 500
6. **Duration**: "1-2 hours"
7. **Status**: Active
8. Click **Add Service**

---

## 🔄 How to Update Existing Services

### Option 1: Via Admin Panel
1. Go to **Manage Services**
2. Click **Edit** on any service
3. Select proper **Category** and **Subcategory**
4. Add **Gadget Name** if applicable
5. Click **Update Service**

### Option 2: Via Database (Bulk Update)
```sql
-- Example: Update all AC services
UPDATE tms_service 
SET s_category = 'Electronic Repair',
    s_subcategory = 'Major Appliances',
    s_gadget_name = 'Split AC'
WHERE s_name LIKE '%Split AC%';
```

---

## 🎫 Quick Booking Flow

### Admin Quick Booking
1. Enter customer phone (auto-fills if registered)
2. **Select Category** → "Installation & Setup"
3. **Select Subcategory** → "Appliance Setup"
4. **Select Service** → Shows only relevant services
5. Price displays automatically
6. Fill address and notes
7. Create Booking

### Guest Booking (Homepage)
1. Enter phone and name
2. **Select Category** → "Electronic Repair"
3. **Select Service Type** → "Major Appliances"
4. **Select Service** → "Refrigerator Repair"
5. Fill address
6. Submit

---

## 🐛 Troubleshooting

### Subcategories not loading?
- Check browser console for errors
- Ensure jQuery is loaded
- Verify `admin/get-subcategories.php` exists

### Services not appearing?
- Ensure service has `s_subcategory` set
- Check service status is 'Active'
- Verify `admin/get-services-by-subcategory.php` is accessible

### Old services not showing?
Update them with subcategories:
```sql
UPDATE tms_service 
SET s_subcategory = 'Wiring & Fixtures' 
WHERE s_category = 'Basic Electrical Work' 
AND s_subcategory IS NULL;
```

---

## 📁 New Files

### Created:
- `admin/get-subcategories.php` - AJAX endpoint
- `admin/get-services-by-subcategory.php` - AJAX endpoint
- `admin/vendor/inc/check-customer.php` - Customer lookup
- `DATABASE FILE/add_hierarchical_service_structure.sql` - Migration
- `setup-hierarchical-services.php` - Setup script
- `HIERARCHICAL_SERVICE_SYSTEM_GUIDE.md` - Full documentation

### Modified:
- `admin/admin-add-service.php` - Cascading dropdowns
- `admin/admin-manage-single-service.php` - Edit with hierarchy
- `admin/admin-quick-booking.php` - Hierarchical booking
- `index.php` - Guest booking with categories

---

## ✅ Testing Checklist

- [ ] Run setup script successfully
- [ ] Add new service with category → subcategory
- [ ] Edit existing service
- [ ] Create quick booking (admin)
- [ ] Submit guest booking (homepage)
- [ ] Verify cascading dropdowns work
- [ ] Check services filter by subcategory

---

## 💡 Tips

1. **Gadget Name is Optional** - Use it for specific devices (e.g., "Split AC", "LED TV")
2. **Subcategories Auto-Load** - Based on selected category
3. **Services Filter Automatically** - Only relevant services show
4. **Old Services Still Work** - Just update them with subcategories
5. **Scalable Structure** - Easy to add more categories/subcategories

---

## 🆘 Need Help?

1. Check `HIERARCHICAL_SERVICE_SYSTEM_GUIDE.md` for detailed docs
2. Review browser console for JavaScript errors
3. Check PHP error logs for backend issues
4. Verify database structure matches migration

---

**Version:** 2.0  
**Last Updated:** November 2024  
**Status:** ✅ Production Ready
