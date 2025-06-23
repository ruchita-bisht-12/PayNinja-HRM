<?php

namespace App\Console\Commands;

use App\Models\AcademicHoliday;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MarkHolidayAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-holidays {--date= : The date to check (Y-m-d), defaults to today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark attendance as Holiday for all employees on academic holidays';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : now();
        $dateString = $date->toDateString();
        
        $this->info("Checking for holidays on: " . $dateString);
        
        // Get all companies that have academic holidays on this date
        $holidays = AcademicHoliday::whereDate('from_date', '<=', $dateString)
            ->whereDate('to_date', '>=', $dateString)
            ->with('company')
            ->get();
            
        if ($holidays->isEmpty()) {
            $this->info("No academic holidays found for " . $dateString);
            return 0;
        }
        
        $this->info("Found " . $holidays->count() . " academic holiday(s) on this date");
        
        $totalMarked = 0;
        
        foreach ($holidays as $holiday) {
            $this->line("Processing: " . $holiday->name . " for company: " . $holiday->company->name);
            
            // Get all active employees for this company with their names
            $employees = Employee::where('company_id', $holiday->company_id)
                // ->where('status', 'active')
                ->with('user:id,name') // Eager load user relationship to get names
                ->get(['id', 'user_id']);
                
            $this->info("Found " . $employees->count() . " active employees");
            
            // Display list of employees being processed
            $this->line("Employees being marked for holiday:");
            $this->table(
                ['ID', 'Name'],
                $employees->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->user->name ?? 'N/A'
                    ];
                })->toArray()
            );
            
            // Prepare data for bulk insert/update
            $attendanceData = [];
            $now = now();
            
            foreach ($employees as $employee) {
                $attendanceData[] = [
                    'employee_id' => $employee->id,
                    'date' => $dateString,
                    'status' => 'Holiday',
                    'remarks' => 'Academic Holiday: ' . $holiday->name,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
            
            if (!empty($attendanceData)) {
                // Use upsert to handle duplicates
                Attendance::upsert(
                    $attendanceData,
                    ['employee_id', 'date'],
                    ['status', 'remarks', 'updated_at']
                );
                
                $totalMarked += count($attendanceData);
                $this->info("✓ Marked " . count($attendanceData) . " employees for " . $holiday->name);
            }
        }
        
        $this->info("\nTotal attendance records marked as Holiday: " . $totalMarked);
        return 0;
    }
}
