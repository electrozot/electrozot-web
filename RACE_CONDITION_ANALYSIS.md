# Race Condition Analysis: Same Service Assignment

## 🚨 RACE CONDITION IDENTIFIED

### Scenario: Multiple Admins Assigning Same Service Simultaneously

**Problem**: When multiple bookings for the same specific service (e.g., "AC (Split) - Repair") are assigned at the same time, the system can assign the same technician to multiple bookings, exceeding their capacity.

---

## The Race Condition

### Timeline Example

```
Time    Admin A                          Admin B                          Database
----    -------------------------------- -------------------------------- -----------------
T1      Opens booking #101               Opens booking #102               Tech1: 2/3 slots
        Service: AC (Split) - Repair     Service: AC (Split) - Repair     
        
T2      Queries available techs          Queries available techs          Tech1: 2/3 slots
        Sees: Tech1 (1 slot free)        Sees: Tech1 (1 slot free)        
        
T3      Selects Tech1                    Selects Tech1                    Tech1: 2/3 slots
        
T4      Starts transaction               Starts transaction               Tech1: 2/3 slots
        Locks Tech1 row ✅               Waits for lock...                
        
T5      Checks: 2 < 3 ✅                 Still waiting...                 Tech1: 2/3 slots
        
T6      Assigns booking #101             Still waiting...                 Tech1: 3/3 slots
        Increments counter: 3/3          
        
T7      Commits transaction              Gets lock                        Tech1: 3/3 slots
        Releases lock                    
        
T8                                       Checks: 3 < 3 ❌                Tech1: 3/3 slots
                                         SHOULD FAIL!                     
                                         
T9                                       Transaction rolls back ✅        Tech1: 3/3 slots
                                         Shows error to Admin B           
```

**Current Status**: ✅ **PROTECTED** by transaction + row locking in `admin-assign-technician.php`

---

## However, There's ANOTHER Race Condition!

### Scenario: Technician List Display vs Assignment

**Problem**: The technician list is fetched BEFORE the transaction starts, so it can show outdated availability.

### Timeline Example

```
Time    Admin A                          Admin B                          Database
----    -------------------------------- -------------------------------- -----------------
T1      Opens booking #101               Opens booking #102               Tech1: 2/3 slots
        Fetches available techs          Fetches available techs          
        Sees: Tech1 (1 slot free) ✅     Sees: Tech1 (1 slot free) ✅     
        
T2      Selects Tech1                    Selects Tech1                    Tech1: 2/3 slots
        Clicks "Assign"                  Clicks "Assign"                  
        
T3      Transaction starts               Transaction starts               Tech1: 2/3 slots
        Locks Tech1 ✅                   Waits...                         
        
T4      Assigns successfully             Still waiting...                 Tech1: 3/3 slots
        Counter: 3/3                     
        
T5      Commits & releases lock          Gets lock                        Tech1: 3/3 slots
        
T6                                       Checks: 3 < 3 ❌                Tech1: 3/3 slots
                                         FAILS! ✅                        
                                         Shows error                      
```

**Result**: Admin B sees an error AFTER clicking assign, even though Tech1 was shown as available.

**User Experience**: ❌ Confusing - "Why did it show available if it's not?"

---

## The Real Issue: Time-of-Check vs Time-of-Use (TOCTOU)

### Current Flow
```
1. Fetch available technicians (CHECK)
   ↓ [Time gap - other admins can assign]
2. Admin selects technician
   ↓ [Time gap - other admins can assign]
3. Admin clicks "Assign"
   ↓ [Time gap - other admins can assign]
4. Transaction starts (USE)
5. Check availability again ✅
6. Assign or fail
```

**Gap**: Between step 1 (CHECK) and step 4 (USE), availability can change.

---

## Current Protection (What We Have)

### ✅ Protected by Transaction + Row Locking

**File**: `admin/admin-assign-technician.php` (lines 33-60)

