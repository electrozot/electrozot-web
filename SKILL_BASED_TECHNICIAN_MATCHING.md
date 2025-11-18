# Skill-Based Technician Matching System

## Overview
Technicians are now matched to bookings based on their detailed service skills, providing more accurate assignments.

## Matching Priority

### Priority 1: Detailed Service Skills (HIGHEST)
- Matches technicians who have the specific skill in `tms_technician_skills` table
- Example: Booking for "AC Repair" → Shows technicians with "Air Conditioner (AC) Repair" skill
- Marked as: **✓ SKILL MATCH**

### Priority 2: Service Category
- If no skill matches, falls back to category matching
- Example: Booking category "ELECTRONIC REPAIR" → Shows technicians with that category
- Marked as: **Category Match**

### Priority 3: Specialization
- Matches by technician's specialization field
- Partial text matching

## How It Works

### 1. Skill Matching Query
```sql
SELECT DISTINCT t.*, GROUP_CONCAT(ts.skill_name) as skills
FROM tms_technician t
INNER JOIN tms_technician_skills ts ON t.t_id = ts.t_id
WHERE ts.skill_name LIKE '%service_name%'
GROUP BY t.t_id
```

### 2. Category Matching (Fallback)
```sql
SELECT * FROM tms_technician
WHERE t_category = 'category' 
OR t_category LIKE '%category%'
OR t_specialization LIKE '%category%'
```

## Display Format

### Skill Match (Priority 1)
```
John Doe - Electrical Specialist ✓ SKILL MATCH | Skills: AC Repair, Refrigerator Repair...
```

### Category Match (Priority 2)
```
Jane Smith - Electronic Technician - Category Match
```

## Benefits

✅ **Accurate Matching** - Right technician for the right job
✅ **Skill-Based** - Uses detailed skills from approval/add process
✅ **Fallback System** - Still shows technicians if no exact skill match
✅ **Visual Indicators** - Clear labels show match type
✅ **Availability Check** - Only shows available (not engaged) technicians

## Example Scenarios

### Scenario 1: AC Repair Booking
**Service:** Air Conditioner Repair
**Skill Match:** Technicians with "Air Conditioner (AC) Repair" skill
**Result:** Shows 3 technicians with ✓ SKILL MATCH

### Scenario 2: Custom Service
**Service:** Smart Home Setup
**Skill Match:** Technicians with "Smart Home Device Setup" skill
**Fallback:** If no skill match, shows "INSTALLATION & SETUP" category technicians

### Scenario 3: No Matches
**Service:** Rare service
**Skill Match:** None
**Category Match:** None
**Result:** Shows message "No available technicians"

## Integration Points

1. **Admin Assign Technician** - Uses skill matching
2. **Auto-Assignment** - Can use skill matching for automatic assignment
3. **Technician Dashboard** - Shows relevant bookings based on skills

## Database Structure

### tms_technician_skills
```sql
ts_id INT PRIMARY KEY
t_id INT (technician ID)
skill_name VARCHAR(255)
created_at TIMESTAMP
```

### Skills Added During:
- Admin adds technician
- Admin approves guest technician

## Future Enhancements
- Skill level indicators (Beginner, Expert)
- Certification requirements
- Skill-based pricing
- Auto-suggest skills during booking
