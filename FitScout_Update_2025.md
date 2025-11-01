
---

## 🔧 IMPLEMENTATION BY MILESTONE

### **Milestone 1 – Registration System**
- `/app/Http/Controllers/Auth/*`
- `/resources/views/auth/*`
- `/database/migrations/create_users_table.php`
- `/app/Notifications/VerifyEmail.php`
- `/routes/web.php` – registration and confirmation routes

### **Milestone 2 – Seller Dashboard**
- `/filament/Resources/SellerResource.php`
- `/filament/Pages/Dashboard.php`
- `/app/Http/Controllers/Seller/*`
- `/app/Models/Seller.php`
- `/app/Notifications/RenewalReminder.php`
- `/resources/views/seller/dashboard.blade.php`

### **Milestone 3 – Google Calendar & Booking System**
- `/app/Services/GoogleCalendarService.php`
- `/app/Http/Controllers/BookingController.php`
- `/app/Models/Booking.php`
- `/filament/Resources/BookingResource.php`
- `/filament/Pages/CalendarSync.php`
- `/app/Notifications/BookingCreated.php`
- `/routes/api.php` – booking endpoints
- `/config/google.php` – OAuth keys and scopes
- `/app/Console/Kernel.php` – schedule recurring sync jobs

---

## 📆 SUGGESTED DEVELOPMENT TIMELINE

| Milestone | Duration | Priority | Complexity |
|------------|-----------|-----------|-------------|
| **1. Registration System** | 2–3 weeks | 🔵 High | 🟡 Medium |
| **2. Seller Dashboard Improvements** | 3–4 weeks | 🔵 High | 🟢 Medium |
| **3. Google Calendar Integration & Booking System** | 5–6 weeks | 🔴 Critical | 🔴 High |

---

**Document Owner:** Product Team  
**Prepared for:** Client Presentation & Development Specification  
**Version:** v1.3 – October 2025  
**Backend Framework:** Laravel 11  
**Admin System:** Filament 3  
**Frontend:** Bootstrap 5 Template (No JS Framework)
