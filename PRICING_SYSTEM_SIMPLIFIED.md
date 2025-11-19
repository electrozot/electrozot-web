# Pricing System Simplified

## Changes Made

The pricing system has been simplified to use only ONE price field (`s_price`) managed by admin, removing the confusing dual-price system.

### Key Changes:

1. **Single Price Field**: Only `s_price` exists - no more `s_admin_price` or "base price"
   
2. **Admin Control**: Admin sets the price in service add/edit forms
   - If admin sets a price → Fixed price for that service
   - If admin leaves it empty/0 → Technician fills price during service completion

3. **Price Flexibility**: 
   - Services with fixed costs (like installation) → Admin sets price
   - Services with variable costs (repairs with parts) → Leave empty, tech sets during completion

## Files Updated:

### Admin Files:
- `admin/admin-add-service.php` - Made price optional with helper text
- `admin/admin-manage-single-service.php` - Made price optional with helper text  
- `admin/admin-quick-booking.php` - Removed s_admin_price reference
- `admin/admin-view-service-booking.php` - Simplified price display
- `admin/admin-service-prices.php` - Updated to use s_price instead of s_admin_price

### Booking Files:
- `process-guest-booking.php` - Removed s_admin_price logic

### Technician Files:
- `tech/complete-service.php` - Updated to check s_price instead of s_admin_price
- `tech/service-prices.php` - Updated price display logic

## How It Works Now:

1. **Admin adds/edits service**:
   - Sets price if it's fixed (e.g., ₹500 for fan installation)
   - Leaves empty if price varies (e.g., AC repair depends on parts)

2. **Customer books service**:
   - If price is set → Shows fixed price
   - If price is 0/empty → Shows "Price to be determined"

3. **Technician completes service**:
   - If admin set price → Field is locked, uses that price
   - If no admin price → Technician enters final price based on work/parts

## Benefits:

✅ Simpler system - one price field only
✅ Clear admin control over pricing
✅ Flexibility for variable-cost services
✅ No confusion about "base price" vs "admin price"
✅ Technicians can still set prices when needed
