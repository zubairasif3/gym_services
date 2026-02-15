@extends('web.layouts.app')
@section('title', 'Book Appointment')

@section('content')

<!-- Booking Section: Dark theme, sidebar + week grid -->
<section class="booking-section-dark">
    <div class="booking-wrapper">
        <!-- Left Sidebar -->
        <aside class="booking-sidebar">
            {{-- <a href="{{ route('web.index') }}" class="booking-sidebar-logo">
                <img src="{{ asset('web/images/logo.png') }}" alt="FitScout" width="120">
            </a> --}}
            <div class="booking-profile">
                <div class="booking-avatar">
                    @if($professional->avatar_url)
                        <img src="{{ asset('storage/' . $professional->avatar_url) }}" alt="{{ $professional->name }}">
                    @else
                        <img src="{{ asset('web/images/resource/user.png') }}" alt="{{ $professional->name }}">
                    @endif
                </div>
                <h2 class="booking-profile-name">{{ $professional->name }} {{ $professional->surname }}</h2>
            </div>
            <div class="booking-sidebar-block">
                <label for="service-select" class="booking-label">Services</label>
                <select id="service-select" class="booking-select">
                    <option value="">-- Select a Service --</option>
                    @foreach($professional->services as $service)
                        <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                            {{ $service->title }} - €{{ number_format($service->price, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="booking-sidebar-block">
                <span class="booking-label">Appointments</span>
                <div class="booking-legend">
                    <span class="booking-legend-item"><i class="booking-legend-box booking-legend-available"></i> Available</span>
                    <span class="booking-legend-item"><i class="booking-legend-box booking-legend-waiting"></i> Waiting</span>
                    <span class="booking-legend-item"><i class="booking-legend-box booking-legend-booked"></i> Booked</span>
                </div>
            </div>
        </aside>

        <!-- Main: Calendar or Confirm -->
        <main class="booking-main">
            <!-- Step: Date & Time (week grid) -->
            <div id="step-datetime" class="booking-step">
                <div id="calendar-header-placeholder" class="booking-calendar-header"></div>
                <div id="calendar-container" class="booking-calendar-container"></div>
                <div id="calendar-no-service" class="booking-no-service" style="display: none;">
                    <p>Select a service from the sidebar to see available time slots.</p>
                </div>
                <div id="calendar-error" class="booking-calendar-error" style="display: none;"></div>
                <div id="selected-datetime-bar" class="booking-selected-bar" style="display: none;">
                    <span class="booking-selected-label">Selected:</span>
                    <span id="selected-date-display"></span> at <span id="selected-time-display"></span>
                    <button type="button" class="booking-btn booking-btn-primary" id="btn-next-datetime">
                        Confirm Booking <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="bookingConfirmModal" tabindex="-1" aria-labelledby="bookingConfirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background: #2d2d2d; border: 1px solid #404040;">
                        <div class="modal-header" style="border-color: #404040;">
                            <h5 class="modal-title text-white" id="bookingConfirmModalLabel">Confirm Appointment</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-white">
                            <p><strong>Service:</strong> <span id="modal-service"></span></p>
                            <p><strong>Date:</strong> <span id="modal-date"></span></p>
                            <p><strong>Time:</strong> <span id="modal-time"></span></p>
                        </div>
                        <div class="modal-footer" style="border-color: #404040;">
                            <button type="button" class="booking-btn booking-btn-outline" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="booking-btn booking-btn-primary" id="btn-confirm-booking">
                                <i class="far fa-calendar-check"></i> Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="booking-messages" class="booking-messages"></div>
        </main>
    </div>
</section>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
<style>
.booking-section-dark {
    background: #1a1a1a;
    min-height: calc(100vh - 78px);
    padding: 0;
}
.booking-wrapper {
    display: flex;
    min-height: calc(100vh - 78px);
}
.booking-sidebar {
    width: 280px;
    min-width: 280px;
    background: #0d0d0d;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    border-right: 1px solid #2d2d2d;
}
.booking-sidebar-logo {
    display: block;
}
.booking-sidebar-logo img {
    max-width: 140px;
    height: auto;
}
.booking-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}
.booking-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    background: #2d2d2d;
}
.booking-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.booking-profile-name {
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    text-align: center;
}
.booking-label {
    color: #9ca3af;
    font-size: 0.875rem;
    font-weight: 500;
    display: block;
    margin-bottom: 8px;
}
.booking-select {
    width: 100%;
    padding: 10px 12px;
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #111;
    font-size: 0.9375rem;
}
.booking-legend {
    margin-top: 8px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.booking-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #e5e7eb;
    font-size: 0.875rem;
}
.booking-legend-box {
    width: 16px;
    height: 16px;
    border-radius: 3px;
    flex-shrink: 0;
}
.booking-legend-available {
    background: #00b3f1;
}
.booking-legend-waiting {
    background: #dc3545;
}
.booking-legend-booked {
    background: #28a745;
}
.booking-main {
    flex: 1;
    padding: 24px;
    overflow: auto;
}
.booking-calendar-container {
    background: #2d2d2d;
    border-radius: 8px;
    padding: 16px;
    min-height: 500px;
}
.booking-no-service {
    color: #9ca3af;
    text-align: center;
    padding: 48px 24px;
}
.booking-calendar-error {
    color: #fca5a5;
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid #ef4444;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 12px;
    font-size: 0.9375rem;
}
.booking-selected-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 16px;
    padding: 12px 16px;
    background: #2d2d2d;
    border-radius: 8px;
    color: #e5e7eb;
    flex-wrap: wrap;
}
.booking-selected-label {
    font-weight: 600;
    color: #00b3f1;
}
.booking-btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 0.9375rem;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
}
.booking-btn-primary {
    background: #00b3f1;
    color: #fff;
}
.booking-btn-primary:hover:not(:disabled) {
    background: #0099d4;
}
.booking-btn-outline {
    background: transparent;
    color: #00b3f1;
    border: 1px solid #00b3f1;
}
.booking-btn-outline:hover {
    background: rgba(0, 179, 241, 0.1);
}
.booking-btn-block {
    width: 100%;
    justify-content: center;
}
.booking-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.booking-confirm-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}
.booking-confirm-header h3 {
    color: #fff;
    margin: 0;
    font-size: 1.25rem;
}
.booking-confirm-body {
    max-width: 560px;
}
.booking-confirm-details {
    background: #2d2d2d;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
    color: #e5e7eb;
}
.booking-confirm-details h4,
.booking-form h4 {
    color: #fff;
    margin: 0 0 12px 0;
    font-size: 1rem;
}
.booking-confirm-details p {
    margin: 0 0 8px 0;
    font-size: 0.9375rem;
}
.booking-form .booking-form-group {
    margin-bottom: 16px;
}
.booking-form .booking-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.booking-form label {
    display: block;
    color: #e5e7eb;
    font-size: 0.875rem;
    margin-bottom: 6px;
}
.booking-input {
    width: 100%;
    padding: 10px 12px;
    background: #2d2d2d;
    border: 1px solid #404040;
    border-radius: 6px;
    color: #fff;
    font-size: 0.9375rem;
}
.booking-input:focus {
    outline: none;
    border-color: #00b3f1;
}
.booking-form-note {
    color: #9ca3af;
    font-size: 0.875rem;
    margin-bottom: 16px;
}
.booking-messages {
    margin-top: 24px;
}
.booking-messages .alert {
    padding: 16px;
    border-radius: 8px;
}
.booking-messages .alert-success {
    background: rgba(34, 197, 94, 0.15);
    color: #86efac;
    border: 1px solid #22c55e;
}
.booking-messages .alert-danger {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
    border: 1px solid #ef4444;
}

