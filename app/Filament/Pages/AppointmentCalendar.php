<?php

namespace App\Filament\Pages;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\ServiceAvailability;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use Carbon\Carbon;

class AppointmentCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    
    protected static string $view = 'filament.pages.appointment-calendar';
    
    protected static ?string $navigationLabel = 'Appointment Calendar';
    
    protected static ?string $navigationGroup = 'Appointments';
    
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public $selectedServiceId = null;
    public $services = [];
    public $appointments = [];
    public $availabilities = [];

    public function mount(): void
    {
        // Only professionals can access
        if (Filament::auth()->user()->user_type !== 3) {
            abort(403, 'Only professionals can access this page.');
        }

        $this->loadServices();
    }

    public function loadServices(): void
    {
        $this->services = Service::where('user_id', Filament::auth()->id())
            ->where('is_active', true)
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                ];
            })
            ->toArray();
    }

    public function loadCalendarData($start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        // Load appointments
        $appointmentsQuery = Appointment::where('professional_id', Filament::auth()->id())
            ->whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('is_external', false);

        if ($this->selectedServiceId) {
            $appointmentsQuery->where('service_id', $this->selectedServiceId);
        }

        $appointments = $appointmentsQuery->with(['service', 'client'])
            ->get()
            ->map(function ($appointment) {
                $color = match($appointment->status) {
                    'pending' => '#dc3545', // Red
                    'confirmed' => '#28a745', // Green
                    'cancelled' => '#6c757d', // Gray
                    default => '#00b3f1', // Primary
                };

                return [
                    'id' => $appointment->id,
                    'title' => $appointment->service->title . ' - ' . $appointment->client_name,
                    'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->appointment_time->format('H:i:s'),
                    'color' => $color,
                    'status' => $appointment->status,
                    'service_id' => $appointment->service_id,
                    'client_name' => $appointment->client_name . ' ' . $appointment->client_surname,
                    'client_email' => $appointment->client_email,
                    'client_phone' => $appointment->client_phone,
                ];
            })
            ->toArray();

        // Load availabilities (by date in range)
        $availabilitiesQuery = ServiceAvailability::whereHas('service', function ($q) {
            $q->where('user_id', Filament::auth()->id());
        })
            ->where('is_active', true)
            ->whereBetween('availability_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($this->selectedServiceId) {
            $availabilitiesQuery->where('service_id', $this->selectedServiceId);
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
            })
            ->toArray();

        return [
            'appointments' => $appointments,
            'availabilities' => $availabilities,
        ];
    }

    public function updatedSelectedServiceId($value): void
    {
        // Reload calendar data when service filter changes
        $this->dispatch('service-changed');
    }
}
