# Skill-Based Technician Matching System

## Overview
This system ensures that only technicians with the required skills are shown when assigning bookings. It matches the exact service name from the booking with technician skills.

## Setup Instructions

### Step 1: Run SQL File
```sql
-- Run this file in phpMyAdmin or MySQL
admin/simple-skill-matching.sql
```

This adds:
- `t_skills` column to `tms_technician` table (stores comma-separated service names)
- `sb_service_name`, `sb_category`, `sb_subcategory` columns to `tms_service_booking` table

### Step 2: Add Technicians with Skills
Use the new page: `admin-add-technician-with-skills.php`

Features:
- Shows all 43 services organized by category
- Admin ticks the services technician can handle
- "Select All" button for each category
- Live counter shows how many skills selected

### Step 3: Assign Bookings
Use the new page: `admin-assign-booking-skill-match.php?booking_id=X`

Features:
- Shows the required skill for the booking
- **Only displays technicians who have that exact skill**
- Shows skill match badge
- Displays all technician skills (highlights the matching one)
- Shows available slots (current bookings / limit)
- If no match found, shows helpful message

## How It Works

### 1. When Booking is Created
The booking stores:
- `sb_service_name` = "AC (Split) - Repair"
- `sb_category` = "Electronic Repair"
- `sb_subcategory` = "Major Appliances"

### 2. When Admin Assigns Technician
System searches:
```sql
SELECT * FROM tms_technician 
WHERE FIND_IN_SET('AC (Split) - Repair', t_skills) > 0
  AND t_status = 'Available'
  AND t_current_bookings < t_booking_limit
```

### 3. Only Matching Technicians Shown
- Technician A has skills: "AC (Split) - Repair, Refrigerator - Repair" ✅ SHOWN
- Technician B has skills: "Washing Machine - Repair, TV Repair" ❌ NOT SHOWN

### 4. Assignment Validation
Before assigning, system checks:
1. Does technician have the required skill? ✅
2. Is technician available? ✅
3. Has technician reached booking limit? ✅

If all checks pass → Booking assigned ✅
If any check fails → Error message shown ❌

## All 43 Services

### Basic Electrical Work (12 services)
**Wiring & Fixtures:**
1. Home Wiring - New Installation
2. Home Wiring - Repair
3. Switch/Socket - Installation
4. Switch/Socket - Replacement
5. Light Fixture - Installation
6. Festive Lighting - Setup

**Safety & Power:**
7. Circuit Breaker - Repair
8. Inverter - Installation
9. UPS - Installation
10. Voltage Stabilizer - Installation
11. Grounding System - Installation
12. Electrical Fault - Repair

### Electronic Repair (15 services)
**Major Appliances:**
13. AC (Split) - Repair
14. AC (Window) - Repair
15. Refrigerator - Repair
16. Refrigerator - Gas Charging
17. Washing Machine - Repair
18. Microwave Oven - Repair
19. Geyser/Water Heater - Repair

**Other Gadgets:**
20. Ceiling Fan - Repair
21. Table Fan - Repair
22. LED TV - Repair
23. Smart TV - Repair
24. Electric Iron - Repair
25. Induction Cooktop - Repair
26. Air Cooler - Repair
27. Water Filter/Purifier - Repair

### Installation & Setup (12 services)
**Appliance Setup:**
28. TV/DTH - Installation
29. Electric Chimney - Installation
30. Ceiling Fan - Installation
31. Washing Machine - Installation
32. Air Cooler - Installation
33. Water Filter/Purifier - Installation
34. Geyser/Water Heater - Installation

**Tech & Security:**
35. CCTV Camera - Installation (Single)
36. CCTV Camera - Installation (4 Cameras)
37. Wi-Fi Router - Setup
38. Smart Switch - Installation
39. Smart Light - Installation

### Servicing & Maintenance (7 services)
**Routine Care:**
40. AC - Wet Servicing
41. AC - Dry Servicing
42. Washing Machine - Cleaning
43. Geyser - Descaling
44. Water Filter - Cartridge Replacement
45. Water Tank - Cleaning (Manual)
46. Water Tank - Cleaning (Motorized)

### Plumbing Work (8 services)
**Fixtures & Taps:**
47. Tap/Faucet - Installation
48. Tap/Faucet - Repair
49. Shower - Installation
50. Shower - Repair
51. Washbasin - Installation
52. Kitchen Sink - Installation
53. Toilet/Commode - Installation
54. Flush Tank - Installation

## API Endpoint

### Check Skill Match
```
GET/POST: admin/api-check-skill-match.php?service=AC (Split) - Repair
```

Response:
```json
{
  "success": true,
  "service_name": "AC (Split) - Repair",
  "count": 2,
  "technicians": [
    {
      "t_id": 5,
      "t_name": "John Doe",
      "t_phone": "1234567890",
      "available_slots": 3,
      "skills": ["AC (Split) - Repair", "AC (Window) - Repair"]
    }
  ]
}
```

## Benefits

✅ **Accurate Matching**: Only qualified technicians shown
✅ **No Manual Checking**: System automatically filters
✅ **Prevents Errors**: Can't assign wrong technician
✅ **Easy Management**: Simple checkbox interface
✅ **Scalable**: Easy to add more services
✅ **Fast**: Uses MySQL FIND_IN_SET for quick search

## Integration with Existing System

This works with your current booking flow:
1. User selects service in `book-service-step3.php`
2. Booking created with service name
3. Admin opens assignment page
4. **System shows only matching technicians** ✅
5. Admin assigns booking
6. Technician receives notification

No changes needed to user-facing pages!
