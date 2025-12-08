# Design Document

## Overview

The technician rejection alert system is designed to notify administrators immediately when a technician rejects 3 or more bookings within a 7-day period. The system consists of a non-dismissible modal popup that appears on any admin page, requiring the administrator to take explicit action (lock account, block bookings, or dismiss with no action) before continuing their work.

The current implementation has all the necessary components in place but is experiencing issues where the popup is not appearing when technicians exceed the rejection threshold. This design document outlines the architecture, identifies potential failure points, and provides a comprehensive solution to ensure reliable alert delivery.

## Architecture

### System Components

The rejection alert system follows a client-server architecture with the following components:

1. **Frontend Modal Component** (`widget-rejection-alert-modal.php`)
   - Bootstrap modal with non-dismissible configuration
   - JavaScript polling mechanism that checks for alerts
   - Queue management for multiple flagged technicians
   - Action buttons for admin response

2. **Backend API Layer**
   - `api-check-rejection-threshold.php`: Queries database for technicians exceeding threshold
   - `api-take-rejection-action.php`: Processes admin actions and updates database

3. **Database Layer**
   - `tms_technician_rejections`: Stores rejection records with notification status
   - `tms_technician`: Stores technician status and blocking information
   - `tms_service_booking`: Provides customer contact information

4. **Integration Points**
   - Footer inclusion: Modal is included in all admin pages via `vendor/inc/footer.php`
   - Rejection recording: Technician rejection actions trigger database inserts
   - Auto-unblock cron: Scheduled job to remove expired blocks

### Data Flow

```
Technician Rejects Booking
    ↓
Record inserted into tms_technician_rejections (tr_admin_notified = 0)
    ↓
Admin loads any admin page
    ↓
Modal JavaScript executes immediately (0ms delay)
    ↓
AJAX call to api-check-rejection-threshold.php
    ↓
API queries for technicians with 3+ unnotified rejections in last 7 days
    ↓
If found: Return technician data + customer info
    ↓
Modal displays with pulsing border (non-dismissible)
    ↓
Admin takes action (Lock/Block/No Action)
    ↓
AJAX call to api-take-rejection-action.php
    ↓
Update database: tr_admin_notified = 1, apply penalties if selected
    ↓
Modal closes, check for next technician in queue
    ↓
Continue polling every 5 seconds
```

## Components and Interfaces

### Frontend Component: Rejection Alert Modal

**File**: `admin/widget-rejection-alert-modal.php`

**Responsibilities**:
- Display non-dismissible modal with urgent styling
- Poll API every 5 seconds for new alerts
- Execute immediate check on page load (no delay)
- Manage queue of multiple flagged technicians
- Handle admin action submissions
- Provide visual feedback (pulsing border animation)

**Key Configuration**:
```javascript
// Modal attributes
data-backdrop="static"  // Prevents closing by clicking outside
data-keyboard="false"   // Prevents closing with ESC key

// Timing
Immediate check: checkRejectionAlerts() on page load
Interval: setInterval(checkRejectionAlerts, 5000)
```

**Critical Requirements**:
- NO session storage filtering (removed to prevent alert suppression)
- NO delay before first check (immediate execution)
- Single setInterval instance (no duplicates)
- jQuery dependency check (wait for jQuery to load)

### Backend API: Check Rejection Threshold

**File**: `admin/api-check-rejection-threshold.php`

**Input**: None (uses session for authentication)

**Output**:
```json
{
  "success": true,
  "has_alerts": true,
  "technicians": [
    {
      "t_id": 123,
      "t_name": "John Doe",
      "t_phone": "1234567890",
      "t_email": "john@example.com",
      "rejection_count": 5,
      "rejection_list": ["Booking #456 - Reason (date)", ...],
      "customers": {
        "456": {"name": "Customer Name", "phone": "9876543210"}
      }
    }
  ],
  "threshold": 3
}
```

**Query Logic**:
```sql
SELECT technicians with:
- COUNT(rejections) >= 3
- tr_rejected_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
- tr_admin_notified = 0
GROUP BY technician
ORDER BY rejection_count DESC
```

### Backend API: Take Rejection Action

**File**: `admin/api-take-rejection-action.php`

**Input**:
```json
{
  "technician_id": 123,
  "action": "lock_account|block_bookings|no_action",
  "admin_notes": "Reason for action"
}
```

**Actions**:
1. **lock_account**: Set `t_status = 'Locked'`, `t_blocked_until = NOW() + 2 days`
2. **block_bookings**: Set `t_blocked_until = NOW() + 2 days` (status remains active)
3. **no_action**: No penalties applied

