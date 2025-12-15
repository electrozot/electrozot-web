# Implementation Plan

- [x] 1. Verify and set up database infrastructure


  - Verify `tms_technician_rejections` table exists with correct schema
  - Run `admin/setup-rejection-tracking.php` if table is missing
  - Verify indexes on `tr_technician_id`, `tr_admin_notified`, and `tr_rejected_at`
  - _Requirements: 5.1, 5.2_



- [ ] 2. Implement rejection recording mechanism
  - Locate technician booking completion flow where "not done" is marked
  - Add INSERT statement to record rejection in `tms_technician_rejections` table
  - Set `tr_admin_notified = 0` for new rejections
  - Include booking ID, technician ID, and rejection reason
  - _Requirements: 1.5_

- [ ]* 2.1 Write property test for rejection recording
  - **Property 19: Rejection records are preserved**
  - **Validates: Requirements 5.1**

- [ ] 3. Fix backend API: Check rejection threshold
  - Review `admin/api-check-rejection-threshold.php` for correct query logic
  - Ensure query filters by `tr_admin_notified = 0` AND date within 7 days
  - Verify query groups by technician and counts rejections correctly
  - Add customer contact information join from `tms_service_booking`
  - Return JSON with technician details, rejection count, and customer info
  - _Requirements: 1.1, 1.5, 2.1, 2.2_

- [ ]* 3.1 Write property test for threshold detection
  - **Property 1: Threshold triggers alert display**
  - **Validates: Requirements 1.1**

- [ ]* 3.2 Write property test for rejection filtering
  - **Property 5: Correct rejection filtering**
  - **Validates: Requirements 1.5**

- [ ] 4. Fix backend API: Take rejection action
  - Review `admin/api-take-rejection-action.php` for action processing
  - Implement database transaction for all updates
  - Add lock_account action: Set `t_status = 'Locked'` and `t_blocked_until = NOW() + 2 days`
  - Add block_bookings action: Set `t_blocked_until = NOW() + 2 days` (keep status unchanged)
  - Add no_action: No penalties applied
  - Mark all rejections as notified: `UPDATE tr_admin_notified = 1`
  - Reset rejection counter: `UPDATE t_rejection_count = 0`
  - Store admin action metadata: `tr_admin_action`, `tr_admin_action_at`, `tr_admin_notes`
  - Add validation for required notes on penalty actions
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 5.3, 5.4_

- [ ]* 4.1 Write property test for lock action
  - **Property 9: Lock account action updates database correctly**
  - **Validates: Requirements 3.1**

- [ ]* 4.2 Write property test for block action
  - **Property 10: Block bookings action updates database correctly**
  - **Validates: Requirements 3.2**

- [ ]* 4.3 Write property test for no action
  - **Property 11: No action leaves penalties unchanged**
  - **Validates: Requirements 3.3**

- [ ]* 4.4 Write property test for notification marking
  - **Property 12: All actions mark rejections as notified**
  - **Validates: Requirements 3.4**

- [ ]* 4.5 Write property test for counter reset
  - **Property 13: All actions reset rejection counter**
  - **Validates: Requirements 3.5**

- [ ]* 4.6 Write property test for notes validation
  - **Property 14: Notes required for penalty actions**
  - **Validates: Requirements 3.6**

- [ ]* 4.7 Write property test for record preservation
  - **Property 20: Rejections updated not deleted**
  - **Validates: Requirements 5.2**

- [ ] 5. Fix frontend modal component
  - Review `admin/widget-rejection-alert-modal.php` for correct configuration
  - Ensure modal has `data-backdrop="static"` and `data-keyboard="false"`
  - Remove all session storage checks that suppress alerts
  - Implement immediate check on page load (0ms delay, no setTimeout)
  - Implement 5-second polling interval with `setInterval`
  - Add jQuery dependency check with retry mechanism
  - Implement queue management for multiple flagged technicians
  - Add pulsing border animation for urgency
  - Display technician info: name, phone, rejection count
  - Display customer contact information for rejected bookings
  - Display rejection dates and reasons
  - _Requirements: 1.2, 1.3, 1.4, 2.1, 2.2, 2.3, 2.4_

- [ ]* 5.1 Write property test for modal non-dismissibility
  - **Property 3: Modal is non-dismissible**
  - **Validates: Requirements 1.3**

- [ ]* 5.2 Write property test for sequential display
  - **Property 4: Sequential alert display**
  - **Validates: Requirements 1.4**

- [ ]* 5.3 Write property test for required information display
  - **Property 6: Required technician information display**
  - **Validates: Requirements 2.1**

- [ ]* 5.4 Write property test for immediate check execution
  - **Property 2: Alert check executes immediately**
  - **Validates: Requirements 1.2**

- [ ] 6. Implement action submission handling
  - Add click handlers for "Lock Account", "Block Bookings", and "No Action" buttons
  - Validate admin notes are provided for penalty actions
  - Submit AJAX request to `api-take-rejection-action.php`
  - Handle success: Close modal, check for next technician in queue
  - Handle errors: Display error message, keep modal open
  - Add loading state during submission
  - _Requirements: 3.6, 3.7_

- [x]* 6.1 Write property test for successful action flow


  - **Property 15: Successful action closes modal and shows next**
  - **Validates: Requirements 3.7**

- [ ] 7. Verify footer inclusion
  - Check `admin/vendor/inc/footer.php` includes `widget-rejection-alert-modal.php`
  - Add inclusion if missing
  - Verify modal appears on all admin pages
  - _Requirements: 1.1_

- [ ] 8. Implement continuous monitoring
  - Ensure polling continues after errors (log to console, don't stop interval)
  - Verify new rejections trigger alerts within 5 seconds
  - Test that no alerts appear when threshold not exceeded
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ]* 8.1 Write property test for new rejection detection
  - **Property 16: New rejections trigger alerts within polling interval**
  - **Validates: Requirements 4.2**

- [ ]* 8.2 Write property test for error handling
  - **Property 17: API failures are logged and polling continues**
  - **Validates: Requirements 4.3**

- [ ]* 8.3 Write property test for idle state
  - **Property 18: Idle state continues monitoring**
  - **Validates: Requirements 4.4**

- [ ] 9. Verify auto-unblock cron job
  - Check `admin/cron-auto-unblock-technicians.php` exists and is scheduled
  - Verify it updates technicians where `t_blocked_until < NOW()`
  - Ensure it clears `t_blocked_until` and updates status if locked
  - Add system log entry for each unblock
  - _Requirements: 6.1, 6.2, 6.3_

- [ ] 10. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 11. Create test utilities and fixtures
  - Create `generateTechnician()` helper for random technician data
  - Create `generateRejection()` helper for random rejection records
  - Create `generateBooking()` helper for random booking with customer data
  - Create `createTestRejections()` helper to insert test data
  - Create `cleanupTestData()` helper to remove test records
  - Add database seeding for property tests

- [x]* 12. Set up property-based testing framework



  - Install fast-check for JavaScript property tests
  - Configure PHPUnit with property testing extensions for backend
  - Set minimum 100 iterations per property test
  - Create test configuration files

- [ ] 13. Manual testing and verification
  - Use `admin/test-rejection-alert.php` to view current rejection statistics


  - Use `admin/comprehensive-rejection-test.php` to run all 15 critical tests
  - Test in multiple browsers (Chrome, Firefox, Edge)
  - Verify alert appears immediately on page load when threshold exceeded
  - Verify sequential display of multiple flagged technicians
  - Verify all three action types work correctly
  - Verify polling continues and detects new rejections
  - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [ ] 14. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
