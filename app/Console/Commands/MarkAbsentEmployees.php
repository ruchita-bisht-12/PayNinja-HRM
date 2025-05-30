<?php

namespace App\Console\Commands;

use App\Services\AttendanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MarkAbsentEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent {date? : The date to mark absences for (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark employees as absent if they have not checked in';

    /**
     * The attendance service instance.
     *
     * @var \App\Services\AttendanceService
     */
    protected $attendanceService;

    /**
     * Create a new command instance.
     *
     * @param  \App\Services\AttendanceService  $attendanceService
     * @return void
     */
    public function __construct(AttendanceService $attendanceService)
    {
        parent::__construct();
        $this->attendanceService = $attendanceService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    // public function handle()
    // {
    //     $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : now()->subDay();
        
    //     $this->info("Marking absent employees for date: " . $date->toDateString());
        
    //     $count = $this->attendanceService->markAbsentEmployees($date);

    //     $this->info("Marked {$count} employees as absent.");
        
    //     return 0;
    // }

    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : now()->subDay();
        $dateString = $date->toDateString();

        $this->info("Marking absent employees for date: " . $dateString);

        // Get all employees who haven't checked in for the date
        $employees = \App\Models\Employee::whereDoesntHave('attendances', function($query) use ($dateString) {
            $query->whereDate('date', $dateString);
        })->get();

        $markedAbsent = 0;
        $absentNames = [];

        foreach ($employees as $employee) {
            try {
                // Get company-specific settings
                $settings = $this->attendanceService->getAttendanceSettings($employee->company_id);
                
                if (!$settings) {
                    $this->error("Attendance settings not found for company ID: " . $employee->company_id);
                    Log::error('Attendance settings not found for company', ['company_id' => $employee->company_id]);
                    continue;
                }

                // Create absent record
                $employee->attendances()->create([
                    'date' => $dateString,
                    'status' => 'Absent',
                    'check_in_status' => 'Absent',
                    'office_start_time' => $settings->office_start_time ?? '09:00:00',
                    'office_end_time' => $settings->office_end_time ?? '18:00:00',
                    'grace_period' => $settings->grace_period ?? '00:15:00',
                    'remarks' => 'Automatically marked as absent by system',
                ]);

                $markedAbsent++;
                $absentNames[] = $employee->name;
                
                $this->info("Marked employee as absent: " . $employee->name);
                
            } catch (\Exception $e) {
                Log::error('Failed to mark employee as absent', [
                    'employee_id' => $employee->id,
                    'date' => $dateString,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $this->error("Error marking employee as absent (ID: {$employee->id}): " . $e->getMessage());
            }
        }

        $this->info("Marked {$markedAbsent} employees as absent for {$dateString}");
        
        if ($markedAbsent > 0) {
            $this->info("Absent Employees:");
            foreach ($absentNames as $name) {
                $this->line("- {$name}");
            }
        }

        return 0;
    }

}
