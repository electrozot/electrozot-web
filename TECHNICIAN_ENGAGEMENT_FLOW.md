# Technician Engagement Flow Diagram

## System Flow Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    TECHNICIAN LIFECYCLE                          │
└─────────────────────────────────────────────────────────────────┘

    ┌──────────────┐
    │  AVAILABLE   │ ◄──────────────────────────┐
    │  (Free)      │                            │
    └──────┬───────┘                            │
           │                                    │
           │ Admin assigns booking              │
           │                                    │
           ▼                                    │
    ┌──────────────┐                            │
    │   ENGAGED    │                            │
    │  (Working)   │                            │
    └──────┬───────┘                            │
           │                                    │
           │ Technician completes/rejects       │
           │                                    │
           └────────────────────────────────────┘
```

---

## Detailed Assignment Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    BOOKING ASSIGNMENT FLOW                       │
└─────────────────────────────────────────────────────────────────┘

1. NEW BOOKING CREATED
   │
   ├─► Booking Status: "Pending"
   └─► Technician: NULL

2. ADMIN ASSIGNS TECHNICIAN
   │
   ├─► System checks: Is technician engaged?
   │   │
   │   ├─► YES (Engaged with another booking)
   │   │   └─► ❌ REJECT: Show error message
   │   │       "Technician is engaged with Booking #123"
   │   │
   │   └─► NO (Available)
   │       └─► ✅ ALLOW: Proceed with assignment
   │
   ├─► Update Booking:
   │   ├─► sb_technician_id = [technician_id]
   │   └─► sb_status = "Assigned"
   │
   └─► Update Technician:
       ├─► t_status = "Booked"
       ├─► t_is_available = 0
       └─► t_current_booking_id = [booking_id]

3. TECHNICIAN WORKS ON BOOKING
   │
   └─► Booking Status: "In Progress"

4. TECHNICIAN COMPLETES/REJECTS
   │
   ├─► OPTION A: Mark as "Done"
   │   │
   │   ├─► Upload service image
   │   ├─► Upload bill image
   │   ├─► Enter amount charged
   │   │
   │   ├─► Update Booking:
   │   │   └─► sb_status = "Completed"
   │   │
   │   └─► Update Technician:
   │       ├─► t_status = "Available"
   │       ├─► t_is_available = 1
   │       └─► t_current_booking_id = NULL
   │
   └─► OPTION B: Mark as "Not Done"
       │
       ├─► Enter rejection reason
       │
       ├─► Update Booking:
       │   └─► sb_status = "Not Done"
       │
       └─► Update Technician:
           ├─► t_status = "Available"
           ├─► t_is_available = 1
           └─► t_current_booking_id = NULL

5. TECHNICIAN NOW AVAILABLE FOR NEW BOOKING
   │
   └─► Can be assigned to fresh/reassigned/changed bookings
```

---

## Reassignment Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    REASSIGNMENT SCENARIO                         │
└─────────────────────────────────────────────────────────────────┘

SCENARIO: Technician A rejects booking, Admin reassigns to Technician B

