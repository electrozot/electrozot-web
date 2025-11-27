# Subcategory-Based Technician Filtering for Custom Bookings

## 🎯 Overview

For custom/other service bookings, the system now shows **ONLY technicians who have skills in the selected subcategory**. This ensures precise matching based on the 8 service subcategories.

---

## 📋 The 8 Subcategories

### ⚡ ELECTRICAL
1. **Wiring & Fixtures** - Wiring, switches, sockets, lights
2. **Safety & Power** - MCB, inverters, UPS, stabilizers

### 🔧 REPAIR
3. **Major Appliances** - AC, fridge, washing machine, geyser
4. **Other Gadgets** - TV, microwave, small appliances

### 🔌 INSTALL
5. **Appliance Setup** - New appliance installation
6. **Tech & Security** - CCTV, smart devices, security systems

### 🛠️ MAINTAIN
7. **Routine Care** - Regular maintenance, servicing

### 💧 PLUMBING
8. **Fixtures & Taps** - Taps, pipes, leaks, drains

---

## 🔄 How It Works

### Step 1: Admin Edits Custom Booking
```
1. Go to All Bookings page
2. Find custom booking (yellow edit icon)
3. Click Edit button
4. Select appropriate subcategory from dropdown
5. Add detailed service name
6. Click Update
```

### Step 2: System Filters Technicians
```
System Query:
- Find all services in selected subcategory
- Find technicians with skills in those services
- Filter by availability (has capacity)
- Show ONLY matched technicians
```

### Step 3: Admin Assigns Technician
```
Dropdown shows:
✅ Available Technicians - Subcategory Match (X)
  - Only technicians with relevant skills
  - Sorted by capacity and experience
  
🔴 At Capacity - Subcategory Match (X)
  - Matched but currently busy
  - Shown as disabled for reference
```

---

## 💡 Examples

### Example 1: Fan Repair
```
Customer Request: "Ceiling fan not working"
Admin Selects: Wiring & Fixtures
System Shows: Only technicians who checked:
  - Fan Installation
  - Wiring
  - Electrical Fixtures
  - Switch/Socket Installation
```

### Example 2: AC Service
```
Customer Request: "AC not cooling"
Admin Selects: Major Appliances
System Shows: Only technicians who checked:
  - AC Installation/Repair
  - Refrigerator Repair
  - Appliance Servicing
```

### Example 3: Plumbing Issue
```
Customer Request: "Kitchen tap leaking"
Admin Selects: Fixtures & Taps
System Shows: Only technicians who checked:
  - Tap Installation/Repair
  - Plumbing Services
  - Leak Fixing
```

---

## 🎨 User Interface

### Edit Custom Booking Page
```
┌─────────────────────────────────────┐
│ Service Subcategory *               │
│ ┌─────────────────────────────────┐ │
│ │ -- Select Subcategory --        │ │
│ │ ⚡ ELECTRICAL                    │ │
│ │   Wiring & Fixtures             │ │
│ │   Safety & Power                │ │
│ │ 🔧 REPAIR                        │ │
│ │   Major Appliances              │ │
│ │   Other Gadgets                 │ │
│ │ 🔌 INSTALL                       │ │
│ │   Appliance Setup               │ │
│ │   Tech & Security               │ │
│ │ 🛠️ MAINTAIN                      │ │
│ │   Routine Care                  │ │
│ │ 💧 PLUMBING                      │ │
│ │   Fixtures & Taps               │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

### Assign Technician Page
```
Matching: Showing technicians who checked "Wiring & Fixtures" during registration

✅ Available Technicians - Subcategory Match (3)
  - Rajesh Kumar (5 yrs, 2 slots free) | Skills: Fan, Wiring, Switch
  - Amit Singh (3 yrs, 1 slot free) | Skills: Wiring, Socket, MCB
  - Suresh Verma (2 yrs, 3 slots free) | Skills: Fan, Light, Wiring

🔴 At Capacity - Subcategory Match (1)
  - Ramesh Sharma (4 yrs) - At capacity | Skills: Wiring, Fan
