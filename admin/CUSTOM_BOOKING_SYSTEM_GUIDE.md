# Custom/Other Booking System - Complete Guide

## 🎯 Overview

The enhanced custom booking system intelligently matches technicians based on service descriptions and treats all custom bookings as **non-fixed-price** (technician sets price during completion).

---

## 🚀 Setup Instructions

### Step 1: Run the Upgrade Script
Navigate to: `http://your-domain/admin/upgrade-custom-booking-system.php`

This will:
- ✅ Add `sb_detailed_service_name` column for admin to specify exact service
- ✅ Add `sb_extracted_keywords` column for smart matching
- ✅ Add `sb_suggested_category` column for auto-categorization
- ✅ Create `tms_service_keywords` table with 30+ keyword mappings
- ✅ Set all custom bookings to ₹0 (non-fixed-price)

---

## 📋 How It Works

### For Custom/Other Service Bookings:

#### 1. **Customer Books Service**
- Customer selects "Other" service
- Provides description (e.g., "Ceiling fan regulator not working, need repair")
- May upload image of the issue

#### 2. **Admin Adds Detailed Service Name**
- Go to **All Bookings** page
- Find the custom booking (shows ⚠️ edit icon)
- Click **Edit** button (yellow icon)
- Add detailed service name: `"Ceiling Fan Regulator Repair and Wiring Check"`
- Add any additional description
- Click **Update**

#### 3. **Smart Keyword Extraction**
System automatically extracts keywords:
```
Input: "Ceiling Fan Regulator Repair and Wiring Check"
Extracted Keywords: fan, regulator, repair, wiring
Suggested Category: ELECTRICAL
```

#### 4. **Smart Technician Matching**
System finds technicians with matching skills:
- Searches for technicians who can handle: Fan, Regulator, Wiring, Repair
- Filters by availability (has capacity)
- Ranks by skill match score
- Shows only relevant technicians

#### 5. **Assign Technician**
- Admin sees **matched technicians first** (highlighted)
- Technicians with relevant skills shown at top
- Can still see all technicians if needed
- Assign the best match

#### 6. **Technician Completes Work**
- Technician treats it like **non-fixed-price booking**
- Sets final price based on actual work done
- Uploads completion photo and bill
- Collects payment

---

## 🔑 Key Features

### 1. **Smart Keyword Extraction**
Automatically identifies service type from description:

| Keywords Found | Matched Services | Category |
|---|---|---|
| fan, wiring, switch | Fan Installation, Wiring | ELECTRICAL |
| ac, repair, cooling | AC Repair, Cooling | APPLIANCE REPAIR |
| tap, leak, pipe | Plumbing, Tap Repair | PLUMBING |
| geyser, water heater | Geyser Installation | APPLIANCE REPAIR |

### 2. **Intelligent Technician Filtering**
Shows only technicians with relevant skills:

```
Example: "AC not cooling, need gas refill"
Keywords: ac, cooling, repair
Matched Technicians:
  ✅ Rajesh (AC Specialist, 5 yrs) - 2 slots free
  ✅ Amit (AC & Refrigeration, 3 yrs) - 1 slot free
  ❌ Suresh (Electrician only) - Hidden (no AC skills)
```

### 3. **Non-Fixed-Price Treatment**
All custom bookings automatically:
- Set to ₹0 initially
- Technician enters final price during completion
- Based on actual parts used + labor
- Same flow as regular non-fixed-price bookings

### 4. **30+ Keyword Mappings**
Pre-configured keywords for common services:

**Electrical:**
- wiring, switch, socket, fan, light, bulb, mcb, inverter, ups, stabilizer

**Appliances:**
- ac, fridge, washing machine, geyser, microwave, tv, chimney

**Plumbing:**
- tap, pipe, leak, drain, toilet, basin, tank

**Actions:**
- repair, install, replace, service, fix, broken

---

## 📊 Workflow Diagram