**Database Updates**:
- Mark rejections: `UPDATE tms_technician_rejections SET tr_admin_notified = 1`
- Reset counter: `UPDATE tms_technician SET t_rejection_count = 0`
- Log action: Insert into `tms_syslogs`

**Output**:
```json
{
  "success": true,
  "message": "Action completed successfully"
}
```

## Data Models

### tms_technician_rejections

Primary table for tracking rejection events.

```sql
CREATE TABLE tms_technician_rejections (
  tr_id INT PRIMARY KEY AUTO_INCREMENT,
  tr_technician_id INT NOT NULL,
  tr_booking_id INT NOT NULL,
  tr_reason VARCHAR(500),
  tr_rejected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  tr_admin_notified TINYINT(1) DEFAULT 0,
  tr_admin_action VARCHAR(50),
  tr_admin_action_at DATETIME,
  tr_admin_notes TEXT,
  FOREIGN KEY (tr_technician_id) REFERENCES tms_technician(t_id),
  FOREIGN KEY (tr_booking_id) REFERENCES tms_service_booking(sb_id),
  INDEX idx_tech_notified (tr_technician_id, tr_admin_notified),
  INDEX idx_rejected_at (tr_rejected_at)
)
```

**Key Fields**:
- `tr_admin_notified`: 0 = not yet alerted, 1 = admin has been notified
- `tr_admin_action`: Type of action taken (lock_account, block_bookings, no_action)
- `tr_admin_action_at`: Timestamp when admin took action
- `tr_admin_notes`: Admin's explanation for their decision

### tms_technician (relevant fields)

```sql
t_id INT PRIMARY KEY
t_name VARCHAR(100)
t_phone VARCHAR(20)
t_email VARCHAR(100)
t_status ENUM('Active', 'Locked', 'Inactive')
t_blocked_until DATETIME
t_block_reason VARCHAR(500)
t_rejection_count INT DEFAULT 0
```

**Blocking Logic**:
- `t_blocked_until`: Timestamp when block expires (NULL = not blocked)
- `t_status = 'Locked'`: Cannot login at all
- `t_blocked_until` set without status change: Can login but won't receive bookings

### tms_service_booking (relevant fields)

```sql
sb_id INT PRIMARY KEY
sb_user_id INT
sb_phone VARCHAR(20)
sb_status VARCHAR(50)
```

Used to retrieve customer contact information for rejected bookings.

## 
Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Threshold triggers alert display

*For any* technician with exactly 3 or more unnotified rejections within a 7-day window, when an admin loads any page, the alert popup should appear.

**Validates: Requirements 1.1**

### Property 2: Alert check executes immediately

*For any* admin page load, the rejection alert check should execute within 100 milliseconds of page load completion.

**Validates: Requirements 1.2**

### Property 3: Modal is non-dismissible

*For any* displayed rejection alert modal, the modal element should have `data-backdrop="static"` and `data-keyboard="false"` attributes, and should not contain a close button.

**Validates: Requirements 1.3**

### Property 4: Sequential alert display

*For any* set of multiple technicians exceeding the threshold, the system should display alerts one at a time in sequence, not simultaneously.

**Validates: Requirements 1.4**

### Property 5: Correct rejection filtering

*For any* database state containing rejections with various dates and notification statuses, the alert check should only count rejections where `tr_admin_notified = 0` AND `tr_rejected_at >= NOW() - 7 days`.

**Validates: Requirements 1.5**

### Property 6: Required technician information display

*For any* technician triggering an alert, the rendered modal HTML should contain the technician's name, phone number, and total rejection count.

**Validates: Requirements 2.1**

### Property 7: Customer information display

*For any* rejection shown in the alert, if customer information exists for that booking, the modal should display the customer's name and phone number.

**Validates: Requirements 2.2**

### Property 8: Rejection dates displayed

*For any* rejection shown in the alert, the modal should display the date and time when the rejection occurred.

**Validates: Requirements 2.4**

### Property 9: Lock account action updates database correctly

*For any* technician, when admin selects "Lock Account", the database should be updated with `t_status = 'Locked'` AND `t_blocked_until = NOW() + 2 days`.

**Validates: Requirements 3.1**

### Property 10: Block bookings action updates database correctly

*For any* technician, when admin selects "Block Bookings", the database should be updated with `t_blocked_until = NOW() + 2 days` while `t_status` remains unchanged.

**Validates: Requirements 3.2**

### Property 11: No action leaves penalties unchanged

*For any* technician, when admin selects "No Action", both `t_status` and `t_blocked_until` should remain unchanged from their values before the action.

