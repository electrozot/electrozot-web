# 📋 Service Database Setup Guide

## Overview

This guide will help you populate your database with all the services that match your user dashboard structure, complete with subcategories and gadget names.

---

## 🗄️ Database Structure

Your services will have:
- **Service Name** (e.g., "AC Repair")
- **Subcategory** (e.g., "Major Appliances")
- **Gadget Name** (e.g., "Split AC") - Optional but recommended
- **Price, Duration, Description**

---

## 🚀 Setup Steps

### Step 1: Run the SQL File

Execute this file in your database:
```
DATABASE FILE/populate_services_with_gadgets.sql
```

**How to run:**
1. Open phpMyAdmin
2. Select `electrozot_db` database
3. Go to SQL tab
4. Copy and paste the contents of `populate_services_with_gadgets.sql`
5. Click "Go"

### Step 2: Verify Services

After running, you should see:
- ✅ 70+ services added
- ✅ 8 service types (subcategories)
- ✅ Services with gadget names

---

## 📊 Complete Service List

### 1. Wiring & Fixtures (6 services)
```
• Home Wiring Installation
• Switch/Socket Installation
• Tube Light Installation (Tube Light)
• LED Panel Installation (LED Panel)
• Chandelier Installation (Chandelier)
• Festive Lighting Setup
```

### 2. Safety & Power (9 services)
```
• Circuit Breaker Repair
• Fuse Box Repair
• Inverter Installation (Inverter)
• UPS Installation (UPS)
• Voltage Stabilizer Installation (Stabilizer)
• Grounding System Installation
• Electrical Outlet Installation
• Fan Regulator Repair (Fan Regulator)
• Electrical Fault Diagnosis
```

### 3. Major Appliances (9 services)
```
• AC Repair (Split AC)
• AC Repair (Window AC)
• AC Repair (Central AC)
• Refrigerator Repair (Refrigerator)
• Washing Machine Repair (Semi-Automatic)
• Washing Machine Repair (Fully Automatic)
• Washing Machine Repair (Front Load)
• Microwave Oven Repair (Microwave)
• Geyser Repair (Geyser)
```

### 4. Small Gadgets (11 services)
```
• TV Repair (LED TV)
• TV Repair (LCD TV)
• Fan Repair (Ceiling Fan)
• Fan Repair (Table Fan)
• Fan Repair (Exhaust Fan)
• Heater Repair (Room Heater)
• Cooler Repair (Air Cooler)
• Music System Repair (Music System)
• Induction Cooktop Repair (Induction)
• Iron Repair (Electric Iron)
• Power Tools Repair (Drill/Cutter)
```

### 5. Appliance Setup (11 services)
```
• TV Installation (LED TV)
• DTH Installation (DTH Dish)
• Chimney Installation (Electric Chimney)
• Fan Installation (Ceiling Fan)
• Fan Installation (Wall Fan)
• Washing Machine Installation (Washing Machine)
• Cooler Installation (Air Cooler)
• Water Filter Installation (Water Filter)
• RO Purifier Installation (RO Purifier)
• Geyser Installation (Geyser)
• Light Fixture Installation (Light Fixture)
```

### 6. Tech & Security (4 services)
```
• Camera Installation (CCTV Camera)
• WiFi Installation (WiFi Router)
• Smart Device Setup (Smart Switch)
• Smart Device Setup (Smart Light)
```

### 7. Routine Care (6 services)
```
• AC Servicing (AC)
• Washing Machine Cleaning (Washing Machine)
• Geyser Descaling (Geyser)
• Water Filter Servicing (Water Filter)
• Water Tank Cleaning
• Chimney Cleaning (Electric Chimney)
```

### 8. Fixtures & Taps (6 services)
```
• Tap Repair (Tap/Faucet)
• Shower Installation (Shower)
• Washbasin Installation (Washbasin)
• Toilet Installation (Toilet/Commode)
• Flush Tank Repair (Flush Tank)
• Pipe Leak Fix
```

---

## 🎯 How Services Display

