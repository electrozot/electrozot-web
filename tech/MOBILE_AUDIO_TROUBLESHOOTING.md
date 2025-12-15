# 📱 Mobile Audio Troubleshooting Guide

## 🚨 **Common Mobile Audio Issues & Solutions**

### Issue 1: "Sound not playing on mobile devices"

#### **Causes:**
- Mobile browsers block autoplay audio until user interaction
- Audio context is suspended on mobile devices
- Device is in silent/vibrate mode
- Browser audio permissions not granted

#### **Solutions:**
1. **Enable Audio Properly:**
   - Visit `tech/notification-settings.php`
   - Click "Enable Sound" button
   - Ensure you see "Sound Enabled Forever!" message

2. **Check Device Settings:**
   - Turn off silent mode
   - Increase media volume (not ringer volume)
   - Check if "Do Not Disturb" is enabled

3. **Browser-Specific Fixes:**
   - **Chrome Android**: Enable "Site notifications" in browser settings
   - **Safari iOS**: Check Settings > Safari > Camera & Microphone Access
   - **Firefox Mobile**: Enable audio autoplay in about:config

### Issue 2: "Sound not working when phone is locked"

#### **Causes:**
- Browser notifications disabled
- Service worker not registered
- Audio context suspended in background

#### **Solutions:**
1. **Enable Browser Notifications:**
   ```javascript
   // Check permission status
   console.log('Notification permission:', Notification.permission);
   
   // Request permission if needed
   if (Notification.permission === 'default') {
       Notification.requestPermission();
   }
   ```

2. **Verify Service Worker:**
   - Visit `tech/test-mobile-audio.php`
   - Click "Test Service Worker"
   - Should show "Service Worker is registered"

3. **Test Background Notifications:**
   - Enable notifications in browser
   - Lock your phone
   - Have someone create a test booking
   - Should receive notification with sound

### Issue 3: "Notifications work but no sound"

#### **Causes:**
- Audio files not accessible
- Web Audio API not working
- Multiple audio contexts conflicting

#### **Solutions:**
1. **Test Audio Files:**
   ```bash
   # Check if sound file exists
   curl -I https://your-domain.com/admin/vendor/sounds/arived.mp3
   ```

2. **Use Multiple Audio Methods:**
   - Traditional HTML5 Audio API
   - Web Audio API (better mobile support)
   - Browser notification sounds

3. **Debug Audio Context:**
   ```javascript
   // Check audio context state
   console.log('Audio context state:', audioContext.state);
   
   // Resume if suspended
   if (audioContext.state === 'suspended') {
       audioContext.resume();
   }
   ```

## 🔧 **Testing Tools**

### Quick Tests
1. **`tech/test-mobile-audio.php`** - Comprehensive mobile audio testing
2. **`tech/notification-settings.php`** - Settings management
3. **`tech/test-notification-sound.php`** - Simple sound test

### Browser Console Tests
```javascript
// Test 1: Check if audio can be created
const audio = new Audio('../admin/vendor/sounds/arived.mp3');
console.log('Audio created:', audio);

// Test 2: Check Web Audio API
const AudioContext = window.AudioContext || window.webkitAudioContext;
const ctx = new AudioContext();
console.log('Audio context state:', ctx.state);

// Test 3: Check notification permission
console.log('Notification permission:', Notification.permission);

// Test 4: Check service worker
navigator.serviceWorker.getRegistration().then(reg => {
    console.log('Service worker registered:', !!reg);
});
```

## 📱 **Device-Specific Solutions**

### iPhone/iPad (iOS)
1. **Settings > Safari > Advanced > Experimental Features**
   - Enable "Web Audio API"
   - Enable "Media Source Extensions"

2. **Settings > Notifications > Safari**
   - Allow notifications
   - Enable sounds

3. **iOS Silent Mode:**
   - Check the physical silent switch
   - iOS may not play notification sounds in silent mode

### Android Devices
1. **Chrome Settings:**
   - Site Settings > Notifications > Allow
   - Site Settings > Sound > Allow

2. **System Settings:**
   - Apps > Chrome > Notifications > Allow
   - Sound > Media volume (increase)