**Validates: Requirements 3.3**

### Property 12: All actions mark rejections as notified

*For any* admin action (lock_account, block_bookings, or no_action), all rejection records for that technician should have `tr_admin_notified` updated to 1.

**Validates: Requirements 3.4**

### Property 13: All actions reset rejection counter

*For any* admin action (lock_account, block_bookings, or no_action), the technician's `t_rejection_count` should be reset to 0.

**Validates: Requirements 3.5**

### Property 14: Notes required for penalty actions

*For any* admin action of type "Lock Account" or "Block Bookings", if admin notes are empty, the system should reject the submission and display an error message.

**Validates: Requirements 3.6**

### Property 15: Successful action closes modal and shows next

*For any* successful admin action, the current modal should close, and if additional technicians are in the queue, the next technician's alert should appear within 1 second.

**Validates: Requirements 3.7**

### Property 16: New rejections trigger alerts within polling interval

*For any* new rejection that causes a technician to reach the threshold during an active admin session, the alert should appear within 5 seconds (the polling interval).

**Validates: Requirements 4.2**

### Property 17: API failures are logged and polling continues

*For any* API failure during alert checking, an error should be logged to the browser console AND the polling interval should continue executing.

**Validates: Requirements 4.3**

### Property 18: Idle state continues monitoring

*For any* alert check that returns no flagged technicians, no modal should be displayed AND the next check should occur after the 5-second interval.

**Validates: Requirements 4.4**

### Property 19: Rejection records are preserved

*For any* admin action on a rejection alert, the count of rejection records in `tms_technician_rejections` for that technician should remain the same before and after the action.

**Validates: Requirements 5.1**

### Property 20: Rejections updated not deleted

*For any* admin action, all rejection records for that technician should still exist in the database with `tr_admin_notified = 1`.

**Validates: Requirements 5.2**

### Property 21: Action metadata recorded

*For any* admin action, the rejection records should have `tr_admin_action` set to the action type and `tr_admin_action_at` set to a timestamp.

**Validates: Requirements 5.3**

### Property 22: Admin notes stored

*For any* admin action where notes are provided, the rejection records should have `tr_admin_notes` populated with the provided text.

**Validates: Requirements 5.4**

## Error Handling

### Frontend Error Handling

**AJAX Request Failures**:
- All AJAX calls include error handlers that log to console
- Failed API calls do not stop the polling mechanism
- User-friendly error messages for action submission failures

**jQuery Dependency**:
- Modal initialization waits for jQuery to load
- Recursive retry with 100ms delay if jQuery not available
- Prevents "undefined" errors on page load

**Modal Display Errors**:
- Validate response structure before attempting to display
- Gracefully handle missing customer data
- Default values for optional fields

### Backend Error Handling

**Database Connection Failures**:
- All queries use prepared statements to prevent SQL injection
- Transaction rollback on any error during action processing
- Error messages returned in JSON format

**Authentication Failures**:
- All API endpoints check for valid admin session
- Return 401-equivalent JSON response if not authenticated
- No sensitive data exposed in error messages

**Invalid Input Handling**:
- Validate all POST parameters before processing
- Check for required fields (technician_id, action)
- Sanitize admin notes before database insertion

**Race Conditions**:
- Use database transactions for multi-step updates
- Lock rows during action processing if necessary
- Handle case where technician is deleted during alert display

## Testing Strategy

### Unit Testing

Unit tests will verify specific examples and edge cases for individual components:

**Frontend Unit Tests**:
- Modal configuration attributes are correct
- Queue management adds and removes technicians correctly
- Action button click handlers call correct functions
- Empty notes validation works for penalty actions
- jQuery wait function retries correctly

**Backend Unit Tests**:
- API authentication check rejects unauthenticated requests
- Query correctly filters by date range (7 days)
- Query correctly filters by notification status
- Action processing updates correct database fields
- Transaction rollback works on errors

**Edge Cases**:
- Technician with exactly 3 rejections (boundary)
- Rejections exactly 7 days old (boundary)
- Empty customer data (missing foreign key)
- Concurrent admin actions on same technician
- Modal display with very long rejection reasons

### Property-Based Testing

Property-based tests will verify universal properties across many randomly generated inputs using **fast-check** (JavaScript) for frontend tests and **PHPUnit with property testing extensions** for backend tests.

**Configuration**:
- Minimum 100 iterations per property test
- Each test tagged with format: `**Feature: technician-rejection-alert-fix, Property {number}: {property_text}**`
- One property-based test per correctness property

**Frontend Property Tests**:

