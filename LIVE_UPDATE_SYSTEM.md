# Live Data Update System

## Overview
Implemented real-time data updates across user and technician dashboards without requiring manual page refreshes.

## Features

### 1. User Track Booking Page (`usr/user-track-booking.php`)
- **Update Interval**: Every 5 seconds
- **What Updates**:
  - Booking status changes
  - Technician assignment
  - Automatically reloads page when changes detected
- **API Endpoint**: `usr/api-live-booking-data.php`

### 2. User Dashboard (`usr/user-dashboard.php`)
- **Update Interval**: Every 10 seconds
- **What Updates**:
  - Pending bookings count
  - Approved bookings count
  - Completed bookings count
  - Total bookings count
- **API Endpoint**: `usr/api-dashboard-stats.php`

### 3. Technician Dashboard (`tech/dashboard.php`)
- **Update Interval**: Every 8 seconds
- **What Updates**:
  - Pending assignments count
  - In-progress bookings count
  - Completed bookings count
  - Total bookings count
  - Visual pulse animation on stat changes
- **API Endpoint**: `tech/api-tech-dashboard-stats.php`

### 4. User Manage Bookings (`usr/user-manage-booking.php`)
- **Update Interval**: Every 10 seconds
- **What Updates**:
  - Detects new or removed bookings
  - Automatically reloads when booking list changes
- **API Endpoint**: `usr/api-dashboard-stats.php`

## API Endpoints Created

### 1. `usr/api-live-booking-data.php`
Returns real-time booking data including:
- Booking status
- Service details
- Technician information (name, photo)

### 2. `usr/api-dashboard-stats.php`
Returns user booking statistics:
- Pending count
- Approved count
- Completed count
- Total count
- Recent bookings list

### 3. `tech/api-tech-dashboard-stats.php`
Returns technician booking statistics:
- Pending assignments
- In-progress bookings
- Completed bookings
- Total bookings
- Recent bookings with customer info

## How It Works

1. **AJAX Polling**: JavaScript fetches data from API endpoints at regular intervals
2. **Change Detection**: Compares new data with current displayed data
3. **Smart Updates**: 
   - For stats: Updates numbers in-place with animation
   - For booking details: Reloads page to show complete updated information
4. **No User Action Required**: Everything happens automatically in the background

## Benefits

✅ **Real-time visibility** - Users see updates instantly
✅ **No manual refresh** - Automatic updates without user intervention
✅ **Better UX** - Smooth experience with visual feedback
✅ **Efficient** - Only updates when changes detected
✅ **Lightweight** - Minimal server load with optimized queries

## Technical Details

- Uses `fetch()` API for AJAX requests
- JSON response format for easy parsing
- Session-based authentication maintained
- Prepared statements for security
- Optimized SQL queries for performance

## Future Enhancements

- WebSocket support for instant push notifications
- Service Worker for offline capability
- Progressive Web App (PWA) features
- Real-time chat between user and technician
