<?php

namespace App\Http\Controllers;

use App\Models\ServiceAvailability;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceAvailabilityController extends Controller
{
    /**
     * Display a listing of availabilities for a service
     */
    public function index(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $service = Service::findOrFail($request->service_id);

        if ($service->user_id !== Auth::id() || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $availabilities = ServiceAvailability::where('service_id', $service->id)
            ->orderBy('availability_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn ($a) => $a->availability_date->format('Y-m-d'));

        return response()->json(['availabilities' => $availabilities]);
    }

    /**
     * Store a newly created availability
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'availability_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
        ]);

        $service = Service::findOrFail($request->service_id);

        if ($service->user_id !== Auth::id() || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $overlapping = ServiceAvailability::where('service_id', $service->id)
            ->where('availability_date', $request->availability_date)
            ->where('is_active', true)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($overlapping) {
            return response()->json(['error' => 'This time slot overlaps with existing availability for this date.'], 422);
        }

        // Max 2 slots per hour (two 30-minute slots per hour)
        $startHour = (int) substr($request->start_time, 0, 2);
        $hourStart = sprintf('%02d:00', $startHour);
        $hourEnd = $startHour < 23 ? sprintf('%02d:00', $startHour + 1) : '23:59';
        $countInHour = ServiceAvailability::where('service_id', $service->id)
            ->where('availability_date', $request->availability_date)
            ->where('is_active', true)
            ->where('start_time', '<', $hourEnd)
            ->where('end_time', '>', $hourStart)
            ->count();

        if ($countInHour >= 2) {
            return response()->json(['error' => 'Maximum two 30-minute slots per hour for this service and date.'], 422);
        }

        $availability = ServiceAvailability::create([
            'service_id' => $service->id,
            'availability_date' => $request->availability_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Availability created successfully.',
            'availability' => $availability,
        ]);
    }

    /**
     * Store one or more availabilities with optional repeat (daily/weekly until end date).
     */
    public function storeWithRepeat(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'availability_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
            'repeat_type' => 'required|in:none,daily,weekly',
            'repeat_end_date' => [
                'required_if:repeat_type,daily,weekly',
                'date',
                'after_or_equal:availability_date',
                function (string $attr, $value, \Closure $fail) use ($request) {
                    $start = Carbon::parse($request->availability_date);
                    $end = Carbon::parse($value);
                    if ($start->diffInMonths($end) > 6) {
                        $fail('Repeat end date must be within 6 months of the start date.');
                    }
                },
            ],
        ]);

        $service = Service::findOrFail($request->service_id);

        if ($service->user_id !== Auth::id() || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $repeatType = $request->input('repeat_type', 'none');
        if ($repeatType === 'none') {
            $overlapping = ServiceAvailability::where('service_id', $service->id)
                ->where('availability_date', $request->availability_date)
                ->where('is_active', true)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                        });
                })
                ->exists();

            if ($overlapping) {
                return response()->json(['error' => 'This time slot overlaps with existing availability for this date.'], 422);
            }

            if (! ServiceAvailability::canCreateSlot($service->id, $request->availability_date, $request->start_time, $request->end_time)) {
                return response()->json(['error' => 'Maximum two 30-minute slots per hour for this service and date.'], 422);
            }

            $availability = ServiceAvailability::create([
                'service_id' => $service->id,
                'availability_date' => $request->availability_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Availability created successfully.',
                'created' => 1,
                'availability' => $availability,
            ]);
        }

        $startDate = Carbon::parse($request->availability_date);
        $endDate = Carbon::parse($request->repeat_end_date);
        $dates = [];
        $current = $startDate->copy();
        $step = $repeatType === 'weekly' ? 7 : 1;
        while ($current->lte($endDate)) {
            $dates[] = $current->toDateString();
            $current->addDays($step);
        }

        DB::beginTransaction();
        try {
            $created = 0;
            foreach ($dates as $dateStr) {
                if (! ServiceAvailability::canCreateSlot($service->id, $dateStr, $request->start_time, $request->end_time)) {
                    continue;
                }
                ServiceAvailability::create([
                    'service_id' => $service->id,
                    'availability_date' => $dateStr,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'is_active' => $request->is_active ?? true,
                ]);
                $created++;
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Availability created for {$created} date(s) successfully.",
                'created' => $created,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create availability.'], 500);
        }
    }

    /**
     * Update availability
     */
    public function update(Request $request, ServiceAvailability $serviceAvailability)
    {
        $request->validate([
            'availability_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        if ($request->has('end_time') && $request->has('start_time') && $request->end_time <= $request->start_time) {
            return response()->json(['error' => 'End time must be after start time.'], 422);
        }

        if ($serviceAvailability->service->user_id !== Auth::id() || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($request->has('availability_date') || $request->has('start_time') || $request->has('end_time')) {
            $date = $request->availability_date ?? $serviceAvailability->availability_date->format('Y-m-d');
            $startTime = $request->start_time ?? $serviceAvailability->start_time->format('H:i');
            $endTime = $request->end_time ?? $serviceAvailability->end_time->format('H:i');

            $overlapping = ServiceAvailability::where('service_id', $serviceAvailability->service_id)
                ->where('id', '!=', $serviceAvailability->id)
                ->where('availability_date', $date)
                ->where('is_active', true)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                        });
                })
                ->exists();

            if ($overlapping) {
                return response()->json(['error' => 'This time slot overlaps with existing availability for this date.'], 422);
            }
        }

        $serviceAvailability->update($request->only(['availability_date', 'start_time', 'end_time', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'Availability updated successfully.',
            'availability' => $serviceAvailability->fresh(),
        ]);
    }

    /**
     * Remove availability
     */
    public function destroy(ServiceAvailability $serviceAvailability)
    {
        if ($serviceAvailability->service->user_id !== Auth::id() || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $serviceAvailability->delete();

        return response()->json([
            'success' => true,
            'message' => 'Availability deleted successfully.',
        ]);
    }

    /**
     * Replicate availability to multiple dates
     */
    public function replicate(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'source_date' => 'required|date',
            'source_start_time' => 'required|date_format:H:i',
            'source_end_time' => 'required|date_format:H:i',
            'target_dates' => 'required|array|min:1',
            'target_dates.*' => 'date',
        ]);

        $service = Service::findOrFail($request->service_id);

        if ($service->user_id !== Auth::id() || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        DB::beginTransaction();
        try {
            $created = 0;
            $targetDates = array_unique($request->target_dates);

            foreach ($targetDates as $targetDate) {
                $dateStr = Carbon::parse($targetDate)->toDateString();
                if (! ServiceAvailability::canCreateSlot($service->id, $dateStr, $request->source_start_time, $request->source_end_time)) {
                    continue;
                }
                ServiceAvailability::create([
                    'service_id' => $service->id,
                    'availability_date' => $dateStr,
                    'start_time' => $request->source_start_time,
                    'end_time' => $request->source_end_time,
                    'is_active' => true,
                ]);
                $created++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Availability replicated to {$created} date(s) successfully.",
                'created' => $created,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to replicate availability.'], 500);
        }
    }

    /**
     * Bulk create availabilities for multiple dates with same time slot
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'dates' => 'required|array|min:1',
            'dates.*' => 'date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $service = Service::findOrFail($request->service_id);

        if ($service->user_id !== Auth::id() || Auth::user()->user_type !== 3) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        DB::beginTransaction();
        try {
            $created = 0;
            $dates = array_unique($request->dates);

            foreach ($dates as $date) {
                $dateStr = Carbon::parse($date)->toDateString();
                if (! ServiceAvailability::canCreateSlot($service->id, $dateStr, $request->start_time, $request->end_time)) {
                    continue;
                }
                ServiceAvailability::create([
                    'service_id' => $service->id,
                    'availability_date' => $dateStr,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'is_active' => true,
                ]);
                $created++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Availability created for {$created} date(s) successfully.",
                'created' => $created,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create availability.'], 500);
        }
    }

}