```
Customer Books "Other" Service
         ↓
Admin Adds Detailed Service Name
         ↓
System Extracts Keywords
         ↓
System Suggests Category
         ↓
System Finds Matching Technicians
         ↓
Admin Assigns Best Match
         ↓
Technician Completes Work
         ↓
Technician Sets Final Price
         ↓
Payment Collected
```

---

## 🎨 User Interface

### All Bookings Page
- Custom bookings show **yellow edit icon** (⚠️)
- Click to add detailed service name
- After editing, shows **green assign icon** (✓)

### Edit Custom Booking Page
- Clean, modern interface
- Shows customer info
- Shows original custom service request
- Input for detailed service name
- Smart matching info displayed
- Shows extracted keywords after update

### Assign Technician Page
- **Matched technicians highlighted** at top
- Shows skill match score
- Shows available capacity
- Groups by availability
- Can still see all technicians if needed

---

## 💡 Best Practices

### For Admin:

1. **Be Specific in Service Name**
   - ❌ Bad: "Fan problem"
   - ✅ Good: "Ceiling Fan Regulator Repair and Speed Control Fix"

2. **Include Key Details**
   - Type of appliance/fixture
   - What's wrong (repair/install/replace)
   - Location if relevant

3. **Review Suggested Keywords**
   - Check if extracted keywords make sense
   - Add more details if keywords are missing

4. **Choose Best Matched Technician**
   - Prioritize those with exact skill match
   - Check availability and experience
   - Consider current workload

### For Technicians:

1. **Review Service Details Carefully**
   - Read detailed service name
   - Check description
   - View any uploaded images

2. **Set Fair Price**
   - Calculate parts cost
   - Add labor charges
   - Consider complexity
   - Be transparent with customer

3. **Document Work**
   - Take before/after photos
   - Upload clear bill/receipt
   - Add completion notes

---

## 🔧 Troubleshooting

### No Keywords Extracted
**Problem:** System shows no keywords
**Solution:** 
- Add more descriptive words in service name
- Include action words (repair, install, fix)
- Mention specific items (fan, ac, tap)

### No Matching Technicians
**Problem:** No technicians shown
**Solution:**
- Check if technicians have required skills added
- Verify technician availability
- May need to add skills to technician profiles

### Wrong Category Suggested
**Problem:** System suggests wrong category
**Solution:**
- Manually assign correct technician
- Keywords may be ambiguous
- Admin can override suggestion

---

## 📈 Future Enhancements

### Planned Features:
1. **Image Analysis** - Extract service details from uploaded photos
2. **AI-Powered Matching** - Machine learning for better technician selection
3. **Price Estimation** - Suggest price range based on similar bookings
4. **Customer Feedback Loop** - Learn from successful matches

---

## 📞 Support

### Common Questions:

**Q: Can I edit a custom booking after assigning technician?**
A: Yes, but you'll need to reassign the technician after editing.

**Q: What if customer doesn't provide enough details?**
A: Admin can call customer and add detailed service name based on conversation.

**Q: Can technician reject custom booking?**
A: Yes, same as regular bookings. Admin can then reassign to another technician.

**Q: How is price determined for custom bookings?**
A: Technician sets final price during service completion based on actual work done.

---

## ✅ Checklist for Custom Bookings

- [ ] Customer books "Other" service with description
- [ ] Admin reviews booking in All Bookings page
- [ ] Admin clicks edit icon (yellow) for custom booking
- [ ] Admin adds detailed service name
- [ ] System extracts keywords automatically
- [ ] Admin proceeds to assign technician
- [ ] System shows matched technicians first
- [ ] Admin assigns best match
- [ ] Technician completes work
- [ ] Technician sets final price (₹0 → actual amount)
- [ ] Payment collected
- [ ] Booking marked complete

---

## 🎓 Training Tips

### For New Admins:
1. Practice with test bookings
2. Learn common keywords
3. Understand technician skills
4. Review successful matches

### For Technicians:
1. Understand non-fixed-price flow
2. Learn to estimate costs accurately
3. Document work properly
4. Communicate with customers

---

**System Version:** 2.0  
**Last Updated:** November 2025  
**Status:** ✅ Production Ready