1. INITIAL STATE
   │
   ├─► Booking #100: Assigned to Technician A
   ├─► Technician A: ENGAGED (working on #100)
   └─► Technician B: AVAILABLE (free)

2. TECHNICIAN A REJECTS
   │
   ├─► Booking #100: Status = "Not Done"
   ├─► Technician A: AVAILABLE (freed up)
   └─► Admin receives notification

3. ADMIN REASSIGNS TO TECHNICIAN B
   │
   ├─► System checks: Is Technician B engaged?
   │   └─► NO → ✅ Allow assignment
   │
   ├─► Update Booking #100:
   │   ├─► sb_technician_id = [Technician B ID]
   │   └─► sb_status = "Assigned"
   │
   └─► Update Technician B:
       ├─► t_status = "Booked"
       ├─► t_is_available = 0
       └─► t_current_booking_id = 100

4. FINAL STATE
   │
   ├─► Booking #100: Assigned to Technician B
   ├─► Technician A: AVAILABLE (can take new bookings)
   └─► Technician B: ENGAGED (working on #100)
```

---

## Change Technician Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                  CHANGE TECHNICIAN SCENARIO                      │
└─────────────────────────────────────────────────────────────────┘

SCENARIO: Admin changes technician from A to B (not responding, etc.)

1. INITIAL STATE
   │
   ├─► Booking #200: Assigned to Technician A
   ├─► Technician A: ENGAGED (working on #200)
   └─► Technician B: AVAILABLE (free)

2. ADMIN CHANGES TECHNICIAN
   │
   ├─► Admin enables "Allow Technician Change" checkbox
   │
   ├─► System checks: Is Technician B engaged?
   │   └─► NO → ✅ Allow change
   │
   ├─► Free up Technician A:
   │   ├─► t_status = "Available"
   │   ├─► t_is_available = 1
   │   └─► t_current_booking_id = NULL
   │
   ├─► Record cancellation for Technician A:
   │   └─► Insert into tms_cancelled_bookings
   │
   ├─► Update Booking #200:
   │   └─► sb_technician_id = [Technician B ID]
   │
   └─► Engage Technician B:
       ├─► t_status = "Booked"
       ├─► t_is_available = 0
       └─► t_current_booking_id = 200

3. FINAL STATE
   │
   ├─► Booking #200: Assigned to Technician B
   ├─► Technician A: AVAILABLE (freed up, can take new bookings)
   └─► Technician B: ENGAGED (working on #200)
```

---

## Multiple Booking Attempt (BLOCKED)

```
┌─────────────────────────────────────────────────────────────────┐
│              DOUBLE ASSIGNMENT PREVENTION                        │
└─────────────────────────────────────────────────────────────────┘

SCENARIO: Admin tries to assign engaged technician to new booking

1. CURRENT STATE
   │
   ├─► Booking #300: Assigned to Technician C (In Progress)
   ├─► Booking #400: Pending (needs assignment)
   └─► Technician C: ENGAGED (working on #300)

2. ADMIN TRIES TO ASSIGN TECHNICIAN C TO BOOKING #400
   │
   ├─► System checks: Is Technician C engaged?
   │   │
   │   └─► YES (engaged with Booking #300)
   │
   ├─► System blocks assignment
   │
   └─► Show error message:
       "Technician is currently engaged with Booking #300 
        (Status: In Progress). Please wait until they 
        complete or reject that booking."

3. ADMIN OPTIONS
   │
   ├─► OPTION A: Wait for Technician C to complete #300
   │   └─► Then assign to #400
   │
   ├─► OPTION B: Choose different available technician
   │   └─► Assign to Technician D (if available)
   │
   └─► OPTION C: Force change technician on #300
       └─► Free up Technician C, assign to #400

4. RESULT
   │
   └─► ✅ ONE BOOKING PER TECHNICIAN RULE ENFORCED
```

---

## Status Transitions

```
┌─────────────────────────────────────────────────────────────────┐
│                  BOOKING STATUS FLOW                             │
└─────────────────────────────────────────────────────────────────┘

Pending ──────► Assigned ──────► In Progress ──────► Completed
   │               │                  │                    │
   │               │                  │                    ▼
   │               │                  │              [Technician
   │               │                  │               Available]
   │               │                  │
   │               │                  └──────► Not Done
   │               │                              │
   │               │                              ▼
   │               │                        [Technician
   │               │                         Available]
   │               │
   │               └──────► Rejected
   │                           │
   │                           ▼
   │                     [Technician
   │                      Available]
   │
   └──────► Cancelled
               │
               ▼
         [No Technician
          Assigned]
```

---

## Technician Availability States

```
┌─────────────────────────────────────────────────────────────────┐
│              TECHNICIAN AVAILABILITY MATRIX                      │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┬──────────────┬──────────────┬──────────────────┐
│   t_status   │t_is_available│current_booking│  Can Assign?    │
├──────────────┼──────────────┼──────────────┼──────────────────┤
│  Available   │      1       │     NULL     │   ✅ YES        │
├──────────────┼──────────────┼──────────────┼──────────────────┤
│   Booked     │      0       │   [booking]  │   ❌ NO         │
├──────────────┼──────────────┼──────────────┼──────────────────┤
│  Available   │      0       │   [booking]  │   ⚠️ FIX NEEDED │
├──────────────┼──────────────┼──────────────┼──────────────────┤
│   Booked     │      1       │     NULL     │   ⚠️ FIX NEEDED │
└──────────────┴──────────────┴──────────────┴──────────────────┘

⚠️ Inconsistent states should be fixed using maintenance queries
```

---

## System Components

```
┌─────────────────────────────────────────────────────────────────┐
│                    SYSTEM ARCHITECTURE                           │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                         ADMIN PANEL                              │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │  admin-assign-technician.php                           │    │
│  │  • Shows only available technicians                    │    │
│  │  • Validates engagement before assignment              │    │
│  │  • Updates technician status                           │    │
│  └────────────────────────────────────────────────────────┘    │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │  test-technician-engagement.php                        │    │
│  │  • Real-time monitoring dashboard                      │    │
│  │  • Shows all technicians and their status              │    │
│  │  • Statistics and reports                              │    │
│  └────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      CORE LOGIC LAYER                            │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │  check-technician-availability.php                     │    │
│  │  • checkTechnicianEngagement()                         │    │
│  │  • getAvailableTechnicians()                           │    │
│  │  • engageTechnician()                                  │    │
│  │  • freeTechnician()                                    │    │
│  │  • getTechnicianEngagementSummary()                    │    │
│  └────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    TECHNICIAN PANEL                              │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │  complete-booking.php                                  │    │
│  │  • Mark as Done (with images & amount)                 │    │
│  │  • Mark as Not Done (with reason)                      │    │
│  │  • Auto-frees technician on completion/rejection       │    │
│  └────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                         DATABASE                                 │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │  tms_technician                                        │    │
│  │  • t_status (Available/Booked)                         │    │
│  │  • t_is_available (1/0)                                │    │
│  │  • t_current_booking_id                                │    │
│  └────────────────────────────────────────────────────────┘    │
│                                                                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │  tms_service_booking                                   │    │
│  │  • sb_technician_id                                    │    │
│  │  • sb_status                                           │    │
│  └────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
```

---

## Key Decision Points

```
┌─────────────────────────────────────────────────────────────────┐
│                    DECISION FLOWCHART                            │
└─────────────────────────────────────────────────────────────────┘

                    [Admin wants to assign booking]
                                │
                                ▼
                    ┌───────────────────────┐
                    │ Is technician engaged │
                    │  with another booking?│
                    └───────────┬───────────┘
                                │
                    ┌───────────┴───────────┐
                    │                       │
                   YES                     NO
                    │                       │
                    ▼                       ▼
            ┌───────────────┐      ┌───────────────┐
            │ Show error    │      │ Allow         │
            │ message       │      │ assignment    │
            └───────────────┘      └───────┬───────┘
                    │                       │
                    │                       ▼
                    │              ┌───────────────┐
                    │              │ Update booking│
                    │              │ & technician  │
                    │              └───────┬───────┘
                    │                       │
                    │                       ▼
                    │              ┌───────────────┐
                    │              │ Technician    │
                    │              │ now ENGAGED   │
                    │              └───────────────┘
                    │
                    ▼
            ┌───────────────┐
            │ Admin chooses │
            │ different     │
            │ technician    │
            └───────────────┘
```

---

## Summary

✅ **One booking per technician at a time**  
✅ **Automatic status management**  
✅ **Prevents double assignments**  
✅ **Works for all assignment types**  
✅ **Real-time availability tracking**  
✅ **Admin monitoring dashboard**  

The system ensures efficient technician utilization while maintaining service quality.
