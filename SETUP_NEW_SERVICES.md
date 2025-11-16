# 🔧 Complete Service Catalog Setup Guide

## 📋 Overview
This guide will help you add 100+ electrical and related services to your Electrozot booking system.

## 🗂️ Service Categories

### 1. **Basic Electrical Work** (18 services)
- Wiring & Fixtures (8 services)
- Safety & Power (10 services)

### 2. **Electronic Repair** (29 services)
- Major Appliances (11 services)
- Other Gadgets (18 services)

### 3. **Installation & Setup** (20 services)
- Appliance Setup (12 services)
- Tech & Security (8 services)

### 4. **Servicing & Maintenance** (13 services)
- Routine Care & Maintenance

### 5. **Plumbing Work** (17 services)
- Fixtures, Taps, and Installations

---

## 🚀 Installation Steps

### Step 1: Add Services to Database

1. Open **phpMyAdmin** or your MySQL client
2. Select your `electrozot_db` database
3. Go to the **SQL** tab
4. Open the file: `DATABASE FILE/add_all_electrical_services.sql`
5. Copy all content and paste into SQL tab
6. Click **Go** to execute

**Expected Result:**
```
Services added successfully!
Total services by category displayed
```

### Step 2: Verify Services Added

Run this query to check:
```sql
SELECT s_category, COUNT(*) as Total 
FROM tms_service 
GROUP BY s_category;
```

You should see:
- Basic Electrical Work: 18
- Electronic Repair: 29
- Installation & Setup: 20
- Servicing & Maintenance: 13
- Plumbing Work: 17

---

## 📱 User Booking Pages

### Desktop Version
**File:** `usr/usr-book-service-categorized.php`

**Features:**
- ✅ Category filter buttons
- ✅ Card-based service display
- ✅ Grouped by category
- ✅ Hover effects
- ✅ Price and duration badges
- ✅ Direct booking button

**Access:** 
```
http://localhost/electrozot/usr/usr-book-service-categorized.php
```

### Mobile Version
**File:** `usr/mobile-book-service-new.php`

**Features:**
- ✅ Mobile-optimized design
- ✅ Horizontal scrolling category tabs
- ✅ Search functionality
- ✅ Bottom navigation
- ✅ Touch-friendly cards
- ✅ Gradient design

**Access:**
```
http://localhost/electrozot/usr/mobile-book-service-new.php
```

---

## 🔗 Update Navigation Links

### Update Sidebar (Desktop)
Edit: `usr/vendor/inc/sidebar.php`

Find the "Book Service" link and update to:
```php
<li class="nav-item">
    <a class="nav-link" href="usr-book-service-categorized.php">
        <i class="fas fa-calendar-plus"></i>
        <span>Book Service</span>
    </a>
</li>
```

### Update Mobile Dashboard
Edit: `usr/user-dashboard.php`

Update the book service button:
```php
<a href="mobile-book-service-new.php" class="btn btn-primary">
    <i class="fas fa-calendar-plus"></i> Book Service
</a>
```

---

## 🎨 Service Display Features

### Desktop Features:
1. **Category Filtering** - Click category buttons to filter
2. **All Services View** - See all services at once
3. **Card Layout** - Clean, organized cards
4. **Hover Effects** - Cards lift on hover
5. **Service Count** - Shows number of services per category

### Mobile Features:
1. **Search Bar** - Search services by name/description
2. **Category Tabs** - Horizontal scrolling tabs
3. **Grouped Display** - Services grouped by category
4. **Bottom Navigation** - Easy access to main features
5. **Touch Optimized** - Large touch targets

---

## 💰 Price Format

All prices are in INR (₹):
- Basic services: ₹150 - ₹2,500
- Repairs: ₹200 - ₹1,500
- Installations: ₹300 - ₹4,000
- Servicing: ₹400 - ₹1,500
- Plumbing: ₹200 - ₹1,200

---

## ⏱️ Duration Format

Services have estimated durations:
- Quick fixes: 30 mins - 1 hour
- Standard repairs: 1-2 hours
- Complex work: 2-4 hours
- Major installations: 4-6 hours or 1-2 days

---

## 🔧 Customization

### Change Prices
Edit the SQL file before importing, or update via phpMyAdmin:
```sql
UPDATE tms_service 
SET s_price = 500.00 
WHERE s_name = 'Service Name';
```

### Add More Services
```sql
INSERT INTO tms_service (s_name, s_description, s_category, s_price, s_duration, s_status) 
VALUES ('New Service', 'Description', 'Category', 500.00, '1-2 hours', 'Active');
```

### Disable Services
```sql
UPDATE tms_service 
SET s_status = 'Inactive' 
WHERE s_name = 'Service Name';
```

---

## 📊 Service Categories Breakdown

### Basic Electrical Work
- Home Wiring (New & Repair)
- Switch/Socket Installation
- Light Fixtures (Tube, LED, Chandelier)
- Festive Lighting
- Circuit Breaker & Fuse Box
- Inverter/UPS/Stabilizer
- Grounding System
- Electrical Fault Finding

### Electronic Repair
- AC Repair (Split, Window, Central)
- Refrigerator Repair & Gas Charging
- Washing Machine (All Types)
- Microwave, Geyser
- Fans (Ceiling, Table, Exhaust)
- TV Repair (LED, LCD, Smart)
- Small Appliances
- Power Tools

### Installation & Setup
- TV/DTH Installation
- Chimney Installation
- Fan Installation
- Washing Machine Setup
- Water Filter/Purifier
- CCTV & Security Cameras
- Wi-Fi Router Setup
- Smart Home Devices

### Servicing & Maintenance
- AC Servicing (Wet/Dry)
- Washing Machine Cleaning
- Geyser Descaling
- Water Filter Service
- Water Tank Cleaning
- Refrigerator Servicing

### Plumbing Work
- Tap/Faucet Installation & Repair
- Shower Installation
- Washbasin & Sink
- Toilet & Commode
- Flush Tank
- Pipe Leakage
- Water Motor

---

## ✅ Testing Checklist

- [ ] Services imported successfully
- [ ] All 5 categories visible
- [ ] Desktop page loads correctly
- [ ] Mobile page loads correctly
- [ ] Category filtering works
- [ ] Search functionality works (mobile)
- [ ] Book Now buttons work
- [ ] Prices display correctly
- [ ] Duration shows properly
- [ ] Navigation links updated

---

## 🐛 Troubleshooting

### Services Not Showing
1. Check database connection in `vendor/inc/config.php`
2. Verify services imported: `SELECT COUNT(*) FROM tms_service;`
3. Check s_status is 'Active'

### Category Filter Not Working
1. Clear browser cache
2. Check category names match exactly
3. Verify SQL query in PHP file

### Mobile Page Issues
1. Check viewport meta tag
2. Test on actual mobile device
3. Clear mobile browser cache

---

## 📞 Support

If you need help:
1. Check database for services
2. Verify file paths are correct
3. Check PHP error logs
4. Test with simple query first

---

## 🎉 You're All Set!

Your Electrozot booking system now has:
- ✅ 100+ professional services
- ✅ 5 organized categories
- ✅ Desktop booking interface
- ✅ Mobile-optimized interface
- ✅ Search & filter functionality
- ✅ Professional pricing structure

**Next Steps:**
1. Import the SQL file
2. Test both booking pages
3. Update navigation links
4. Customize prices if needed
5. Start taking bookings!
