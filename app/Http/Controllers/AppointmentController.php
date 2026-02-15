<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Models\ServiceAvailability;
use App\Models\ClientCancellationTracking;
use App\Notifications\AppointmentRequestReceived;
use App\Notifications\AppointmentConfirmed;
use App\Notifications\AppointmentCancelledByProfessional;
use App\Notifications\AppointmentCancelledByClient;
use App\Notifications\NewBookingRequest;
use App\Services\CancellationTrackingService;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    protected $cancellationService;
    protected $chatService;

    public function __construct()
    {
        $this->cancellationService = app(CancellationTrackingService::class);
        $this->chatService = app(ChatService::class);
    }

    /**
     * Show booking interface for a professional
     */
    public function book($username)
    {
        $professional = User::where('username', $username)
            ->where('user_type', 3)
            ->with(['services' => function($query) {
                $query->where('is_active', true);
            }])
            ->firstOrFail();

        // Check if user is authenticated and is a client
        if (Auth::check() && Auth::user()->user_type !== 2) {
            return redirect()->back()->with('error', 'Only clients can book appointments.');
        }

        return view('web.appointments.book', compact('professional'));
    }

    /**
     * Store a new appointment booking request
     */
    public function store(Request $request)
    {
        // Check if user is authenticated and is a client
        if (!Auth::check() || Auth::user()->user_type !== 2) {
            return response()->json(['error' => 'Only clients can book appointments.'], 403);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        $service = Service::findOrFail($request->service_id);
        $professional = $service->user;

        // Use logged-in user credentials
        $user = Auth::user();
        $clientName = $user->name;
        $clientSurname = $user->surname;
        $clientEmail = $user->email;
        $clientPhone = $user->profile?->phone ?? null;
        $clientDateOfBirth = $user->profile?->date_of_birth?->format('Y-m-d');

        if (!$clientDateOfBirth) {
            return response()->json(['error' => 'Please complete your profile with date of birth before booking.'], 422);
        }

        // Reject past appointments
        $appointmentDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time);
        if ($appointmentDateTime->lte(now())) {
            return response()->json(['error' => 'Please select future appointments.'], 422);
        }

        // Check if client is blocked from booking
        if (!ClientCancellationTracking::canClientBook(Auth::id())) {
            $tracking = ClientCancellationTracking::getOrCreateForCurrentMonth(Auth::id());
            return response()->json([
                'error' => 'You have exceeded the monthly cancellation limit. You cannot book new appointments until ' . $tracking->blocked_until->format('F d, Y') . '.'
            ], 403);
        }

        // Check if time slot is available (1-hour slot; availability must cover full hour)
        $appointmentEnd = Carbon::createFromFormat('H:i', $request->appointment_time)->addHour()->format('H:i:s');
        $isAvailable = ServiceAvailability::where('service_id', $service->id)
            ->where('availability_date', $request->appointment_date)
            ->where('is_active', true)
            ->where('start_time', '<=', $request->appointment_time)
            ->where('end_time', '>=', $appointmentEnd)
            ->exists();

        if (!$isAvailable) {
            return response()->json(['error' => 'The selected time slot is not available.'], 422);
        }

        // Check if slot is already booked
        $isBooked = Appointment::where('service_id', $service->id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($isBooked) {
            return response()->json(['error' => 'This time slot is already booked.'], 422);
        }

        DB::beginTransaction();
        try {
            $appointment = Appointment::create([
                'service_id' => $service->id,
                'client_id' => Auth::id(),
                'professional_id' => $professional->id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'status' => 'pending',
                'client_name' => $clientName,
                'client_surname' => $clientSurname,
                'client_email' => $clientEmail,
                'client_phone' => $clientPhone,
                'client_date_of_birth' => $clientDateOfBirth,
            ]);

            // Send notifications
            $user->notify(new AppointmentRequestReceived($appointment));
            $professional->notify(new NewBookingRequest($appointment));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment request submitted successfully.',
                'appointment_id' => $appointment->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create appointment. Please try again.'], 500);
        }
    }

    /**
     * Confirm an appointment (Professional only)
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        // Check if user is the professional
        if (Auth::id() !== $appointment->professional_id || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($appointment->status !== 'pending') {
            return response()->json(['error' => 'Only pending appointments can be confirmed.'], 422);
        }

        DB::beginTransaction();
        try {
            $appointment->update(['status' => 'confirmed']);

            // Send notifications
            $client = $appointment->client;
            $client->notify(new AppointmentConfirmed($appointment));

            // Send chat message with buttons
            $this->chatService->sendAppointmentConfirmationMessage($appointment);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment confirmed successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to confirm appointment.'], 500);
        }
    }

    /**
     * Cancel appointment by client
     */
    public function cancelByClient(Request $request, Appointment $appointment)
    {
        // Check if user is the client
        if (Auth::id() !== $appointment->client_id) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($appointment->status === 'cancelled') {
            return response()->json(['error' => 'Appointment is already cancelled.'], 422);
        }

        // Check 24-hour window
        if (!$appointment->canBeCancelled()) {
            return response()->json([
                'error' => 'Appointments can only be cancelled at least 24 hours in advance.'
            ], 422);
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $appointment->update([
                'status' => 'cancelled',
                'cancelled_by' => 'client',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Track cancellation
            $this->cancellationService->trackCancellation($appointment->client_id);

            // Send notification to professional
            $professional = $appointment->professional;
            $professional->notify(new AppointmentCancelledByClient($appointment));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to cancel appointment.'], 500);
        }
    }

    /**
     * Cancel appointment by professional
     */
    public function cancelByProfessional(Request $request, Appointment $appointment)
    {
        // Check if user is the professional
        if (Auth::id() !== $appointment->professional_id || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($appointment->status === 'cancelled') {
            return response()->json(['error' => 'Appointment is already cancelled.'], 422);
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $appointment->update([
                'status' => 'cancelled',
                'cancelled_by' => 'professional',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Send notifications
            $client = $appointment->client;
            $client->notify(new AppointmentCancelledByProfessional($appointment));

            // Send chat message
            $this->chatService->sendAppointmentCancellationMessage($appointment);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to cancel appointment.'], 500);
        }
    }

    /**
     * Store external appointment (Professional only)
     */
    public function storeExternal(Request $request)
    {
        if (Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Only professionals can create external appointments.'], 403);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'external_color' => 'nullable|string|max:50',
        ]);

        $service = Service::findOrFail($request->service_id);

        // Check if professional owns the service
        if ($service->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        DB::beginTransaction();
        try {
            $appointment = Appointment::create([
                'service_id' => $service->id,
                'client_id' => Auth::id(), // Use professional as client for external
                'professional_id' => Auth::id(),
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'status' => 'confirmed',
                'is_external' => true,
                'external_color' => $request->external_color ?? '#00b3f1',
                'client_name' => 'External',
                'client_surname' => 'Appointment',
                'client_email' => Auth::user()->email,
                'client_date_of_birth' => now()->subYears(30),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'External appointment created successfully.',
                'appointment' => $appointment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create external appointment.'], 500);
        }
    }

    /**
     * Get calendar data (AJAX endpoint)
     */
    public function calendarData(Request $request)
    {
        if (Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $professionalId = Auth::id();
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        // Get appointments
        $appointmentsQuery = Appointment::where('professional_id', $professionalId)
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->where('is_external', false);

        if ($request->service_id) {
            $appointmentsQuery->where('service_id', $request->service_id);
        }

        $appointments = $appointmentsQuery->with(['service', 'client'])
            ->get()
            ->map(function ($appointment) {
                $color = match($appointment->status) {
                    'pending' => '#dc3545',   // Red - Waiting/Pending
                    'confirmed' => '#28a745', // Green - Booked
                    'cancelled' => '#6c757d', // Gray
                    default => '#00b3f1',    // Primary
                };

                $startCarbon = Carbon::parse($appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->appointment_time->format('H:i:s'));
                $startStr = $startCarbon->format('Y-m-d\TH:i:s');
                $endStr = $startCarbon->copy()->addHour()->format('Y-m-d\TH:i:s');

                return [
                    'id' => $appointment->id,
                    'title' => $appointment->service->title . ' - ' . $appointment->client_name,
                    'start' => $startStr,
                    'end' => $endStr,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'color' => $color,
                    'extendedProps' => [
                        'status' => $appointment->status,
                        'service_id' => $appointment->service_id,
                        'client_name' => $appointment->client_name . ' ' . $appointment->client_surname,
                        'client_email' => $appointment->client_email ?? '',
                        'client_phone' => $appointment->client_phone ?? '',
                    ],
                ];
            });
        // Get availabilities (for display as available slots) - by date in range
        $availabilitiesQuery = ServiceAvailability::whereHas('service', function ($q) use ($professionalId) {
            $q->where('user_id', $professionalId);
        })
            ->where('is_active', true)
            ->whereBetween('availability_date', [$start->toDateString(), $end->toDateString()]);

        if ($request->service_id) {
            $availabilitiesQuery->where('service_id', $request->service_id);
        }

        $availabilities = $availabilitiesQuery->get()
            ->map(function ($availability) {
                $dateStr = $availability->availability_date->format('Y-m-d');
                return [
                    'title' => 'Available',
                    'start' => $dateStr . 'T' . $availability->start_time->format('H:i:s'),
                    'end' => $dateStr . 'T' . $availability->end_time->format('H:i:s'),
                    'color' => '#00b3f1',
                    'display' => 'background',
                    'rendering' => 'background',
                ];
            });

        return response()->json([
            'appointments' => $appointments,
            'availabilities' => $availabilities,
        ]);
    }

    /**
     * Get available time slots for a service on a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
        ]);

        $service = Service::findOrFail($request->service_id);
        $date = Carbon::parse($request->date);
        $dateStr = $date->toDateString();

        $availabilities = ServiceAvailability::where('service_id', $service->id)
            ->where('availability_date', $dateStr)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        $slots = [];
        foreach ($availabilities as $availability) {
            $start = Carbon::parse($dateStr . ' ' . $availability->start_time->format('H:i:s'));
            $end = Carbon::parse($dateStr . ' ' . $availability->end_time->format('H:i:s'));
            $current = $start->copy();
            while ($current->copy()->addMinutes(60)->lte($end)) {
                $slotTime = $current->format('H:i');
                $isBooked = Appointment::where('service_id', $service->id)
                    ->where('appointment_date', $dateStr)
                    ->where('appointment_time', $slotTime)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->exists();
                if (!$isBooked) {
                    $slots[] = [
                        'time' => $slotTime,
                        'display' => $current->format('h:i A'),
                    ];
                }
                $current->addMinutes(60);
            }
        }

        return response()->json(['slots' => $slots]);
    }

    /**
     * Public calendar data for client booking: only available (cyan) slots.
     * No appointments (red/green) exposed.
     */
    public function publicCalendarData(Request $request, string $username)
    {
        $professional = User::where('username', $username)
            ->where('user_type', 3)
            ->first();

        if (!$professional) {
            return response()->json(['error' => 'Professional not found.'], 404);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $service = Service::where('id', $request->service_id)
            ->where('user_id', $professional->id)
            ->where('is_active', true)
            ->first();

        if (!$service) {
            return response()->json(['error' => 'Service not found or not available.'], 404);
        }

        $start = Carbon::parse($request->start)->startOfDay();
        $end = Carbon::parse($request->end);

        $availabilities = ServiceAvailability::where('service_id', $service->id)
            ->where('is_active', true)
            ->whereBetween('availability_date', [$start->toDateString(), $end->copy()->subDay()->toDateString()])
            ->orderBy('availability_date')
            ->orderBy('start_time')
            ->get();

        $events = [];
        foreach ($availabilities as $availability) {
            $dateStr = $availability->availability_date->format('Y-m-d');
            $slotStart = Carbon::parse($dateStr . ' ' . $availability->start_time->format('H:i:s'));
            $slotEnd = Carbon::parse($dateStr . ' ' . $availability->end_time->format('H:i:s'));
            $slot = $slotStart->copy();
            while ($slot->copy()->addMinutes(60)->lte($slotEnd)) {
                $slotTime = $slot->format('H:i');
                $appointment = Appointment::where('service_id', $service->id)
                    ->where('appointment_date', $dateStr)
                    ->where('appointment_time', $slotTime)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->first();

                $title = 'Available';
                $color = '#00b3f1';
                $bookable = true;

                if ($appointment) {
                    if ($appointment->status === 'pending') {
                        $title = 'Waiting';
                        $color = '#dc3545';
                        $bookable = false;
                    } else {
                        $title = 'Booked';
                        $color = '#28a745';
                        $bookable = false;
                    }
                }

                $events[] = [
                    'title' => $title,
                    'start' => $dateStr . 'T' . $slot->format('H:i:s'),
                    'end' => $dateStr . 'T' . $slot->copy()->addMinutes(60)->format('H:i:s'),
                    'color' => $color,
                    'extendedProps' => [
                        'time' => $slotTime,
                        'date' => $dateStr,
                        'bookable' => $bookable,
                    ],
                ];
                $slot->addMinutes(60);
            }
        }

        return response()->json(['events' => $events]);
    }

    /**
     * Public available slots for a single date (client booking flow).
     */
    public function publicAvailableSlots(Request $request, string $username)
    {
        $professional = User::where('username', $username)
            ->where('user_type', 3)
            ->firstOrFail();

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
        ]);

        $service = Service::where('id', $request->service_id)
            ->where('user_id', $professional->id)
            ->where('is_active', true)
            ->firstOrFail();

        $date = Carbon::parse($request->date);
        $dateStr = $date->toDateString();

        $availabilities = ServiceAvailability::where('service_id', $service->id)
            ->where('availability_date', $dateStr)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        $slots = [];
        foreach ($availabilities as $availability) {
            $start = Carbon::parse($dateStr . ' ' . $availability->start_time->format('H:i:s'));
            $end = Carbon::parse($dateStr . ' ' . $availability->end_time->format('H:i:s'));
            $current = $start->copy();
            while ($current->copy()->addMinutes(60)->lte($end)) {
                $slotTime = $current->format('H:i');
                $isBooked = Appointment::where('service_id', $service->id)
                    ->where('appointment_date', $dateStr)
                    ->where('appointment_time', $slotTime)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->exists();
                if (!$isBooked) {
                    $slots[] = [
                        'time' => $slotTime,
                        'display' => $current->format('h:i A'),
                    ];
                }
                $current->addMinutes(60);
            }
        }

        return response()->json(['slots' => $slots]);
    }
}