/* FullCalendar dark overrides */
.booking-calendar-container .fc {
    --fc-border-color: #404040;
    --fc-page-bg-color: #2d2d2d;
    --fc-neutral-bg-color: #2d2d2d;
    --fc-list-event-hover-bg-color: #404040;
    --fc-button-bg-color: #404040;
    --fc-button-border-color: #404040;
    --fc-button-hover-bg-color: #525252;
    --fc-button-hover-border-color: #525252;
    --fc-button-active-bg-color: #00b3f1;
    --fc-button-active-border-color: #00b3f1;
    --fc-today-bg-color: rgba(0, 179, 241, 0.15);
    --fc-non-business: #252525;
    --fc-small-font-size: 0.85em;
}
.booking-calendar-container .fc-theme-standard td,
.booking-calendar-container .fc-theme-standard th {
    border-color: #404040;
}
.booking-calendar-container .fc-scrollgrid {
    border-color: #404040;
}
.booking-calendar-container .fc-col-header-cell-cushion,
.booking-calendar-container .fc-timegrid-slot-label-cushion {
    color: #9ca3af;
}
.booking-calendar-container .fc-timegrid-slot {
    min-height: 32px;
}
.booking-calendar-container .fc-event {
    cursor: pointer;
    border: none;
    font-size: 0.8rem;
}
.booking-calendar-container .fc-event:hover {
    opacity: 0.9;
}
.booking-calendar-container .fc .fc-toolbar-title {
    color: #fff;
    font-size: 1.25rem;
}
.booking-calendar-container .fc .fc-button {
    color: #e5e7eb;
}
.booking-calendar-container .fc .fc-timegrid-axis-cushion {
    color: #9ca3af;
}
.booking-calendar-container .fc-direction-ltr .fc-timegrid-slot-label-frame {
    text-align: center;
}

