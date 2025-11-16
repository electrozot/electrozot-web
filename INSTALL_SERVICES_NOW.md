# 🚀 Install All Services - Quick Guide

## ⚡ Quick Steps (5 minutes):

### Step 1: Open phpMyAdmin
```
http://localhost/phpmyadmin
```

### Step 2: Select Database
- Click on `electrozot_db` in the left sidebar

### Step 3: Go to SQL Tab
- Click the **SQL** tab at the top

### Step 4: Run This SQL
Copy and paste this into the SQL box:

```sql
-- Add subcategory column
ALTER TABLE `tms_service` 
ADD COLUMN `s_subcategory` VARCHAR(200) NULL AFTER `s_category`;
```

Click **Go** button.

### Step 5: Import Services
Now go to the **Import** tab and:
1. Click **Choose File**
2. Select: `DATABASE FILE/setup_complete_services.sql`
3. Click **Go**

---

## ✅ Done!

Your booking system now has:
- ✅ 75+ services
- ✅ 5 main categories
- ✅ 8 subcategories
- ✅ Full responsive design

Refresh your booking page: `http://localhost/electrozot/usr/book-service.php`

---

## 🔍 Verify Installation

Run this query to check:
```sql
SELECT s_category, s_subcategory, COUNT(*) as Total 
FROM tms_service 
GROUP BY s_category, s_subcategory;
```

You should see:
- Basic Electrical Work → Wiring & Fixtures (8)
- Basic Electrical Work → Safety & Power (10)
- Electronic Repair → Major Appliances (11)
- Electronic Repair → Other Gadgets (19)
- Installation & Setup → Appliance Setup (10)
- Installation & Setup → Tech & Security (8)
- Servicing & Maintenance → Routine Care (10)
- Plumbing Work → Fixtures & Taps (12)

**Total: 78 Services**
