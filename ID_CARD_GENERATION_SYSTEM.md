# Technician ID Card Generation System

## Overview
A complete system for generating professional ID cards for technicians, accessible from admin settings.

## Features

### 1. **Search Technician by Mobile**
- Enter 10-digit mobile number
- Auto-fetches technician details
- Shows error if not found

### 2. **ID Card Design**
Based on the provided image with:
- ✅ Red gradient background (crimson to dark red)
- ✅ Electrozot logo at top
- ✅ Company name "ELECTROZOT"
- ✅ Tagline "We Make Perfect"
- ✅ White text throughout
- ✅ Photo in white rounded container (right side)
- ✅ Technician details in white info box
- ✅ Professional footer with contact info

### 3. **ID Card Information**
- Technician Name
- Employee ID (EZ ID)
- Mobile Number
- Service Category
- Photo (if available)
- Company contact details

### 4. **Action Buttons**

#### Download as Image
- Downloads ID card as PNG
- High resolution (2x scale)
- Filename: `ID_Card_[Name].png`

#### Download as PDF
- Converts ID card to PDF
- A4 format, centered
- Filename: `ID_Card_[Name].pdf`

#### Send to WhatsApp
- Saves ID card to server
- Opens WhatsApp with pre-filled message
- Sends download link to technician
- Message includes greeting and download link

#### Delete
- Clears current ID card
- Returns to search form
- Confirmation dialog

## File Structure

```
admin/
├── admin-generate-id-card.php    # Main ID card generation page
├── api-send-id-card-whatsapp.php # WhatsApp sending API
└── vendor/inc/sidebar.php        # Updated with ID card link

uploads/
└── id_cards/                     # Stored ID card images
```

## Access

**Location**: Admin Panel → Settings → Generate ID Card

## How to Use

### Step 1: Access the Page
1. Login to admin panel
2. Click "Settings" in sidebar
3. Click "Generate ID Card"

### Step 2: Search Technician
1. Enter technician's 10-digit mobile number
2. Click "Search Technician"
3. System fetches technician details

### Step 3: Preview ID Card
- ID card displays with all details
- Photo shows if available
- All information formatted professionally

### Step 4: Take Action

**Option A: Download as Image**
- Click "Download as Image"
- PNG file downloads automatically
- High quality, ready to print

**Option B: Download as PDF**
- Click "Download as PDF"
- PDF file downloads automatically
- Professional format

**Option C: Send to WhatsApp**
- Click "Send to WhatsApp"
- ID card saved to server
- WhatsApp opens with message
- Technician receives download link

**Option D: Delete/Clear**
- Click "Delete"
- Confirm action
- Returns to search form

## ID Card Design Specifications

### Dimensions
- Width: 400px
- Height: 600px
- Border Radius: 20px

### Colors
- Background: Linear gradient (#dc143c to #8b0000)
- Text: White (#ffffff)
- Info Box: White with 95% opacity
- Footer: Black with 30% opacity

### Sections

#### Header
- Logo: 120px width
- Company Name: 24px, bold
- Tagline: 14px, italic

#### Body
- Photo Container: 150x150px, white border
- Info Box: White background, rounded corners
- Fields: Label (11px) + Value (16px, bold)

#### Footer
- Contact information
- Website
- "Authorized Technician" text

## WhatsApp Integration

### Message Format
```
Hello [Name],

Your Electrozot Technician ID Card is ready!

Please download your ID card from the link below:
[Download Link]

Thank you for being part of Team Electrozot!

- Electrozot Management
```

### Phone Number Format
- Input: 10 digits (e.g., 9876543210)
- WhatsApp: +91 prefix added (919876543210)
- Opens WhatsApp Web/App automatically

## Technical Details

### Libraries Used
- **html2canvas**: Converts HTML to canvas/image
- **jsPDF**: Generates PDF from canvas
- **SweetAlert**: Beautiful alert dialogs

### Image Generation
```javascript
html2canvas(card, {
    scale: 2,              // High resolution
    backgroundColor: null,  // Transparent background
    logging: false         // No console logs
})
```

### PDF Generation
```javascript
const pdf = new jsPDF({
    orientation: 'portrait',
    unit: 'mm',
    format: 'a4'
});
```

## Database Requirements

### Technician Table Columns
- `t_phone` - Mobile number (search key)
- `t_name` - Full name
- `t_ez_id` or `t_id_no` - Employee ID
- `t_category` - Service category
- `t_pic` - Photo filename (optional)

## File Storage

### Upload Directory
```
uploads/id_cards/
```

### Filename Format
```
ID_Card_[Phone]_[Timestamp].png
```

### Example
```
ID_Card_9876543210_1700123456.png
```

## Security

✅ Session validation (admin only)
✅ Phone number validation (10 digits)
✅ File upload validation
✅ SQL injection prevention (prepared statements)
✅ XSS prevention (htmlspecialchars)

## Error Handling

### No Technician Found
```
"No technician found with this phone number."
```

### Invalid Phone Number
```
"Please enter a valid 10-digit phone number."
```

### Download Failed
```
"Failed to download ID card"
```

### WhatsApp Send Failed
```
"Failed to send ID card to WhatsApp"
```

## Browser Compatibility

✅ Chrome/Edge (Recommended)
✅ Firefox
✅ Safari
✅ Mobile browsers

## Printing

### Print Settings
- Paper: A4 or Letter
- Orientation: Portrait
- Scale: Fit to page
- Margins: Default

### Best Practice
1. Download as PDF first
2. Open PDF
3. Print from PDF viewer
4. Better quality and formatting

## Customization

### Change Colors
Edit CSS in `admin-generate-id-card.php`:
```css
.id-card {
    background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%);
}
```

### Change Logo
Replace logo path:
```html
<img src="../vendor/EZlogonew.png" alt="Electrozot Logo">
```

### Change Tagline
Edit text:
```html
<div class="id-card-tagline">We Make Perfect</div>
```

## Troubleshooting

### ID Card Not Generating
1. Check if html2canvas library loaded
2. Check browser console for errors
3. Ensure technician has valid data

### WhatsApp Not Opening
1. Check if phone number is valid
2. Ensure WhatsApp installed/accessible
3. Check popup blocker settings

### Photo Not Showing
1. Verify photo file exists in `/vendor/img/`
2. Check file permissions
3. Ensure correct filename in database

## Future Enhancements

1. **QR Code**: Add QR code with technician details
2. **Barcode**: Add barcode for scanning
3. **Batch Generation**: Generate multiple ID cards at once
4. **Templates**: Multiple ID card designs
5. **Email**: Send ID card via email
6. **Expiry Date**: Add validity period
7. **Digital Signature**: Add authorized signature
8. **Lamination Guide**: Print with lamination marks

---

**Created**: November 17, 2025
**Status**: ✅ Fully Functional
**Version**: 1.0