```

---

## ✅ Benefits

### 1. **Precise Matching**
- Shows only relevant technicians
- No confusion with unrelated skills
- Better service quality

### 2. **Faster Assignment**
- Smaller, focused list
- Easy to choose right person
- Less scrolling through options

### 3. **Better Outcomes**
- Right technician for the job
- Fewer rejections
- Higher completion rates

### 4. **Clear Communication**
- Technician knows what to expect
- Customer gets expert service
- Admin makes informed decisions

---

## 🔧 Technical Details

### Database Query
```sql
-- Find services in subcategory
SELECT s_id FROM tms_service 
WHERE s_subcategory = 'Wiring & Fixtures'

-- Find technicians with those skills
SELECT DISTINCT t.* 
FROM tms_technician t
JOIN tms_technician_skills ts ON t.t_id = ts.ts_technician_id
WHERE ts.ts_service_id IN (service_ids)
AND t.t_current_bookings < t.t_booking_limit
ORDER BY t.t_current_bookings ASC
```

### Smart Matcher Function
```php
$matcher->findTechniciansBySubcategory(
    $subcategory,      // e.g., "Wiring & Fixtures"
    $booking_date,     // Booking date
    $booking_time      // Booking time
)
```

---

## 📊 Comparison

### Before (All Technicians)
```
Custom Booking: "Fan repair needed"
Technicians Shown: 25
- 8 with fan skills ✅
- 17 without fan skills ❌
Admin must manually review all 25
```

### After (Subcategory Filter)
```
Custom Booking: "Fan repair needed"
Subcategory: Wiring & Fixtures
Technicians Shown: 8
- 8 with fan/wiring skills ✅
- 0 without relevant skills ❌
Admin sees only relevant options
```

---

## 🚨 Important Notes

### 1. **Subcategory is Required**
- Admin MUST select subcategory for custom bookings
- Without subcategory, system shows warning
- Link provided to edit and add subcategory

### 2. **Technician Skills Must Be Set**
- Technicians must have skills added in their profile
- Skills are linked to services
- Services are grouped by subcategory

### 3. **No Match = No Technicians**
- If no technician has skills in subcategory
- System shows "No available technicians"
- Admin may need to add skills to technicians

---

## 🎓 Training Guide

### For Admins:

**Step 1: Understand Subcategories**
- Learn what each subcategory covers
- Know which services belong where
- Match customer request to subcategory

**Step 2: Edit Custom Bookings**
- Always add subcategory first
- Be specific in service name
- Add detailed description

**Step 3: Assign Wisely**
- Review matched technicians
- Check their specific skills
- Consider experience and availability

### For Technicians:

**Step 1: Update Your Skills**
- Add all services you can perform
- Be honest about capabilities
- Update when you learn new skills

**Step 2: Understand Subcategories**
- Know which subcategories you belong to
- Understand what bookings you'll receive
- Prepare for those types of jobs

---

## 📈 Success Metrics

### Expected Improvements:
- ✅ 80% reduction in irrelevant technician options
- ✅ 50% faster assignment time
- ✅ 30% fewer booking rejections
- ✅ Higher customer satisfaction
- ✅ Better technician utilization

---

## 🔍 Troubleshooting

### Problem: No Technicians Shown
**Cause:** No technician has skills in selected subcategory
**Solution:** 
1. Check if subcategory is correct
2. Add skills to technicians
3. Or select different subcategory

### Problem: Wrong Technicians Shown
**Cause:** Services not properly categorized
**Solution:**
1. Check service subcategory in database
2. Update service subcategory if needed
3. Refresh technician skills

### Problem: Technician Can't See Booking
**Cause:** Technician doesn't have required skill
**Solution:**
1. Add skill to technician profile
2. Or reassign to different technician

---

**System Version:** 2.1  
**Feature:** Subcategory-Based Filtering  
**Status:** ✅ Production Ready  
**Last Updated:** November 2025
