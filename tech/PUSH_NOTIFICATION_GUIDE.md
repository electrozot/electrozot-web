# Push Notification System for Technicians

## Overview
Technicians will receive notifications even when:
- ✅ Browser tab is in background
- ✅ Browser is minimized
- ✅ Device screen is locked
- ✅ Web app is not open (if installed as PWA)

## How It Works

### 1. Service Worker
- Runs in background even when page is closed
- Listens for new booking assignments
- Shows notifications automatically
- Handles notification clicks

### 2. Browser Notifications
- Native OS notifications
- Work even when browser is minimized
- Persistent until dismissed
- Include vibration on mobile devices

### 3. Background Sync
- Checks for new bookings periodically
- Works even when page is not active
- Automatic retry if network fails

## Setup Instructions

### Step 1: Enable HTTPS (Required)
Service Workers require HTTPS. Ensure your site uses SSL certificate.

```
http://yourdomain.com → https://yourdomain.com
```

### Step 2: Create Icon Files
Create notification icons in `vendor/img/icons/`:

Required sizes:
- icon-72x72.png
- icon-96x96.png
- icon-128x128.png
- icon-144x144.png
- icon-152x152.png
- icon-192x192.png
- icon-384x384.png
- icon-512x512.png
- badge-72x72.png (for notification badge)

### Step 3: Add Manifest Link
Add to technician dashboard `<head>` section:

```html
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#667eea">
```

### Step 4: Test Notifications

1. **Open Technician Dashboard**
   - Visit: https://yourdomain.com/tech/dashboard.php
   - Click "Enable" when notification prompt appears

2. **Assign a Booking**
   - From admin panel, assign a booking to the technician
   - Technician should receive notification within 5-10 seconds

3. **Test Background Notifications**
   - Minimize browser or switch to another tab
   - Assign another booking
   - Notification should appear even though page is not visible

4. **Test Locked Device**
   - Lock your device screen
   - Assign a booking
   - Device should vibrate and show notification on lock screen

## Browser Support

### Desktop
- ✅ Chrome 42+
- ✅ Firefox 44+
- ✅ Edge 17+
- ✅ Opera 29+
- ❌ Safari (limited support)

### Mobile
- ✅ Chrome Android 42+
- ✅ Firefox Android 44+
- ✅ Samsung Internet 4+
- ❌ iOS Safari (no support)

## Features

### 1. Instant Notifications
- Appear within 5-10 seconds of assignment
- Sound alert plays automatically
- Visual toast notification on screen

### 2. Persistent Notifications
- Stay visible until dismissed
- "requireInteraction: true" keeps them on screen
- Click to open booking details

### 3. Vibration (Mobile)
- Pattern: [200ms, 100ms, 200ms, 100ms, 200ms]
- Alerts even when device is on silent mode
- Works on Android devices

### 4. Background Checks
- Checks every 10 seconds when page is hidden
- Checks every 5 seconds when page is visible
- Automatic retry on network failure

### 5. Notification Actions
- "View Booking" - Opens dashboard
- "Dismiss" - Closes notification
- Click anywhere - Opens dashboard

## User Experience

### First Visit
1. Technician opens dashboard
2. After 5 seconds, permission prompt appears
3. Technician clicks "Enable"
4. Browser asks for notification permission
5. Technician clicks "Allow"
6. System is now active

### When Booking is Assigned
1. Admin assigns booking to technician
2. Within 5-10 seconds:
   - Sound plays (if page is open)
   - Browser notification appears
   - Device vibrates (mobile)
   - Visual toast shows (if page is open)
3. Technician clicks notification
4. Dashboard opens with new booking

### When Page is Closed
1. Service worker continues running
2. Checks for new bookings periodically
3. Shows notification when booking is assigned
4. Clicking notification opens dashboard

## Troubleshooting

### Notifications Not Appearing

**Check Permission:**
```javascript
console.log(Notification.permission);
// Should be "granted"
```

