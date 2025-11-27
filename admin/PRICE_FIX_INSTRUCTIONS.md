# Fix Completed Booking Prices - Instructions

## Problem
Completed bookings (like #18 and #20) are showing ₹0 even though payment was collected by the technician.

## Solution
The system has been updated to correctly display prices from multiple sources:
1. **sb_final_price** - Price set when technician completes the service
2. **Payment collection records** - Actual payment collected by technician
3. **sb_total_price** - Initial booking price

## How to Fix Existing Bookings

### Step 1: Run the Fix Script
1. Login to admin panel
2. Navigate to: `http://your-domain/admin/fix-completed-booking-prices.php`
3. The script will automatically:
   - Find all completed bookings with ₹0 price
   - Check payment collection records
   - Update booking prices accordingly
   - Show a detailed report

### Step 2: Verify the Fix
1. Go to Admin Dashboard
2. Check "All Bookings" page
3. Verify that completed bookings now show correct prices
4. Check statistics and revenue reports

## What Was Fixed

### Files Updated:
1. **admin/admin-dashboard.php** - Dashboard displays correct prices
2. **admin/admin-all-bookings.php** - All bookings page shows correct prices with "Tech" badge
3. **admin/ajax-get-booking-details.php** - Booking details modal shows correct prices
4. **admin/admin-view-service-booking.php** - Individual booking view shows correct prices
5. **admin/get-stats-data.php** - Statistics use correct revenue calculations
6. **admin/admin-stats.php** - Main stats page uses correct prices
7. **admin/admin-stats-services.php** - Service stats use correct revenue
8. **admin/admin-stats-technicians.php** - Technician stats use correct revenue

### Database Fields Used:
- `sb_final_price` - Final price charged (set by technician or admin)
- `sb_tech_decided_price` - Price decided by technician
- `sb_price_set_by_tech` - Flag indicating who set the price (1=tech, 0=admin)
- `tms_payment_collection.pc_amount` - Actual payment collected

## Price Display Logic

The system now uses this priority:
```
Display Price = COALESCE(sb_final_price, sb_total_price, 0)
```

This means:
1. If technician set final price → use that
2. Else if admin set booking price → use that
3. Else → show ₹0 (price not set yet)

## Indicators

- **"Tech" badge** - Price was set by technician
- **"Price not set yet" warning** - Booking has no price (needs attention)
- **"Price set by technician"** - Shown in booking details

## Future Bookings

All new bookings will automatically:
1. Store the correct price when technician completes service
2. Link to payment collection records
3. Display correctly in all admin pages

## Troubleshooting

If prices still show ₹0 after running the fix:
1. Check if payment was actually collected (check `tms_payment_collection` table)
2. Check if service has a price set (check `tms_service.s_price`)
3. Manually update the booking in database:
   ```sql
   UPDATE tms_service_booking 
   SET sb_final_price = [amount], 
       sb_tech_decided_price = [amount],
       sb_price_set_by_tech = 1
   WHERE sb_id = [booking_id];
   ```

## Support

If you need help, check:
- Booking details in admin panel
- Payment collection records
- Service price settings
- Technician completion records
