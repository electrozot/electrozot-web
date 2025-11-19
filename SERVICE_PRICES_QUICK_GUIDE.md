# Service Prices - Quick Reference Guide

## 🚀 Quick Start

### For Admin

1. **Access Service Prices**
   - Login to Admin Dashboard
   - Click **Services** → **Service Prices**

2. **Set Prices**
   - Enter price in Indian Rupees (₹) for each service
   - Leave empty if technician should set the price
   - Click **Update All Prices**

3. **View Statistics**
   - Total Services: All services in system
   - Priced by Admin: Services with fixed prices
   - Technician Pricing: Services with flexible pricing

### For Technicians

1. **View Service Prices**
   - Login to Technician Dashboard
   - Click **Service Prices** button in quick bar

2. **During Service Completion**
   - 🔒 **Admin Set Price**: Price is locked, cannot change
   - ✏️ **Flexible Price**: You can enter the price

## 📋 How It Works

### Admin Sets Price
```
Admin sets AC Repair = ₹500
↓
All new bookings use ₹500
↓
Technician sees locked price ₹500
↓
Customer pays ₹500
```

### Admin Doesn't Set Price
```
Admin leaves price empty
↓
Booking created with default price
↓
Technician sets actual price during completion
↓
Customer pays technician-set price
```

## 💡 Best Practices

### When to Set Admin Prices
✅ Standard services with fixed costs
✅ Popular services with consistent pricing
✅ Services where you want price control
✅ Services with known market rates

### When to Leave Prices Empty
✅ Complex services requiring inspection
✅ Services with variable costs
✅ Custom/other services
✅ Services where technician expertise is needed

## 🎯 Common Scenarios

### Scenario 1: Update Price for All Services
1. Go to Service Prices
2. Change the price for the service
3. Click Update All Prices
4. All pending bookings will use new price

### Scenario 2: Allow Technician Pricing
1. Go to Service Prices
2. Clear the price field (leave empty)
3. Click Update All Prices
4. Technicians can now set price during completion

### Scenario 3: Mix of Fixed and Flexible
1. Set prices for standard services (e.g., AC Repair, Fan Installation)
2. Leave prices empty for complex services (e.g., Custom Electrical Work)
3. Technicians see which prices they can modify

## 📊 Price Display

### In Admin Dashboard
- **Service Prices Page**: All services with price input fields
- **Manage Services**: Shows current prices
- **Bookings**: Shows booking prices

### In Technician Dashboard
- **Service Prices Page**: All services with pricing info
- **Complete Service**: Shows price with lock/edit indicator
- **Booking Details**: Shows service price

### In User Dashboard
- **Booking Details**: Shows service price
- **Service List**: Shows service prices (if public)

## 🔧 Troubleshooting

### Price Not Updating in Bookings
- Check if booking status is Pending/In Progress
- Completed bookings don't update automatically
- Cancelled bookings don't update

### Technician Can't Change Price
- Check if admin has set a price for that service
- Admin-set prices are locked for consistency
- Contact admin to change the price

### Price Shows as 0
- Service might be custom/other service
- Admin hasn't set a price yet
- Technician should set price during completion

## 📞 Support

For issues or questions:
1. Check this guide first
2. Review SERVICE_PRICES_FEATURE.md for detailed info
3. Contact system administrator

## 🎨 Currency

All prices are in **Indian Rupees (₹)**

Format: ₹500.00 or ₹1,500.00

## ⚡ Quick Tips

1. **Bulk Update**: Update all prices at once, no need to save individually
2. **Statistics**: Check dashboard stats to see pricing coverage
3. **Flexibility**: Mix fixed and flexible pricing as needed
4. **Consistency**: Fixed prices ensure consistent customer experience
5. **Control**: Admin prices override default service prices

## 📈 Recommended Pricing Strategy

### Phase 1: Start with Popular Services
- Set prices for top 10 most booked services
- Leave others flexible for now

### Phase 2: Analyze and Expand
- Review technician-set prices
- Set admin prices for services with consistent pricing
- Keep complex services flexible

### Phase 3: Optimize
- Adjust prices based on market rates
- Monitor booking trends
- Update prices seasonally if needed

---

**Last Updated**: November 2025
**Version**: 1.0
