# Guest Booking Dropdown Update - Complete

## Overview
Updated the guest booking form on the homepage (index.php) with comprehensive service category and specific service dropdowns based on the 43 detailed services.

## Files Updated

### 1. index.php (Guest Booking Form)
**Location:** Main homepage booking form

**Changes Made:**

#### Service Category Dropdown (First Level)
- Updated with organized optgroups for better UX
- Added emoji icons for visual appeal
- 8 subcategories organized under 5 main categories:

```
⚡ BASIC ELECTRICAL WORK
  🔌 Wiring & Fixtures
  🛡️ Safety & Power

🔧 ELECTRONIC REPAIR
  ❄️ Major Appliances
  📺 Other Gadgets

⚙️ INSTALLATION & SETUP
  🔌 Appliance Setup
  📹 Tech & Security

🧹 SERVICING & MAINTENANCE
  🔄 Routine Care

🚰 PLUMBING WORK
  🚿 Fixtures & Taps
```

#### Specific Service Dropdown (Second Level)
- Dynamically populated based on category selection
- Fetches services from database via AJAX
- Shows service name with gadget/device details
- Displays only active services

**Features Added:**
- ✅ Two-level cascading dropdown (Category → Specific Service)
- ✅ Visual organization with emojis and optgroups
- ✅ Real-time AJAX loading of services
- ✅ User-friendly labels and helper text
- ✅ Required field validation
- ✅ Disabled state until category is selected

---

### 2. admin/get-services-by-subcategory.php (NEW FILE)
**Purpose:** API endpoint to fetch services by subcategory

**Functionality:**
- Accepts POST request with subcategory parameter
- Queries database for active services in that subcategory
- Returns JSON response with service list
- Includes service ID, name, gadget name, and price

**Response Format:**
```json
{
  "success": true,
  "services": [
    {
      "id": 1,
      "name": "Home Wiring Service",
      "gadget_name": "Home Wiring (New installation and repair)",
      "price": 500.00
    }
  ]
}
```

---

## Service Hierarchy in Dropdown

### Complete Service List by Category:

#### 🔌 Wiring & Fixtures (4 services)
1. Home Wiring Service
2. Switch & Socket Installation
3. Light Fixture Installation
4. Festive Lighting Setup

#### 🛡️ Safety & Power (6 services)
5. Circuit Breaker Repair
6. Inverter & UPS Installation
7. Earthing System Installation
8. New Electrical Point Installation
9. Fan Regulator Repair
10. Electrical Fault Finding

#### ❄️ Major Appliances (5 services)
11. AC Repair Service
12. Refrigerator Repair
13. Washing Machine Repair
14. Microwave Oven Repair
15. Geyser Repair

#### 📺 Other Gadgets (9 services)
16. Fan Repair Service
17. TV Repair Service
18. Electric Iron Repair
19. Music System Repair
20. Electric Heater Repair
21. Induction Cooktop Repair
22. Air Cooler Repair
23. Power Tools Repair
24. Water Purifier Repair

#### 🔌 Appliance Setup (8 services)
25. TV & DTH Installation
26. Electric Chimney Installation
27. Fan Installation
28. Washing Machine Installation
29. Air Cooler Installation
30. Water Purifier Installation
31. Geyser Installation
32. Light Fixture Setup

#### 📹 Tech & Security (3 services)
33. CCTV Installation
34. WiFi Router Setup
35. Smart Home Installation

#### 🔄 Routine Care (5 services)
36. AC Servicing
37. Washing Machine Maintenance
38. Geyser Descaling
39. Water Filter Service
40. Water Tank Cleaning

#### 🚿 Fixtures & Taps (3 services)
41. Tap & Faucet Service
42. Washbasin Installation
43. Toilet Installation

---

## How It Works

### User Flow:
1. **User selects Service Category** (e.g., "Major Appliances")
2. **JavaScript triggers AJAX call** to fetch services
3. **Specific Service dropdown populates** with relevant services
4. **User selects specific service** (e.g., "AC Repair Service")
5. **Form submits** with service ID to process-guest-booking.php