### In User Dashboard:
```
Major Appliances
  ├── AC Repair (Split AC) - ₹1500
  ├── AC Repair (Window AC) - ₹1200
  ├── Refrigerator Repair (Refrigerator) - ₹1200
  └── Washing Machine Repair (Fully Automatic) - ₹1000
```

### In Booking Forms:
```
[Dropdown] Service Type
  → Major Appliances

[Dropdown] Select Service
  → AC Repair (Split AC)
  → AC Repair (Window AC)
  → Refrigerator Repair (Refrigerator)
  → Washing Machine Repair (Semi-Automatic)
  → Washing Machine Repair (Fully Automatic)
  → Washing Machine Repair (Front Load)
```

---

## ✨ Key Features

### Gadget Names:
- Help identify specific devices
- Show in parentheses: "AC Repair (Split AC)"
- Optional but recommended

### Multiple Variants:
- Same service, different gadgets
- Example: "AC Repair" for Split AC, Window AC, Central AC
- Each has its own price

### Clear Organization:
- Services grouped by type
- Easy to find and book
- Professional appearance

---

## 🔧 Customization

### To Add a New Service:
```sql
INSERT INTO tms_service 
(s_name, s_description, s_category, s_subcategory, s_gadget_name, s_price, s_duration, s_status) 
VALUES
('Service Name', 'Description', 'Category', 'Subcategory', 'Gadget Name', 500.00, '1-2 hours', 'Active');
```

### To Update a Service:
```sql
UPDATE tms_service 
SET s_price = 600.00, 
    s_gadget_name = 'New Gadget Name'
WHERE s_id = 1;
```

### To Delete a Service:
```sql
DELETE FROM tms_service WHERE s_id = 1;
```

---

## 📋 Verification Queries

### Check Total Services:
```sql
SELECT COUNT(*) as total FROM tms_service WHERE s_status = 'Active';
```

### Services by Type:
```sql
SELECT s_subcategory, COUNT(*) as count 
FROM tms_service 
WHERE s_status = 'Active'
GROUP BY s_subcategory;
```

### Services with Gadget Names:
```sql
SELECT s_name, s_gadget_name, s_price 
FROM tms_service 
WHERE s_gadget_name IS NOT NULL 
ORDER BY s_subcategory, s_name;
```

---

## ✅ Testing Checklist

After populating services:

- [ ] Run the SQL file successfully
- [ ] Verify 70+ services added
- [ ] Check user dashboard shows services
- [ ] Test booking form dropdowns
- [ ] Verify gadget names display
- [ ] Check service prices correct
- [ ] Test service search/filter

---

## 🎉 Benefits

### For Customers:
- Clear service identification
- Know exactly what they're booking
- See specific device types

### For Admin:
- Organized service management
- Easy to add new services
- Professional service catalog

### For Business:
- Comprehensive service offering
- Scalable structure
- Better customer experience

---

## 💡 Tips

1. **Gadget Names are Optional** - Use them for device-specific services
2. **Same Service, Different Gadgets** - Create separate entries for variants
3. **Pricing** - Set different prices for different gadget types
4. **Descriptions** - Keep them clear and concise
5. **Status** - Use 'Active' for available services, 'Inactive' to hide

---

## 🆘 Troubleshooting

### Services not showing?
- Check `s_status = 'Active'`
- Verify `s_subcategory` is set
- Check database connection

### Gadget names not displaying?
- Ensure `s_gadget_name` column exists
- Check AJAX endpoint returns gadget names
- Verify JavaScript displays them

### Booking form empty?
- Run the SQL file first
- Check service status is Active
- Verify subcategory matches dropdown

---

## 📚 Related Files

- `populate_services_with_gadgets.sql` - Main SQL file
- `admin/get-services-by-subcategory.php` - AJAX endpoint
- `usr/book-service.php` - User dashboard
- `index.php` - Guest booking form
- `admin/admin-quick-booking.php` - Admin booking

---

**Status:** ✅ Ready to populate  
**Total Services:** 70+  
**Service Types:** 8  
**Version:** 1.0

---

**Run the SQL file and your service catalog will be complete!** 🚀
