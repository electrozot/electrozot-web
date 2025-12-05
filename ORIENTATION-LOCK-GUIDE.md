# PWA Orientation Lock Implementation Guide

## Overview
This implementation locks the PWA app to **portrait mode on mobile devices** while allowing **landscape mode on desktops**.

## What Was Changed

### 1. Manifest Configuration
**File:** `manifest.json`
- Changed `"orientation": "portrait"` to `"orientation": "portrait-primary"`
- This enforces portrait mode at the PWA manifest level

### 2. CSS Orientation Lock
**File:** `css/pwa-orientation-lock.css`
- Detects landscape orientation on mobile devices (max-width: 1024px)
- Shows a warning overlay when mobile device is tilted to landscape
- Allows normal landscape behavior on desktop (min-width: 1025px)

### 3. JavaScript Orientation Control
**File:** `js/orientation-lock.js`
- Uses Screen Orientation API to programmatically lock orientation
- Detects device type (mobile vs desktop)
- Only applies lock on mobile devices
- Handles orientation change events

### 4. Integration Points
Updated the following head files to include orientation lock:
- `vendor/inc/head.php` (Main site)
- `admin/vendor/inc/head.php` (Admin panel)
- `usr/vendor/inc/head.php` (User dashboard)
- `tech/includes/head.php` (Technician dashboard)

## How It Works

### Mobile Devices (≤1024px width)
1. **Portrait Mode:** App works normally
2. **Landscape Mode:** 
   - Warning overlay appears: "Please rotate your device to portrait mode"
   - Animated phone icon shows rotation instruction
   - Content is blocked until device is rotated back

### Desktop/Tablets (>1024px width)
- No restrictions applied
- Landscape and portrait both work normally
- Full responsive behavior maintained

## Testing

### Test Page
Open `test-orientation-lock.html` in your browser to test:
- Shows current device type (Mobile/Desktop)
- Shows current orientation (Portrait/Landscape)
- Displays screen dimensions
- Updates in real-time

### Manual Testing Steps

#### On Mobile Phone:
1. Open the PWA app in portrait mode ✓
2. Tilt phone to landscape mode
3. You should see a warning overlay with rotation instruction
4. Rotate back to portrait - overlay disappears

#### On Desktop:
1. Open the PWA app
2. Resize browser window to any size
3. Both portrait and landscape orientations work normally

## Browser Support

### Screen Orientation API Support:
- ✅ Chrome/Edge (Android, Desktop)
- ✅ Firefox (Android, Desktop)
- ✅ Safari (iOS 16.4+)
- ⚠️ Older browsers: Falls back to CSS-only solution

### CSS Media Query Support:
- ✅ All modern browsers
- ✅ iOS Safari
- ✅ Android Chrome
- ✅ Desktop browsers

## Customization

### Change Mobile Breakpoint
Edit `css/pwa-orientation-lock.css` and `js/orientation-lock.js`:
```css
/* Change 1024px to your preferred breakpoint */
@media screen and (max-width: 1024px) and (orientation: landscape) {
```

### Customize Warning Message
Edit `css/pwa-orientation-lock.css`:
```css
body::before {
    content: "Your custom message here";
}
```

### Disable for Specific Pages
Add this to any page where you want to allow landscape:
```html
<style>
    body::before, body::after {
        display: none !important;
    }
</style>
```

## Troubleshooting

### Issue: Orientation lock not working on iOS
**Solution:** iOS requires the app to be installed as PWA (Add to Home Screen) for orientation lock to work.

### Issue: Warning overlay not showing
**Solution:** 
1. Clear browser cache
2. Ensure `css/pwa-orientation-lock.css` is loaded
3. Check browser console for errors

### Issue: Desktop showing warning
**Solution:** Check if screen width is correctly detected. Desktop should be >1024px.

### Issue: Landscape still works on mobile
**Solution:** 
1. Ensure JavaScript is enabled
2. Check if Screen Orientation API is supported
3. Verify PWA is installed (not just browser view)

## Files Modified

```
✅ manifest.json
✅ vendor/inc/head.php
✅ admin/vendor/inc/head.php
✅ usr/vendor/inc/head.php
✅ tech/includes/head.php
```

## Files Created

```
✅ css/pwa-orientation-lock.css
✅ js/orientation-lock.js
✅ test-orientation-lock.html
✅ ORIENTATION-LOCK-GUIDE.md
```

## Next Steps

1. **Test on actual mobile device** (not just browser DevTools)
2. **Install as PWA** (Add to Home Screen) for full functionality
3. **Test on different devices:** iPhone, Android, tablets
4. **Monitor user feedback** for any orientation issues

## Support

If you encounter any issues:
1. Check browser console for errors
2. Verify all files are properly linked
3. Test with `test-orientation-lock.html`
4. Ensure PWA is properly installed

---

**Implementation Date:** December 4, 2025
**Status:** ✅ Complete and Ready for Testing
