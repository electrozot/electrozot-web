# Implementation Plan

- [x] 1. Update database schema and notification tracking table


  - Add timestamp columns to booking table if not exists (sb_created_at, sb_updated_at, sb_assigned_at)
  - Create notification tracking table with unique constraint on (technician_id, booking_id, action_type, status)
  - Add indexes for efficient querying
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_



- [ ] 2. Enhance booking assignment API to set proper timestamps
  - Modify api-assign-booking.php to set sb_assigned_at to NOW() on assignment
  - Modify api-assign-booking.php to set sb_updated_at to NOW() on assignment
  - Detect reassignment by checking if old_technician_id differs from new technician_id
  - Ensure both timestamps are updated on reassignment
  - _Requirements: 1.1, 2.3, 10.1, 10.3_

- [ ] 3. Enhance booking state change operations to update timestamps
  - Modify hold/unhold operations to set sb_updated_at to NOW()
  - Modify status change operations to set sb_updated_at to NOW()


  - Ensure all booking update queries include explicit sb_updated_at = NOW()
  - _Requirements: 3.3, 4.3, 5.4, 10.2, 10.4_

- [ ] 4. Rewrite notification detection logic in check-technician-notifications.php
  - Query bookings with sb_updated_at in last hour for the technician
  - Determine action type based on: sb_assigned_at recency, is_on_hold status, and sb_status
  - Join with tracking table to exclude already-shown events
  - Return only events not in tracking table
  - _Requirements: 1.2, 2.4, 3.4, 4.4, 5.1, 5.2, 5.3, 6.2_

- [ ] 5. Implement notification tracking to prevent duplicates
  - After returning notifications, insert records into tracking table
  - Use INSERT IGNORE to handle concurrent requests
  - Include booking_id, technician_id, action_type, and status in tracking record
  - Implement cleanup query to delete records older than 24 hours
  - _Requirements: 6.1, 6.3, 6.4_

- [ ] 6. Enhance action type determination logic
  - Check if sb_assigned_at is within last 5 minutes for "assigned" action
  - Check if booking was previously assigned to different technician for "reassigned" action
  - Check is_on_hold flag for "hold" action
  - Check previous hold status for "unhold" action
  - Map status values to action types (Approved → approved, In Progress → in_progress, etc.)
  - _Requirements: 1.1, 2.1, 3.1, 4.1, 5.1, 5.2, 5.3_

- [ ] 7. Update notification message generation
  - Generate distinct messages for each action type



  - Include hold reason in hold notifications
  - Indicate reassignment in reassignment notifications
  - Include status in status change notifications
  - _Requirements: 2.2, 3.2, 4.2, 5.4, 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ] 8. Verify client-side notification display
  - Confirm visual notifications display all required booking information
  - Confirm notification badge count updates correctly
  - Confirm notifications auto-dismiss after 15 seconds
  - Confirm multiple notifications display simultaneously
  - _Requirements: 1.2, 2.4, 3.4, 4.4, 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ] 9. Verify client-side audio alert functionality
  - Confirm audio plays once per polling cycle with new notifications
  - Confirm audio initialization on page load
  - Confirm audio enable on user interaction
  - Confirm visual prompt displays when audio is blocked
  - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

- [ ] 10. Verify polling frequency and behavior
  - Confirm polling occurs every 5 seconds
  - Confirm polling continues when browser tab is hidden
  - Confirm polling handles AJAX errors gracefully
  - Confirm polling handles JSON parse errors gracefully
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ] 11. Test assignment notification flow
  - Assign a booking to a technician
  - Verify notification appears within 5 seconds
  - Verify notification shows action type "assigned"
  - Verify audio alert plays
  - Verify notification is marked as shown in tracking table
  - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [ ] 12. Test reassignment notification flow
  - Assign a booking to technician A
  - Reassign the same booking to technician B
  - Verify technician B receives notification with action type "reassigned"
  - Verify notification message indicates reassignment
  - Verify notification is distinct from original assignment
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

- [ ] 13. Test hold notification flow
  - Place a booking on hold with a reason
  - Verify technician receives notification with action type "hold"
  - Verify notification includes hold reason
  - Verify audio alert plays
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ] 14. Test unhold notification flow
  - Remove hold status from a booking
  - Verify technician receives notification with action type "unhold"
  - Verify notification indicates booking is ready to proceed
  - Verify audio alert plays
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [ ] 15. Test status change notification flow
  - Change booking status to "Approved"
  - Verify technician receives notification with action type "approved"
  - Change status to "In Progress"
  - Verify technician receives notification with action type "in_progress"
  - Change status to "Rejected"
  - Verify technician receives notification with action type "rejected"
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ] 16. Test duplicate prevention
  - Trigger a notification event
  - Verify notification appears once
  - Wait for next polling cycle
  - Verify same notification does not appear again
  - Verify tracking table contains record for the event
  - _Requirements: 6.1, 6.2, 6.3_

- [ ] 17. Test multiple state changes create separate notifications
  - Assign a booking (notification 1: assigned)
  - Place booking on hold (notification 2: hold)
  - Remove hold (notification 3: unhold)
  - Change status to In Progress (notification 4: in_progress)
  - Verify technician receives all 4 distinct notifications
  - _Requirements: 6.5_

- [ ] 18. Test tracking record cleanup
  - Create tracking records with shown_at timestamp older than 24 hours
  - Trigger notification check
  - Verify old records are deleted from tracking table
  - Verify recent records remain in tracking table
  - _Requirements: 6.4_

- [ ] 19. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