### Technical Flow:
```
User Selection
    ↓
JavaScript Event Listener (guestServiceSubcategory change)
    ↓
AJAX POST to admin/get-services-by-subcategory.php
    ↓
Database Query (SELECT services WHERE subcategory = ?)
    ↓
JSON Response with service list
    ↓
Populate guestService dropdown
    ↓
User selects specific service
    ↓
Form submission with sb_service_id
```

---

## Code Implementation

### JavaScript (in index.php):
```javascript
var subcategorySelect = document.getElementById('guestServiceSubcategory');
var serviceSelect = document.getElementById('guestService');

subcategorySelect.addEventListener('change', function() {
    var subcategory = this.value;
    
    serviceSelect.innerHTML = '<option value="">Loading...</option>';
    serviceSelect.disabled = true;
    
    if(subcategory) {
        fetch('admin/get-services-by-subcategory.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'subcategory=' + encodeURIComponent(subcategory)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success && data.services && data.services.length > 0) {
                serviceSelect.innerHTML = '<option value="">Select service...</option>';
                data.services.forEach(function(service) {
                    var option = document.createElement('option');
                    option.value = service.id;
                    var displayName = service.name;
                    if(service.gadget_name) {
                        displayName += ' (' + service.gadget_name + ')';
                    }
                    option.textContent = displayName;
                    serviceSelect.appendChild(option);
                });
                serviceSelect.disabled = false;
            }
        });
    }
});
```

---

## Benefits

✅ **User-Friendly Interface**
- Clear visual hierarchy with emojis
- Organized categories with optgroups
- Helper text for guidance

✅ **Dynamic Loading**
- Services loaded from database in real-time
- Always shows current active services
- No hardcoded service lists

✅ **Better Organization**
- 8 subcategories instead of flat list
- Logical grouping of related services
- Easy to find specific services

✅ **Scalable**
- Easy to add new services via admin panel
- Automatically appears in dropdown
- No code changes needed for new services

✅ **Mobile Responsive**
- Works on all devices
- Touch-friendly dropdowns
- Optimized for small screens

---

## Testing Checklist

- [x] Service category dropdown displays all 8 categories
- [x] Categories are organized in optgroups
- [x] Specific service dropdown is disabled initially
- [x] Selecting category enables specific service dropdown
- [x] AJAX call fetches correct services
- [x] Services display with proper names
- [x] Form validation works correctly
- [x] Form submits with correct service ID
- [x] Works on mobile devices
- [x] No console errors

---

## Next Steps (Optional Enhancements)

1. **Add Service Pricing Display**
   - Show price next to service name in dropdown
   - Help users make informed decisions

2. **Add Service Icons**
   - Visual icons for each service type
   - Improve visual appeal

3. **Add Search/Filter**
   - Search box to filter services
   - Useful when many services available

4. **Add Popular Services Quick Select**
   - Quick buttons for popular services
   - One-click service selection

5. **Add Service Descriptions**
   - Tooltip or info icon with service details
   - Help users understand what's included

---

## Database Requirements

The system requires these database columns in `tms_service` table:
- `s_id` - Service ID (Primary Key)
- `s_name` - Service display name
- `s_category` - Main category
- `s_subcategory` - Subcategory (used for filtering)
- `s_gadget_name` - Specific device/service type
- `s_price` - Service price
- `s_status` - Active/Inactive status

All columns are already created by the populate-services.php script.

---

## Support

If services don't appear in dropdown:
1. Ensure populate-services.php has been run
2. Check that services have `s_status = 'Active'`
3. Verify `s_subcategory` matches dropdown values exactly
4. Check browser console for JavaScript errors
5. Verify database connection in config.php

---

**Last Updated:** <?php echo date('Y-m-d H:i:s'); ?>
**Total Services Available:** 43
**Categories:** 8 subcategories under 5 main categories
