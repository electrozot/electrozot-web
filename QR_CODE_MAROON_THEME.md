# QR Code with Dark Maroon Theme

## Implementation

### QR Code Specifications

**Color Scheme:**
- **Dark Color**: #8b0000 (Dark Maroon) - Matches ID card gradient
- **Light Color**: #ffffff (White) - Clean background
- **Size**: 90x90 pixels
- **Error Correction**: High (Level H)

**Position:**
- **Location**: Right side of ID card info section
- **Adjacent to**: Technician details (left side)
- **Container**: White background with padding and shadow
- **Label**: "Scan to Visit electrozot.in"

### Layout Structure

```
┌─────────────────────────────────────────┐
│  PHOTO (Center)                         │
├─────────────────────────────────────────┤
│ ┌──────────────────┐  ┌──────────────┐ │
│ │ NAME             │  │              │ │
│ │ EMPLOYEE ID      │  │   QR CODE    │ │
│ │ EMAIL            │  │   (Maroon)   │ │
│ │ CATEGORY         │  │              │ │
│ │ SPECIALIZATION   │  │ Scan to Visit│ │
│ │ EXPERIENCE       │  │ electrozot.in│ │
│ │ SERVICE AREA     │  └──────────────┘ │
│ │ ADDRESS          │                   │
│ └──────────────────┘                   │
└─────────────────────────────────────────┘
```

### Flexbox Layout

```css
display: flex;
gap: 10px;
```

**Left Side (flex: 1):**
- All technician details
- Full width available
- Stacked vertically

**Right Side (fixed width):**
- QR code container
- Centered alignment
- White background box
- Label below QR

### QR Code Container Styling

```css
background: white;
padding: 8px;
border-radius: 8px;
box-shadow: 0 2px 8px rgba(0,0,0,0.15);
```

### Label Styling

```css
margin-top: 5px;
font-size: 7px;
color: #666;
text-align: center;
font-weight: 600;
```

## QR Code Generation

### Library Used
**QRCode.js** - Lightweight JavaScript QR code generator

### CDN Link
```html
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
```

### Generation Code
```javascript
var qrcode = new QRCode(document.getElementById("qrcode"), {
    text: "https://electrozot.in",
    width: 90,
    height: 90,
    colorDark: "#8b0000",  // Dark maroon
    colorLight: "#ffffff",  // White
    correctLevel: QRCode.CorrectLevel.H  // High error correction
});
```

## Color Matching

### ID Card Theme Colors
- **Primary**: #dc143c (Crimson)
- **Secondary**: #8b0000 (Dark Red/Maroon)
- **Gradient**: 135deg, #dc143c 0%, #8b0000 100%

### QR Code Color
- **Selected**: #8b0000 (Dark Maroon)
- **Reason**: Matches the darker end of card gradient
- **Contrast**: High contrast with white background
- **Scanability**: Excellent (dark on light)

## Error Correction Levels

**Level H (High) - 30% recovery**
- Best for important QR codes
- Can recover even if 30% damaged
- Slightly larger QR pattern
- More reliable scanning

## Scanning Experience

### When Scanned:
1. User opens camera/QR scanner
2. Points at QR code
3. Detects: https://electrozot.in
4. Opens company website
5. User can verify technician/company

### Use Cases:
- **Customer Verification**: Scan to verify company
- **Quick Contact**: Access company website
- **Service Booking**: Direct link to services
- **Company Info**: About, contact, services
- **Trust Building**: Professional appearance

## Responsive Design

### Desktop View:
- QR code on right side
- Full details on left
- Balanced layout

### Print View:
- QR code maintains size
- Scannable from printed card
- High contrast for printing

### Mobile View:
- Layout adapts
- QR code remains visible
- Touch-friendly size

## Print Quality

### QR Code:
- **Size**: 90x90 pixels
- **DPI**: 300+ recommended
- **Format**: Vector-based (SVG alternative available)
- **Scan Distance**: Up to 30cm

### Color Accuracy:
- **Dark Maroon**: #8b0000
- **CMYK**: C:0 M:100 Y:100 K:45
- **Pantone**: Similar to Pantone 188 C

## Accessibility

### Visual:
- High contrast (dark on light)
- Clear borders
- Adequate size
- Label for context

### Functional:
- Works with all QR scanners
- Works with phone cameras
- Works with dedicated apps
- Cross-platform compatible

## Testing

### Scan Test:
1. Generate ID card
2. Display on screen
3. Scan with phone
4. Verify opens: https://electrozot.in
5. Test from different angles
6. Test from different distances

### Print Test:
1. Print ID card
2. Scan printed QR code
3. Verify functionality
4. Check color accuracy
5. Test durability

## Benefits

✅ **Brand Consistency**: Matches card theme
✅ **Professional**: Clean, modern look
✅ **Functional**: Direct website access
✅ **Verification**: Customers can verify company
✅ **Marketing**: Drives traffic to website
✅ **Trust**: Shows legitimacy
✅ **Convenience**: Quick access to info

## Alternative Colors

If dark maroon doesn't scan well:
- **Option 1**: #000000 (Pure black) - Best contrast
- **Option 2**: #4a0000 (Darker maroon) - More contrast
- **Option 3**: #dc143c (Crimson) - Brighter, matches primary

## Future Enhancements

1. **Dynamic QR**: Include technician ID in URL
2. **Analytics**: Track QR scans
3. **Deep Links**: Direct to technician profile
4. **vCard**: Contact information QR
5. **Multi-QR**: Different QRs for different purposes

---

**Created**: November 17, 2025
**Status**: ✅ Implemented
**Color**: #8b0000 (Dark Maroon)
**Target**: https://electrozot.in
