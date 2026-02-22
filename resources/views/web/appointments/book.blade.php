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
                <div id="calendar-header-placeholder" class="booking-calendar-header booking-custom-toolbar-wrap" style="display: none;"></div>
                <div id="calendar-container" class="booking-calendar-container"></div>
                <div id="calendar-no-service" class="booking-no-service" style="display: none;">
                    <p>Select a service from the sidebar to see available time slots.</p>
                </div>
                <div id="calendar-error" class="booking-calendar-error" style="display: none;"></div>
                @if(!$isServiceOwner)
                <div id="selected-datetime-bar" class="booking-selected-bar" style="display: none;">
                    <span class="booking-selected-label">Selected:</span>
                    <span id="selected-date-display"></span> at <span id="selected-time-display"></span>
                    <button type="button" class="booking-btn booking-btn-primary" id="btn-next-datetime">
                        Confirm Booking <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                @endif
            </div>

            @if(!$isServiceOwner)
            <!-- Confirmation Modal (only for clients booking; hidden for service owner) -->
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
            @endif

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
.booking-calendar-container .fc-timegrid-slot-lane {
    position: relative;
    z-index: 1;
}
/* Events layer: transparent so slot lane (+ buttons) shows through in empty cells */
.booking-calendar-container .fc-timegrid-col-events {
    background: transparent;
    pointer-events: none;
}
.booking-calendar-container .fc-timegrid-col-events .fc-timegrid-event-harness {
    pointer-events: auto;
}
/* Ensure + add-slot row is visible and clickable (professional only) */
.booking-calendar-container .fc-timegrid-slot .fc-timegrid-slot-lane .booking-slot-plus-row {
    z-index: 2;
    pointer-events: auto;
}
/* Event harness: allow event to fill segment so 30-min slots are not clipped */
.booking-calendar-container .fc-timegrid-event-harness {
    overflow: visible;
}
/* 30-min slots: half-width side by side (first half left, second half right) like reference */
.booking-calendar-container .fc-timegrid-event-harness.fc-harness-first-half {
    left: 0 !important;
    width: calc(50% - 2px) !important;
}
.booking-calendar-container .fc-timegrid-event-harness.fc-harness-second-half {
    left: calc(50% + 2px) !important;
    width: calc(50% - 2px) !important;
}
.booking-calendar-container .fc-timegrid-event-harness .fc-timegrid-event {
    min-height: 100%;
}
/* Reference: rounded button-like event blocks, white text */
.booking-calendar-container .fc-event {
    cursor: pointer;
    border: none;
    border-radius: 8px;
    font-size: 0.8rem;
    padding: 4px 8px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-weight: 500;
    padding-top: 0px;
    height: 100%;
    min-height: 100%;
}
.booking-calendar-container .fc-event .fc-event-main {
    border-radius: 8px;
    height: 100%;
    min-height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.booking-calendar-container .fc-event:hover {
    opacity: 0.92;
}
/* Empty slot: light grey cell with + icon (reference second SS) */
.booking-slot-plus-row {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    height: 100%;
    min-height: 32px;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
}
/* Each day cell contains two plus buttons: first half (e.g. 09:00) and second half (09:30) */
.booking-slot-plus-cell {
    display: flex;
    gap: 2px;
    margin: 2px;
    min-width: 0;
}
.booking-slot-plus-cell .booking-slot-plus,
.booking-slot-plus-cell .booking-slot-placeholder {
    flex: 1;
    min-width: 0;
}
.booking-slot-plus {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 1.1rem;
    font-weight: 300;
    background: #252525;
    border-radius: 6px;
    cursor: pointer;
    pointer-events: auto;
    transition: background 0.15s, color 0.15s;
}
.booking-slot-plus:hover {
    background: #333;
    color: #06a7e1;
}
.booking-slot-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #252525;
    border-radius: 6px;
    pointer-events: none;
}
.booking-calendar-container .fc .fc-toolbar-title {
    color: #ffffff;
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: uppercase;
}
.booking-calendar-container .fc .fc-button {
    color: #ffffff;
}
.booking-calendar-container .fc .fc-timegrid-axis-cushion {
    color: #9ca3af;
}
.booking-calendar-container .fc-direction-ltr .fc-timegrid-slot-label-frame {
    text-align: center;
}
/* Same width for all time labels (01:00 and 11:00) – reference design */
.booking-calendar-container .fc-timegrid-axis {
    min-width: 52px;
    width: 52px;
}
.booking-calendar-container .fc-timegrid-slot-label-cushion,
.booking-calendar-container .fc-timegrid-axis-cushion {
    font-variant-numeric: tabular-nums;
    min-width: 3ch;
    display: inline-block;
    text-align: right;
}
.booking-calendar-container .fc-timegrid-slot-label {
    width: 100%;
}
.booking-calendar-container .fc-direction-ltr .fc-timegrid-slot-label-frame {
    text-align: right;
    justify-content: flex-end;
    padding-right: 8px;
}

