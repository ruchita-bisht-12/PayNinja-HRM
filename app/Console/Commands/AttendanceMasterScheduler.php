<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AttendanceMasterScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:run-all {date? : The date to run attendance marking for (Y-m-d), defaults to yesterday}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all daily attendance marking commands in the correct priority order: Holiday > Week-Off > Leaves > Absent';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : now()->subDay();
        $dateString = $date->toDateString();

        $this->info("Starting Master Attendance Scheduler for date: " . $dateString);

        // 1. Mark Holidays (Highest Priority)
        $this->info("\n--- Marking Holidays ---");
        $this->call('attendance:mark-holidays', ['--date' => $dateString]);

        // 2. Mark Weekends as Week-Off
        $this->info("\n--- Marking Weekends as Week-Off ---");
        $this->call('attendance:mark-weekend', ['date' => $dateString]);

        // 3. Mark Approved Leaves
        $this->info("\n--- Marking Approved Leaves ---");
        $this->call('attendance:mark-leaves', ['date' => $dateString]);

        // 4. Mark Remaining as Absent (Lowest Priority)
        $this->info("\n--- Marking Absent Employees ---");
        $this->call('attendance:mark-absent', ['date' => $dateString]);

        $this->info("\nMaster Attendance Scheduler completed for date: " . $dateString);

        return 0;
    }
} 