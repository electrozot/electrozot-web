# Notification Marquee & Dedicated Notifications Page

## Overview
A comprehensive notification system for the admin dashboard featuring a real-time marquee banner and a dedicated notifications page.

## Features Implemented

### 1. Notification Marquee (Dashboard)
Located at the top of the admin dashboard (`admin/admin-dashboard.php`)

**Features:**
- **Real-time Updates**: Auto-refreshes every 30 seconds
- **Animated Bell Icon**: Eye-catching bell animation to draw attention
- **Pause on Hover**: Marquee pauses when you hover over it for easy reading
- **Recent Activity Display**: Shows the 10 most recent booking activities
- **Status Icons**: Visual indicators for different booking statuses:
  - 🆕 New bookings
  - ✅ Approved/Assigned bookings
  - 🔧 In Progress bookings
  - ✔️ Completed bookings
  - ❌ Rejected bookings
- **Quick Access Button**: "View All" button links to the dedicated notifications page
- **Gradient Design**: Beautiful purple gradient background with hover effects

**Technical Details:**
- Fetches data from `get-recent-notifications.php`
- Updates automatically without page reload
- Integrated with existing real-time notification system
- Responsive design that works on all screen sizes

### 2. Dedicated Notifications Page
New page: `admin/admin-notifications.php`

**Features:**
- **Comprehensive View**: See all booking notifications in one place
- **Status Statistics**: Quick overview cards showing counts for:
  - Pending bookings
  - In Progress bookings
  - Completed bookings
  - Rejected/Cancelled bookings
- **Advanced Filtering**:
  - Filter by status (All, Pending, Approved, In Progress, Completed, Rejected)
  - Search by customer name, phone, service name, or booking ID
  - Combine filters and search for precise results
- **Rich Notification Cards**:
  - Color-coded borders based on status
  - Gradient backgrounds for visual appeal
  - Status icons and badges
  - Complete booking information displayed
  - Time elapsed since booking creation
  - Customer details (name, phone)
  - Service information
  - Technician assignment status
  - Address information
- **Quick Actions**:
  - View booking details
  - Assign technician (for pending/approved bookings)
- **Pagination**: Navigate through large numbers of notifications (20 per page)
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile

**Status Color Coding:**
- **Yellow/Warning**: Pending bookings
- **Blue/Info**: Approved/Assigned bookings
- **Primary Blue**: In Progress bookings
- **Green/Success**: Completed bookings
- **Red/Danger**: Rejected/Cancelled bookings

### 3. Navigation Integration
Added "Notifications" link to the admin sidebar for easy access

## Usage

### Accessing the Notifications Page
1. Log in to the admin panel
2. Click on "Notifications" in the left sidebar
3. Or click "View All" button in the dashboard marquee

### Using Filters
1. Click on any status button (Pending, Approved, etc.) to filter
2. Use the search box to find specific bookings
3. Combine filters and search for precise results
4. Results update automatically

### Reading the Marquee
1. The marquee scrolls automatically showing recent activities
2. Hover over it to pause and read details
3. Click "View All" to see complete information
4. The marquee updates every 30 seconds automatically

## Technical Implementation

### Files Modified
1. `admin/admin-dashboard.php` - Enhanced marquee with animations and auto-refresh
2. `admin/vendor/inc/sidebar.php` - Added Notifications menu item
3. `admin/get-recent-notifications.php` - Already existed, provides data for marquee

### Files Created
1. `admin/admin-notifications.php` - New dedicated notifications page

### Database Tables Used
- `tms_service_booking` - Main bookings table
- `tms_user` - Customer information
- `tms_service` - Service details
- `tms_technician` - Technician information

### Key Functions
- `loadRecentNotifications()` - Fetches and displays marquee data
- `time_elapsed_string()` - Converts timestamps to human-readable format
- Auto-refresh intervals for real-time updates

## Benefits

1. **Improved Awareness**: Admins can see all booking activities at a glance
2. **Quick Response**: Easy to identify pending bookings that need attention
3. **Better Organization**: Filter and search capabilities for efficient management
4. **Professional Look**: Modern, attractive design with smooth animations
5. **User-Friendly**: Intuitive interface with clear visual indicators
6. **Real-time Updates**: Stay informed without manual page refreshes

## Audio Notification System

### Dual Sound Support
The system includes a robust audio notification system with automatic fallback:

**1. Custom Sound (Primary)**
- Uses custom MP3 files (`arived.mp3` for dashboard, `notification.mp3` for notifications page)
- High-quality audio notifications
- Customizable volume and sound files

**2. Web Audio API (Fallback)**
- Automatically activates if custom sound fails or is unavailable
- Generates pleasant two-tone beep (800Hz + 1000Hz)
- No external files required
- Works in all modern browsers

### Features
- **Automatic Fallback**: Seamlessly switches to Web API if custom sound fails
- **User Interaction Required**: Initializes on first click/keypress (browser security)
- **Error Handling**: Comprehensive error logging and graceful degradation
- **Browser Notifications**: Desktop notifications with click-to-focus
- **Toast Notifications**: In-page visual notifications with animations

### How It Works
1. System attempts to play custom sound file
2. If custom sound fails (file missing, permission denied, etc.):
   - Automatically falls back to Web Audio API beep
   - Logs the fallback action in console
3. Browser notification appears (if permission granted)
4. Toast notification slides in from the right
5. Page updates automatically to show new bookings

### Setup
- Place custom sound files in `admin/vendor/sounds/`
- No configuration needed - fallback is automatic
- See `admin/vendor/sounds/README.md` for details

## Future Enhancements (Optional)

- Email/SMS notifications integration
- Export notifications to PDF/Excel
- Mark notifications as read/unread
- Notification preferences and settings
- Push notifications for mobile devices
- Custom sound upload interface

## Support

For any issues or questions about the notification system, refer to:
- `REALTIME_NOTIFICATIONS.md` - Real-time notification system documentation
- `UNIVERSAL_NOTIFICATIONS.md` - Universal notification system documentation
