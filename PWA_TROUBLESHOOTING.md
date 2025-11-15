# PWA Install Button Not Showing - Troubleshooting Guide

## Why Install Button Might Not Show

### 1. **Icons Missing** ⚠️ MOST COMMON
The PWA requires icons to be installable.

**Solution:**
1. Open `create-placeholder-icons.html` in your browser
2. Click "Generate Icons"
3. Download each icon (right-click → Save as)
4. Save to `vendor/img/icons/` folder
5. Refresh your page

### 2. **Not on HTTPS** 
PWA requires HTTPS (except localhost)

**Check:**
- URL starts with `https://` ✅
- OR URL is `localhost` ✅
- If `http://` on production ❌

**Solution:**
- Enable SSL certificate
- Or test on localhost

### 3. **Already Installed**
If you already installed the app, button won't show.

**Check:**
- Look for app in your applications
- Check Chrome → Apps
- Check phone home screen

**Solution:**
- Uninstall the app first
- Then refresh page

### 4. **Browser Not Supported**
Some browsers don't support PWA install prompts.

**Supported:**
- ✅ Chrome (Desktop/Android)
- ✅ Edge (Desktop/Android)
- ✅ Samsung Internet
- ⚠️ Safari (iOS) - Manual only
- ❌ Firefox - Limited support

**Solution:**
- Test in Chrome or Edge
- On iOS: Use Share → Add to Home Screen

### 5. **Manifest Not Loading**
The manifest.json file might not be found.

**Check in Browser Console (F12):**
```
Look for errors like:
- "Failed to fetch manifest"
- "Manifest: Line 1, column 1, Syntax error"
```

**Solution:**
- Verify `manifest.json` exists in root
- Check file permissions
- Validate JSON syntax

### 6. **Service Worker Not Registering**
Service worker must register successfully.

**Check in Browser Console (F12):**
```
Should see:
✅ Service Worker registered successfully
```

**If you see errors:**
- Check `sw.js` exists in root
- Verify file permissions
- Check for JavaScript errors

---

## How to Debug

### Step 1: Open Browser Console
1. Press `F12` or right-click → Inspect
2. Go to "Console" tab
3. Look for messages starting with:
   - ✅ (success)
   - ❌ (error)
   - 🔍 (debug info)

### Step 2: Check Application Tab
1. Press `F12`
2. Go to "Application" tab
3. Check:
   - **Manifest:** Should show app details
   - **Service Workers:** Should show "activated and running"
   - **Storage:** Check if files are cached

### Step 3: Run Lighthouse Audit
1. Press `F12`
2. Go to "Lighthouse" tab
3. Select "Progressive Web App"
4. Click "Generate report"
5. Check what's failing

---

## Quick Fixes

### Fix 1: Create Icons (5 minutes)
```
1. Open: create-placeholder-icons.html
2. Download all 8 icons
3. Create folder: vendor/img/icons/
4. Save icons there
5. Refresh page
```

### Fix 2: Check Paths
Open browser console and check:
```javascript
// Should see these logs:
✅ Service Worker registered successfully
🔍 Checking PWA installability...
- Service Worker support: true
- Manifest link: ✅ Found
- HTTPS: true
```

### Fix 3: Clear Cache
```
1. Press F12
2. Go to Application tab
3. Click "Clear storage"
4. Check all boxes
5. Click "Clear site data"
6. Refresh page
```

### Fix 4: Test Install Manually
Even without the button, you can install:

**Chrome/Edge:**
1. Click address bar icon (⊕ or install icon)
2. Or Menu (⋮) → "Install ElectroZot"

**Mobile:**
1. Menu → "Add to Home screen"
2. Or "Install app"

---

## Expected Console Output

When everything works, you should see:
```
✅ Service Worker registered successfully: http://localhost/
📍 Service Worker path: /sw.js
🔍 Checking PWA installability...
- Service Worker support: true
- Manifest link: ✅ Found
- HTTPS: true
✅ beforeinstallprompt event fired - App is installable!
```

---

## Common Error Messages

### "Failed to register service worker"
**Cause:** sw.js not found  
**Fix:** Verify sw.js is in root directory

### "Manifest: Line 1, column 1, Syntax error"
**Cause:** Invalid JSON in manifest.json  
**Fix:** Validate JSON at jsonlint.com

### "No matching service worker detected"
**Cause:** Service worker not registered  
**Fix:** Check console for registration errors

### "Site cannot be installed: no matching service worker"
**Cause:** Service worker scope issue  
**Fix:** Ensure sw.js is in root directory

---

## Testing Checklist

- [ ] Icons created (all 8 sizes)
- [ ] Icons saved to `vendor/img/icons/`
- [ ] manifest.json exists in root
- [ ] sw.js exists in root
- [ ] offline.html exists in root
- [ ] Testing on HTTPS or localhost
- [ ] Using Chrome or Edge browser
- [ ] App not already installed
- [ ] Console shows no errors
- [ ] Manifest loads in DevTools
- [ ] Service worker registered

---

## Still Not Working?

### Try This:
1. **Hard Refresh:** Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
2. **Incognito Mode:** Test in private/incognito window
3. **Different Browser:** Try Chrome if using Edge, or vice versa
4. **Check File Paths:** Ensure all files are in correct locations
5. **Validate Manifest:** Use https://manifest-validator.appspot.com/

### Get Help:
1. Check browser console for specific errors
2. Run Lighthouse audit for detailed report
3. Verify all files exist and are accessible
4. Test on different device/browser

---

## Alternative: Manual Installation

If install button doesn't show, users can still install manually:

### Desktop (Chrome/Edge):
1. Look for install icon in address bar
2. Or Menu → "Install ElectroZot"

### Android:
1. Menu (⋮) → "Add to Home screen"
2. Or "Install app"

### iOS (Safari):
1. Share button (□↑)
2. "Add to Home Screen"
3. Tap "Add"

---

## Success Indicators

You'll know it's working when:
- ✅ Install button appears (bottom-right)
- ✅ Console shows "beforeinstallprompt event fired"
- ✅ Lighthouse PWA score > 90
- ✅ Can install from browser menu
- ✅ App appears in applications list after install

---

## Next Steps After Installing

1. Test offline functionality
2. Check app icon on home screen
3. Verify app opens in standalone mode
4. Test all features work as installed app
5. Share with users!

---

*If you're still having issues, check the browser console for specific error messages and refer to the error message section above.*
