# Admin Quick Reference: One-Booking-Per-Technician System

## 🎯 Core Rule

**A technician can only handle ONE booking at a time.**

---

## ✅ What This Means

### When Assigning Bookings:

- ✅ You can only assign **available** technicians
- ❌ You **cannot** assign technicians who are already working on another booking
- ✅ System automatically shows only available technicians in dropdown
- ✅ If you try to assign an engaged technician, you'll see an error message

### When Technicians Complete Work:

- ✅ Technician marks booking as "Done" → **Automatically becomes available**
- ✅ Technician marks booking as "Not Done" → **Automatically becomes available**
- ✅ You can immediately assign them to a new booking

---

## 📊 Monitoring Dashboard

**URL:** `admin/test-technician-engagement.php`

### What You'll See:

| Column | Meaning |
|--------|---------|
| **Status** | Available (green) or Booked (red) |
| **Engagement** | Free (can take bookings) or Engaged (working) |
| **Current Booking** | Which booking they're working on (if any) |

### Quick Stats:

- **Total Technicians:** All registered technicians
- **Available:** Ready for new bookings
- **Engaged:** Currently working on bookings

---

## 🔄 Common Scenarios

### Scenario 1: Assigning a New Booking

1. Go to pending booking
2. Click "Assign Technician"
3. Select from **available technicians only**
4. Set deadline and status
5. Click "Assign Technician"
6. ✅ Done! Technician is now engaged

### Scenario 2: Technician Not Responding

**Problem:** Technician assigned but not responding

**Solution:**
1. Go to the booking
2. Check "Allow Technician Change" checkbox
3. Select a different **available** technician
4. Click "Change Technician"
5. ✅ Old technician freed up, new technician engaged

### Scenario 3: Reassigning Rejected Booking

**Problem:** Technician rejected a booking

**What Happens:**
1. Technician marks as "Not Done" with reason
2. System **automatically frees up** the technician
3. You receive notification
4. Go to booking and click "Reassign"
5. Select from **available technicians** (including the one who rejected, if needed)
6. ✅ New technician engaged

### Scenario 4: No Available Technicians

**Problem:** All technicians are engaged

**Options:**
1. **Wait:** Check dashboard to see who's close to completing
2. **Add Technicians:** Hire more technicians for that category
3. **Prioritize:** Contact engaged technicians to prioritize urgent bookings

---

## ⚠️ Error Messages

### "Technician is currently engaged with Booking #123"

**Meaning:** This technician is already working on another booking

**What to Do:**
- Choose a different available technician, OR
- Wait for them to complete current booking, OR
- Check dashboard to see their current booking status

### "No available technicians for this category"

**Meaning:** All technicians in this category are engaged

**What to Do:**
- Wait for technicians to complete current bookings
- Add more technicians for this category
- Check if technicians are properly categorized

---

## 🔍 Quick Checks

### Is a Technician Available?

1. Go to `test-technician-engagement.php`
2. Find the technician in the list
3. Check "Engagement" column:
   - **Free** = Available for new bookings ✅
   - **Engaged** = Working on a booking ❌

### Why Can't I Assign This Technician?

**Possible Reasons:**
1. ❌ Technician is engaged with another booking
2. ❌ Technician category doesn't match service category
3. ❌ Technician status is not "Available"

**Check:**
- Visit monitoring dashboard
- Look at their current booking
- Verify their category matches the service

### How to Free Up a Stuck Technician?

**If technician shows as "Engaged" but has no active booking:**

1. Contact your developer/database admin
2. They will run this query:
```sql
UPDATE tms_technician 
SET t_status = 'Available', 
    t_is_available = 1, 
    t_current_booking_id = NULL 
WHERE t_id = [technician_id];
```

---

## 📋 Daily Workflow

### Morning Routine:

1. ✅ Check monitoring dashboard
2. ✅ See how many technicians are available
3. ✅ Review pending bookings
4. ✅ Assign available technicians to urgent bookings

### During the Day:

1. ✅ Monitor technician engagement
2. ✅ Handle rejected bookings promptly
3. ✅ Reassign as needed
4. ✅ Respond to technician issues

### End of Day:

1. ✅ Check if any technicians are stuck as "Engaged"
2. ✅ Review completed bookings
3. ✅ Plan next day's assignments

---

## 💡 Best Practices

### DO:

- ✅ Check monitoring dashboard regularly
- ✅ Assign bookings to available technicians only
- ✅ Handle rejected bookings quickly
- ✅ Distribute work fairly among technicians
- ✅ Set realistic deadlines

### DON'T:

- ❌ Try to force-assign engaged technicians
- ❌ Ignore rejected bookings
- ❌ Overload specific technicians
- ❌ Assign without checking availability
- ❌ Change technicians unnecessarily

---

## 🆘 Quick Help

### Need to See All Bookings?
→ `admin-all-bookings.php`

### Need to See Technician Status?
→ `test-technician-engagement.php`

### Need to Assign a Booking?
→ `admin-assign-technician.php?sb_id=[booking_id]`

### Need to View Booking Details?
→ `admin-view-single-booking.php?sb_id=[booking_id]`

---

## 📞 Support

### Common Questions:

**Q: Can a technician work on multiple bookings?**  
A: No, only ONE booking at a time.

**Q: What if all technicians are busy?**  
A: Wait for completions or add more technicians.

**Q: Can I override the system?**  
A: No, this ensures quality service and prevents overload.

**Q: How do I know when a technician is free?**  
A: Check the monitoring dashboard or they'll appear in assignment dropdown.

**Q: What happens if I change a technician?**  
A: Old technician is freed up, new technician becomes engaged.

---

## 🎓 Training Checklist

For new admin staff:

- [ ] Understand the one-booking rule
- [ ] Know how to access monitoring dashboard
- [ ] Practice assigning bookings
- [ ] Practice reassigning rejected bookings
- [ ] Practice changing technicians
- [ ] Know how to interpret error messages
- [ ] Understand technician availability states
- [ ] Know when to escalate issues

---

## 📈 Key Metrics to Track

- **Average Engagement Time:** How long technicians stay engaged
- **Completion Rate:** % of bookings completed vs rejected
- **Technician Utilization:** % of time technicians are engaged
- **Reassignment Rate:** How often bookings need reassignment
- **Response Time:** How quickly technicians complete bookings

---

## ✨ Remember

The system is designed to:
- ✅ Ensure quality service (one booking at a time)
- ✅ Prevent technician overload
- ✅ Maintain fair work distribution
- ✅ Provide clear visibility of availability
- ✅ Automate status management

**Trust the system - it's working to make your job easier!** 🎉
