# Service Prices Management Feature

## Overview
This feature allows the admin to control service pricing centrally. Admin can set fixed prices for services, which will be automatically applied to bookings and shown to technicians. If admin doesn't set a price, technicians can add the price during service completion.

## Features Implemented

### 1. Admin Dashboard - Service Prices Management
**Location:** Admin Dashboard → Services → Service Prices

**Features:**
- View all 43 services organized by category
- Set fixed prices in Indian Rupees (₹) for each service
- Leave price empty to allow technician pricing
- Real-time statistics showing:
  - Total services
  - Services with admin-set prices
  - Services with technician pricing
- Bulk update all prices at once

**How to Use:**
1. Login to admin dashboard
2. Go to Services → Service Prices
3. Enter prices in Indian Rupees (₹) for services you want to control
4. Leave price field empty for services where technicians should set the price
5. Click "Update All Prices" to save changes

### 2. Technician Dashboard - Service Prices View
**Location:** Technician Dashboard → Service Prices (Quick Bar)

**Features:**
- View all active services with their prices
- See which services have admin-set prices (locked)
- See which services allow flexible pricing
- Organized by category for easy reference

**Price Indicators:**
- 🔒 **Admin Set** - Price is fixed by admin, cannot be changed
- ✏️ **Flexible** - Technician can set price during completion

### 3. Service Completion - Smart Pricing
**Location:** Technician Dashboard → Complete Service

**Features:**
- If admin has set a price:
  - Price field is pre-filled and locked
  - Technician cannot modify the price
  - Shows "Admin Set Price" badge
- If admin hasn't set a price:
  - Technician can enter the price
  - Shows "You can modify" badge
  - Default price is shown as reference

### 4. Automatic Price Application

**When Admin Sets a Price:**
- New bookings automatically use the admin price
- Existing pending/in-progress bookings are updated to admin price
- Completed bookings are not affected

**When Creating Bookings:**
- Guest bookings (process-guest-booking.php)
- Admin quick bookings (admin-quick-booking.php)
- All booking creation uses admin price if available

## Database Changes

### New Column Added
```sql
ALTER TABLE tms_service 
ADD COLUMN s_admin_price DECIMAL(10,2) DEFAULT NULL;
```

**Column Details:**
- `s_admin_price`: Stores admin-set price in Indian Rupees
- `NULL` value means technician sets the price
- Non-NULL value means admin has set a fixed price

## Files Modified

### Admin Files
1. **admin/admin-service-prices.php** (NEW)
   - Main service prices management page
   - Bulk price update functionality
   - Statistics dashboard

2. **admin/vendor/inc/sidebar.php**
   - Added "Service Prices" menu item under Services dropdown

3. **admin/admin-quick-booking.php**
   - Updated to use admin prices when creating bookings

### Technician Files
1. **tech/service-prices.php** (NEW)
   - Service prices viewing page for technicians
   - Shows all services with pricing information

2. **tech/complete-service.php**
   - Updated to show admin prices
   - Lock price field when admin has set a price
   - Allow modification when admin hasn't set a price

3. **tech/includes/nav.php**
   - Added "Service Prices" button to dashboard quick bar

### Booking Files
1. **process-guest-booking.php**
   - Updated to use admin prices for guest bookings

### Database Files
1. **admin/add-admin-price-column.sql** (NEW)
   - SQL migration to add admin price column
   - Updates existing bookings with admin prices

## Currency
All prices are displayed and stored in **Indian Rupees (₹)**

## Benefits

### For Admin
- Central control over service pricing
- Flexibility to set fixed prices or allow technician pricing
- Easy bulk updates
- Clear visibility of pricing strategy

### For Technicians
- Clear pricing information before service
- Know which prices are fixed vs flexible
- Easy reference to all service prices
- No confusion about pricing authority

### For Customers
- Consistent pricing when admin sets prices
- Transparent pricing structure
- Automatic price updates in bookings

## Usage Scenarios

### Scenario 1: Fixed Price Services
Admin sets prices for common services (e.g., AC Repair = ₹500)
- All bookings automatically use ₹500
- Technicians see locked price during completion
- Consistent pricing for customers

### Scenario 2: Variable Price Services
Admin leaves price empty for complex services
- Technicians assess and set price during completion
- Flexibility for services requiring inspection
- Price based on actual work done

### Scenario 3: Price Updates
Admin changes a service price from ₹500 to ₹600
- All new bookings use ₹600
- Pending bookings are updated to ₹600
- Completed bookings remain at original price

## Testing Checklist

- [ ] Admin can view all 43 services in Service Prices page
- [ ] Admin can set prices for services
- [ ] Admin can leave prices empty
- [ ] Statistics update correctly
- [ ] Technician can view service prices
- [ ] Technician sees locked prices for admin-set services
- [ ] Technician can modify prices for flexible services
- [ ] New bookings use admin prices
- [ ] Existing bookings update when admin changes price
- [ ] Guest bookings use admin prices
- [ ] Quick bookings use admin prices

## Future Enhancements

1. **Price History**
   - Track price changes over time
   - Show price change logs

2. **Bulk Price Operations**
   - Apply percentage increase/decrease to all services
   - Copy prices from one category to another

3. **Dynamic Pricing**
   - Time-based pricing (peak hours)
   - Location-based pricing (by pincode)
   - Seasonal pricing

4. **Price Approval Workflow**
   - Technician suggests price
   - Admin approves/rejects
   - Notification system for price approvals

## Support

For any issues or questions about this feature, contact the development team.
