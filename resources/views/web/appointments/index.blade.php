@extends('web.layouts.app')

@section('title', 'My Appointments')

@push('styles')
<style>
    /* Modal above fixed toolbar + header — must be clickable (wrapper stacking can trap modal) */
    #cancelModal.modal {
        z-index: 110000 !important;
    }
    /* Backdrop below dialog but above everything else */
    body > .modal-backdrop.show {
        z-index: 109900 !important;
    }
    /* Ensure dialog receives clicks */
    #cancelModal .modal-dialog,
    #cancelModal .modal-content {
        position: relative;
        z-index: 1;
        pointer-events: auto;
    }
    /* Dialog sits below fixed toolbar; centered would clash with toolbar overlap */
    #cancelModal .modal-dialog {
        margin: 5rem auto 2rem;
        max-height: calc(100vh - 6rem);
    }
    #cancelModal .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    #cancelModal .modal-header {
        background: linear-gradient(135deg, #1b1b18 0%, #2d2d28 100%);
        color: #fff;
        border-bottom: none;
        padding: 1rem 1.25rem;
    }
    #cancelModal .modal-header .modal-title { color: #fff; font-weight: 600; }
    #cancelModal .modal-header .btn-close { filter: invert(1); opacity: 0.85; }
    #cancelModal .modal-header .btn-close:hover { opacity: 1; }
    #cancelModal .modal-footer { border-top: 1px solid #eee; padding: 1rem 1.25rem; }
    /* Modal footer buttons (modal is outside .appt-page — scope by #cancelModal) */
    #cancelModal .appt-btn-keep {
        background: linear-gradient(135deg, #495057 0%, #343a40 100%);
        color: #fff !important;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.5rem 1.15rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    #cancelModal .appt-btn-keep:hover { color: #fff !important; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(52, 58, 64, 0.4); }
    #cancelModal .appt-btn-confirm-cancel {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        color: #fff !important;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.5rem 1.15rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    #cancelModal .appt-btn-confirm-cancel:hover { color: #fff !important; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(220, 53, 69, 0.5); }

    /* Status tags — outlined / chip style so they read as labels, not buttons */
    .appt-page .appt-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.55rem;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-radius: 3px;
        border: 1px solid;
        background: transparent;
        line-height: 1.2;
        vertical-align: middle;
    }
    .appt-page .appt-tag-confirmed {
        color: #146c43;
        border-color: #198754;
        background: rgba(25, 135, 84, 0.08);
    }
    .appt-page .appt-tag-pending {
        color: #b45309;
        border-color: #d97706;
        background: rgba(217, 119, 6, 0.1);
    }
    .appt-page .appt-tag-cancelled {
        color: #495057;
        border-color: #6c757d;
        background: rgba(108, 117, 125, 0.1);
    }
    .appt-page .appt-tag-hr {
        color: #0a58ca;
        border-color: #0d6efd;
        background: rgba(13, 110, 253, 0.08);
        text-transform: none;
        letter-spacing: 0.02em;
        font-size: 0.7rem;
    }
    /* Optional: small “tag” notch feel — left accent only */
    .appt-page .appt-tag::before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        margin-right: 0.35rem;
        background: currentColor;
        opacity: 0.6;
    }
    .appt-page .appt-tag-hr::before { display: none; }

    /* Notice boxes with icon — no Bootstrap alert left bar */
    .appt-info-notice {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 1rem 1.15rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
    }
    .appt-info-notice__icon {
        flex-shrink: 0;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #e0f2fe;
        color: #0369a1;
        font-size: 1.1rem;
    }
    .appt-info-notice--danger {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }
    .appt-info-notice--danger .appt-info-notice__icon {
        background: #fee2e2;
        color: #dc2626;
    }
    .appt-info-notice--warning {
        border-color: #fde68a;
        background: #fffbeb;
        color: #92400e;
    }
    .appt-info-notice--warning .appt-info-notice__icon {
        background: #fef3c7;
        color: #d97706;
    }
    .appt-info-notice--info {
        border-color: #bae6fd;
        background: #f0f9ff;
        color: #0c4a6e;
    }
    .appt-info-notice--info .appt-info-notice__icon {
        background: #e0f2fe;
        color: #0284c7;
    }
    .appt-info-notice__text { flex: 1; font-size: 0.95rem; line-height: 1.5; }
    .appt-info-notice__text strong { font-weight: 600; }
    .appt-info-notice__text .appt-info-notice__extra {
        margin-top: 0.65rem;
        padding-top: 0.65rem;
        border-top: 1px solid rgba(0,0,0,0.06);
        opacity: 0.95;
    }

    /* Buttons: background + white text + hover */
    .appt-page .appt-btn-cancel-outline {
        background: #dc3545;
        color: #fff !important;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.45rem 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .appt-page .appt-btn-cancel-outline:hover {
        background: #bb2d3b;
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 53, 69, 0.45);
    }
    .appt-page .appt-btn-keep {
        background: linear-gradient(135deg, #495057 0%, #343a40 100%);
        color: #fff !important;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.5rem 1.15rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .appt-page .appt-btn-keep:hover {
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(52, 58, 64, 0.4);
    }
    .appt-page .appt-btn-confirm-cancel {
        background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
        color: #fff !important;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.5rem 1.15rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .appt-page .appt-btn-confirm-cancel:hover {
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 53, 69, 0.5);
    }
    .appt-page .appt-btn-browse {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: #fff !important;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .appt-page .appt-btn-browse:hover {
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13, 110, 253, 0.45);
    }
    /* Card hover on list */
    .appt-page .dashboard_setting_box .card {
        transition: box-shadow 0.25s ease, transform 0.25s ease;
        border-radius: 10px;
    }
    .appt-page .dashboard_setting_box .card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<section class="our-dashbord dashbord bgc-f7 pb50 pt-4 appt-page">
    <div class="container-fluid ovh">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard_navigationbar dn db-1024">
                    <div class="dropdown">
                        <button onclick="myFunction()" class="dropbtn"><i class="fa fa-bars pr10"></i> Dashboard Navigation</button>
                        <ul id="myDropdown" class="dropdown-content">
                            <li><a href="{{ route('filament.admin.pages.dashboard') }}"><span class="flaticon-home mr10"></span>Dashboard</a></li>
                            <li><a href="{{ route('following') }}"><span class="flaticon-heart mr10"></span>Following</a></li>
                            <li><a href="{{ route('notifications') }}"><span class="flaticon-bell mr10"></span>Notifications</a></li>
                            <li><a href="{{ route('messages') }}"><span class="flaticon-chat mr10"></span>Messages</a></li>
                            <li><a href="{{ route('appointments.index') }}" class="active"><span class="flaticon-calendar mr10"></span>My Appointments</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12 mb20">
                        <div class="dashboard_title_area">
                            <h2>My Appointments</h2>
                            <p class="text">View and manage your bookings</p>
                        </div>
                    </div>

                    {{-- Cancellation limit / block notice (icon + custom box — no alert left bar) --}}
                    <div class="col-lg-12 mb20">
                        @php
                            $apptInstruction = 'You can cancel confirmed appointments at least 24 hours in advance. Up to 3 cancellations per month; exceeding that blocks new bookings until next month.';
                        @endphp
                        @if(!$canBook && $tracking->blocked_until)
                            <div class="appt-info-notice appt-info-notice--danger">
                                <span class="appt-info-notice__icon" aria-hidden="true"><i class="fas fa-exclamation-circle"></i></span>
                                <div class="appt-info-notice__text">
                                    <strong>Booking temporarily blocked.</strong>
                                    You exceeded the monthly cancellation limit (3). New bookings are blocked until
                                    {{ $tracking->blocked_until->format('F d, Y') }}.
                                    <p class="appt-info-notice__extra mb-0">{{ $apptInstruction }}</p>
                                </div>
                            </div>
                        @elseif($tracking->cancellation_count > 0)
                            <div class="appt-info-notice {{ $tracking->cancellation_count >= 3 ? 'appt-info-notice--warning' : 'appt-info-notice--info' }}">
                                <span class="appt-info-notice__icon" aria-hidden="true"><i class="fas fa-info-circle"></i></span>
                                <div class="appt-info-notice__text">
                                    Cancellations this month: <strong>{{ $tracking->cancellation_count }} / 3</strong>.
                                    @if($tracking->cancellation_count >= 2)
                                        Further cancellations may block new bookings until next month.
                                    @endif
                                    <p class="appt-info-notice__extra mb-0">{{ $apptInstruction }}</p>
                                </div>
                            </div>
                        @else
                            <div class="appt-info-notice">
                                <span class="appt-info-notice__icon" aria-hidden="true"><i class="fas fa-info-circle"></i></span>
                                <div class="appt-info-notice__text">
                                    {{ $apptInstruction }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-12">
                        <div class="dashboard_setting_box">
                            @forelse($appointments as $appointment)
                                @php
                                    $isCancelled = $appointment->status === 'cancelled';
                                    $canCancel = !$isCancelled && $appointment->status === 'confirmed' && $appointment->canBeCancelled();
                                @endphp
                                <div class="card shadow-sm mb-3 {{ $isCancelled ? 'opacity-75' : '' }}">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h5 class="mb-1">{{ $appointment->service->title ?? 'Service' }}</h5>
                                                <p class="text-muted mb-1 small">
                                                    <i class="flaticon-calendar mr5"></i>
                                                    {{ $appointment->appointment_date->format('l, F d, Y') }}
                                                    at {{ $appointment->appointment_time->format('g:i A') }}
                                                    @if(($appointment->duration_minutes ?? 30) == 60)
                                                        <span class="appt-tag appt-tag-hr">1 hr</span>
                                                    @endif
                                                </p>
                                                <p class="mb-0 small">
                                                    Professional:
                                                    @if($appointment->professional->username ?? null)
                                                        <a href="{{ route('appointments.book', $appointment->professional->username) }}">{{ $appointment->professional->name }} {{ $appointment->professional->surname }}</a>
                                                    @else
                                                        {{ $appointment->professional->name }} {{ $appointment->professional->surname }}
                                                    @endif
                                                </p>
                                                @if($isCancelled && $appointment->cancellation_reason)
                                                    <p class="text-muted small mb-0 mt-2"><em>Reason: {{ Str::limit($appointment->cancellation_reason, 120) }}</em></p>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                                @if($appointment->status === 'pending')
                                                    <span class="appt-tag appt-tag-pending">Pending</span>
                                                @elseif($appointment->status === 'confirmed')
                                                    <span class="appt-tag appt-tag-confirmed">Confirmed</span>
                                                @elseif($isCancelled)
                                                    <span class="appt-tag appt-tag-cancelled">Cancelled</span>
                                                @else
                                                    <span class="appt-tag appt-tag-cancelled">{{ $appointment->status }}</span>
                                                @endif
                                                @if($canCancel)
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm appt-btn-cancel-outline"
                                                                data-bs-toggle="modal" data-bs-target="#cancelModal"
                                                                data-appointment-id="{{ $appointment->id }}"
                                                                data-service-title="{{ e($appointment->service->title ?? 'Appointment') }}"
                                                                data-appointment-datetime="{{ e($appointment->appointment_date->format('l, F d, Y') . ' at ' . $appointment->appointment_time->format('g:i A')) }}">
                                                            Cancel appointment
                                                        </button>
                                                    </div>
                                                @elseif(!$isCancelled && $appointment->status === 'confirmed')
                                                    <p class="small text-muted mb-0 mt-2">Cancellation only possible 24+ hours before.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="flaticon-calendar text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-0">You have no appointments yet.</p>
                                    <a href="{{ route('web.services') }}" class="btn mt-3 appt-btn-browse">Browse services</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Cancel modal (outside appt-page section so backdrop covers full viewport; styles above still apply) --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="cancelModalService" class="mb-1 fw-semibold"></p>
                <p id="cancelModalDateTime" class="text-muted small mb-3"></p>
                <div class="mb-3">
                    <label for="cancellation_reason" class="form-label">Reason (optional)</label>
                    <textarea class="form-control" id="cancellation_reason" rows="3" maxlength="500" placeholder="Optional message to the professional"></textarea>
                </div>
                <div id="cancelError" class="alert alert-danger d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn appt-btn-keep" data-bs-dismiss="modal">Keep appointment</button>
                <button type="button" class="btn appt-btn-confirm-cancel" id="confirmCancelBtn">Confirm cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const cancelModal = document.getElementById('cancelModal');
    const cancelModalService = document.getElementById('cancelModalService');
    const cancelModalDateTime = document.getElementById('cancelModalDateTime');
    const reasonInput = document.getElementById('cancellation_reason');
    const cancelError = document.getElementById('cancelError');
    const confirmBtn = document.getElementById('confirmCancelBtn');
    let currentAppointmentId = null;

    /* Move modal to body so it is not inside .wrapper stacking context (fixes overlay eating clicks) */
    function ensureCancelModalInBody() {
        if (cancelModal && cancelModal.parentNode !== document.body) {
            document.body.appendChild(cancelModal);
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureCancelModalInBody);
    } else {
        ensureCancelModalInBody();
    }

    if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function (event) {
            ensureCancelModalInBody();
            const button = event.relatedTarget;
            if (!button) return;
            currentAppointmentId = button.getAttribute('data-appointment-id');
            const title = button.getAttribute('data-service-title') || 'Appointment';
            const dateTime = button.getAttribute('data-appointment-datetime') || '';
            cancelModalService.textContent = 'Service: ' + title;
            if (cancelModalDateTime) {
                cancelModalDateTime.textContent = dateTime ? dateTime : '';
                cancelModalDateTime.style.display = dateTime ? '' : 'none';
            }
            reasonInput.value = '';
            cancelError.classList.add('d-none');
            cancelError.textContent = '';
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (!currentAppointmentId) return;
            cancelError.classList.add('d-none');
            confirmBtn.disabled = true;
            /* Layout has no meta csrf-token — use Blade token like book.blade.php */
            const csrf = '{{ csrf_token() }}';

            fetch('{{ url('/appointments') }}/' + currentAppointmentId + '/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    _token: csrf,
                    cancellation_reason: reasonInput.value || null
                })
            })
            .then(function (r) {
                return r.text().then(function (t) {
                    var j = {};
                    try { j = t ? JSON.parse(t) : {}; } catch (e) {}
                    return { ok: r.ok, status: r.status, body: j };
                });
            })
            .then(function (res) {
                if (res.ok && res.body && res.body.success) {
                    window.location.reload();
                    return;
                }
                var msg = (res.body && res.body.error) ? res.body.error : (res.body && res.body.message) ? res.body.message : 'Could not cancel. Please try again.';
                if (res.status === 419) msg = 'Session expired. Refresh the page and try again.';
                cancelError.textContent = msg;
                cancelError.classList.remove('d-none');
            })
            .catch(function () {
                cancelError.textContent = 'Network error. Please try again.';
                cancelError.classList.remove('d-none');
            })
            .finally(function () {
                confirmBtn.disabled = false;
            });
        });
    }
})();
</script>
@endpush
