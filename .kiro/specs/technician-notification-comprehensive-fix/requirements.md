# Requirements Document

## Introduction

The technician notification system currently fails to reliably notify technicians of all booking state changes. Technicians must receive real-time notifications for every assignment, reassignment, hold, unhold, and status change event to ensure they can respond promptly to customer bookings. This feature will establish a comprehensive notification system that captures all booking events and delivers them reliably to technicians through both visual alerts and audio notifications.

## Glossary

- **Technician Portal**: The web interface used by technicians to view and manage their assigned bookings
- **Booking Assignment**: The action of linking a booking to a specific technician for service completion
- **Booking Reassignment**: The action of changing which technician is assigned to an existing booking
- **Hold Status**: A temporary pause state for a booking where the technician cannot proceed
- **Unhold Status**: The removal of hold status, allowing the technician to proceed with the booking
- **Notification Event**: Any booking state change that requires technician awareness
- **Notification Tracking System**: The database mechanism that prevents duplicate notifications
- **Real-time Polling**: The periodic checking for new notifications at regular intervals
- **Visual Notification**: An on-screen toast/alert showing booking details
- **Audio Notification**: A sound alert that plays when new notifications arrive

## Requirements

### Requirement 1

**User Story:** As a technician, I want to receive immediate notifications when a booking is assigned to me, so that I can promptly acknowledge and begin work on the service request.

#### Acceptance Criteria

1. WHEN an admin assigns a booking to the technician THEN the system SHALL create a notification event with action type "assigned"
2. WHEN the technician's notification polling detects a new assignment THEN the system SHALL display a visual notification with booking details
3. WHEN a new assignment notification is displayed THEN the system SHALL play an audio alert
4. WHEN the technician views the assignment notification THEN the system SHALL mark that specific assignment event as shown
5. WHEN the same booking is reassigned to the same technician THEN the system SHALL create a new notification event with action type "reassigned"

### Requirement 2

**User Story:** As a technician, I want to receive notifications when a booking is reassigned to me from another technician, so that I understand I am taking over an existing service request.

#### Acceptance Criteria

1. WHEN an admin reassigns a booking from one technician to another THEN the system SHALL create a notification event with action type "reassigned" for the new technician
2. WHEN the reassignment notification is displayed THEN the system SHALL indicate this is a reassignment in the message text
3. WHEN a booking is reassigned THEN the system SHALL update the booking's assigned_at timestamp to the current time
4. WHEN the new technician's notification polling detects the reassignment THEN the system SHALL display a visual notification with booking details
5. WHEN a reassignment notification is displayed THEN the system SHALL play an audio alert

### Requirement 3

**User Story:** As a technician, I want to receive notifications when a booking is placed on hold, so that I know to pause work and understand the reason for the hold.

#### Acceptance Criteria

1. WHEN an admin places a booking on hold THEN the system SHALL create a notification event with action type "hold"
2. WHEN the hold notification is displayed THEN the system SHALL include the hold reason in the notification message
3. WHEN a booking is placed on hold THEN the system SHALL update the booking's updated_at timestamp to the current time
4. WHEN the technician's notification polling detects a hold event THEN the system SHALL display a visual notification with hold details
5. WHEN a hold notification is displayed THEN the system SHALL play an audio alert

### Requirement 4

**User Story:** As a technician, I want to receive notifications when a booking is removed from hold status, so that I know I can resume work on the service request.

#### Acceptance Criteria

1. WHEN an admin removes hold status from a booking THEN the system SHALL create a notification event with action type "unhold"
2. WHEN the unhold notification is displayed THEN the system SHALL indicate the booking is ready to proceed
3. WHEN a booking is removed from hold THEN the system SHALL update the booking's updated_at timestamp to the current time
4. WHEN the technician's notification polling detects an unhold event THEN the system SHALL display a visual notification
5. WHEN an unhold notification is displayed THEN the system SHALL play an audio alert

### Requirement 5

**User Story:** As a technician, I want to receive notifications for all booking status changes, so that I stay informed about the current state of my assigned work.

#### Acceptance Criteria

1. WHEN a booking status changes to "Approved" THEN the system SHALL create a notification event with action type "approved"
2. WHEN a booking status changes to "In Progress" THEN the system SHALL create a notification event with action type "in_progress"
3. WHEN a booking status changes to "Rejected" THEN the system SHALL create a notification event with action type "rejected"
4. WHEN any status change notification is displayed THEN the system SHALL include the new status in the notification message
5. WHEN any status change notification is displayed THEN the system SHALL play an audio alert

### Requirement 6

**User Story:** As a technician, I want the notification system to prevent duplicate alerts for the same event, so that I am not overwhelmed by repeated notifications.

#### Acceptance Criteria

1. WHEN a notification event is created THEN the system SHALL record the booking ID, action type, and status in the tracking table
2. WHEN checking for new notifications THEN the system SHALL exclude events that have already been shown to the technician
3. WHEN a notification is displayed to the technician THEN the system SHALL immediately mark it as shown in the tracking table
4. WHEN the tracking table contains records older than 24 hours THEN the system SHALL automatically delete those records
5. WHEN the same booking undergoes multiple different state changes THEN the system SHALL create separate notification events for each change

### Requirement 7

**User Story:** As a technician, I want the notification polling system to check frequently for new events, so that I receive alerts with minimal delay.

#### Acceptance Criteria

1. WHEN the technician portal loads THEN the system SHALL begin polling for notifications every 5 seconds
2. WHEN the polling interval elapses THEN the system SHALL query the notification endpoint for new events
3. WHEN the notification endpoint is queried THEN the system SHALL return only events not yet shown to the technician
4. WHEN new events are detected THEN the system SHALL display all new notifications simultaneously
5. WHEN the browser tab is hidden THEN the system SHALL continue polling at the same frequency

### Requirement 8

**User Story:** As a technician, I want visual notifications to display comprehensive booking information, so that I can quickly understand what action is required without navigating to another page.

#### Acceptance Criteria

1. WHEN a notification is displayed THEN the system SHALL show the booking ID prominently
2. WHEN a notification is displayed THEN the system SHALL show the customer name and phone number
3. WHEN a notification is displayed THEN the system SHALL show the service name
4. WHEN a notification is displayed THEN the system SHALL show the booking status
5. WHEN a notification is displayed THEN the system SHALL show the deadline date and time if available

### Requirement 9

**User Story:** As a technician, I want audio alerts to play automatically when new notifications arrive, so that I am alerted even when not actively viewing the screen.

#### Acceptance Criteria

1. WHEN the notification system initializes THEN the system SHALL preload the audio file
2. WHEN a user interaction is detected THEN the system SHALL enable audio playback capability
3. WHEN new notifications are detected THEN the system SHALL play the audio alert once
4. WHEN the browser blocks audio autoplay THEN the system SHALL display a visual prompt to enable sound
5. WHEN audio playback fails THEN the system SHALL log the error to the console for debugging

### Requirement 10

**User Story:** As a system administrator, I want the notification system to properly update booking timestamps during state changes, so that the notification detection logic can accurately identify new events.

#### Acceptance Criteria

1. WHEN a booking is assigned to a technician THEN the system SHALL set the sb_assigned_at timestamp to the current time
2. WHEN a booking undergoes any state change THEN the system SHALL update the sb_updated_at timestamp to the current time
3. WHEN a booking is reassigned THEN the system SHALL update both sb_assigned_at and sb_updated_at timestamps
4. WHEN a booking is placed on hold or removed from hold THEN the system SHALL update the sb_updated_at timestamp
5. WHEN querying for new notifications THEN the system SHALL use the sb_updated_at timestamp to filter recent events
