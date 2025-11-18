# Guest Technician Approval - Skills Selection Feature

## Overview
Admin can now select detailed service skills for guest technicians during the approval process, just like when adding a regular technician.

## Features Added

### 1. Collapsible Skills Section
- **Button to expand/collapse** - Keeps the form clean and organized
- **Optional selection** - Admin can choose to add skills or skip
- **Categorized skills** - Organized by service categories

### 2. Service Categories Included

#### 🔌 BASIC ELECTRICAL WORK
- Home Wiring
- Switch/Socket Installation
- Light Fixture Installation
- Circuit Breaker troubleshooting
- Inverter/UPS installation
- Electrical fault finding

#### 🔧 ELECTRONIC REPAIR
- AC Repair
- Refrigerator Repair
- Washing Machine Repair
- Microwave Oven Repair
- Geyser Repair
- Fan Repair

#### ⚙️ INSTALLATION & SETUP
- CCTV Camera Installation
- WiFi Router Setup
- Smart Home Device Setup
- TV Wall Mounting
- Appliance Installation

#### 🛠️ SERVICING & MAINTENANCE
- AC Servicing & Cleaning
- Chimney Cleaning
- Water Purifier Servicing
- Geyser Servicing

#### 🚰 PLUMBING WORK
- Tap/Faucet Repair
- Toilet Repair
- Washbasin Installation
- Pipe Leakage Repair
- Drainage Cleaning

## How It Works

### Admin Approval Process

1. **View Guest Application**
   - See all guest technician details
   - Phone, Email, Aadhaar, Experience, Skills

2. **Fill Approval Form**
   - Generate EZ ID (auto-generate button)
   - Select Service Category (required)
   - Set Booking Limit (1-5 bookings)
   - Enter Specialization (optional)

3. **Select Detailed Skills (Optional)**
   - Click "Select Detailed Service Skills" button
   - Form expands to show all skill categories
   - Check all applicable skills
   - Skills are color-coded by category

4. **Approve**
   - Enter admin password
   - Click "Approve & Make EZ Technician"
   - Skills are saved to database
   - Success message shows skill count

### Database Storage

Skills are stored in the `tms_technician_skills` table:
```sql
CREATE TABLE tms_technician_skills (
    ts_id INT AUTO_INCREMENT PRIMARY KEY,
    t_id INT NOT NULL,
    skill_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tech_skill (t_id, skill_name)
)
```

### Success Messages

**With Skills:**
```
Guest technician approved successfully with 8 skills! Now a regular EZ Technician.
```

**Without Skills:**
```
Guest technician approved successfully! Now a regular EZ Technician.
```

## UI/UX Features

### Collapsible Design
- Skills section is hidden by default
- Click button to expand
- Keeps form clean and manageable
- Easy to navigate

### Color-Coded Categories
- **Blue** - Basic Electrical Work
- **Green** - Electronic Repair
- **Yellow** - Installation & Setup
- **Cyan** - Servicing & Maintenance
- **Red** - Plumbing Work

### Unique IDs
- Each checkbox has unique ID per guest
- Prevents conflicts when multiple guests are shown
- Format: `skill_X_GUEST_ID`

## Benefits

✅ **Comprehensive Skill Tracking** - Know exactly what each technician can do
✅ **Better Job Assignment** - Match technicians to jobs based on skills
✅ **Professional Management** - Detailed technician profiles
✅ **Optional Feature** - Admin can skip if not needed
✅ **Clean UI** - Collapsible design keeps form organized
✅ **Consistent Experience** - Same as adding regular technician

## Technical Implementation

### Form Structure
```html
<button data-toggle="collapse" data-target="#skills_GUEST_ID">
    Select Detailed Service Skills
</button>

<div class="collapse" id="skills_GUEST_ID">
    <!-- Skills checkboxes -->
    <input type="checkbox" name="tech_skills[]" value="Skill Name">
</div>
```

### PHP Processing
```php
if(isset($_POST['tech_skills']) && is_array($_POST['tech_skills'])) {
    foreach($_POST['tech_skills'] as $skill) {
        // Insert into tms_technician_skills
    }
}
```

## Testing Checklist

- [ ] Expand skills section - should show all categories
- [ ] Select multiple skills - checkboxes work
- [ ] Approve without skills - should work fine
- [ ] Approve with skills - should save to database
- [ ] Success message shows skill count
- [ ] Multiple guests on same page - unique IDs work
- [ ] Form resubmission - should not occur (PRG pattern)

## Future Enhancements

- Auto-suggest skills based on guest's entered skills text
- Skill level indicators (Beginner, Intermediate, Expert)
- Certification upload for specific skills
- Skill-based search and filtering
