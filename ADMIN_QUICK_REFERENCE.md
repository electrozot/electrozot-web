# Admin Quick Reference - Customer Login Info

## 📞 Quick Booking Default Credentials

When you create a quick booking for a **NEW customer**, the system automatically creates their account.

### Tell the customer:
```
Login URL: [Your Website]/usr/index.php

Phone Number: [Customer's 10-digit phone]
Password: electrozot123
```

### Example Message to Customer:
```
Hi [Customer Name],

Your booking has been confirmed! 

To track your order online:
1. Visit: [Your Website]/usr/
2. Login with:
   - Phone: [Their Phone Number]
   - Password: electrozot123
3. View your booking status anytime!

Thank you for choosing Electrozot!
```

---

## 🔐 Important Notes for Admin:

### New Customer (Phone NOT in system):
- ✅ System creates account automatically
- ✅ Default password: `electrozot123`
- ✅ Customer can login immediately
- ⚠️ Advise customer to change password after first login

### Existing Customer (Phone already in system):
- ✅ Uses existing account
- ✅ Uses their existing password
- ✅ Booking automatically linked to their account
- ℹ️ No need to tell them password

### Guest Booking (from website):
- ✅ Creates guest account
- ⚠️ No password set initially
- ℹ️ Customer must register or reset password to login
- ✅ Bookings auto-link when they register with same phone

---

## 📋 Quick Checklist After Creating Booking:

- [ ] Note down booking ID
- [ ] Confirm customer phone number is correct
- [ ] If NEW customer, inform them of default password
- [ ] Send booking confirmation message
- [ ] Remind customer they can track online

---

## 🆘 If Customer Can't Login:

### "Password not working"
1. Check if they're using correct phone number (10 digits, no spaces)
2. Try default password: `electrozot123`
3. If still fails, use "Forgot Password" feature
4. Or reset their password from admin panel

### "Can't see my booking"
1. Verify phone number matches booking
2. Check if booking was created under different phone
3. Ask customer to logout and login again
4. Check "My Bookings" page, not just dashboard

### "Account doesn't exist"
1. Check if booking was actually created
2. Verify phone number in booking matches login phone
3. May need to create booking again
4. Check for typos in phone number

---

## 🔧 Admin Panel Access:

**Admin Login:** [Your Website]/admin/
- Use your admin credentials
- Can view all bookings
- Can create quick bookings
- Can manage customer accounts

---

## 📱 Customer Portal Features:

Once logged in, customers can:
- ✅ View all their bookings
- ✅ Track booking status in real-time
- ✅ See technician details (when assigned)
- ✅ View booking history
- ✅ Update profile information
- ✅ Change password
- ✅ Give feedback after service

---

## 🔒 Security Best Practices:

1. **Always verify customer identity** before creating booking
2. **Confirm phone number** is correct (no typos!)
3. **Advise customers** to change default password
4. **Don't share** default password publicly
5. **Use SMS** to send login details (more secure than verbal)

---

## 💡 Pro Tips:

- Save a template message with login instructions
- Keep a log of bookings created with new accounts
- Remind customers about online tracking feature
- Follow up after service to ensure satisfaction
- Encourage customers to use online booking next time

---

**Need Help?** Contact system administrator or check documentation.
