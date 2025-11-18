# Photo Update Fix - Prevent Photo Removal on Update

## Problem
When updating technician details without uploading a new photo, the existing photo was being removed/deleted from the database.

## Root Cause
The update query was always trying to update the photo field, even when no new photo was uploaded. This resulted in an empty value being saved, effectively removing the existing photo.

### Before (Buggy Code):
```php
$t_pic = $_FILES["t_pic"]["name"];
move_uploaded_file($_FILES["t_pic"]["tmp_name"], "../vendor/img/" . $_FILES["t_pic"]["name"]);

$query = "UPDATE tms_technician SET t_name=?, t_pic=?, ... WHERE t_id = ?";
$stmt->bind_param('ss...', $t_name, $t_pic, ...);
```

**Issue:** If no file is uploaded, `$_FILES["t_pic"]["name"]` is empty, so `$t_pic` becomes empty string, overwriting the existing photo.

## Solution
Check if a new photo is uploaded before updating. If no new photo, exclude the photo field from the update query.

### After (Fixed Code):
```php
if(!empty($_FILES["t_pic"]["name"])) {
    // New photo uploaded - update with new photo
    $t_pic = $_FILES["t_pic"]["name"];
    move_uploaded_file($_FILES["t_pic"]["tmp_name"], "../vendor/img/" . $t_pic);
    
    $query = "UPDATE tms_technician SET t_name=?, t_pic=?, ... WHERE t_id = ?";
    $stmt->bind_param('ss...', $t_name, $t_pic, ...);
} else {
    // No new photo - update without changing photo
    $query = "UPDATE tms_technician SET t_name=?, ... WHERE t_id = ?";
    $stmt->bind_param('s...', $t_name, ...);
}
```

## Files Fixed

### ✅ admin/admin-manage-single-technician.php
- **Issue:** Photo removed when updating technician details
- **Fix:** Conditional update - only update photo if new file uploaded
- **Status:** FIXED

### ✅ admin/admin-profile.php
- **Status:** Already correct - keeps existing photo by default
- **Code:** `$a_photo = $admin->a_photo;` before checking for new upload

### ✅ usr/user-update-profile.php
- **Status:** No photo field - no issue

### ✅ admin/admin-manage-single-usr.php
- **Status:** No photo field - no issue

## How It Works Now

### Scenario 1: Update Without New Photo
```
User updates technician name
    ↓
No file selected in photo field
    ↓
Check: $_FILES["t_pic"]["name"] is empty
    ↓
Use UPDATE query WITHOUT photo field
    ↓
Existing photo remains unchanged ✅
```

### Scenario 2: Update With New Photo
```
User updates technician name + uploads new photo
    ↓
File selected in photo field
    ↓
Check: $_FILES["t_pic"]["name"] has value
    ↓
Upload new photo to server
    ↓
Use UPDATE query WITH photo field
    ↓
New photo replaces old photo ✅
```

## Technical Details

### Conditional Query Logic
```php
if(!empty($_FILES["t_pic"]["name"])) {
    // Query includes t_pic field
    $query = "UPDATE tms_technician SET 
              t_name=?, t_id_no=?, t_specialization=?, 
              t_category=?, t_experience=?, t_pic=?, 
              t_status=?, t_booking_limit=? 
              WHERE t_id = ?";
    // 9 parameters (8 fields + 1 WHERE)
    $stmt->bind_param('sssssssii', ...);
} else {
    // Query excludes t_pic field
    $query = "UPDATE tms_technician SET 
              t_name=?, t_id_no=?, t_specialization=?, 
              t_category=?, t_experience=?, 
              t_status=?, t_booking_limit=? 
              WHERE t_id = ?";
    // 8 parameters (7 fields + 1 WHERE)
    $stmt->bind_param('ssssssii', ...);
}
```

### File Upload Check
```php
!empty($_FILES["t_pic"]["name"])
```

This checks if:
- File input field has a value
- User actually selected a file
- File name is not empty

## Benefits

✅ **Preserves Existing Photos** - Photos not removed accidentally
✅ **Optional Photo Update** - Admin can update other fields without touching photo
✅ **Better User Experience** - No need to re-upload photo every time
✅ **Data Integrity** - Existing data preserved when not explicitly changed
✅ **Flexible Updates** - Update only what needs to be updated

## Best Practices Applied

1. **Check Before Update** - Always verify if new data exists before overwriting
2. **Conditional Queries** - Use different queries based on what's being updated
3. **Preserve Existing Data** - Don't overwrite unless explicitly changed
4. **File Upload Validation** - Check if file actually uploaded before processing

## Testing Checklist

- [x] Update technician name only - photo remains
- [x] Update technician category only - photo remains
- [x] Update technician with new photo - photo changes
- [x] Update multiple fields without photo - photo remains
- [x] Update all fields including photo - all update correctly

## Similar Pattern in Other Files

### Admin Profile (Already Correct)
```php
$a_photo = $admin->a_photo; // Keep existing

if(isset($_FILES['a_photo']) && $_FILES['a_photo']['error'] == 0) {
    // Upload new photo
    $a_photo = $new_filename;
}

// Always update, but with existing or new photo
UPDATE tms_admin SET a_photo = ?
```

This is another valid approach - always include the field but use existing value if no new upload.

## Future Improvements

- Add photo preview before upload
- Allow photo removal (separate checkbox)
- Validate photo file type and size
- Compress photos before saving
- Generate thumbnails automatically
- Store photos in organized folders by date
