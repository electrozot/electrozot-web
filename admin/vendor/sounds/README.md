# Notification Sounds Directory

This directory contains audio files for notification sounds.

## Required Files

- `arived.mp3` - Main notification sound for new bookings (dashboard)
- `notification.mp3` - Alternative notification sound (notifications page)

## Fallback System

If custom sound files are not available, the system will automatically use Web Audio API to generate notification beeps.

### Fallback Features:
- Two-tone beep sound (800Hz and 1000Hz)
- Pleasant sine wave tones
- No external files required
- Works in all modern browsers

## Adding Custom Sounds

1. Place your MP3 files in this directory
2. Ensure files are named correctly:
   - `arived.mp3` for dashboard
   - `notification.mp3` for notifications page
3. Recommended format: MP3, 44.1kHz, 128kbps
4. Keep file size small (< 100KB) for fast loading

## Testing

The notification system will:
1. Try to load and play custom sound
2. If custom sound fails, automatically use Web API beep
3. Log the result in browser console

## Browser Support

- Custom sounds: All modern browsers
- Web API fallback: Chrome, Firefox, Safari, Edge (latest versions)
- Requires user interaction before first sound (browser security policy)
