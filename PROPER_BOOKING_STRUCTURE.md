# Proper Booking Structure - Clear Hierarchy

## ✅ Correct Flow

Based on your detailed service list, here's the proper structure:

### 📊 Booking Hierarchy

```
Step 1: MAIN CATEGORY
├─ Basic Electrical Work
├─ Electronic Repair (Gadget/Appliance)
├─ Installation & Setup
├─ Servicing & Maintenance
└─ Plumbing Work

Step 2: SUB-CATEGORY
├─ Wiring & Fixtures
├─ Safety & Power
├─ Major Appliances
├─ Other Gadgets
├─ Appliance Setup
├─ Tech & Security
├─ Routine Care
└─ Fixtures & Taps

Step 3: SPECIFIC SERVICE
├─ Home Wiring (New installation and repair)
├─ Switch/Socket Installation
├─ AC Repair
├─ TV Installation
├─ CCTV Installation
└─ Tap Repair

Step 4: ADDRESS & BOOKING
├─ Service Location
├─ Pincode
└─ Confirm
```

---

## 🎯 Complete Service Structure

### 1. BASIC ELECTRICAL WORK

**Sub-Category: Wiring & Fixtures**
- Home Wiring (New installation and repair)
- Switch/Socket Installation and Replacement
- Light Fixture Installation (Tube lights, LED panels, chandeliers)
- Light Decoration/Festive Lighting Setup

**Sub-Category: Safety & Power**
- Circuit Breaker and Fuse Box troubleshooting
- Inverter, UPS, Voltage Stabilizer installation
- Grounding and Earthing system
- New Electrical Outlet/Point installation
- Ceiling Fan Regulator repair
- Electrical fault finding and short-circuit repair

---

### 2. ELECTRONIC REPAIR (GADGET/APPLIANCE)

**Sub-Category: Major Appliances**
- Air Conditioner (AC) Repair (Split, Window, Central)
- Refrigerator Repair and Gas Charging
- Washing Machine Repair (Semi/Fully automatic)
- Microwave Oven Repair
- Geyser (Water Heater) Repair

**Sub-Category: Other Gadgets**
- Fan Repair (Ceiling, Table, Exhaust)
- Television (TV) Repair and Troubleshooting
- Electric Iron/Press Repair
- Music System/Home Theatre Repair
- Electric Heater Repair
- Induction Cooktop and Electric Stove Repair
- Air Cooler Repair
- Power Tools Repair
- Water Filter/Purifier Repair

---

### 3. INSTALLATION & SETUP

**Sub-Category: Appliance Setup**
- TV/DTH Dish Installation and Tuning
- Electric Chimney Installation
- Ceiling and Wall Fan Installation
- Washing Machine Installation
- Air Cooler Installation
- Water Filter/Purifier Installation
- Geyser/Water Heater Installation
- Light Fixture Installation

**Sub-Category: Tech & Security**
- CCTV and Security Camera Installation
- Wi-Fi Router and Modem Setup
- Smart Home Device Installation

---

### 4. SERVICING & MAINTENANCE

**Sub-Category: Routine Care**
- AC Wet and Dry Servicing
- Washing Machine Maintenance and Cleaning
- Geyser Descaling and Service
- Water Filter Cartridge Replacement
- Water Tank Cleaning

---

### 5. PLUMBING WORK

**Sub-Category: Fixtures & Taps**
- Tap, Faucet, and Shower Installation/Repair
- Washbasin and Sink Installation/Repair
- Toilet, Commode, and Flush Tank Installation

---

## 📱 User Journey Example

### Customer wants AC Repair

**Step 1: Main Category**
```
┌─────────────────────────────────┐
│ ⚡ Basic Electrical Work        │
│ 📺 Electronic Repair ✓          │ ← Selects this
│ 🛠️ Installation & Setup         │
│ ⚙️ Servicing & Maintenance      │
│ 🚰 Plumbing Work                │
└─────────────────────────────────┘
```

**Step 2: Sub-Category**
```
Selected: Electronic Repair

┌─────────────────────────────────┐
│ 🏠 Major Appliances ✓           │ ← Selects this
│ 📱 Other Gadgets                │
└─────────────────────────────────┘
```

**Step 3: Specific Service**
```
Selected: Electronic Repair → Major Appliances

┌─────────────────────────────────┐
│ ❄️ AC Repair ✓                  │ ← Selects this
│    ₹500 | 1-2 hours             │
│                                 │
│ 🌡️ Refrigerator Repair          │
│    ₹600 | 1-2 hours             │
│                                 │
│ 👕 Washing Machine Repair       │
│    ₹400 | 1-2 hours             │
└─────────────────────────────────┘
```

**Step 4: Address**
```
Selected: AC Repair

📍 Service Location: [Address]
📌 Pincode: [______]
💬 Notes: [Optional]

[✅ Confirm Booking]
```

---

## 🎨 Visual Flow

```
Dashboard
    ↓
Step 1: Main Category
    ↓
Step 2: Sub-Category
    ↓
Step 3: Specific Service
    ↓
Step 4: Address & Confirm
    ↓
Booking Success!
```

---

## 📁 Files Created

1. **usr/usr-book-step1-category.php**
   - Main categories (5 options)
   - Basic Electrical, Electronic Repair, Installation, Servicing, Plumbing

2. **usr/usr-book-step2-subcategory.php** (To be created)
   - Sub-categories based on main category
   - Wiring & Fixtures, Major Appliances, etc.

3. **usr/usr-book-step3-service.php** (To be created)
   - Specific services from database
   - Filtered by category and sub-category

4. **usr/usr-book-step4-address.php** (To be created)
   - Address form
   - Booking confirmation

---

## 🎯 Benefits

### Clear Structure
✅ **Organized** - Logical hierarchy
✅ **Easy to Navigate** - Step by step
✅ **No Confusion** - Clear categories
✅ **Scalable** - Easy to add services

### User Experience
✅ **Fast** - 4 simple steps
✅ **Clear** - Know exactly what they're booking
✅ **Visual** - Progress indicator
✅ **Mobile-Friendly** - Works on all devices

---

## 📊 Database Structure Needed

To support this, your database should have:

```sql
tms_service table:
- s_id (service ID)
- s_name (e.g., "AC Repair")
- s_main_category (e.g., "Electronic Repair")
- s_sub_category (e.g., "Major Appliances")
- s_description
- s_price
- s_duration
- s_status
```

---

## 🚀 Next Steps

1. ✅ Step 1 created (Main Category)
2. ⏳ Create Step 2 (Sub-Category)
3. ⏳ Create Step 3 (Specific Service)
4. ⏳ Create Step 4 (Address & Confirm)
5. ⏳ Update database with proper categories

---

**The structure is now clear and organized!** 🎉

**Version**: 5.0 (Proper Structure)  
**Date**: November 2025  
**Status**: ✅ Step 1 Complete