```php
// START TRANSACTION to prevent race conditions
$mysqli->begin_transaction();

try {
    // Lock technician row (prevents concurrent assignments)
    $check_tech_query = "SELECT t_id, t_name, t_current_bookings, t_booking_limit 
                        FROM tms_technician 
                        WHERE t_id = ? FOR UPDATE";  // ← Row lock here!
    
    // Check if technician has available slots
    if($tech_data->t_current_bookings >= $tech_data->t_booking_limit) {
        throw new Exception("Technician at capacity");
    }
    
    // Assign booking
    // Increment counter
    
    $mysqli->commit();
} catch(Exception $e) {
    $mysqli->rollback();
}
```

**Protection Level**: 🟢 **STRONG** - Prevents actual overbooking

---

## Remaining Issues

### Issue 1: Stale Technician List (UX Problem)

**Problem**: Dropdown shows outdated availability

**Example**:
```
Admin sees: "Tech1 (1 slot free)"
Admin clicks assign
Error: "Tech1 is at capacity (3/3)"
Admin confused: "But it said 1 slot free!"
```

**Severity**: 🟡 **MEDIUM** - Doesn't cause data corruption, but bad UX

---

### Issue 2: No Real-Time Updates

**Problem**: Multiple admins see same availability simultaneously

**Example**:
```
10 admins open assignment page at same time
All see: "Tech1 (3 slots free)"
All try to assign to Tech1
Only 3 succeed, 7 get errors
```

**Severity**: 🟡 **MEDIUM** - Wastes admin time

---

### Issue 3: Skill-Based Race Condition

**Problem**: Multiple bookings for same specific service compete for same specialist

**Example**:
```
Service: "AC (Split) - Repair"
Only 1 technician has this exact skill: Tech1
5 bookings for this service arrive
All 5 admins try to assign to Tech1
Only 3 succeed (Tech1's limit)
2 bookings stuck without specialist
```

**Severity**: 🟠 **HIGH** - Business impact (delayed service)

---

## Solutions

### Solution 1: Refresh Availability Before Assignment (Quick Fix)

**Add AJAX check before form submission**:

```javascript
// Before submitting assignment form
function validateTechnicianAvailability(techId) {
    return fetch(`check-tech-availability.php?tech_id=${techId}`)
        .then(r => r.json())
        .then(data => {
            if(!data.available) {
                alert(`Technician is now at capacity. Please select another.`);
                return false;
            }
            return true;
        });
}

// On form submit
$('#assignForm').submit(function(e) {
    e.preventDefault();
    const techId = $('#sb_technician_id').val();
    
    validateTechnicianAvailability(techId).then(isAvailable => {
        if(isAvailable) {
            this.submit();
        }
    });
});
```

**Benefit**: Catches stale data before submission
**Limitation**: Still a small race window

---

### Solution 2: Real-Time Availability Updates (Better)

**Use WebSocket or polling to update dropdown**:

```javascript
// Poll for availability updates every 5 seconds
setInterval(() => {
    const serviceId = $('#service_id').val();
    
    fetch(`get-available-techs.php?service_id=${serviceId}`)
        .then(r => r.json())
        .then(techs => {
            updateTechnicianDropdown(techs);
        });
}, 5000);
```

**Benefit**: Always shows current availability
**Limitation**: Adds server load

---

### Solution 3: Optimistic Locking (Advanced)

**Add version number to technician record**:

```sql
ALTER TABLE tms_technician ADD COLUMN t_version INT DEFAULT 0;

-- On assignment
UPDATE tms_technician 
SET t_current_bookings = t_current_bookings + 1,
    t_version = t_version + 1
WHERE t_id = ? 
AND t_version = ?  -- Must match expected version
AND t_current_bookings < t_booking_limit;

-- If affected_rows = 0, someone else modified it
```

**Benefit**: Detects concurrent modifications
**Limitation**: Requires schema change

---

### Solution 4: Queue System (Best for High Load)

**Implement booking queue**:

```
1. Admin submits assignment request → Queue
2. Background worker processes queue (one at a time)
3. Worker assigns technician with proper locking
4. Admin gets notification when done
```