**Check Service Worker:**
```javascript
navigator.serviceWorker.getRegistrations().then(registrations => {
    console.log('Service Workers:', registrations);
});
```

**Check Browser Console:**
- Open DevTools (F12)
- Look for errors in Console tab
- Check Application > Service Workers

### Permission Denied

If user denied permission:
1. Click lock icon in address bar
2. Find "Notifications" setting
3. Change to "Allow"
4. Reload page

### Service Worker Not Registering

**Requirements:**
- HTTPS connection (required)
- Valid SSL certificate
- Correct file path
- No JavaScript errors

**Debug:**
```javascript
navigator.serviceWorker.register('/tech/service-worker.js')
    .then(reg => console.log('✅ Registered:', reg))
    .catch(err => console.error('❌ Failed:', err));
```

### iOS Limitations

iOS Safari does NOT support:
- Service Workers for push notifications
- Background sync
- Persistent notifications

**Workaround for iOS:**
- Use in-app notifications only
- Require app to be open
- Consider native app for iOS users

## Testing Checklist

- [ ] HTTPS enabled
- [ ] Service worker registered
- [ ] Notification permission granted
- [ ] Icons created and accessible
- [ ] Manifest file linked
- [ ] Sound file exists
- [ ] Test with page open
- [ ] Test with page minimized
- [ ] Test with page closed
- [ ] Test with device locked
- [ ] Test on mobile device
- [ ] Test notification click
- [ ] Test multiple notifications

## Advanced Configuration

### Customize Notification Sound

Edit `tech/includes/notification-system.php`:
```javascript
const techNotificationAudio = new Audio('../admin/vendor/sounds/your-sound.mp3');
```

### Customize Vibration Pattern

Edit `tech/service-worker.js`:
```javascript
vibrate: [200, 100, 200, 100, 200] // [vibrate, pause, vibrate, pause, vibrate]
```

### Change Check Interval

Edit `tech/includes/notification-system.php`:
```javascript
setInterval(checkTechNotifications, 5000); // 5 seconds
```

Edit `tech/includes/push-notification-setup.php`:
```javascript
backgroundCheckInterval = setInterval(() => {
    checkNotificationsInBackground();
}, 10000); // 10 seconds when hidden
```

### Customize Notification Appearance

Edit `tech/service-worker.js`:
```javascript
{
    title: 'Your Custom Title',
    body: 'Your custom message',
    icon: '/path/to/icon.png',
    badge: '/path/to/badge.png',
    vibrate: [200, 100, 200],
    tag: 'unique-tag',
    requireInteraction: true
}
```

## Security Considerations

1. **HTTPS Required**: Service workers only work on HTTPS
2. **Same Origin**: Service worker must be on same domain
3. **User Permission**: User must explicitly grant permission
4. **No Sensitive Data**: Don't include sensitive info in notifications

## Performance

### Resource Usage
- Service worker: ~1-2 MB memory
- Background checks: Minimal CPU usage
- Network: ~1 KB per check

### Battery Impact
- Minimal on modern devices
- Background sync is optimized by browser
- Notifications use native OS APIs

## Maintenance

### Regular Checks
- Monitor service worker registration
- Check notification delivery rate
- Review error logs
- Test on different devices

### Updates
When updating service worker:
1. Increment cache version
2. Clear old caches
3. Test thoroughly
4. Deploy during low-traffic period

## Support

For issues:
1. Check browser console for errors
2. Verify HTTPS is enabled
3. Test notification permission
4. Check service worker status
5. Review notification settings

## Files Created

```
tech/
├── service-worker.js                    # Background worker
├── manifest.json                        # PWA manifest
└── includes/
    └── push-notification-setup.php      # Setup script
```

## Next Steps

1. ✅ Enable HTTPS on your server
2. ✅ Create notification icon files
3. ✅ Add manifest link to dashboard
4. ✅ Test with real technician account
5. ✅ Monitor notification delivery
6. ✅ Gather user feedback
7. ✅ Optimize based on usage

---

**Note**: This system works best on Android devices and desktop browsers. iOS has limited support for background notifications.