/* Custom toolbar: week [‹][›] | [‹] MONTH [›] | [‹] YEAR [›] – reference blue bar */
.booking-custom-toolbar-wrap {
    background: #06a7e1;
    border-radius: 8px 8px 0 0;
    padding: 10px 12px;
    margin-bottom: 0;
}
.booking-custom-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    width: 100%;
}
.booking-toolbar-center {
    flex: 0 0 auto;
}
.booking-toolbar-center-label {
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    padding: 0 4px;
}
.booking-toolbar-group {
    display: flex;
    align-items: center;
    gap: 6px;
}
.booking-toolbar-group .booking-toolbar-btn {
    background: #fff;
    border: 1px solid rgba(255,255,255,0.5);
    color: #06a7e1;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    padding: 0;
    font-size: 1.1rem;
    line-height: 1;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.booking-toolbar-group .booking-toolbar-btn:hover {
    background: rgba(255, 255, 255, 0.9);
    color: #06a7e1;
}
.booking-toolbar-group .booking-toolbar-label {
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: uppercase;
    min-width: 4ch;
    text-align: center;
}
/* Hide FullCalendar’s default toolbar when using custom header */
.booking-calendar-container .fc-header-toolbar {
    display: none;
}
.booking-calendar-container .fc-col-header {
    background: #1f2933;
}
.booking-calendar-container .fc-col-header-cell {
    padding: 8px 4px;
}
.booking-calendar-container .fc-col-header-cell-cushion {
    font-weight: 600;
    color: #7dd3fc;
    font-size: 0.9rem;
}
.booking-calendar-container .fc-timegrid-axis {
    background: #252525;
}
/* Ensure time grid scrolls so 01:00–24:00 are all reachable */
.booking-calendar-container .fc-scrollgrid-section-body > * {
    overflow-y: auto;
}
.booking-calendar-container .fc-timegrid-body {
    min-height: 0;
}

.text-white p {
    color: #fff;
}
.modal-footer .booking-btn {
    margin-left: 10px;
}
.booking-calendar-error {
    display: none;
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
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales-all.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    /* Prevent shake: use one consistent scrollbar-width padding. Capture width before modal opens, then in didOpen overwrite body padding (so we replace SweetAlert2's padding with our value, no double padding). */
    function getScrollbarWidth() {
        return window.innerWidth - document.documentElement.clientWidth;
    }
    var _scrollbarWidth = 0;
    var bookingSwal = Swal.mixin({
        didOpen: function() {
            var w = _scrollbarWidth > 0 ? _scrollbarWidth : getScrollbarWidth();
            document.body.style.paddingRight = w + 'px';
        },
        didClose: function() {
            document.body.style.paddingRight = '';
        }
    });
    var _realFire = bookingSwal.fire.bind(bookingSwal);
    bookingSwal.fire = function(opts) {
        _scrollbarWidth = getScrollbarWidth();
        return _realFire(opts);
    };

    const calendarDataUrl = '{{ route("appointments.book.calendar-data", ["username" => $professional->username]) }}';
    const storeUrl = '{{ route("appointments.store") }}';
    const availabilityStoreUrl = '{{ route("service-availabilities.store") }}';
    const availabilityStoreWithRepeatUrl = '{{ route("service-availabilities.store-with-repeat") }}';
    const loginUrl = '{{ route("web.login") }}';
    const returnUrl = '{{ url()->current() }}';
    @php
        // Always derive from auth + page professional so plus button shows for own calendar (e.g. username "AC PT" = cadelano)
        $isServiceOwner = Auth::check() && (
            (int) Auth::id() === (int) $professional->id
            || (Auth::user()->username !== null && trim((string) Auth::user()->username) !== '' && Auth::user()->username === $professional->username)
        );
        $bookingDebug = [
            'logged_in' => Auth::check(),
            'auth_id' => Auth::check() ? Auth::id() : null,
            'auth_username' => Auth::check() ? (Auth::user()->username ?? null) : null,
            'auth_email' => Auth::check() ? Auth::user()->email : null,
            'professional_id' => $professional->id,
            'professional_username' => $professional->username,
            'url_username' => request()->route('username'),
            'isServiceOwner' => $isServiceOwner,
            'match_by_id' => Auth::check() ? ((int) Auth::id() === (int) $professional->id) : false,
            'match_by_username' => Auth::check() && Auth::user()->username && $professional->username ? (Auth::user()->username === $professional->username) : false,
        ];
    @endphp
    const isServiceOwner = @json($isServiceOwner);
    const bookingDebug = @json($bookingDebug);

    console.log('[Booking] Debug:', bookingDebug);
    console.log('[Booking] isServiceOwner =', isServiceOwner, '(plus button should', isServiceOwner ? 'show)' : 'be hidden)');

    // Detect current Google Translate language (defaults to English)
    function getCurrentGoogleLanguage() {
        const match = document.cookie.match(/googtrans=\/[a-z]{2}\/([a-z]{2})/);
        return match ? match[1] : 'en';
    }

    function getCalendarLocale() {
        if (typeof window._bookingCalendarLocale === 'string') return window._bookingCalendarLocale;
        const lang = getCurrentGoogleLanguage();
        return (lang === 'it') ? 'it' : 'en';
    }
    function setBookingCalendarLocale(locale) {
        window._bookingCalendarLocale = locale;
    }

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
        console.log('[Booking] Service changed: selectedServiceId =', selectedServiceId, 'isServiceOwner =', isServiceOwner);
        var errEl = document.getElementById('calendar-error');
        if (errEl) errEl.style.display = 'none';
        var headerPlaceholder = document.getElementById('calendar-header-placeholder');
        if (selectedServiceId) {
            calendarNoService.style.display = 'none';
            calendarContainer.style.display = 'block';
            if (calendar) {
                calendar.refetchEvents();
                if (headerPlaceholder) headerPlaceholder.style.display = 'block';
            } else {
                initCalendar();
            }
        } else {
            calendarContainer.style.display = 'none';
            calendarNoService.style.display = 'block';
            if (headerPlaceholder) headerPlaceholder.style.display = 'none';
            if (calendar) {
                calendar.removeAllEvents();
            }
        }
        if (selectedDatetimeBar) selectedDatetimeBar.style.display = 'none';
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

        setBookingCalendarLocale(getCalendarLocale());
        console.log('[Booking] initCalendar: selectedServiceId =', selectedServiceId, 'isServiceOwner =', isServiceOwner);
        var calendarHeaderPlaceholder = document.getElementById('calendar-header-placeholder');
        calendar = new FullCalendar.Calendar(calendarContainer, {
            initialView: 'timeGridWeek',
            locale: getCalendarLocale(),
            firstDay: 1, // start week on Monday
            headerToolbar: false,
            slotMinTime: '01:00:00',
            slotMaxTime: '25:00:00', /* include 24:00 row */
            slotDuration: '01:00:00', /* hourly rows; 30-min slots added via + (max 2 per hour) */
            contentHeight: 747,
            slotLabelFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            slotLabelContent: function(arg) {
                var d = arg.date;
                if (d.getHours() === 0 && d.getMinutes() === 0) return '24:00';
                return arg.text;
            },
            slotLaneContent: function(arg) {
                if (!isServiceOwner) return '';
                var slotDate = arg.date;
                var slotTime = String(slotDate.getHours()).padStart(2, '0') + ':' + String(slotDate.getMinutes()).padStart(2, '0');
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                /* Use view's visible week start (Monday) when available so plus buttons match displayed columns after week switch */
                var weekStart;
                if (calendar && calendar.view && calendar.view.activeStart) {
                    var as = calendar.view.activeStart;
                    weekStart = new Date(as.getFullYear(), as.getMonth(), as.getDate());
                } else {
                    var d = new Date(slotDate.getFullYear(), slotDate.getMonth(), slotDate.getDate());
                    var dayOfWeek = d.getDay();
                    var daysFromMonday = (dayOfWeek === 0) ? 6 : dayOfWeek - 1;
                    d.setDate(d.getDate() - daysFromMonday);
                    weekStart = d;
                }
                /* First half = HH:00, second half = HH:30 */
                var parts = slotTime.split(':');
                var hour = parseInt(parts[0], 10);
                var timeFirst = slotTime;
                var timeSecond = String(hour).padStart(2, '0') + ':30';
                var html = '<div class="booking-slot-plus-row">';
                for (var c = 0; c < 7; c++) {
                    var cellDay = new Date(weekStart.getFullYear(), weekStart.getMonth(), weekStart.getDate() + c);
                    cellDay.setHours(0, 0, 0, 0);
                    var isPast = cellDay < today;
                    html += '<div class="booking-slot-plus-cell">';
                    if (isPast) {
                        html += '<span class="booking-slot-placeholder"></span><span class="booking-slot-placeholder"></span>';
                    } else {
                        html += '<span class="booking-slot-plus" data-slot-time="' + timeFirst + '" data-column="' + c + '" data-half="first" role="button" tabindex="0">+</span>';
                        html += '<span class="booking-slot-plus" data-slot-time="' + timeSecond + '" data-column="' + c + '" data-half="second" role="button" tabindex="0">+</span>';
                    }
                    html += '</div>';
                }
                html += '</div>';
                return { html: html };
            },
            /* Per-column day header: EN = Sun/Mon…, IT = Dom/Lun… (week starts Monday) */
            dayHeaderContent: function(arg) {
                var d = arg && arg.date;
                if (!d || typeof d.getDay !== 'function') return (arg && arg.text) ? arg.text : '';
                var dayOfWeek = d.getDay();
                var dayNum = d.getDate();
                var locale = getCalendarLocale();
                var namesEn = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                var namesIt = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
                var names = (locale === 'it') ? namesIt : namesEn;
                return names[dayOfWeek] + ' ' + String(dayNum).padStart(2, '0');
            },
            datesSet: function(arg) {
                if (window._bookingHeaderUpdate) window._bookingHeaderUpdate(arg.start, arg.end);
                /* Debug: log visible range every time week/month changes */
                var startStr = arg.start ? (arg.start.getFullYear() + '-' + String(arg.start.getMonth() + 1).padStart(2, '0') + '-' + String(arg.start.getDate()).padStart(2, '0')) : '';
                var endStr = arg.end ? (arg.end.getFullYear() + '-' + String(arg.end.getMonth() + 1).padStart(2, '0') + '-' + String(arg.end.getDate()).padStart(2, '0')) : '';
                var viewStart = (calendar && calendar.view && calendar.view.activeStart) ? (calendar.view.activeStart.getFullYear() + '-' + String(calendar.view.activeStart.getMonth() + 1).padStart(2, '0') + '-' + String(calendar.view.activeStart.getDate()).padStart(2, '0')) : 'n/a';
                console.log('[Booking] datesSet (week switched): visible start =', startStr, 'end =', endStr, 'view.activeStart =', viewStart, 'isServiceOwner =', isServiceOwner);
                /* After week navigation, re-render so slot lanes (plus buttons) use the new week */
                if (calendar && isServiceOwner && !window._bookingDatesSetRendering) {
                    window._bookingDatesSetRendering = true;
                    setTimeout(function() {
                        try {
                            if (calendar.view) calendar.render();
                            setTimeout(function() {
                                var plusCount = calendarContainer.querySelectorAll('.booking-slot-plus').length;
                                var placeholderCount = calendarContainer.querySelectorAll('.booking-slot-placeholder').length;
                                console.log('[Booking] After week switch render: .booking-slot-plus =', plusCount, '.booking-slot-placeholder =', placeholderCount);
                            }, 150);
                        } finally {
                            window._bookingDatesSetRendering = false;
                        }
                    }, 0);
                }
            },
            eventContent: function(arg) {
                var start = arg.event.start;
                var h = start.getHours();
                var m = start.getMinutes();
                var startStr = (h === 0 && m === 0) ? '24:00' : (h.toString().padStart(2, '0') + ':' + m.toString().padStart(2, '0'));
                return startStr;
            },
            eventClassNames: function(arg) {
                var start = arg.event.start;
                var end = arg.event.end;
                var durationMs = end - start;
                var is30Min = durationMs <= (30 * 60 * 1000) + 1000;
                if (!is30Min) return [];
                var m = start.getMinutes();
                if (m === 0) return ['fc-event-first-half'];
                if (m === 30) return ['fc-event-second-half'];
                return [];
            },
            eventDidMount: function(arg) {
                var start = arg.event.start;
                var end = arg.event.end;
                var durationMs = end - start;
                var is30Min = durationMs <= (30 * 60 * 1000) + 1000;
                if (!is30Min) return;
                var m = start.getMinutes();
                var harness = arg.el.closest('.fc-timegrid-event-harness');
                if (!harness) return;
                if (m === 0) harness.classList.add('fc-harness-first-half');
                else if (m === 30) harness.classList.add('fc-harness-second-half');
                var container = document.getElementById('calendar-container');
                var colEvents = harness.closest('.fc-timegrid-col-events');
                if (!colEvents) return;
                var hourIndex = start.getHours() - 1;
                var row = null;
                var axisSlots = container.querySelectorAll('.fc-timegrid-axis .fc-timegrid-slot');
                if (axisSlots.length > hourIndex) {
                    var axisSlot = axisSlots[hourIndex];
                    row = axisSlot ? axisSlot.closest('tr') : null;
                }
                if (!row) {
                    var rows = container.querySelectorAll('.fc-timegrid-slots tbody tr');
                    row = rows[hourIndex] || null;
                }
                if (row) {
                    var rowRect = row.getBoundingClientRect();
                    var colRect = colEvents.getBoundingClientRect();
                    var top = rowRect.top - colRect.top + colEvents.scrollTop;
                    var height = rowRect.height;
                    harness.style.top = top + 'px';
                    harness.style.height = height + 'px';
                } else {
                    var slotHeight = 32;
                    var firstSlot = container.querySelector('.fc-timegrid-slot');
                    if (firstSlot) slotHeight = firstSlot.offsetHeight || 32;
                    harness.style.top = (hourIndex * slotHeight) + 'px';
                    harness.style.height = slotHeight + 'px';
                }
            },
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
                    bookingSwal.fire({
                        icon: 'info',
                        title: 'Slot Unavailable',
                        text: 'This slot is ' + info.event.title.toLowerCase() + '. Please select an available slot.',
                        confirmButtonColor: '#00b3f1'
                    });
                    return;
                }
                const slotStart = info.event.start;
                if (slotStart < new Date()) {
                    bookingSwal.fire({
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
                    return d.toLocaleTimeString('it-IT', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });
                })();
                if (selectedDatetimeBar) {
                    var dateDisp = document.getElementById('selected-date-display');
                    var timeDisp = document.getElementById('selected-time-display');
                    if (dateDisp) dateDisp.textContent = info.event.start.toLocaleDateString('it-IT', {
                        weekday: 'short',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    if (timeDisp) timeDisp.textContent = selectedTimeDisplay;
                    selectedDatetimeBar.style.display = 'flex';
                }
                if (btnNextDateTime) btnNextDateTime.disabled = false;
            }
        });
        calendar.render();

        setTimeout(function() {
            var plusCount = calendarContainer.querySelectorAll('.booking-slot-plus').length;
            var placeholderCount = calendarContainer.querySelectorAll('.booking-slot-placeholder').length;
            console.log('[Booking] After render: .booking-slot-plus count =', plusCount, '.booking-slot-placeholder count =', placeholderCount, 'isServiceOwner =', isServiceOwner);
        }, 100);

        /* Custom toolbar: left [‹] MONTH [›] | center Mon 16 - Sun 22 | right [‹] YEAR [›] */
        var placeholder = document.getElementById('calendar-header-placeholder');
        placeholder.innerHTML = '';
        var toolbar = document.createElement('div');
        toolbar.className = 'booking-custom-toolbar';

        var monthGrp = document.createElement('div');
        monthGrp.className = 'booking-toolbar-group';
        monthGrp.innerHTML = '<button type="button" class="booking-toolbar-btn" id="booking-prev-month" aria-label="Previous month">‹</button><span id="booking-month-title" class="booking-toolbar-label"></span><button type="button" class="booking-toolbar-btn" id="booking-next-month" aria-label="Next month">›</button>';
        var centerGrp = document.createElement('div');
        centerGrp.className = 'booking-toolbar-group booking-toolbar-center';
        centerGrp.innerHTML = '<button type="button" class="booking-toolbar-btn" id="booking-prev-week" aria-label="Previous week">‹</button><span id="booking-week-range" class="booking-toolbar-center-label"></span><button type="button" class="booking-toolbar-btn" id="booking-next-week" aria-label="Next week">›</button>';
        var yearGrp = document.createElement('div');
        yearGrp.className = 'booking-toolbar-group';
        yearGrp.innerHTML = '<button type="button" class="booking-toolbar-btn" id="booking-prev-year" aria-label="Previous year">‹</button><span id="booking-year-title" class="booking-toolbar-label"></span><button type="button" class="booking-toolbar-btn" id="booking-next-year" aria-label="Next year">›</button>';

        toolbar.appendChild(monthGrp);
        toolbar.appendChild(centerGrp);
        toolbar.appendChild(yearGrp);
        placeholder.appendChild(toolbar);

        window._bookingHeaderUpdate = function(start, end) {
            var mEl = document.getElementById('booking-month-title');
            var yEl = document.getElementById('booking-year-title');
            var wEl = document.getElementById('booking-week-range');
            if (!mEl || !yEl) return;
            var loc = getCalendarLocale();
            var tag = loc === 'it' ? 'it-IT' : 'en-GB';
            mEl.textContent = start.toLocaleDateString(tag, { month: 'long' });
            yEl.textContent = String(start.getFullYear());
            if (wEl && end) {
                var lastDay = new Date(end);
                lastDay.setDate(lastDay.getDate() - 1);
                var monthName = start.toLocaleDateString(tag, { month: 'long' });
                var startNum = start.getDate();
                var endNum = lastDay.getDate();
                wEl.textContent = monthName + ' ' + startNum + ' - ' + endNum;
            }
        };
        /* Use the view's actual visible week range so the toolbar label matches the grid column dates */
        var viewStart, viewEnd;
        if (calendar.view && calendar.view.activeStart && calendar.view.activeEnd) {
            viewStart = new Date(calendar.view.activeStart);
            viewEnd = new Date(calendar.view.activeEnd);
        } else {
            viewStart = calendar.getDate();
            viewEnd = new Date(viewStart);
            viewEnd.setDate(viewEnd.getDate() + 7);
        }
        window._bookingHeaderUpdate(viewStart, viewEnd);

        document.getElementById('booking-prev-week').addEventListener('click', function() { calendar.incrementDate({ weeks: -1 }); });
        document.getElementById('booking-next-week').addEventListener('click', function() { calendar.incrementDate({ weeks: 1 }); });
        document.getElementById('booking-prev-month').addEventListener('click', function() { calendar.incrementDate({ months: -1 }); });
        document.getElementById('booking-next-month').addEventListener('click', function() { calendar.incrementDate({ months: 1 }); });
        document.getElementById('booking-prev-year').addEventListener('click', function() { calendar.incrementDate({ years: -1 }); });
        document.getElementById('booking-next-year').addEventListener('click', function() { calendar.incrementDate({ years: 1 }); });

        if (calendarHeaderPlaceholder) calendarHeaderPlaceholder.style.display = 'block';
    }

    /* Clickable "+" for owner: add availability for that slot (like Google Calendar) */
    calendarContainer.addEventListener('click', function(e) {
        var plus = e.target.closest('.booking-slot-plus');
        if (!plus || !isServiceOwner) return;
        e.preventDefault();
        e.stopPropagation();
        var slotTime = plus.getAttribute('data-slot-time');
        var col = parseInt(plus.getAttribute('data-column'), 10);
        if (!slotTime || isNaN(col)) return;
        if (!calendar) return;
        if (!selectedServiceId) {
            bookingSwal.fire({ icon: 'info', title: 'Select a service', text: 'Please select a service from the sidebar first.', confirmButtonColor: '#00b3f1' });
            return;
        }
        var viewStart = calendar.view.activeStart;
        var d = new Date(viewStart.getFullYear(), viewStart.getMonth(), viewStart.getDate());
        d.setDate(d.getDate() + col);
        var parts = slotTime.split(':');
        var hour = parseInt(parts[0], 10);
        var min = parseInt(parts[1], 10) || 0;
        d.setHours(hour, min, 0, 0);
        var dateStr = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        var start1 = String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        var endD = new Date(d.getTime());
        endD.setMinutes(endD.getMinutes() + 30);
        var end1 = endD.getHours() === 0 && endD.getMinutes() === 0 ? '23:59' : (String(endD.getHours()).padStart(2, '0') + ':' + String(endD.getMinutes()).padStart(2, '0'));
        var start2 = end1 === '23:59' ? null : end1;
        var end2 = start2 ? (function() { var h = parseInt(start2.slice(0,2), 10); var m = parseInt(start2.slice(3), 10); m += 30; if (m >= 60) { h++; m = 0; } return h >= 24 ? '23:59' : (String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0')); })() : null;
        var displayDate = d.toLocaleDateString(getCalendarLocale() === 'it' ? 'it-IT' : 'en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
        var displayTime = start1 + ' – ' + (end1 === '23:59' ? '24:00' : end1);
        var maxEnd = new Date(d.getFullYear(), d.getMonth(), d.getDate());
        maxEnd.setMonth(maxEnd.getMonth() + 6);
        var maxEndStr = maxEnd.getFullYear() + '-' + String(maxEnd.getMonth() + 1).padStart(2, '0') + '-' + String(maxEnd.getDate()).padStart(2, '0');
        var repeatHtml = '<div class="text-left mt-3" style="padding:0.5rem 0">' +
            '<label class="block text-sm font-medium mb-1">Repeat</label>' +
            '<select id="swal-repeat-type" class="swal2-input" style="width:100%;margin:0;box-sizing:border-box">' +
            '<option value="none">No repeat</option><option value="daily">Daily</option><option value="weekly">Weekly</option>' +
            '</select>' +
            '<div id="swal-repeat-end-wrap" style="display:none;margin-top:0.5rem">' +
            '<label class="block text-sm font-medium mb-1">Until date (max 6 months)</label>' +
            '<input type="date" id="swal-repeat-end" class="swal2-input" style="width:100%;margin:0;box-sizing:border-box" min="' + dateStr + '" max="' + maxEndStr + '">' +
            '</div></div>';
        bookingSwal.fire({
            title: 'Add availability',
            html: 'Add a 30-minute slot on <strong>' + displayDate + '</strong> at <strong>' + displayTime + '</strong>?' + repeatHtml,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00b3f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Add slot',
            didOpen: function() {
                var sel = document.getElementById('swal-repeat-type');
                var wrap = document.getElementById('swal-repeat-end-wrap');
                if (!sel || !wrap) return;
                function toggle() { wrap.style.display = (sel.value === 'daily' || sel.value === 'weekly') ? 'block' : 'none'; }
                sel.addEventListener('change', toggle);
            }
        }).then(function(result) {
            if (!result.isConfirmed) return;
            var repeatType = (document.getElementById('swal-repeat-type') && document.getElementById('swal-repeat-type').value) || 'none';
            var repeatEndEl = document.getElementById('swal-repeat-end');
            var repeatEndDate = repeatEndEl ? repeatEndEl.value : '';
            var useRepeat = (repeatType === 'daily' || repeatType === 'weekly') && repeatEndDate;

            function doPost(sStart, sEnd) {
                var fd = new FormData();
                fd.append('service_id', selectedServiceId);
                fd.append('availability_date', dateStr);
                fd.append('start_time', sStart);
                fd.append('end_time', sEnd);
                fd.append('is_active', '1');
                return fetch(availabilityStoreUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                }).then(function(r) { return r.json().then(function(data) { if (!r.ok) throw { status: r.status, data: data }; return data; }); });
            }
            function doPostWithRepeat(sStart, sEnd) {
                var fd = new FormData();
                fd.append('service_id', selectedServiceId);
                fd.append('availability_date', dateStr);
                fd.append('start_time', sStart);
                fd.append('end_time', sEnd);
                fd.append('is_active', '1');
                fd.append('repeat_type', repeatType);
                fd.append('repeat_end_date', repeatEndDate);
                return fetch(availabilityStoreWithRepeatUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                }).then(function(r) { return r.json().then(function(data) { if (!r.ok) throw { status: r.status, data: data }; return data; }); });
            }

            var req = useRepeat ? doPostWithRepeat(start1, end1) : doPost(start1, end1);
            req.then(function(data) {
                var msg = data.message || (useRepeat && data.created ? 'Added ' + data.created + ' slot(s).' : 'Availability added.');
                bookingSwal.fire({ icon: 'success', title: useRepeat ? 'Slots added' : 'Slot added', text: msg, confirmButtonColor: '#00b3f1' });
                if (calendar) calendar.refetchEvents();
            }).catch(function(err) {
                var msg = err && err.data && err.data.error ? err.data.error : (err && err.error) || 'Could not add slot.';
                if (err && err.status === 422 && !useRepeat && msg.indexOf('overlap') !== -1 && start2 && end2) {
                    doPost(start2, end2)
                        .then(function(data) {
                            bookingSwal.fire({ icon: 'success', title: 'Slot added', text: data.message || 'Second half of hour added.', confirmButtonColor: '#00b3f1' });
                            if (calendar) calendar.refetchEvents();
                        })
                        .catch(function(e2) {
                            var m2 = e2 && e2.data && e2.data.error ? e2.data.error : (e2 && e2.error) || 'Could not add slot.';
                            bookingSwal.fire({ icon: 'error', title: 'Error', text: m2, confirmButtonColor: '#00b3f1' });
                        });
                } else {
                    bookingSwal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#00b3f1' });
                }
            });
        });
    });

    // Keep calendar language in sync with header language toggle
    const languageToggle = document.getElementById('language-toggle');
    if (languageToggle) {
        languageToggle.addEventListener('change', function () {
            if (!calendar) return;
            const newLocale = this.checked ? 'it' : 'en';
            setBookingCalendarLocale(newLocale);
            calendar.setOption('locale', newLocale);
            calendar.changeView('timeGridWeek', calendar.getDate());
        });
    }

    const bookingConfirmModalEl = document.getElementById('bookingConfirmModal');
    const bookingConfirmModal = bookingConfirmModalEl ? new bootstrap.Modal(bookingConfirmModalEl) : null;

    if (btnNextDateTime) {
    btnNextDateTime.addEventListener('click', function() {
        if (!selectedDate || !selectedTime) return;
        const serviceOption = serviceSelect.options[serviceSelect.selectedIndex];
        document.getElementById('modal-service').textContent = serviceOption ? serviceOption.text : '';
        document.getElementById('modal-date').textContent = new Date(selectedDate + 'T12:00:00').toLocaleDateString('it-IT', {
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
        document.getElementById('modal-time').textContent = selectedTimeDisplay || new Date(selectedDate + 'T' + selectedTime + ':00').toLocaleTimeString('it-IT', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
        if (bookingConfirmModal) bookingConfirmModal.show();
    });
    }

    var btnConfirmBooking = document.getElementById('btn-confirm-booking');
    if (btnConfirmBooking) btnConfirmBooking.addEventListener('click', function() {
        if (!bookingUser) {
            bookingSwal.fire({
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
                if (bookingConfirmModal) bookingConfirmModal.hide();
                bookingSwal.fire({
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
                bookingSwal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                    text: msg,
                    confirmButtonColor: '#00b3f1'
                }).then(function() {
                    window.location.href = loginUrl + '?intended=' + encodeURIComponent(returnUrl);
                });
            } else {
                bookingSwal.fire({
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
