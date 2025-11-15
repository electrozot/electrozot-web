# How to Create PWA Icons

## Quick Guide

### Option 1: Online Generator (Easiest)
1. Go to https://realfavicongenerator.net/
2. Upload your logo (at least 512x512px)
3. Configure settings
4. Download generated icons
5. Extract to `vendor/img/icons/`

### Option 2: PWA Builder
1. Go to https://www.pwabuilder.com/imageGenerator
2. Upload your logo
3. Select "Generate"
4. Download ZIP
5. Extract to `vendor/img/icons/`

### Option 3: Manual Creation
Use Photoshop, GIMP, or Figma to create:
- icon-72x72.png
- icon-96x96.png
- icon-128x128.png
- icon-144x144.png
- icon-152x152.png
- icon-192x192.png
- icon-384x384.png
- icon-512x512.png

## Icon Design Tips

### Best Practices
✅ Use simple, recognizable design  
✅ High contrast colors  
✅ Avoid text (hard to read at small sizes)  
✅ Use brand colors  
✅ Test at different sizes  
✅ PNG format with transparency  

### Avoid
❌ Complex details  
❌ Thin lines  
❌ Small text  
❌ Low contrast  
❌ Gradients (can look bad at small sizes)  

## Temporary Solution

If you don't have icons yet, you can use a placeholder:

### Create Simple Icon with Text
1. Open any image editor
2. Create 512x512px canvas
3. Fill with brand color (#667eea)
4. Add large "E" or "EZ" in white
5. Save as PNG
6. Resize to all required sizes

### Or Use Online Tool
https://favicon.io/favicon-generator/
- Enter "EZ" or "E"
- Choose colors
- Download and resize

## File Structure

```
vendor/
└── img/
    └── icons/
        ├── icon-72x72.png
        ├── icon-96x96.png
        ├── icon-128x128.png
        ├── icon-144x144.png
        ├── icon-152x152.png
        ├── icon-192x192.png
        ├── icon-384x384.png
        └── icon-512x512.png
```

## Testing Icons

1. Open Chrome DevTools (F12)
2. Go to Application tab
3. Click "Manifest" in sidebar
4. Check if all icons load
5. Look for errors

## Note

The PWA will work without icons, but:
- Install prompt may not show
- App icon will be generic
- Less professional appearance

**Create icons before production deployment!**