@media (max-width: 768px) {
    .booking-wrapper {
        flex-direction: column;
    }
    .booking-sidebar {
        width: 100%;
        min-width: 100%;
        border-right: none;
        border-bottom: 1px solid #2d2d2d;
    }
    .booking-form .booking-form-row {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarDataUrl = '{{ route("appointments.book.calendar-data", ["username" => $professional->username]) }}';
    const storeUrl = '{{ route("appointments.store") }}';
    const loginUrl = '{{ route("web.login") }}';
    const returnUrl = '{{ url()->current() }}';

    @php
        $bookingUserData = Auth::check() ? [
            'name' => Auth::user()->name,
            'surname' => Auth::user()->surname,
            'email' => Auth::user()->email,
            'phone' => Auth::user()->profile?->phone ?? '',
            'date_of_birth' => Auth::user()->profile?->date_of_birth?->format('Y-m-d') ?? ''
        ] : null;
    @endphp
    const bookingUser = @json($bookingUserData);

    let selectedServiceId = null;
    let selectedDate = null;
    let selectedTime = null;
    let selectedTimeDisplay = null;
    let calendar = null;

    const serviceSelect = document.getElementById('service-select');
    const calendarContainer = document.getElementById('calendar-container');
    const calendarNoService = document.getElementById('calendar-no-service');
    const selectedDatetimeBar = document.getElementById('selected-datetime-bar');
    const btnNextDateTime = document.getElementById('btn-next-datetime');

    function getCalendarDataUrl() {
        return calendarDataUrl;
    }

    serviceSelect.addEventListener('change', function() {
        selectedServiceId = this.value || null;
        var errEl = document.getElementById('calendar-error');
        if (errEl) errEl.style.display = 'none';
        if (selectedServiceId) {
            calendarNoService.style.display = 'none';
            calendarContainer.style.display = 'block';
            if (calendar) {
                calendar.refetchEvents();
            } else {
                initCalendar();
            }
        } else {
            calendarContainer.style.display = 'none';
            calendarNoService.style.display = 'block';
            if (calendar) {
                calendar.removeAllEvents();
            }
        }
        selectedDatetimeBar.style.display = 'none';
        selectedDate = null;
        selectedTime = null;
    });

    function initCalendar() {
        if (calendar) {
            calendar.destroy();
            calendar = null;
        }
        if (!selectedServiceId) {
            calendarNoService.style.display = 'block';
            calendarContainer.style.display = 'none';
            return;
        }
        calendarNoService.style.display = 'none';
        calendarContainer.style.display = 'block';

        calendar = new FullCalendar.Calendar(calendarContainer, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            slotMinTime: '00:00:00',
            slotMaxTime: '24:00:00',
            slotDuration: '01:00:00',
            allDaySlot: false,
            validRange: {
                start: (function() {
                    const d = new Date();
                    d.setFullYear(d.getFullYear() - 1);
                    return d.toISOString().split('T')[0];
                })(),
                end: (function() {
                    const d = new Date();
                    d.setFullYear(d.getFullYear() + 1);
                    return d.toISOString().split('T')[0];
                })()
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                var errEl = document.getElementById('calendar-error');
                if (errEl) errEl.style.display = 'none';
                if (!selectedServiceId) {
                    successCallback([]);
                    return;
                }
                var url = getCalendarDataUrl() + '?service_id=' + encodeURIComponent(selectedServiceId) + '&start=' + encodeURIComponent(fetchInfo.startStr) + '&end=' + encodeURIComponent(fetchInfo.endStr);
                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            if (!response.ok) {
                                var msg = (data && (data.error || data.message)) || 'Unable to load time slots.';
                                if (errEl) {
                                    errEl.textContent = msg;
                                    errEl.style.display = 'block';
                                }
                                successCallback([]);
                                return;
                            }
                            if (data.error || (data.message && !data.events)) {
                                if (errEl) {
                                    errEl.textContent = data.error || data.message || 'Unable to load time slots.';
                                    errEl.style.display = 'block';
                                }
                                successCallback([]);
                                return;
                            }
                            var events = (data.events || []).map(function(ev) {
                                return {
                                    title: ev.title || 'Available',
                                    start: ev.start,
                                    end: ev.end,
                                    backgroundColor: ev.color || '#00b3f1',
                                    borderColor: ev.color || '#00b3f1',
                                    extendedProps: ev.extendedProps || {}
                                };
                            });
                            if (events.length === 0 && errEl) {
                                errEl.textContent = 'No available time slots for this week. Try another week or ensure the professional has set availability for this service.';
                                errEl.style.display = 'block';
                                errEl.style.color = '#9ca3af';
                                errEl.style.background = 'rgba(156, 163, 175, 0.15)';
                                errEl.style.borderColor = '#6b7280';
                            }
                            successCallback(events);
                        });
                    })
                    .catch(function(err) {
                        console.error('Error loading calendar:', err);
                        if (errEl) {
                            errEl.textContent = 'Unable to load time slots. Please try again.';
                            errEl.style.display = 'block';
                        }
                        successCallback([]);
                    });
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                const props = info.event.extendedProps || {};
                if (props.bookable === false) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Slot Unavailable',
                        text: 'This slot is ' + info.event.title.toLowerCase() + '. Please select an available slot.',
                        confirmButtonColor: '#00b3f1'
                    });
                    return;
                }
                const slotStart = info.event.start;
                if (slotStart < new Date()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Selection',
                        text: 'Please select future appointments.',
                        confirmButtonColor: '#00b3f1'
                    });
                    return;
                }
                selectedDate = props.date || info.event.startStr.slice(0, 10);
                selectedTime = props.time || info.event.startStr.slice(11, 16);
                selectedTimeDisplay = (function() {
                    const d = info.event.start;
                    return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
                })();
                document.getElementById('selected-date-display').textContent = info.event.start.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                document.getElementById('selected-time-display').textContent = selectedTimeDisplay;
                selectedDatetimeBar.style.display = 'flex';
                btnNextDateTime.disabled = false;
            }
        });
        calendar.render();
    }

    const bookingConfirmModal = new bootstrap.Modal(document.getElementById('bookingConfirmModal'));

    btnNextDateTime.addEventListener('click', function() {
        if (!selectedDate || !selectedTime) return;
        const serviceOption = serviceSelect.options[serviceSelect.selectedIndex];
        document.getElementById('modal-service').textContent = serviceOption ? serviceOption.text : '';
        document.getElementById('modal-date').textContent = new Date(selectedDate + 'T12:00:00').toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('modal-time').textContent = selectedTimeDisplay || selectedTime;
        bookingConfirmModal.show();
    });

    document.getElementById('btn-confirm-booking').addEventListener('click', function() {
        if (!bookingUser) {
            Swal.fire({
                icon: 'warning',
                title: 'Login Required',
                text: 'Please log in to book an appointment.',
                confirmButtonColor: '#00b3f1'
            }).then(function() {
                window.location.href = loginUrl + '?intended=' + encodeURIComponent(returnUrl);
            });
            return;
        }
        var submitBtn = document.getElementById('btn-confirm-booking');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        var formData = new FormData();
        formData.append('service_id', selectedServiceId);
        formData.append('appointment_date', selectedDate);
        formData.append('appointment_time', selectedTime);
        formData.append('client_name', bookingUser.name);
        formData.append('client_surname', bookingUser.surname);
        formData.append('client_email', bookingUser.email);
        formData.append('client_phone', bookingUser.phone || '');
        formData.append('client_date_of_birth', bookingUser.date_of_birth);

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(response) {
            if (response.status === 401 || response.status === 403) {
                return response.json().then(function(body) {
                    throw { authRequired: true, message: body.error || 'Please log in to book an appointment.' };
                }).catch(function(err) {
                    if (err.authRequired) throw err;
                    throw { authRequired: true, message: 'Please log in to book an appointment.' };
                });
            }
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                bookingConfirmModal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Submitted!',
                    html: '<p>' + (data.message || '') + '</p><p>You will receive a confirmation email once the professional confirms your appointment.</p>',
                    confirmButtonColor: '#00b3f1'
                }).then(function() {
                    window.location.href = '{{ route("web.index") }}';
                });
            } else {
                throw new Error(data.error || data.message || 'Failed to submit booking');
            }
        })
        .catch(function(error) {
            var msg = error.message || 'An error occurred. Please try again.';
            if (error.authRequired) {
                msg = error.message || 'Please log in to book an appointment.';
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                    text: msg,
                    confirmButtonColor: '#00b3f1'
                }).then(function() {
                    window.location.href = loginUrl + '?intended=' + encodeURIComponent(returnUrl);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    confirmButtonColor: '#00b3f1'
                });
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="far fa-calendar-check"></i> Confirm';
        });
    });

    if (selectedServiceId) {
        initCalendar();
    } else {
        calendarNoService.style.display = 'block';
        calendarContainer.style.display = 'none';
    }
});
</script>
@endpush
@endsection
