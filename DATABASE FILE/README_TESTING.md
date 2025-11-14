# ElectroZot Database Testing Guide

## Overview
This guide helps you populate the database with sample data and test all database logic.

## Files Created
1. **sample_data_insert.sql** - Inserts 20+ records for users, technicians, and bookings
2. **test_database_logic.sql** - Comprehensive testing of all database operations
3. **execute_all.sql** - Quick execution script for both files

## Sample Data Summary

### Users/Clients (25 records)
- Email format: `firstname.lastname@email.com`
- Password: `123456` (for all users)
- Phone format: `017123450XX`
- Locations: Various cities across Bangladesh
- Category: All set as "User"

### Technicians (25 records)
- ID format: `TECH006` to `TECH030`
- Categories: Electrical, Plumbing, HVAC, Appliance, General
- Experience: 4-15 years
- Status: All "Available"
- 5 technicians per category

### Bookings (30 records)
- **5 Pending** - Awaiting technician assignment
- **5 Assigned** - Technician assigned, not started
- **5 In Progress** - Currently being worked on
- **5 Completed** - Successfully finished
- **5 Rejected** - Need reassignment
- **5 Cancelled** - Customer cancelled

## How to Execute

### Method 1: Using phpMyAdmin
1. Open phpMyAdmin
2. Select `electrozot_db` database
3. Go to "SQL" tab
4. Copy and paste content from `sample_data_insert.sql`
5. Click "Go" to execute
6. Repeat for `test_database_logic.sql`

### Method 2: Using MySQL Command Line
```bash
# Navigate to DATABASE FILE directory
cd "DATABASE FILE"

# Insert sample data
mysql -u root -p electrozot_db < sample_data_insert.sql

# Run tests
mysql -u root -p electrozot_db < test_database_logic.sql
```

### Method 3: Using execute_all.sql
```bash
mysql -u root -p electrozot_db < execute_all.sql
```

## What Gets Tested

### 1. User Management
- Total user count
- User categories
- Duplicate email detection
- Phone number validation

### 2. Technician Management
- Total technician count
- Distribution by category
- Status tracking
- Duplicate ID detection
- Average experience by category

### 3. Service Management
- Active services
- Service categories
- Price range analysis

### 4. Booking Management
- Total bookings
- Bookings by status
- Bookings by service
- Revenue analysis
- Revenue by status

### 5. Booking-User Relationships
- Top customers by bookings
- Users with no bookings
- Customer spending analysis

### 6. Booking-Technician Relationships
- Top technicians by bookings
- Technicians with no bookings
- Technician workload by status
- Revenue generation by technician

### 7. Pending & Rejected Bookings
- All pending bookings
- All rejected bookings
- Available technicians for reassignment

### 8. Date & Time Analysis
- Bookings by date
- Bookings by time slot
- Future vs past bookings

### 9. Data Integrity Checks
- Orphaned bookings
- Invalid foreign keys
- Assigned bookings without technician

### 10. Business Logic Validation
- Category mismatch detection
- Price consistency
- Status flow validation

### 11. Summary Statistics
- Overall system summary
- Completion rate
- Rejection rate
- Average booking value

## Expected Results

After running the sample data insert:
- **26 Users** (1 existing + 25 new)
- **30 Technicians** (5 existing + 25 new)
- **30 Bookings** (all new)

### Booking Status Distribution:
- Pending: 5 bookings
- Assigned: 5 bookings
- In Progress: 5 bookings
- Completed: 5 bookings
- Rejected: 5 bookings
- Cancelled: 5 bookings

### Revenue Summary:
- Total Revenue: ~4,200 BDT
- Average Booking Value: ~140 BDT

## Testing the Admin Dashboard Features

### Test Rejected Bookings Section:
1. Login to admin dashboard
2. Check "Rejected/Cancelled Bookings" section
3. Should see 10 bookings (5 rejected + 5 cancelled)
4. Click "Reassign" button on any rejected booking
5. Modal should show available technicians for that service category
6. Select a technician and reassign
7. Booking should move to "Assigned" status

### Test Technician Assignment:
1. Go to pending bookings
2. Assign technicians to pending bookings
3. Verify category matching (Electrical service → Electrical technician)
4. Check technician dashboard to see assigned bookings

### Test Booking Flow:
1. **Pending** → Admin assigns technician → **Assigned**
2. **Assigned** → Technician accepts → **In Progress**
3. **In Progress** → Technician completes → **Completed**
4. **Assigned** → Technician rejects → **Rejected** → Admin reassigns

## Verification Queries

Quick queries to verify data:

```sql
-- Check total records
SELECT 
    (SELECT COUNT(*) FROM tms_user) as users,
    (SELECT COUNT(*) FROM tms_technician) as technicians,
    (SELECT COUNT(*) FROM tms_service_booking) as bookings;

-- Check booking status distribution
SELECT sb_status, COUNT(*) as count 
FROM tms_service_booking 
GROUP BY sb_status;

-- Check technician workload
SELECT t.t_name, COUNT(sb.sb_id) as bookings
FROM tms_technician t
LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id
GROUP BY t.t_id
ORDER BY bookings DESC;
```

## Troubleshooting

### Issue: Foreign Key Constraint Fails
**Solution**: Make sure the base database schema is created first using `electrozot_db.sql`

### Issue: Duplicate Entry Error
**Solution**: The database already has sample data. Either:
- Drop and recreate the database
- Modify the INSERT statements to use different values

### Issue: No Results in Tests
**Solution**: Ensure sample data was inserted successfully before running tests

### Issue: Category Mismatch Errors
**Solution**: This is expected! The test will show if any bookings have mismatched categories

## Clean Up (Optional)

To remove all sample data and start fresh:

```sql
-- Delete all bookings
DELETE FROM tms_service_booking WHERE sb_id > 0;

-- Delete sample users (keep original)
DELETE FROM tms_user WHERE u_id > 13;

-- Delete sample technicians (keep original 5)
DELETE FROM tms_technician WHERE t_id > 7;

-- Reset auto-increment
ALTER TABLE tms_service_booking AUTO_INCREMENT = 1;
ALTER TABLE tms_user AUTO_INCREMENT = 14;
ALTER TABLE tms_technician AUTO_INCREMENT = 8;
```

## Next Steps

After testing:
1. Review test results for any data integrity issues
2. Test admin dashboard reassignment feature
3. Test technician dashboard booking acceptance
4. Test user booking creation flow
5. Verify email notifications (if implemented)
6. Test payment processing (if implemented)

## Support

If you encounter any issues:
1. Check MySQL error logs
2. Verify database connection settings
3. Ensure all tables exist with correct structure
4. Check foreign key constraints are enabled