3. **Battery Optimization:**
   - Settings > Battery > Battery Optimization
   - Find Chrome/Browser > Don't optimize

### Samsung Internet
1. **Settings > Sites and downloads > Notifications**
   - Allow for your domain

2. **Smart Manager:**
   - Disable battery optimization for browser

## 🛠️ **Advanced Troubleshooting**

### Check Audio Context State
```javascript
function checkAudioContext() {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (!AudioContext) {
        console.error('Web Audio API not supported');
        return;
    }
    
    const ctx = new AudioContext();
    console.log('Audio Context State:', ctx.state);
    console.log('Sample Rate:', ctx.sampleRate);
    console.log('Base Latency:', ctx.baseLatency);
    
    if (ctx.state === 'suspended') {
        console.log('Attempting to resume audio context...');
        ctx.resume().then(() => {
            console.log('Audio context resumed successfully');
        }).catch(err => {
            console.error('Failed to resume audio context:', err);
        });
    }
}
```

### Test Multiple Audio Methods
```javascript
function testAllAudioMethods() {
    // Method 1: HTML5 Audio
    const audio1 = new Audio('../admin/vendor/sounds/arived.mp3');
    audio1.play().then(() => {
        console.log('✅ HTML5 Audio works');
    }).catch(err => {
        console.log('❌ HTML5 Audio failed:', err.message);
    });
    
    // Method 2: Web Audio API
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (AudioContext) {
        const ctx = new AudioContext();
        fetch('../admin/vendor/sounds/arived.mp3')
            .then(response => response.arrayBuffer())
            .then(data => ctx.decodeAudioData(data))
            .then(buffer => {
                const source = ctx.createBufferSource();
                source.buffer = buffer;
                source.connect(ctx.destination);
                source.start(0);
                console.log('✅ Web Audio API works');
            })
            .catch(err => {
                console.log('❌ Web Audio API failed:', err.message);
            });
    }
    
    // Method 3: Notification Sound
    if (Notification.permission === 'granted') {
        new Notification('Test', {
            body: 'Testing notification sound',
            silent: false
        });
        console.log('✅ Notification sound sent');
    }
}
```

### Monitor Background Activity
```javascript
// Track page visibility
document.addEventListener('visibilitychange', function() {
    console.log('Page hidden:', document.hidden);
    if (document.hidden) {
        console.log('📱 App went to background - notifications should continue via Service Worker');
    } else {
        console.log('📱 App returned to foreground - resuming normal operation');
    }
});

// Track focus/blur
window.addEventListener('focus', () => console.log('📱 App gained focus'));
window.addEventListener('blur', () => console.log('📱 App lost focus'));
```

## 📊 **Success Metrics**

After implementing fixes, you should achieve:
- ✅ **Sound plays immediately** when notifications arrive
- ✅ **Works when phone is locked** (via browser notifications)
- ✅ **Works in background** (via Service Worker)
- ✅ **No repeated permission prompts**
- ✅ **Consistent across all mobile browsers**

## 🆘 **Still Having Issues?**

### Diagnostic Steps:
1. Run `tech/test-mobile-audio.php` - should show 80%+ success rate
2. Check browser console for error messages
3. Test with different browsers (Chrome, Safari, Firefox)
4. Test on different devices (Android, iPhone)
5. Verify sound file is accessible: `https://your-domain.com/admin/vendor/sounds/arived.mp3`

### Common Error Messages:
- **"NotAllowedError"** → User interaction required, show enable prompt
- **"NotSupportedError"** → Audio format not supported, check file
- **"AbortError"** → Audio playback interrupted, retry
- **"NetworkError"** → Sound file not accessible, check path

### Last Resort Solutions:
1. **Replace sound file** with different format (MP3, WAV, OGG)
2. **Use shorter sound file** (< 2 seconds)
3. **Enable vibration as backup** for silent devices
4. **Use visual notifications** as fallback

---

**Remember**: Mobile audio restrictions are designed for user experience. The key is to work WITH the browser's policies, not against them. Always require user interaction before enabling audio, and provide multiple notification methods (sound + vibration + visual) for the best user experience.