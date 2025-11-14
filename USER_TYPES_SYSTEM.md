# User Registration Types System

## Overview
The system now tracks three types of users based on how they were created:

## User Types

### 1. **Admin Created** 
- Badge: Blue (Info)
- Icon: Shield
- Created by: Admin manually adding users
- Has password: Yes (set by admin)
- Can login: Yes

### 2. **Self Registered**
- Badge: Green (Success)  
- Icon: User Plus
- Created by: Users registering themselves via registration page
- Has password: Yes (set by user during registration)
- Can login: Yes

### 3. **Guest Users**
- Badge: Yellow/Orange (Warning)
- Icon: Clock
- Created by: Booking form submissions (no account)
- Has password: No (empty password)
- Can login: No (until password is assigned)

## Automatic Conversion

### Guest → Self Registered
When admin assigns a password to a Guest User:
1. Password is set
2. User type automatically changes from "Guest" to "Self Registered"
3. User can now login with their credentials
4. Badge changes from yellow to green

## Features in User Password Management

### Filters Available:
1. **All Users** - Show everyone
2. **Admin Created** - Only users created by admin
3. **Self Registered** - Users who registered themselves + converted guests
4. **Guest Users** - Users from booking form without passwords

### Search & Actions:
- Search by name, email, phone
- Filter by date (Today, This Week, This Month)
- View passwords (eye icon)
- Change passwords
- Delete single user
- Bulk delete multiple users

## Database Structure

### Column: `registration_type`
- Type: ENUM('admin', 'self', 'guest')
- Default: 'admin'
- Location: `tms_user` table

### Column: `created_at`
- Type: TIMESTAMP
- Default: CURRENT_TIMESTAMP
- Used for date filtering

## How It Works

### When Guest Books Service:
```php
// In process-guest-booking.php
INSERT INTO tms_user (..., registration_type) VALUES (..., 'guest')
```

### When User Self-Registers:
```php
// In usr-register.php
INSERT INTO tms_user (..., registration_type) VALUES (..., 'self')
```

### When Admin Creates User:
```php
// In admin-add-user.php
INSERT INTO tms_user (..., registration_type) VALUES (..., 'admin')
```

### When Admin Assigns Password to Guest:
```php
// In admin-manage-user-passwords.php
if (user is guest) {
    UPDATE tms_user SET u_pwd=?, registration_type='self' WHERE u_id=?
}
```

## Benefits

1. **Track User Origin**: Know where each user came from
2. **Identify Guests**: Easily find users who need passwords
3. **Convert Guests**: Simple password assignment converts them to active users
4. **Better Management**: Filter and manage users by type
5. **Analytics**: Understand user acquisition channels

## Visual Indicators

| Type | Badge Color | Icon | Can Login |
|------|-------------|------|-----------|
| Admin Created | Blue | Shield | ✅ Yes |
| Self Registered | Green | User Plus | ✅ Yes |
| Guest User | Yellow | Clock | ❌ No (until password assigned) |

## Usage Tips

1. **Find Guests**: Filter by "Guest Users" to see who needs passwords
2. **Assign Passwords**: Click "Change Password" on guest users
3. **Auto-Convert**: System automatically converts guest to self-registered
4. **Track Growth**: Use date filters to see new registrations
5. **Clean Up**: Bulk delete old guest users who never converted

---
**System Ready!** All user types are now tracked and managed automatically.
