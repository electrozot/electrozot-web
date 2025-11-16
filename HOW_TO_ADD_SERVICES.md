# How to Add All Services to Database

## 🎯 Quick Setup

You need to add all 42 services to your database for the booking system to work properly.

---

## 📁 SQL File Created

**File:** `DATABASE FILE/add_all_services.sql`

This file contains all 42 services organized exactly as per your list:
- 10 Basic Electrical services
- 14 Electronic Repair services
- 11 Installation & Setup services
- 5 Servicing & Maintenance services
- 3 Plumbing services

---

## 🚀 How to Add Services

### Method 1: Using phpMyAdmin (Easiest)

1. Open phpMyAdmin in your browser
2. Select your database (electrozot_db)
3. Click on "SQL" tab
4. Copy the entire content from `DATABASE FILE/add_all_services.sql`
5. Paste it in the SQL query box
6. Click "Go" button
7. Done! ✅

### Method 2: Using MySQL Command Line

```bash
mysql -u root -p electrozot_db < "DATABASE FILE/add_all_services.sql"
```

### Method 3: Using PHP Script

Create a file `add-services.php` in your root:

```php
<?php
include('admin/vendor/inc/config.php');

$sql = file_get_contents('DATABASE FILE/add_all_services.sql');
$queries = explode(';', $sql);

foreach($queries as $query) {
    $query = trim($query);
    if(!empty($query)) {
        $mysqli->query($query);
    }
}

echo "Services added successfully!";
?>
```

Then open: `http://localhost/electrozot/add-services.php`

---

## 📊 Services Breakdown

### 1. BASIC ELECTRICAL WORK (10 services)

**Wiring & Fixtures (4):**
1. Home Wiring - ₹800
2. Switch/Socket Installation - ₹150
3. Light Fixture Installation - ₹200
4. Light Decoration Setup - ₹500

**Safety & Power (6):**
5. Circuit Breaker Repair - ₹400
6. Inverter/UPS Installation - ₹600
7. Grounding System - ₹700
8. New Outlet Installation - ₹250
9. Fan Regulator Repair - ₹150
10. Fault Finding - ₹350

---

### 2. ELECTRONIC REPAIR (14 services)

**Major Appliances (5):**
11. AC Repair - ₹500
12. Refrigerator Repair - ₹600
13. Washing Machine Repair - ₹400
14. Microwave Repair - ₹350
15. Geyser Repair - ₹400

**Other Gadgets (9):**
16. Fan Repair - ₹200
17. TV Repair - ₹500
18. Iron Repair - ₹150
19. Music System Repair - ₹400
20. Heater Repair - ₹300
21. Induction Cooktop Repair - ₹350
22. Air Cooler Repair - ₹300
23. Power Tools Repair - ₹400
24. Water Filter Repair - ₹350

---

### 3. INSTALLATION & SETUP (11 services)

**Appliance Setup (8):**
25. TV/DTH Installation - ₹400
26. Chimney Installation - ₹500
27. Fan Installation - ₹300
28. Washing Machine Installation - ₹300
29. Air Cooler Installation - ₹250
30. Water Filter Installation - ₹400
31. Geyser Installation - ₹500
32. Light Fixture Installation - ₹200

**Tech & Security (3):**
33. CCTV Installation - ₹1000
34. Wi-Fi Router Setup - ₹300
35. Smart Home Installation - ₹500

---

### 4. SERVICING & MAINTENANCE (5 services)

**Routine Care (5):**
36. AC Servicing - ₹600
37. Washing Machine Cleaning - ₹400
38. Geyser Descaling - ₹450
39. Water Filter Service - ₹350
40. Water Tank Cleaning - ₹800

---

### 5. PLUMBING WORK (3 services)

**Fixtures & Taps (3):**
41. Tap/Faucet/Shower Installation - ₹250
42. Washbasin/Sink Installation - ₹400
43. Toilet/Commode Installation - ₹600

---

## ✅ After Adding Services

### Test the Booking Flow

1. **Step 1: Category**
   - Select "Electronic Repair"

2. **Step 2: Sub-Category**
   - Select "Major Appliances"

3. **Step 3: Services**
   - Should show: AC Repair, Refrigerator Repair, Washing Machine Repair, Microwave Repair, Geyser Repair
   - ✅ Only 5 relevant services!

4. **Step 4: Address**
   - Fill location & pincode
   - Confirm booking

---

## 🎨 Service Categories in Database

After adding, your database will have:

| Category | Count | Examples |
|----------|-------|----------|
| Electrical | 10 | Wiring, Switches, Circuit Breakers |
| Appliance | 14 | AC, Fridge, TV, Fan, Iron |
| Installation | 11 | TV Setup, CCTV, Smart Home |
| Maintenance | 5 | AC Servicing, Cleaning |
| Plumbing | 3 | Taps, Toilets, Washbasins |

**Total: 43 services**

---

## 🔍 How Filtering Works

### Example: Electronic Repair → Major Appliances

**Keywords:** AC, Air Condition, Refrigerator, Fridge, Washing Machine, Microwave, Geyser

**Matches:**
- ✅ "Air Conditioner (AC) Repair" - Contains "AC"
- ✅ "Refrigerator Repair" - Contains "Refrigerator"
- ✅ "Washing Machine Repair" - Contains "Washing Machine"
- ✅ "Microwave Oven Repair" - Contains "Microwave"
- ✅ "Geyser (Water Heater) Repair" - Contains "Geyser"

**Doesn't Match:**
- ❌ "TV Repair" - Not in keywords
- ❌ "Fan Repair" - Not in keywords

---

## 🎯 Verification

After adding services, verify:

```sql
-- Check total services
SELECT COUNT(*) FROM tms_service WHERE s_status = 'Active';
-- Should show: 43

-- Check by category
SELECT s_category, COUNT(*) as count 
FROM tms_service 
WHERE s_status = 'Active' 
GROUP BY s_category;

-- Check specific services
SELECT s_name, s_category, s_price 
FROM tms_service 
WHERE s_name LIKE '%AC%';
```

---

## 🐛 Troubleshooting

### No services showing in Step 3?

**Check:**
1. Services added to database?
2. Service status is 'Active'?
3. Service name/description contains keywords?
4. Database connection working?

**Fix:**
- Run the SQL file again
- Check database for services
- Verify keyword matching in step3 file

---

## 📝 Important Notes

### Service Names
- Use exact names from the list
- Include details in parentheses
- Example: "Air Conditioner (AC) Repair"

### Categories
- Use: Electrical, Appliance, Installation, Maintenance, Plumbing
- Consistent naming is important for filtering

### Prices
- Set realistic prices
- Can be updated later from admin panel

### Duration
- Estimate time needed
- Format: "1-2 hours", "30 mins", etc.

---

## 🎉 After Setup

Once services are added:

1. ✅ Step 1 shows 5 main categories
2. ✅ Step 2 shows relevant sub-categories
3. ✅ Step 3 shows filtered services (only relevant ones!)
4. ✅ Step 4 shows address form
5. ✅ Booking gets confirmed

**The complete system will work perfectly!** 🚀

---

## 📞 Quick Start

1. Open phpMyAdmin
2. Select your database
3. Go to SQL tab
4. Copy content from `DATABASE FILE/add_all_services.sql`
5. Paste and execute
6. Test booking flow
7. Done! ✅

---

**Total Services: 43**  
**Categories: 5**  
**Sub-Categories: 8**  
**Status**: Ready to add!
