<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\AttendanceSetting;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->middleware('auth:api');
        $this->attendanceService = $attendanceService;
    }

    /**
     * Check in the authenticated employee
     */
    /**
     * Check in the authenticated employee with geolocation
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'remarks' => 'nullable|string|max:500'
        ]);

        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee record not found'
            ], 404);
        }

        $result = $this->attendanceService->checkIn(
            $employee, 
            $request->location,
            $request->latitude,
            $request->longitude
        );
        
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Check out the authenticated employee
     */
    /**
     * Check out the authenticated employee with geolocation
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'remarks' => 'nullable|string|max:500'
        ]);

        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee record not found'
            ], 404);
        }

        $result = $this->attendanceService->checkOut(
            $employee, 
            $request->location,
            $request->latitude,
            $request->longitude
        );
        
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get attendance records for the authenticated employee
     */
    /**
     * Get geolocation settings for the current company
     */
    public function getGeolocationSettings()
    {
        $settings = AttendanceSetting::first();
        
        return response()->json([
            'enable_geolocation' => $settings->enable_geolocation ?? false,
            'office_latitude' => $settings->office_latitude,
            'office_longitude' => $settings->office_longitude,
            'geofence_radius' => $settings->geofence_radius ?? 100,
        ]);
    }
    
    /**
     * Check if a location is within the allowed geofence
     */
    public function checkLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);
        
        $settings = AttendanceSetting::first();
        
        if (!$settings || !$settings->enable_geolocation) {
            return response()->json([
                'within_geofence' => true,
                'distance' => 0,
                'message' => 'Geolocation is not required'
            ]);
        }
        
        $userLat = $request->latitude;
        $userLng = $request->longitude;
        $officeLat = $settings->office_latitude;
        $officeLng = $settings->office_longitude;
        $radius = $settings->geofence_radius;
        
        // Calculate distance using Haversine formula
        $earthRadius = 6371000; // Earth's radius in meters
        $lat1 = deg2rad($userLat);
        $lon1 = deg2rad($userLng);
        $lat2 = deg2rad($officeLat);
        $lon2 = deg2rad($officeLng);
        
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        
        $a = sin($dlat / 2) * sin($dlat / 2) +
             cos($lat1) * cos($lat2) *
             sin($dlon / 2) * sin($dlon / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        
        $withinGeofence = $distance <= $radius;
        
        return response()->json([
            'within_geofence' => $withinGeofence,
            'distance' => $distance,
            'message' => $withinGeofence 
                ? 'You are within the allowed area' 
                : 'You are outside the allowed area'
        ]);
    }
    
    /**
     * Get attendance records for the authenticated employee
     */
    public function myAttendance(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => ['nullable', Rule::in(['Present', 'Absent', 'Late', 'On Leave', 'Half Day'])],
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee record not found'
            ], 404);
        }

        $query = $employee->attendances()
            ->with('shift')
            ->orderBy('date', 'desc');

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 30;
        $attendances = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }

    /**
     * Get attendance summary for the authenticated employee
     */
    public function myAttendanceSummary(Request $request)
    {
        $request->validate([
            'year' => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee record not found'
            ], 404);
        }

        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = $employee->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $summary = [
            'present' => $attendances->where('status', 'Present')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'on_leave' => $attendances->where('status', 'On Leave')->count(),
            'half_day' => $attendances->where('status', 'Half Day')->count(),
            'total_days' => $startDate->daysInMonth,
            'working_days' => $this->getWorkingDays($startDate, $endDate, $employee->company_id),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Calculate working days between two dates, excluding weekends and holidays
     */
    protected function getWorkingDays($startDate, $endDate, $companyId)
    {
        $totalDays = 0;
        $current = $startDate->copy();
        $holidays = $this->getHolidaysInRange($startDate, $endDate, $companyId);
        $weekendDays = $this->getCompanyWeekendDays($companyId);

        while ($current->lte($endDate)) {
            $dayOfWeek = $current->format('l');
            
            // Check if it's a weekend
            if (in_array($dayOfWeek, $weekendDays)) {
                $current->addDay();
                continue;
            }

            // Check if it's a holiday
            $isHoliday = $holidays->contains(function ($holiday) use ($current) {
                return $holiday->date->isSameDay($current);
            });

            if (!$isHoliday) {
                $totalDays++;
            }

            $current->addDay();
        }

        return $totalDays;
    }

    /**
     * Get holidays within a date range
     */
    protected function getHolidaysInRange($startDate, $endDate, $companyId)
    {
        return \App\Models\Holiday::where('company_id', $companyId)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('is_recurring', true)
                            ->whereMonth('date', '>=', $startDate->month)
                            ->whereMonth('date', '<=', $endDate->month);
                    });
            })
            ->get();
    }

    /**
     * Get company's weekend days
     */
    protected function getCompanyWeekendDays($companyId)
    {
        $settings = \App\Models\AttendanceSetting::where('company_id', $companyId)->first();
        
        if (!$settings) {
            return ['Saturday', 'Sunday'];
        }

        return explode(',', $settings->weekend_days);
    }
}
