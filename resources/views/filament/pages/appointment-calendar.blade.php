<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Service Filter -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('admin.widgets.filter_by_service') }}
            </label>
            <select wire:model.live="selectedServiceId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                <option value="">{{ __('admin.widgets.all_services') }}</option>
                @foreach($this->services as $service)
                    <option value="{{ $service['id'] }}">{{ $service['title'] }}</option>
                @endforeach
            </select>
            <div wire:loading wire:target="selectedServiceId" class="text-sm text-gray-500 mt-2">
                {{ __('admin.widgets.loading') }}
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div id="appointment-calendar" class="w-full"></div>
        </div>

        <!-- Appointment Details Modal -->
        <div id="appointment-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold" id="modal-title">{{ __('admin.widgets.appointment_details') }}</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="modal-content" class="space-y-2">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                    <div id="modal-actions" class="mt-4 flex gap-2">
                        <!-- Actions will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
    <script>
        let calendar;
        let currentAppointmentId = null;
        const adminCalendarTranslations = @json([
            'appointmentDetails' => __('admin.widgets.appointment_details'),
            'service' => __('admin.widgets.service'),
            'client' => __('admin.widgets.client'),
            'email' => __('admin.widgets.email'),
            'phone' => __('admin.widgets.phone'),
            'date' => __('admin.widgets.date'),
            'time' => __('admin.widgets.time'),
            'status' => __('admin.widgets.status'),
            'confirm' => __('admin.actions.confirm'),
            'cancel' => __('admin.actions.cancel'),
            'cancelAppointment' => __('admin.widgets.cancel_appointment'),
            'appointmentConfirmed' => __('admin.widgets.appointment_confirmed_alert'),
            'appointmentCancelled' => __('admin.widgets.appointment_cancelled_alert'),
            'failedConfirm' => __('admin.widgets.failed_confirm'),
            'failedCancel' => __('admin.widgets.failed_cancel'),
            'genericError' => __('admin.widgets.generic_error'),
            'cancelReasonPrompt' => __('admin.widgets.cancel_reason_prompt'),
        ]);

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('appointment-calendar');
            
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                validRange: {
                    start: new Date().toISOString().split('T')[0]
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    // Get current service ID from Livewire
                    const serviceId = document.querySelector('select[wire\\:model="selectedServiceId"]')?.value || '';
                    fetch(`/appointments/calendar-data?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}&service_id=${serviceId}`, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const events = [
                            ...data.appointments,
                            ...data.availabilities
                        ];
                        successCallback(events);
                    })
                    .catch(error => {
                        console.error('Error loading calendar:', error);
                        failureCallback(error);
                    });
                },
                eventClick: function(info) {
                    if (info.event.extendedProps.status) {
                        // This is an appointment
                        showAppointmentDetails(info.event);
                    }
                },
                eventDidMount: function(info) {
                    // Add tooltip
                    if (info.event.extendedProps.status) {
                        info.el.setAttribute('title', info.event.title);
                    }
                }
            });

            calendar.render();

            // Listen for service changes via Livewire
            document.addEventListener('livewire:init', () => {
                Livewire.on('service-changed', () => {
                    calendar.refetchEvents();
                });
            });
            
            // Also listen for direct model updates
            Livewire.hook('morph.updated', ({ component }) => {
                if (component.get('selectedServiceId') !== undefined) {
                    calendar.refetchEvents();
                }
            });
        });

        function showAppointmentDetails(event) {
            currentAppointmentId = event.id;
            const props = event.extendedProps;
            
            document.getElementById('modal-title').textContent = adminCalendarTranslations.appointmentDetails;
            document.getElementById('modal-content').innerHTML = `
                <p><strong>${adminCalendarTranslations.service}</strong> ${event.title.split(' - ')[0]}</p>
                <p><strong>${adminCalendarTranslations.client}</strong> ${props.client_name || 'N/A'}</p>
                <p><strong>${adminCalendarTranslations.email}</strong> ${props.client_email || 'N/A'}</p>
                <p><strong>${adminCalendarTranslations.phone}</strong> ${props.client_phone || 'N/A'}</p>
                <p><strong>${adminCalendarTranslations.date}</strong> ${event.start.toLocaleDateString()}</p>
                <p><strong>${adminCalendarTranslations.time}</strong> ${event.start.toLocaleTimeString()}</p>
                <p><strong>${adminCalendarTranslations.status}</strong> <span class="px-2 py-1 rounded text-white" style="background-color: ${event.backgroundColor}">${props.status}</span></p>
            `;

            let actionsHtml = '';
            if (props.status === 'pending') {
                actionsHtml = `
                    <button onclick="confirmAppointment(${event.id})" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        ${adminCalendarTranslations.confirm}
                    </button>
                    <button onclick="cancelAppointment(${event.id})" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        ${adminCalendarTranslations.cancel}
                    </button>
                `;
            } else if (props.status === 'confirmed') {
                actionsHtml = `
                    <button onclick="cancelAppointment(${event.id})" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        ${adminCalendarTranslations.cancelAppointment}
                    </button>
                `;
            }
            document.getElementById('modal-actions').innerHTML = actionsHtml;
            
            document.getElementById('appointment-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('appointment-modal').classList.add('hidden');
            currentAppointmentId = null;
        }

        function confirmAppointment(appointmentId) {
            fetch(`/appointments/${appointmentId}/confirm`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(adminCalendarTranslations.appointmentConfirmed);
                    calendar.refetchEvents();
                    closeModal();
                } else {
                    alert('Error: ' + (data.error || adminCalendarTranslations.failedConfirm));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(adminCalendarTranslations.genericError);
            });
        }

        function cancelAppointment(appointmentId) {
            const reason = prompt(adminCalendarTranslations.cancelReasonPrompt);
            
            fetch(`/appointments/${appointmentId}/cancel-professional`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cancellation_reason: reason || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(adminCalendarTranslations.appointmentCancelled);
                    calendar.refetchEvents();
                    closeModal();
                } else {
                    alert('Error: ' + (data.error || adminCalendarTranslations.failedCancel));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(adminCalendarTranslations.genericError);
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('appointment-modal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
    @endpush

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
    <style>
        #appointment-calendar {
            min-height: 600px;
        }
    </style>
    @endpush
</x-filament-pages::page>
