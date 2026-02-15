# Appointment Booking System - Complete Workflows Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Database Schema Requirements](#database-schema-requirements)
3. [Client-Side Workflows](#client-side-workflows)
4. [Professional-Side Workflows](#professional-side-workflows)
5. [Notification System Workflows](#notification-system-workflows)
6. [Cancellation Policy Workflows](#cancellation-policy-workflows)
7. [Calendar Management Workflows](#calendar-management-workflows)
8. [Technical Implementation Details](#technical-implementation-details)

---

## System Overview

### Key Principles
- **Service Model Integration**: All appointment bookings are linked to the `Service` model (`app/Models/Service.php`), NOT the `Gig` model
- **User Types**: 
  - Type 1: Admin
  - Type 2: Customer/Client
  - Type 3: Professional
- **Notification Channels**: Email, Platform Notifications (in-app), and Platform Chat
- **Calendar System**: Color-coded calendar view for professionals

---

## Database Schema Requirements

### New Tables Required

#### 1. `service_availabilities` Table
Stores availability settings for each service.

```sql
- id (bigint, primary key)
- service_id (bigint, foreign key -> services.id)
- day_of_week (integer, 0-6, where 0=Sunday)
- start_time (time)
- end_time (time)
- is_active (boolean, default true)
- created_at (timestamp)
- updated_at (timestamp)
```

#### 2. `appointments` Table
Stores all appointment bookings.

```sql
- id (bigint, primary key)
- service_id (bigint, foreign key -> services.id)
- client_id (bigint, foreign key -> users.id, user_type=2)
- professional_id (bigint, foreign key -> users.id, user_type=3)
- appointment_date (date)
- appointment_time (time)
- status (enum: 'pending', 'confirmed', 'cancelled', 'completed')
- client_name (string)
- client_surname (string)
- client_email (string)
- client_phone (string, nullable)
- client_date_of_birth (date)
- cancellation_reason (text, nullable)
- cancelled_by (enum: 'client', 'professional', nullable)
- cancelled_at (timestamp, nullable)
- is_external (boolean, default false) -- For manually entered appointments
- external_color (string, nullable) -- For external appointments (e.g., 'green')
- created_at (timestamp)
- updated_at (timestamp)
```

#### 3. `client_cancellation_tracking` Table
Tracks monthly cancellation counts for clients.

```sql
- id (bigint, primary key)
- client_id (bigint, foreign key -> users.id)
- month (integer, 1-12)
- year (integer)
- cancellation_count (integer, default 0)
- is_blocked (boolean, default false)
- blocked_until (date, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

#### 4. `appointment_reminders` Table
Tracks sent reminders to prevent duplicates.

```sql
- id (bigint, primary key)
- appointment_id (bigint, foreign key -> appointments.id)
- reminder_type (enum: 'email', 'notification')
- sent_at (timestamp)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## Client-Side Workflows

### Workflow 1: Booking Request Initiation

**Trigger**: Client clicks "Book Now" button on professional profile page

**Steps**:
1. **Authentication Check**
   - If not authenticated: Redirect to login page with return URL
   - If authenticated but user_type ≠ 2: Show error "Only clients can book appointments"
   - If authenticated and user_type = 2: Proceed

2. **Open Booking Interface**
   - Open new tab/window with booking interface
   - URL: `/appointments/book/{professional_username}`
   - Display professional's appointment management system

3. **Service Selection**
   - Show dropdown menu with all active services for the professional
   - Services are loaded from `services` table where `user_id` = professional_id and `is_active` = true
   - **Critical**: When service is selected, automatically filter and display only availability for that specific service
   - Availability is linked to `service_id` in `service_availabilities` table

4. **Date and Time Selection**
   - Display calendar with available dates and time slots
   - Only show slots that:
     - Match the selected service's availability
     - Are not already booked (status != 'cancelled' or 'completed')
     - Are in the future
   - Available slots displayed in **cyan** color

5. **Confirmation Screen**
   - After selecting date and time, show confirmation screen with:
     - Service name (from selected service)
     - Date and time of appointment
     - Client's personal details (pre-filled from user profile):
       - Name (required)
       - Surname (required)
       - Date of birth (required)
       - Email (required, pre-filled)
       - Phone number (optional)
   - All fields except phone number are mandatory
   - Submit button: "Confirm Request"

6. **Request Submission**
   - Validate all required fields
   - Create appointment record with status = 'pending'
   - Link to `service_id` (NOT gig_id)
   - Store client information

7. **Post-Submission Notifications**
   - Send automated email to client (text provided by client)
   - Send automated SMS to client (text provided by client)
   - Both notifications confirm the booking request was received

**Files to Create/Modify**:
- `app/Http/Controllers/AppointmentController.php` (new)
- `resources/views/web/appointments/book.blade.php` (new)
- `routes/web.php` (add routes)
- `app/Notifications/AppointmentRequestReceived.php` (new)

---

### Workflow 2: Appointment Confirmation (After Professional Approval)

**Trigger**: Professional confirms the appointment

**Steps**:
1. **Client Receives Notifications**
   - Confirmation email (text provided by client)
   - In-app notification (text provided by client)
   - Automatic chat message from professional's profile to client's profile
     - Message includes "Confirm" and "Cancel" buttons
     - Chat message text provided by client

2. **Calendar Update**
   - Appointment status changes from 'pending' to 'confirmed'
   - Calendar automatically updates
   - Confirmed appointments displayed in **green** color

**Files to Create/Modify**:
- `app/Notifications/AppointmentConfirmed.php` (new)
- `app/Http/Controllers/ChatController.php` (modify to send system messages)

---

## Professional-Side Workflows

### Workflow 3: Service Management

**Trigger**: Professional accesses service management

**Steps**:
1. **Service Creation/Management**
   - Professional can create and manage services
   - Services selected from dropdown (existing services table)
   - Each service can be configured independently
   - Services linked to `Service` model, NOT `Gig` model

**Files to Modify**:
- `app/Filament/Resources/ServiceResource.php` (existing, ensure no Gig references)

---

### Workflow 4: Availability Setup

**Trigger**: Professional sets availability for a service

**Steps**:
1. **Availability Configuration**
   - For each service, professional can set availability
   - Define preferred days of week (0-6, where 0=Sunday)
   - Define time slots (start_time, end_time)
   - Availability can be modified at any time

2. **Advanced Time Slot Management**
   - **Repeat Same Time Slot**: Select a time slot and apply it to multiple days
   - **Select Specific Days**: Choose specific days of the week
   - **Replicate Availability**:
     - Weekly basis: Copy availability pattern to all weeks
     - Monthly basis: Copy availability pattern to all months
   - This enables fast and automated calendar management

3. **Availability Storage**
   - Store in `service_availabilities` table
   - Linked to `service_id`
   - Each row represents one day-time slot combination

**Files to Create/Modify**:
- `app/Http/Controllers/ServiceAvailabilityController.php` (new)
- `app/Filament/Resources/ServiceAvailabilityResource.php` (new)
- `resources/views/filament/resources/service-availability-form.blade.php` (new)

---

### Workflow 5: Color-Coded Calendar View

**Display Rules**:
- **Available time slots** (from `service_availabilities`): Displayed in **cyan**
- **Pending booking requests** (status = 'pending'): Displayed in **red**
- **Confirmed bookings** (status = 'confirmed'): Displayed in **green**

**Calendar Features**:
- Professional can view all appointments in calendar format
- Filter by service (optional)
- Click on any appointment to view details

**Files to Create/Modify**:
- `app/Filament/Pages/AppointmentCalendar.php` (new)
- `resources/views/filament/pages/appointment-calendar.blade.php` (new)

---

### Workflow 6: Booking Request Management

**Trigger**: Professional clicks on a pending request (red) in calendar

**Steps**:
1. **View Request Details**
   - Display appointment information:
     - Service name
     - Date and time
     - Client details (name, email, phone, date of birth)
     - Request submission date

2. **Confirm Appointment**
   - Click "Confirm" button
   - Appointment status changes to 'confirmed'
   - Slot color changes from red to green
   - System automatically sends notifications to client:
     - Email
     - SMS
     - Chat message with "Confirm" and "Cancel" buttons

3. **Cancel Appointment** (Optional)
   - Professional can reject/cancel pending requests
   - Status changes to 'cancelled'
   - Notifications sent to client

**Files to Create/Modify**:
- `app/Http/Controllers/AppointmentController.php` (add confirm/cancel methods)
- `app/Notifications/AppointmentConfirmed.php` (new)
- `app/Notifications/AppointmentCancelledByProfessional.php` (new)

---

### Workflow 7: External Appointment Entry

**Purpose**: Allow professionals to manually enter appointments booked outside the platform

**Steps**:
1. **Manual Entry**
   - Professional can manually enter appointment details
   - Select date and time
   - Option to select event color (default: green for confirmed)
   - Mark as external appointment (`is_external` = true)

2. **Visibility**
   - External appointments are NOT visible to clients on the platform
   - Only visible to the professional in their calendar
   - Helps prevent overlaps with platform bookings

3. **Storage**
   - Store in `appointments` table
   - `is_external` = true
   - `status` = 'confirmed' (or as selected)
   - `external_color` = selected color

**Files to Create/Modify**:
- `app/Filament/Resources/AppointmentResource.php` (add external appointment form)
- `app/Http/Controllers/AppointmentController.php` (add storeExternal method)

---

### Workflow 8: Professional Cancellation of Confirmed Appointments

**Trigger**: Professional cancels a confirmed appointment from calendar

**Steps**:
1. **Cancellation Action**
   - Professional clicks on confirmed appointment (green) in calendar
   - Click "Cancel Appointment" button
   - Optional: Enter cancellation reason

2. **Status Update**
   - Appointment status changes to 'cancelled'
   - `cancelled_by` = 'professional'
   - `cancelled_at` = current timestamp
   - Calendar automatically updates

3. **Automated Notifications**
   - **Email** to client (automated, text provided by client)
   - **In-app notification** to client (automated, text provided by client)
   - **Chat message** to client (automated, text provided by client)

**Files to Create/Modify**:
- `app/Http/Controllers/AppointmentController.php` (add cancelByProfessional method)
- `app/Notifications/AppointmentCancelledByProfessional.php` (new)

---

### Workflow 9: Professional Notification System

**Trigger**: Professional receives a booking request

**Steps**:
1. **Notification Delivery**
   - Professional receives in-app notification when booking request is created
   - Notification type: 'appointment_request_received'
   - Notification includes: client name, service name, date, time

2. **Notification Display**
   - Show in professional's notification center (existing notification system)
   - Link to appointment details in calendar
   - Clicking notification opens appointment details modal

**Files to Modify**:
- `app/Models/Notification.php` (ensure appointment notification types are supported)
- `app/Http/Controllers/NotificationController.php` (ensure appointment notifications are handled)
- `app/Notifications/NewBookingRequest.php` (new, sends notification to professional)

---

## Notification System Workflows

### Workflow 10: Email and In-App Notifications

**Notification Types** (sent via both Email and In-App Notifications):
1. **Appointment Request Received** (to client)
   - Sent when client submits booking request
   - Text provided by client

2. **Appointment Confirmed** (to client)
   - Sent when professional confirms appointment
   - Text provided by client

3. **Appointment Cancelled by Professional** (to client)
   - Sent when professional cancels confirmed appointment
   - Text provided by client

4. **Appointment Cancelled by Client** (to professional)
   - Sent when client cancels appointment
   - Text provided by client

5. **Appointment Reminder** (to client)
   - Sent automatically 24 hours before appointment
   - Includes: date, time, professional name
   - Text provided by client

6. **New Booking Request** (to professional)
   - Sent when client submits a booking request
   - Text provided by client

**Implementation**:
- Use Laravel Notifications with multiple channels: 'mail' and 'database'
- Create notification classes in `app/Notifications/`
- Use email templates in `resources/views/emails/`
- Store in-app notifications in `notifications` table

**Files to Create**:
- `app/Notifications/AppointmentRequestReceived.php`
- `app/Notifications/AppointmentConfirmed.php`
- `app/Notifications/AppointmentCancelledByProfessional.php`
- `app/Notifications/AppointmentCancelledByClient.php`
- `app/Notifications/AppointmentReminder.php`
- `app/Notifications/NewBookingRequest.php` (for professionals)
- Email templates in `resources/views/emails/appointments/`

---

### Workflow 11: Platform Notifications (In-App)

**In-App Notification System**:
- Use existing `Notification` model and notification system
- Store notifications in `notifications` table
- Display in user's notification center

**Notification Types** (same as email):
1. Appointment Request Received (to client)
2. Appointment Confirmed (to client)
3. Appointment Cancelled by Professional (to client)
4. Appointment Cancelled by Client (to professional)
5. Appointment Reminder (to client)
6. New Booking Request (to professional)

**Implementation**:
- Use Laravel Notifications with 'database' channel
- Create notification records in `notifications` table
- Link to existing notification system
- Display in notification dropdown/center

**Files to Create/Modify**:
- Update all appointment notification classes to support 'database' channel
- `app/Models/Notification.php` (ensure appointment types are supported)
- `app/Http/Controllers/NotificationController.php` (ensure appointment notifications are handled)

---

### Workflow 12: Chat Notifications

**Chat Message Types**:
1. **Appointment Confirmation Message**
   - Sent automatically when professional confirms appointment
   - Message includes:
     - Appointment details (service, date, time)
     - Two interactive buttons: "Confirm" and "Cancel"
   - Message text provided by client
   - Sent from professional's profile to client's profile

2. **Appointment Cancellation Message**
   - Sent when appointment is cancelled
   - Message text provided by client

**Implementation**:
- Use existing `ChatRoom` and `ChatMessage` models
- Create system message sender
- Add button support to chat messages (may require frontend changes)

**Files to Create/Modify**:
- `app/Services/ChatService.php` (new, for system messages)
- `app/Http/Controllers/ChatController.php` (modify to handle system messages)
- Frontend chat component (modify to display buttons)

---

## Cancellation Policy Workflows

### Workflow 13: Client Cancellation (24-Hour Window)

**Rules**:
- Clients can cancel appointments up to 24 hours in advance
- Cancellation performed via button in chat interface with professional
- Maximum 3 cancellations per month per client
- If client exceeds 3 cancellations/month, account blocked from new bookings for 1 month

**Steps**:
1. **Cancellation Request**
   - Client opens chat with professional
   - If appointment is within 24 hours: Cancel button is **disabled**
   - If appointment is more than 24 hours away: Cancel button is **enabled**
   - Client clicks "Cancel" button in chat

2. **Cancellation Processing**
   - Check if cancellation is allowed (24+ hours before appointment)
   - Check monthly cancellation count for client
   - If count < 3:
     - Cancel appointment (status = 'cancelled', cancelled_by = 'client')
     - Increment monthly cancellation count
     - Send notifications to professional
   - If count >= 3:
     - Block client from making new bookings
     - Set `is_blocked` = true in `client_cancellation_tracking`
     - Set `blocked_until` = 1 month from current date
     - Show message: "You have exceeded the monthly cancellation limit. You cannot book new appointments until [date]."

3. **Monthly Reset**
   - Reset cancellation count at start of each month
   - Check if `blocked_until` date has passed, unblock if so

**Files to Create/Modify**:
- `app/Http/Controllers/AppointmentController.php` (add cancelByClient method)
- `app/Services/CancellationTrackingService.php` (new)
- `app/Models/ClientCancellationTracking.php` (new)
- Frontend chat component (add cancel button with 24-hour check)

---

### Workflow 14: Cancellation Lock & Reminder

**Cancellation Button Disablement**:
- Cancel button in chat automatically becomes disabled 24 hours before appointment
- Check: `appointment_date + appointment_time - 24 hours < current_datetime`
- Frontend should check this before enabling/disabling button

**Automatic Appointment Reminders**:
- Clients receive automatic reminders 24 hours before appointment
- Reminder channels:
  - **Email**: Includes date, time, professional name
  - **SMS**: Includes date, time, professional name
- Reminder content provided by client

**Implementation**:
- Create scheduled job to send reminders
- Check `appointment_reminders` table to prevent duplicates
- Run job daily (or hourly) to check for appointments 24 hours away

**Files to Create/Modify**:
- `app/Console/Commands/SendAppointmentReminders.php` (new)
- `app/Console/Kernel.php` (schedule reminder job)
- `app/Notifications/AppointmentReminder.php` (new)

---

## Calendar Management Workflows

### Workflow 15: Calendar Display Logic

**Color Coding**:
- **Cyan**: Available time slots (from `service_availabilities`)
- **Red**: Pending booking requests (`status = 'pending'`)
- **Green**: Confirmed bookings (`status = 'confirmed'`)

**Filtering**:
- Filter by service (dropdown)
- Filter by date range
- Show only appointments for selected service

**Calendar Library**:
- Use FullCalendar.js or similar
- Load appointments via AJAX
- Real-time updates (optional, via polling or WebSockets)

**Files to Create/Modify**:
- `app/Http/Controllers/AppointmentController.php` (add calendarData method)
- `app/Filament/Pages/AppointmentCalendar.php` (new)
- `resources/views/filament/pages/appointment-calendar.blade.php` (new)
- Frontend calendar component

---

## Technical Implementation Details

### Models to Create

#### 1. `app/Models/ServiceAvailability.php`
```php
- service_id (belongsTo Service)
- day_of_week, start_time, end_time, is_active
```

#### 2. `app/Models/Appointment.php`
```php
- service_id (belongsTo Service)
- client_id (belongsTo User)
- professional_id (belongsTo User)
- status, appointment_date, appointment_time
- Relationships: service(), client(), professional()
```

#### 3. `app/Models/ClientCancellationTracking.php`
```php
- client_id (belongsTo User)
- month, year, cancellation_count
- is_blocked, blocked_until
```

#### 4. `app/Models/AppointmentReminder.php`
```php
- appointment_id (belongsTo Appointment)
- reminder_type, sent_at
```

### Controllers to Create

1. `app/Http/Controllers/AppointmentController.php`
   - `book()` - Show booking interface
   - `store()` - Create booking request
   - `confirm()` - Professional confirms appointment
   - `cancelByClient()` - Client cancels appointment
   - `cancelByProfessional()` - Professional cancels appointment
   - `calendarData()` - Get calendar data (AJAX)
   - `storeExternal()` - Store external appointment

2. `app/Http/Controllers/ServiceAvailabilityController.php`
   - `index()` - List availabilities for service
   - `store()` - Create availability
   - `update()` - Update availability
   - `destroy()` - Delete availability
   - `replicate()` - Replicate availability (weekly/monthly)

### Services to Create

1. `app/Services/CancellationTrackingService.php`
   - Track monthly cancellations
   - Check if client is blocked
   - Block/unblock clients

2. `app/Services/ChatService.php`
   - Send system chat messages
   - Create chat rooms if needed
   - Send messages with buttons


### Notifications to Create

1. `app/Notifications/AppointmentRequestReceived.php`
2. `app/Notifications/AppointmentConfirmed.php`
3. `app/Notifications/AppointmentCancelledByProfessional.php`
4. `app/Notifications/AppointmentCancelledByClient.php`
5. `app/Notifications/AppointmentReminder.php`

### Routes to Add

```php
// Public routes
Route::get('/appointments/book/{username}', [AppointmentController::class, 'book'])->name('appointments.book');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Client routes
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancelByClient'])->name('appointments.cancel.client');
    
    // Professional routes (user_type = 3)
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/cancel-professional', [AppointmentController::class, 'cancelByProfessional'])->name('appointments.cancel.professional');
    Route::post('/appointments/external', [AppointmentController::class, 'storeExternal'])->name('appointments.store.external');
    Route::get('/appointments/calendar-data', [AppointmentController::class, 'calendarData'])->name('appointments.calendar.data');
    
    // Availability routes
    Route::resource('service-availabilities', ServiceAvailabilityController::class);
    Route::post('/service-availabilities/replicate', [ServiceAvailabilityController::class, 'replicate'])->name('service-availabilities.replicate');
});
```

### Migrations to Create

1. `create_service_availabilities_table.php`
2. `create_appointments_table.php`
3. `create_client_cancellation_tracking_table.php`
4. `create_appointment_reminders_table.php`

### Scheduled Jobs

Add to `app/Console/Kernel.php`:
```php
$schedule->command('appointments:send-reminders')->daily();
// Or hourly for more frequent checks
```

### Frontend Components

1. **Booking Interface** (`resources/views/web/appointments/book.blade.php`)
   - Service dropdown
   - Calendar with time slots
   - Confirmation form

2. **Calendar View** (Filament page)
   - FullCalendar.js integration
   - Color-coded events
   - Click handlers for appointments

3. **Chat Button Component**
   - Cancel button with 24-hour check
   - Disable/enable logic

---

## Important Notes

1. **Service Model Only**: All new functionality MUST be linked to `Service` model, NOT `Gig` model
2. **Text Content**: All notification texts (email, in-app notifications, chat) will be provided by the client
3. **Notification Channels**: System uses Email, In-App Notifications (database), and Platform Chat messages only
4. **Chat Buttons**: Chat message buttons may require frontend framework changes (Livewire/Alpine.js)
5. **Calendar Library**: Choose appropriate calendar library (FullCalendar.js recommended)
6. **Time Zone**: Consider time zone handling for appointments
7. **Validation**: Ensure proper validation for all appointment operations
8. **Security**: Implement proper authorization checks (clients can only cancel their own appointments, etc.)

---

## Testing Checklist

- [ ] Client can book appointment after selecting service
- [ ] Availability filters correctly by service
- [ ] Notifications sent on booking request
- [ ] Professional can confirm appointment
- [ ] Notifications sent on confirmation
- [ ] Calendar displays correct colors
- [ ] Client can cancel within 24-hour window
- [ ] Cancel button disabled 24 hours before appointment
- [ ] Monthly cancellation limit enforced
- [ ] Client blocked after 3 cancellations
- [ ] Professional can cancel confirmed appointments
- [ ] External appointments don't show to clients
- [ ] Reminders sent 24 hours before appointment
- [ ] Chat messages with buttons work correctly
- [ ] All notifications include required information

---

**Document Version**: 1.1  
**Last Updated**: January 25, 2026  
**Status**: Planning Phase  
**Changes**: Removed SMS notifications - using Email, In-App Notifications, and Platform Chat only