1. **Threshold Detection Property** (Property 1)
   - Generate: Random technician with random number of rejections (0-10) within random dates
   - Test: Alert should appear if and only if unnotified count >= 3 within 7 days

2. **Sequential Display Property** (Property 4)
   - Generate: Random list of 2-5 technicians above threshold
   - Test: Only one modal visible at a time, queue length decreases by 1 after each action

3. **Modal Non-Dismissibility Property** (Property 3)
   - Generate: Random alert data
   - Test: Modal element always has static backdrop and keyboard disabled

4. **Required Information Display Property** (Property 6)
   - Generate: Random technician data
   - Test: Rendered HTML contains name, phone, and count for all generated technicians

**Backend Property Tests**:

1. **Filtering Property** (Property 5)
   - Generate: Random set of rejection records with various dates and notification statuses
   - Test: Query returns only records matching both conditions (unnotified AND within 7 days)

2. **Lock Action Property** (Property 9)
   - Generate: Random technician with random initial status
   - Test: After lock action, status is 'Locked' and blocked_until is ~2 days from now

3. **Block Action Property** (Property 10)
   - Generate: Random technician with random initial status
   - Test: After block action, blocked_until is set but status unchanged

4. **No Action Property** (Property 11)
   - Generate: Random technician with random initial status and blocked_until
   - Test: After no action, both fields remain exactly as they were

5. **Notification Marking Property** (Property 12)
   - Generate: Random technician with random number of unnotified rejections
   - Test: After any action, all rejections have tr_admin_notified = 1

6. **Counter Reset Property** (Property 13)
   - Generate: Random technician with random rejection count
   - Test: After any action, t_rejection_count = 0

7. **Record Preservation Property** (Property 19)
   - Generate: Random technician with random number of rejections
   - Test: Count of rejection records before action = count after action

8. **Notes Validation Property** (Property 14)
   - Generate: Random penalty action (lock or block) with empty notes
   - Test: Action should be rejected with error message

**Integration Tests**:

While not required for core functionality, integration tests can verify end-to-end workflows:

- Complete flow: rejection → alert → action → database update
- Multiple technician queue processing
- Polling mechanism with simulated time advancement
- Cron job execution and auto-unblock

### Test Utilities

**Test Data Generators**:
- `generateTechnician()`: Random technician with valid fields
- `generateRejection()`: Random rejection with configurable date and notification status
- `generateBooking()`: Random booking with customer data
- `generateDateWithinDays(days)`: Random date within specified range

**Test Helpers**:
- `createTestRejections(technicianId, count, daysAgo, notified)`: Insert test data
- `cleanupTestData()`: Remove all test records
- `simulatePageLoad()`: Trigger modal initialization
- `simulateAdminAction(action, notes)`: Submit action form

**Mocking Strategy**:
- Mock AJAX calls for frontend tests (use sinon.js or similar)
- Mock database for unit tests (use in-memory SQLite or test database)
- Use real database for property tests (with cleanup)
- Mock time for date-sensitive tests (use Sinon fake timers)

### Test Execution

**Running Tests**:
```bash
# Frontend tests
npm test

# Backend tests
vendor/bin/phpunit tests/

# Property tests only
npm test -- --grep "Property"
vendor/bin/phpunit --group property

# Integration tests
npm test -- --grep "Integration"
vendor/bin/phpunit --group integration
```

**Continuous Integration**:
- Run all tests on every commit
- Fail build if any property test fails
- Generate coverage report (target: >80% for critical paths)

**Manual Testing**:
- Use `admin/test-rejection-alert.php` for quick manual verification
- Use `admin/comprehensive-rejection-test.php` for full system check
- Test in multiple browsers (Chrome, Firefox, Safari, Edge)

## Potential Failure Points and Solutions

### Issue 1: Modal Not Included in Footer

**Symptom**: Alert never appears on any page

**Root Cause**: `widget-rejection-alert-modal.php` not included in `vendor/inc/footer.php`

**Solution**: Verify inclusion with:
```php
// In vendor/inc/footer.php
<?php include('widget-rejection-alert-modal.php'); ?>
```

**Detection**: Test #5 in comprehensive test suite

### Issue 2: jQuery Not Loaded

**Symptom**: Console error "$ is not defined" or "jQuery is not defined"

**Root Cause**: Modal JavaScript executes before jQuery loads

**Solution**: Already implemented - modal waits for jQuery:
```javascript
(function initRejectionAlerts() {
    if (typeof jQuery === 'undefined') {
        setTimeout(initRejectionAlerts, 100);
        return;
    }
    // ... rest of code
})();
```

**Detection**: Test #15 in comprehensive test suite