**Benefit**: No race conditions possible
**Limitation**: Async (not immediate)

---

## Recommended Immediate Fix

### Add Pre-Submit Availability Check

**Create**: `admin/check-tech-availability.php`

```php
<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

header('Content-Type: application/json');

$tech_id = isset($_GET['tech_id']) ? intval($_GET['tech_id']) : 0;

if($tech_id <= 0) {
    echo json_encode(['available' => false, 'message' => 'Invalid technician']);
    exit;
}

// Check current availability with row lock
$mysqli->begin_transaction();

$query = "SELECT t_id, t_name, t_current_bookings, t_booking_limit 
          FROM tms_technician 
          WHERE t_id = ? FOR UPDATE";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $tech_id);
$stmt->execute();
$result = $stmt->get_result();
$tech = $result->fetch_object();

$mysqli->commit();

if(!$tech) {
    echo json_encode(['available' => false, 'message' => 'Technician not found']);
    exit;
}

$available = $tech->t_current_bookings < $tech->t_booking_limit;
$slots = $tech->t_booking_limit - $tech->t_current_bookings;

echo json_encode([
    'available' => $available,
    'current_bookings' => $tech->t_current_bookings,
    'booking_limit' => $tech->t_booking_limit,
    'available_slots' => $slots,
    'message' => $available 
        ? "{$tech->t_name} has {$slots} slot(s) available" 
        : "{$tech->t_name} is at capacity ({$tech->t_current_bookings}/{$tech->t_booking_limit})"
]);
?>
```

**Add to assignment page**:

```javascript
$('#assignForm').submit(function(e) {
    e.preventDefault();
    const form = this;
    const techId = $('#sb_technician_id').val();
    const submitBtn = $(this).find('button[type="submit"]');
    
    // Disable button
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking...');
    
    // Check availability
    $.get('check-tech-availability.php', {tech_id: techId}, function(data) {
        if(data.available) {
            // Still available, submit form
            form.submit();
        } else {
            // No longer available
            alert('⚠️ ' + data.message + '\n\nPlease select another technician.');
            submitBtn.prop('disabled', false).html('<i class="fas fa-check"></i> Assign Technician');
            
            // Optionally refresh technician list
            location.reload();
        }
    }).fail(function() {
        alert('Error checking availability. Please try again.');
        submitBtn.prop('disabled', false).html('<i class="fas fa-check"></i> Assign Technician');
    });
    
    return false;
});
```

---

## Testing Race Conditions

### Test 1: Concurrent Assignment
```bash
# Terminal 1
curl -X POST http://localhost/admin/admin-assign-technician.php \
  -d "sb_id=101&sb_technician_id=1&..."

# Terminal 2 (simultaneously)
curl -X POST http://localhost/admin/admin-assign-technician.php \
  -d "sb_id=102&sb_technician_id=1&..."
```

**Expected**: One succeeds, one fails with "at capacity" error

---

### Test 2: Stale UI
```
1. Admin A opens assignment page
2. Admin B assigns last slot to Tech1
3. Admin A (still sees old data) tries to assign to Tech1
4. Expected: Error message
```

---

## Summary

| Issue | Severity | Protected? | Solution |
|-------|----------|------------|----------|
| Concurrent assignment | 🔴 Critical | ✅ Yes (transaction + lock) | Already fixed |
| Stale technician list | 🟡 Medium | ❌ No | Add pre-submit check |
| No real-time updates | 🟡 Medium | ❌ No | Add polling/WebSocket |
| Skill-based competition | 🟠 High | ✅ Partially | Add queue system |

**Current Status**: 
- ✅ Data integrity protected (no overbooking)
- ❌ User experience needs improvement (stale data)

**Recommended Action**: 
1. Implement pre-submit availability check (quick win)
2. Consider real-time updates for high-traffic scenarios
3. Monitor for skill-based bottlenecks

---

**Conclusion**: The critical race condition (overbooking) is already protected by transactions and row locking. The remaining issue is UX-related (stale data), which can be fixed with a pre-submit availability check.
