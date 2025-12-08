# Requirements Document

## Introduction

This specification addresses a critical issue in the technician rejection alert system. Currently, when a technician marks 3 or more bookings as "not done" (rejected), the admin should receive an immediate popup alert to take action (lock account, block bookings, or dismiss). However, this popup is not appearing, preventing admins from managing problematic technicians effectively.

## Glossary

- **TMS**: Technician Management System - the overall system managing technicians and bookings
- **Rejection**: When a technician marks a booking as "not done" or declines to complete it
- **Rejection Threshold**: The number of rejections (currently 3) that triggers an admin alert
- **Admin Alert Popup**: A non-dismissible modal that appears to admins requiring action on flagged technicians
- **Unnotified Rejection**: A rejection record where `tr_admin_notified = 0`, meaning admin has not yet been alerted
- **Rejection Window**: The time period (7 days) within which rejections are counted toward the threshold

## Requirements

### Requirement 1

**User Story:** As an admin, I want to be immediately alerted when a technician rejects 3 or more bookings, so that I can take appropriate action to maintain service quality.

#### Acceptance Criteria

1. WHEN a technician's unnotified rejection count reaches 3 within a 7-day window, THEN the TMS SHALL display an alert popup to the admin immediately upon page load
2. WHEN the admin opens any admin page, THEN the TMS SHALL check for technicians exceeding the rejection threshold within 100 milliseconds
3. WHEN the rejection alert popup is displayed, THEN the TMS SHALL prevent the admin from dismissing it without taking an action
4. WHEN multiple technicians exceed the threshold, THEN the TMS SHALL display alerts sequentially, one technician at a time
5. WHEN the alert check runs, THEN the TMS SHALL only count rejections where `tr_admin_notified = 0` and occurred within the last 7 days

### Requirement 2

**User Story:** As an admin, I want to see relevant technician and customer information in the alert, so that I can make informed decisions about what action to take.

#### Acceptance Criteria

1. WHEN the rejection alert popup displays, THEN the TMS SHALL show the technician's name, phone number, and total rejection count
2. WHEN the rejection alert popup displays, THEN the TMS SHALL show customer contact information for the rejected bookings
3. WHEN the rejection alert popup displays, THEN the TMS SHALL include a visual indicator of urgency such as a pulsing border
4. WHEN the rejection alert popup displays, THEN the TMS SHALL show the date range of the rejections being counted

### Requirement 3

**User Story:** As an admin, I want to take action on flagged technicians directly from the alert, so that I can quickly resolve issues without navigating to other pages.

#### Acceptance Criteria

1. WHEN the admin selects "Lock Account", THEN the TMS SHALL set the technician status to 'Locked' and block them for 2 days
2. WHEN the admin selects "Block Bookings", THEN the TMS SHALL prevent the technician from receiving new bookings for 2 days while allowing login
3. WHEN the admin selects "No Action", THEN the TMS SHALL mark the rejections as reviewed without applying penalties
4. WHEN the admin takes any action, THEN the TMS SHALL mark all related rejections as `tr_admin_notified = 1`
5. WHEN the admin takes any action, THEN the TMS SHALL reset the technician's rejection count to 0
6. WHEN the admin selects "Lock Account" or "Block Bookings", THEN the TMS SHALL require admin notes before processing the action
7. WHEN the admin action is processed successfully, THEN the TMS SHALL close the alert popup and check for the next flagged technician

### Requirement 4

**User Story:** As an admin, I want the alert system to continuously monitor for new rejections, so that I don't miss any technicians who exceed the threshold.

#### Acceptance Criteria

1. WHEN the admin is on any admin page, THEN the TMS SHALL check for rejection alerts every 5 seconds
2. WHEN a new technician exceeds the threshold during an admin session, THEN the TMS SHALL display the alert within 5 seconds
3. WHEN the alert check API fails, THEN the TMS SHALL log the error to the console and retry on the next interval
4. WHEN no technicians exceed the threshold, THEN the TMS SHALL continue monitoring without displaying alerts

### Requirement 5

**User Story:** As a system administrator, I want rejection history to be preserved, so that I can review patterns and make data-driven decisions about technician management.

#### Acceptance Criteria

1. WHEN an admin takes action on a rejection alert, THEN the TMS SHALL preserve all rejection records in the database
2. WHEN rejections are marked as notified, THEN the TMS SHALL update the `tr_admin_notified` field to 1 without deleting records
3. WHEN rejections are marked as notified, THEN the TMS SHALL record the admin action type and timestamp
4. WHEN rejections are marked as notified, THEN the TMS SHALL store the admin notes if provided

### Requirement 6

**User Story:** As an admin, I want blocked or locked technicians to be automatically unblocked after the penalty period, so that I don't have to manually manage expiration.

#### Acceptance Criteria

1. WHEN a technician is blocked or locked with a 2-day penalty, THEN the TMS SHALL automatically unblock them after 2 days via cron job
2. WHEN the auto-unblock process runs, THEN the TMS SHALL update the technician status and clear the `t_blocked_until` timestamp
3. WHEN a technician is unblocked automatically, THEN the TMS SHALL log the action in system logs

### Requirement 7

**User Story:** As a developer, I want to test the rejection alert system, so that I can verify it's working correctly before relying on it in production.

#### Acceptance Criteria

1. WHEN a developer accesses the test page, THEN the TMS SHALL display current rejection statistics for all technicians
2. WHEN a developer accesses the test page, THEN the TMS SHALL show which technicians are above the threshold and will trigger alerts
3. WHEN a developer accesses the test page, THEN the TMS SHALL provide a manual trigger button to test the alert API
4. WHEN a developer runs the comprehensive test suite, THEN the TMS SHALL execute all 15 critical tests and report pass/fail status
