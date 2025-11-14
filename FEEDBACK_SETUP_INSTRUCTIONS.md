# Feedback Management System - Setup Instructions

## Features Implemented

### Admin Dashboard Features:
1. **Add Feedback** - Admin can add new testimonials with or without photos
2. **Manage Feedbacks** - View all feedbacks in a table with edit/delete options
3. **Edit Feedback** - Update feedback content, change/remove photos, change status
4. **Delete Feedback** - Remove feedbacks and associated photos
5. **Photo Support** - Upload client photos (JPG, PNG, GIF)

### Homepage Features:
1. **Auto-Sliding Testimonials** - Testimonials slide from right to left
2. **7-Second Interval** - Each testimonial transitions every 7 seconds
3. **Seamless Loop** - Continuous sliding without gaps
4. **Photo Display** - Shows client photos if available, otherwise shows initials
5. **Hover to Pause** - Slider pauses when user hovers over it

## Setup Steps

### Step 1: Run Database Setup
Visit: `admin/setup-feedback-photo.php`
- This will add the `f_photo` column to the `tms_feedback` table
- Creates the feedbacks directory for storing images

### Step 2: Access Admin Panel
Navigate to: **Admin Dashboard > Feedbacks**

You'll see these options:
- **Add Feedback** - Create new testimonials
- **Manage Feedbacks** - Edit/Delete existing feedbacks
- **View All** - See all feedbacks
- **Publish** - Change feedback status

### Step 3: Add Testimonials
1. Go to "Add Feedback"
2. Fill in:
   - Client Name (required)
   - Feedback Content (required)
   - Client Photo (optional)
   - Status (Published/Pending)
3. Click "Add Feedback"

### Step 4: Manage Existing Feedbacks
1. Go to "Manage Feedbacks"
2. You can:
   - Edit any feedback
   - Delete feedbacks
   - Change photos
   - Update status

## File Structure

### Admin Files Created:
- `admin/admin-add-feedback.php` - Add new feedbacks
- `admin/admin-manage-feedback.php` - Manage all feedbacks
- `admin/admin-edit-feedback.php` - Edit existing feedbacks
- `admin/setup-feedback-photo.php` - Database setup script

### Frontend Files Modified:
- `index.php` - Updated testimonials section with auto-sliding
- `vendor/css/custom.css` - Added sliding animation styles

### Image Storage:
- Photos stored in: `vendor/img/feedbacks/`

## Database Changes

### New Column Added:
```sql
ALTER TABLE tms_feedback ADD COLUMN f_photo VARCHAR(255) DEFAULT NULL;
```

## Features Details

### Auto-Sliding Animation:
- Direction: Right to Left
- Speed: 7 seconds per testimonial
- Animation: Smooth CSS animation
- Responsive: Works on all screen sizes
- Pause on Hover: Yes

### Photo Management:
- Upload: Supports JPG, PNG, GIF
- Size: Displayed as 60x60px circle
- Fallback: Shows first letter of name if no photo
- Delete: Removes photo file when feedback is deleted
- Replace: Can upload new photo to replace existing

## Usage Tips

1. **Add Multiple Feedbacks**: The slider works best with 4+ testimonials
2. **Photo Quality**: Use square images for best results
3. **Content Length**: Keep feedback text concise (2-3 sentences)
4. **Status Control**: Use "Pending" for review, "Published" to show on homepage
5. **Hover Effect**: Users can pause the slider by hovering over it

## Troubleshooting

### Slider Not Moving:
- Check if you have at least 2 published feedbacks
- Clear browser cache
- Check browser console for JavaScript errors

### Photos Not Uploading:
- Ensure `vendor/img/feedbacks/` directory exists
- Check directory permissions (should be 0777)
- Verify file size and format

### Photos Not Displaying:
- Check file path in database
- Verify image file exists in feedbacks directory
- Check image file permissions

## Support

For any issues or questions, check:
1. Browser console for errors
2. PHP error logs
3. Database connection
4. File permissions

---
**System Ready!** Your feedback management system with auto-sliding testimonials is now fully functional.
