# Technician Bottom Navigation Bar

## Overview
A mobile-friendly bottom navigation bar for technicians to easily track and access all features.

## Features

### 📱 5 Main Menu Options

1. **🏠 Home** - Dashboard (dashboard.php)
2. **🔔 New** - New bookings with badge counter (new-bookings.php)
3. **📋 Bookings** - All bookings (my-bookings.php)
4. **✅ Completed** - Completed bookings (completed-bookings.php)
5. **👤 Profile** - Technician profile (my-profile.php)

## Design

### Visual Features
- **Gradient Background:** Purple gradient (667eea → 764ba2)
- **Icons:** Large, clear Font Awesome icons
- **Active State:** Highlighted with white background
- **Hover Effect:** Icons scale up and move
- **Badge Counter:** Red badge shows new booking count
- **Animated:** Pulse animation on badge

### Layout
```
┌─────────────────────────────────────────┐
│  🏠    🔔    📋    ✅    👤           │
│ Home  New  Bookings Completed Profile  │
└─────────────────────────────────────────┘
```

## Responsive Behavior

### Mobile (< 768px)
- ✅ Visible
- Smaller icons (20px)
- Compact padding

### Tablet (769px - 1024px)
- ✅ Visible
- Standard size

### Desktop (> 1024px)
- ❌ Hidden
- Uses regular sidebar navigation

## Badge Counter

### Shows on "New" button:
- Counts bookings with status = 'Pending'
- Updates automatically
- Red background with pulse animation
- Example: `🔔 New [3]`

## Active Page Highlighting

The current page is automatically highlighted:
- White background
- Brighter icon
- Slightly larger scale

## Implementation

### File Structure
```
tech/
├── includes/
│   └── bottom-nav.php (Reusable component)
├── dashboard.php (Includes bottom-nav)
├── new-bookings.php
├── my-bookings.php
├── completed-bookings.php
└── my-profile.php
```

### Usage in Any Page
```php
<!-- At the end of body, before </body> -->
<?php include('includes/bottom-nav.php'); ?>
```

## Styling

### Colors
- Background: Linear gradient purple
- Active: White with 20% opacity
- Hover: White with 10% opacity
- Badge: Red (#ff4757)
- Text: White with 70% opacity

### Animations
- **Hover:** Scale 1.2 + translateY(-2px)
- **Active:** Scale 1.1
- **Badge:** Pulse animation (2s infinite)

## Benefits

✅ **Easy Navigation** - One tap access to all sections
✅ **Always Visible** - Fixed at bottom, always accessible
✅ **Visual Feedback** - Clear active state
✅ **Badge Alerts** - See new bookings at a glance
✅ **Mobile Optimized** - Perfect for on-the-go technicians
✅ **Responsive** - Adapts to screen size
✅ **Professional** - Modern, clean design

## User Experience

### Technician Workflow:
1. Opens app → Sees dashboard
2. Checks bottom nav → Sees "3" new bookings
3. Taps "New" → Views new bookings
4. Accepts booking → Returns to dashboard
5. Taps "Bookings" → Tracks active jobs
6. Completes job → Taps "Completed" to view history

## Future Enhancements
- Add "Search" button
- Add "Notifications" button
- Add "Settings" button
- Swipe gestures between pages
- Haptic feedback on tap
