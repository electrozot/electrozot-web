# 📚 Electrozot - Features Documentation

## Overview
Complete documentation of all implemented features in the Electrozot system.

---

## 🔐 Authentication & Sessions

### Permanent Login Feature
**Users & Technicians:** Stay logged in for 10 years (effectively permanent)
**Admins:** Auto-logout after 15 hours for security

**How it works:**
- Checkbox: "Remember me" (checked by default)
- Sessions persist across browser sessions
- Returns to last visited page on login
- Secure session management with HttpOnly cookies

**Files:**
- `usr/vendor/inc/session-config.php` - User session (10 years)
- `admin/vendor/inc/session-config.php` - Admin session (15 hours)
- `tech/process-login.php` - Technician login with session

---

## 📱 One Phone Number = One Account

**Feature:** Ensures each mobile number can only register once

**Implementation:**
1. **Real-time validation** - AJAX check as user types
2. **Application-level check** - PHP validation on registration
3. **Database constraint** - UNIQUE key on `u_phone` column

**Files:**
- `usr/check-phone-availability.php` - AJAX endpoint
- `usr/usr-register.php` - Registration with validation
- `admin/admin-add-user.php` - Admin user creation with validation
- `admin/setup-unique-phone-constraint.php` - Database setup

---

## 🎯 Smart Footer Button

**Feature:** Technician button in footer auto-detects login status

**Behavior:**
- Not logged in: Shows "Technician" → Links to login
- Logged in: Shows "Dashboard" → Links directly to dashboard

**Visual:**
- Gradient background with glow effect
- Icon changes based on login status
- Opens in same window

**File:** `vendor/inc/footer.php`

---

## 🔧 Technician Dashboard

### Bottom Navigation
**5 Buttons:**
1. 📈 Dashboard - Analytics icon (improved)
2. ✓ Completed - Completed bookings
3. 🏠 Main Site - Links to main website
4. 👤 Profile - Technician profile
5. 📞 Call Admin - Direct call support

**Features:**
- Enhanced icons with shadows
- Smooth hover animations
- Active state pulse effect
- Badge notifications for new bookings

**File:** `tech/includes/bottom-nav.php`

### Booking Filters
**3 Filter Options:**
1. ⏰ Pending - In Progress bookings
2. ✓ Completed - Completed bookings
3. 📋 All - All bookings (new bookings automatically at top)

**Smart Sorting:**
- Pending bookings always appear first
- Sorted by status priority then date
- No need for separate "New" filter

**File:** `tech/dashboard.php`

---

## 🚫 Session Redirect Fix

**Issue Fixed:** API endpoints were being tracked as "last page"

**Solution:**
- Excluded all API/AJAX files from page tracking
- Files starting with `api-`, `ajax-`, `get-`, `check-` are ignored
- Emergency fix scripts created for corrupted sessions

**Files:**
- `tech/includes/checklogin.php` - Technician
- `usr/vendor/inc/session-config.php` - User
- `admin/vendor/inc/session-config.php` - Admin
- `tech/fix-session.php` - Emergency fix
- `usr/fix-session.php` - Emergency fix
- `admin/fix-session.php` - Emergency fix

---

## 📊 Session Duration Summary

| Role | Duration | Auto-Logout | Reason |
|------|----------|-------------|--------|
| **User** | 10 years | No | Customer convenience |
| **Technician** | 10 years | No | Field work efficiency |
| **Admin** | 15 hours | Yes | Security (full system access) |

---

## 🎨 Visual Improvements

### Technician Dashboard Icon
- Changed from speedometer to chart-line
- Professional analytics appearance
- Matches footer icon

### Icon Enhancements
- Larger size (22px)
- Drop shadow effects
- Enhanced hover animations
- Active state pulse

### Footer Button
- Gradient background
- Glow effects on hover
- Professional appearance
- Clear visual feedback

---

## 🔒 Security Features

### Session Security
- HttpOnly cookies (JavaScript can't access)
- SameSite=Lax (CSRF protection)
- Session ID regeneration after login
- Secure password validation

### Admin Security
- 15-hour auto-logout
- Forces periodic re-authentication
- Prevents long-term unauthorized access
- Industry-standard security practice

### Phone Number Validation
- Prevents duplicate accounts
- Database-level constraint
- Real-time availability check
- Clear error messages

---

## 📁 Key Files Structure

```
electrozot/
├── admin/
│   ├── vendor/inc/
│   │   └── session-config.php (15 hours)
│   ├── index.php (login)
│   ├── admin-add-user.php (with phone check)
│   └── fix-session.php (emergency fix)
├── usr/
│   ├── vendor/inc/
│   │   └── session-config.php (10 years)
│   ├── index.php (login)
│   ├── usr-register.php (with phone check)
│   ├── check-phone-availability.php (AJAX)
│   └── fix-session.php (emergency fix)
├── tech/
│   ├── includes/
│   │   ├── bottom-nav.php (navigation)
│   │   └── checklogin.php (session check)
│   ├── index.php (login)
│   ├── process-login.php (login handler)
│   ├── dashboard.php (main dashboard)
│   └── fix-session.php (emergency fix)
└── vendor/inc/
    └── footer.php (smart technician button)
```

---

## 🧪 Testing Checklist

### Permanent Login
- [ ] User logs in → Stays logged in after browser close
- [ ] Technician logs in → Stays logged in after browser close
- [ ] Admin logs in → Auto-logout after 15 hours

### Phone Number Validation
- [ ] Real-time check shows availability
- [ ] Cannot register duplicate phone
- [ ] Admin cannot create duplicate user

### Smart Footer
- [ ] Not logged in → Shows "Technician"
- [ ] Logged in → Shows "Dashboard"
- [ ] Redirects correctly

### Session Redirect
- [ ] API calls don't affect last page
- [ ] Returns to correct page after login
- [ ] No JSON output on login

---

## 🔧 Configuration

### Change Admin Session Duration
Edit: `admin/vendor/inc/session-config.php`
```php
// Current: 15 hours (54000 seconds)
ini_set('session.gc_maxlifetime', 54000);

// Examples:
// 12 hours = 43200
// 20 hours = 72000
// 24 hours = 86400
```

### Enable HTTPS (Production)
Edit all `session-config.php` files:
```php
'secure' => true,  // Change from false to true
```

---

## 📞 Support & Troubleshooting

### Session Issues
1. Run fix script: `[module]/fix-session.php`
2. Clear browser cookies
3. Re-login

### Phone Validation Issues
1. Check database constraint exists
2. Run: `admin/setup-unique-phone-constraint.php`
3. Resolve any duplicates

### Login Issues
1. Verify session files are writable
2. Check PHP session settings
3. Clear browser cache

---

## 🎯 Summary

### Implemented Features
✅ Permanent login (Users & Technicians)  
✅ 15-hour admin sessions  
✅ One phone = one account  
✅ Smart footer button  
✅ Improved dashboard icons  
✅ Smart booking filters  
✅ Session redirect fix  
✅ Enhanced security  

### Benefits
🚀 Better user experience  
🚀 Enhanced security  
🚀 Professional appearance  
🚀 Clean, maintainable code  
🚀 Mobile-friendly design  

---

**Last Updated:** November 27, 2025  
**Status:** ✅ All Features Implemented and Working  
**Version:** 2.0
