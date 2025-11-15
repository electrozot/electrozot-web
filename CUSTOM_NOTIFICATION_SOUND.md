# Custom Notification Sound Setup

## Overview
The notification system now uses your custom `arived.mp3` sound file instead of the default beep sounds.

---

## File Location

### Required Path
```
admin/vendor/sounds/arived.mp3
```

### Directory Structure
```
admin/
└── vendor/
    └── sounds/
        └── arived.mp3  ← Your sound file here
```

---

## Setup Instructions

### Step 1: Create Sounds Directory
If the directory doesn't exist, create it:

**Windows (Command Prompt):**
```cmd
cd admin\vendor
mkdir sounds
```

**Windows (PowerShell):**
```powershell
New-Item -ItemType Directory -Path "admin\vendor\sounds"
```

**Linux/Mac:**
```bash
mkdir -p admin/vendor/sounds
```

### Step 2: Place Sound File
Copy your `arived.mp3` file to:
```
admin/vendor/sounds/arived.mp3
```

### Step 3: Verify File Exists
Check if file is accessible by opening in browser:
```
http://yoursite/admin/vendor/sounds/arived.mp3
```

Should play the sound or download the file.

---

## Sound Configuration

### Current Settings
```javascript
const notificationAudio = new Audio('vendor/sounds/arived.mp3');
notificationAudio.volume = 0.7; // 70% volume
```

### Adjust Volume
Change volume (0.0 to 1.0):

**In `admin/vendor/inc/notification-system.php`:**
```javascript
notificationAudio.volume = 0.5; // 50% volume
notificationAudio.volume = 1.0; // 100% volume (max)
notificationAudio.volume = 0.3; // 30% volume (quiet)
```

**In `admin/admin-dashboard.php`:**
Same as above - change both files for consistency.

---

## Supported Audio Formats

### Best Format: MP3
✅ **MP3** - Supported by all browsers  
✅ **OGG** - Good alternative  
✅ **WAV** - Larger file size  
⚠️ **M4A** - Limited support  

### If Using Different Format
Update the file path:
```javascript
const notificationAudio = new Audio('vendor/sounds/arived.ogg');
// or
const notificationAudio = new Audio('vendor/sounds/arived.wav');
```

---

## Testing

### Test Sound Manually
1. Open admin dashboard
2. Open browser console (F12)
3. Type and press Enter:
```javascript
notificationAudio.play();
```

Should play your sound immediately.

### Test with Notification
1. Open admin page
2. Create a booking in another tab
3. Wait 10 seconds
4. Should hear your custom sound

---

## Troubleshooting

### Sound Not Playing

#### Issue 1: File Not Found (404)
**Check:**
- File exists at `admin/vendor/sounds/arived.mp3`
- File name is exactly `arived.mp3` (case-sensitive on Linux)
- File permissions are readable (644)

**Test:**
Open in browser: `http://yoursite/admin/vendor/sounds/arived.mp3`

#### Issue 2: Browser Autoplay Policy
**Cause:** Browsers block audio until user interaction

**Solution:**
- Click anywhere on the page first
- Sound will work after first interaction

**Console Message:**
```
💡 Tip: Click anywhere on the page first to enable audio
```

#### Issue 3: File Format Not Supported
**Check:**
- File is valid MP3
- Not corrupted
- Try converting to MP3 again

**Test:**
Play file in media player to verify it works.

#### Issue 4: Volume Too Low
**Check:**
- Browser volume not muted
- System volume not muted
- Volume setting in code (default 0.7 = 70%)

**Increase Volume:**
```javascript
notificationAudio.volume = 1.0; // Maximum
```

---

## Browser Console Messages

### Success
```
🔊 Notification sound played
```

### Error
```
❌ Error playing sound: [error details]
💡 Tip: Click anywhere on the page first to enable audio
```

---

## Advanced Configuration

### Play Sound Multiple Times
```javascript
function playNotificationSound() {
    notificationAudio.currentTime = 0;
    notificationAudio.play();
    
    // Play again after 1 second
    setTimeout(() => {
        notificationAudio.currentTime = 0;
        notificationAudio.play();
    }, 1000);
}
```

### Different Sounds for Different Events
```javascript
// New booking sound
const newBookingSound = new Audio('vendor/sounds/arived.mp3');

// Status update sound
const updateSound = new Audio('vendor/sounds/update.mp3');

// Use different sounds
if (type === 'new') {
    newBookingSound.play();
} else {
    updateSound.play();
}
```

### Fade In Effect
```javascript
notificationAudio.volume = 0;
notificationAudio.play();

// Fade in over 1 second
let volume = 0;
const fadeIn = setInterval(() => {
    if (volume < 0.7) {
        volume += 0.1;
        notificationAudio.volume = volume;
    } else {
        clearInterval(fadeIn);
    }
}, 100);
```

---

## File Size Recommendations

### Optimal Settings
- **Duration:** 1-3 seconds
- **Bitrate:** 128 kbps
- **File Size:** < 100 KB
- **Sample Rate:** 44.1 kHz

### Why?
- Faster loading
- Less bandwidth
- Quick playback
- Better performance

---

## Converting Audio Files

### Online Tools
- **Online Audio Converter:** https://online-audio-converter.com/
- **CloudConvert:** https://cloudconvert.com/
- **Convertio:** https://convertio.co/

### Desktop Tools
- **Audacity** (Free) - Export as MP3
- **VLC Media Player** - Convert/Save
- **FFmpeg** (Command line)

### FFmpeg Command
```bash
ffmpeg -i input.wav -b:a 128k -ar 44100 arived.mp3
```

---

## Security Considerations

### File Permissions
```bash
chmod 644 admin/vendor/sounds/arived.mp3
```

### Directory Permissions
```bash
chmod 755 admin/vendor/sounds
```

### .htaccess Protection (Optional)
Create `admin/vendor/sounds/.htaccess`:
```apache
# Allow only audio files
<FilesMatch "\.(mp3|ogg|wav)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Deny access to other files
<FilesMatch "^(?!.*\.(mp3|ogg|wav)$).*$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

---

## Performance

### Loading
- Audio file loaded once on page load
- Cached by browser
- Instant playback on subsequent plays

### Memory Usage
- ~100 KB for audio file
- Minimal memory footprint
- No performance impact

---

## Fallback Options

### If Sound File Missing
Add fallback to beep:
```javascript
function playNotificationSound() {
    notificationAudio.play()
        .catch((error) => {
            console.warn('Custom sound failed, using beep');
            // Fallback to Web Audio API beep
            const audioContext = new AudioContext();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            oscillator.frequency.value = 800;
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.5);
        });
}
```

---

## Summary

✅ **Custom sound:** `arived.mp3`  
✅ **Location:** `admin/vendor/sounds/`  
✅ **Volume:** 70% (adjustable)  
✅ **Format:** MP3 (best compatibility)  
✅ **Works on:** All admin pages  
✅ **Plays for:** New bookings & status updates  

**Your custom notification sound is now active!** 🔊✨

---

## Quick Checklist

- [ ] Created `admin/vendor/sounds/` directory
- [ ] Placed `arived.mp3` in sounds directory
- [ ] Verified file is accessible in browser
- [ ] Tested sound plays in console
- [ ] Tested with actual notification
- [ ] Adjusted volume if needed
- [ ] Checked browser console for errors

---

*Custom sound implemented: November 15, 2025*  
*Using: arived.mp3*
