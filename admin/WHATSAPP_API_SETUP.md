# WhatsApp Business API Setup Guide

## Overview
The ID card system now supports automatic PDF attachment via WhatsApp Business API.

## Current Functionality
✅ Domain updated to **electrozot.in**
✅ PDF and Image files are saved on server
✅ WhatsApp opens with welcome message and download links
✅ Ready for WhatsApp Business API integration

## How It Works Now

### Without WhatsApp Business API (Current)
1. Admin generates ID card
2. System saves PDF and Image files
3. WhatsApp opens with message containing download links
4. Admin manually downloads PDF and attaches it in WhatsApp

### With WhatsApp Business API (Optional Enhancement)
1. Admin generates ID card
2. System saves PDF and Image files
3. **PDF is automatically sent via WhatsApp Business API**
4. Technician receives PDF directly in WhatsApp

## Setup WhatsApp Business API (Optional)

### Step 1: Get WhatsApp Business API Access
1. Go to [Meta for Developers](https://developers.facebook.com/)
2. Create a Business App
3. Add WhatsApp product to your app
4. Complete business verification

### Step 2: Get API Credentials
You need two things:
- **Access Token**: From your WhatsApp Business App settings
- **Phone Number ID**: Your WhatsApp Business phone number ID

### Step 3: Configure in Code
Edit `admin/api-send-id-card-whatsapp.php`:

```php
// Line ~70-71: Add your credentials
$whatsapp_api_token = 'YOUR_ACCESS_TOKEN_HERE';
$whatsapp_phone_id = 'YOUR_PHONE_NUMBER_ID_HERE';
```

### Step 4: Test
1. Generate an ID card
2. Click "Send to WhatsApp"
3. If configured correctly, PDF will be sent automatically
4. Check response message for confirmation

## File Locations

### Saved Files
- **Location**: `/uploads/id_cards/`
- **Format**: `ID_Card_{phone}_{timestamp}.png` and `.pdf`
- **Access**: Via direct URL (e.g., `https://electrozot.in/uploads/id_cards/...`)

### Key Files
- `admin/admin-generate-id-card.php` - ID card generator interface
- `admin/api-send-id-card-whatsapp.php` - WhatsApp sending logic
- `uploads/id_cards/` - Saved ID card files

## Message Format

The WhatsApp message includes:
- Welcome greeting with technician name
- Welcome from Mohit Choudhary
- PDF download link
- Image download link
- Contact information
- Website: www.electrozot.in

## Troubleshooting

### PDF Not Sending Automatically
- Check if API credentials are configured
- Verify WhatsApp Business API is active
- Check server logs for API errors
- Ensure PDF URL is publicly accessible

### Files Not Saving
- Check `/uploads/id_cards/` directory exists
- Verify write permissions (777)
- Check PHP upload limits

### WhatsApp Not Opening
- Verify phone number is 10 digits
- Check browser popup blocker
- Ensure WhatsApp Web is accessible

## Security Notes

⚠️ **Important**:
- Keep API tokens secure
- Don't commit tokens to version control
- Use environment variables for production
- Restrict file upload directory permissions
- Validate phone numbers before sending

## Support

For issues or questions:
- Contact: 7559606925
- Website: www.electrozot.in
