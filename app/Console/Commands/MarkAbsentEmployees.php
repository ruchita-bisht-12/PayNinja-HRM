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
    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : now()->subDay();
        
        $this->info("Marking absent employees for date: " . $date->toDateString());
        
        $count = $this->attendanceService->markAbsentEmployees($date);
        
        $this->info("Marked {$count} employees as absent.");
        
        return 0;
    }
}