### Issue 3: Session Storage Blocking

**Symptom**: Alert appears once then never again, even with new rejections

**Root Cause**: Previous implementation used session storage to track shown alerts

**Solution**: Removed all session storage checks - alert will show until admin takes action

**Detection**: Test #8 in comprehensive test suite

### Issue 4: Database Table Missing

**Symptom**: API returns errors, no alerts ever appear

**Root Cause**: `tms_technician_rejections` table not created

**Solution**: Run `admin/setup-rejection-tracking.php` to create table

**Detection**: Test #1 in comprehensive test suite

### Issue 5: Rejection Not Being Recorded

**Symptom**: Technicians reject bookings but no records in database

**Root Cause**: Rejection recording code not implemented in technician booking completion flow

**Solution**: Add INSERT statement when technician marks booking as "not done":
```php
$stmt = $mysqli->prepare("INSERT INTO tms_technician_rejections 
    (tr_technician_id, tr_booking_id, tr_reason, tr_admin_notified) 
    VALUES (?, ?, ?, 0)");
```

**Detection**: Test #10 in comprehensive test suite

### Issue 6: API Authentication Failure

**Symptom**: API returns "Not authenticated" even when logged in

**Root Cause**: Session not started in API files or session expired

**Solution**: Ensure `session_start()` at top of all API files

**Detection**: Manual testing or API response validation

### Issue 7: Multiple Intervals Running

**Symptom**: Alert check happens too frequently, multiple modals appear

**Root Cause**: Modal included multiple times or setInterval called multiple times

**Solution**: Ensure modal included only once in footer, single setInterval call

**Detection**: Test #14 in comprehensive test suite

### Issue 8: Date Range Calculation Error

**Symptom**: Old rejections trigger alerts or recent rejections don't

**Root Cause**: Incorrect SQL date calculation

**Solution**: Use `DATE_SUB(NOW(), INTERVAL 7 DAY)` consistently

**Detection**: Property test for filtering (Property 5)

### Issue 9: Transaction Rollback Failure

**Symptom**: Partial updates when action fails (e.g., technician locked but rejections not marked)

**Root Cause**: Not using database transactions

**Solution**: Wrap all action updates in transaction:
```php
$mysqli->begin_transaction();
try {
    // ... updates
    $mysqli->commit();
} catch (Exception $e) {
    $mysqli->rollback();
}
```

**Detection**: Integration tests with simulated failures

### Issue 10: Race Condition on Concurrent Actions

**Symptom**:
}.. rest o  // .    }
     return;
100);
     ionAlerts, tRejectnisetTimeout(i
        ed') {ndefiny === 'uf jQuerpeoty
    if ( {()tserRejectionAl initonunctivascript
(f```jar jQuery:
waits fol daed - mo implementlreadylution**: A*Soy loads

*jQuerfore ecutes beaScript ex Jav*: Modalt Cause*
**Roo"
eddefin not isery jQu" or "defined"$ is not r  erro**: Console**Symptomaded

ery Not Lo: jQussue 2
### Itest suite
e rehensiv in compt #5*: Tesection**Det

*
``` ?>.php');modalt-ion-aler-reject('widget includehpter.php
<?pr/inc/foo In vendop
//th:
```phwiinclusion ify Verlution**: So`

**er.phpr/inc/foot in `vendo includedl.php` not-modaertalection-et-rejwidg: `oot Cause**page

**R any  appears onAlert never*Symptom**: Footer

*Included in dal Not sue 1: Mo## Istions

#nd Solue Points atial Failur## Poten, Edge)

 Safari, Firefox,ers (Chrometiple browsTest in mulm check
- yste for full son-test.php`sive-rejectien/comprehUse `admintion
- l verificaquick manuaphp` for lert.tion-aest-rejece `admin/tUs- g**:
 TestinManual
**
aths)ical pr critget: >80% foport (tar coverage reGenerate- est fails
ty troperd if any pil
- Fail buvery commit on ell tests- Run aion**:
ntegratinuous I
**Cont`
on
``atip integrnit --groubin/phpundor/
veion"atp "Integr- --gret -
npm testson tes Integratiperty

#pro --group unitr/bin/phpy"
vendop "Propertre- --g
npm test - tests onlyoperty
# Prts/
/phpunit tesr/binvendotests
 Backend test

#tests
npm rontend sh
# F:
```baning Tests**
**Runon
est Executirs)

### Tfake timeinon ests (use S-sensitive tr dateme fo)
- Mock tileanupwith certy tests ( for propbaseeal data)
- Use rest database tite oremory SQL