<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Overtime Settings
    |--------------------------------------------------------------------------
    |
    | overtime_rate_multiplier: The factor by which the hourly rate is multiplied
    | for overtime calculations. E.g., 1.5 for time-and-a-half.
    |
    */
    'overtime_rate_multiplier' => 1.5,

    /*
    |--------------------------------------------------------------------------
    | Default Work Schedule Assumptions
    |--------------------------------------------------------------------------
    |
    | These values are used as fallbacks if specific shift data is unavailable
    | or for general calculations (e.g., deriving an hourly rate from a monthly salary).
    |
    | default_monthly_working_days: Assumed number of working days in a month.
    | default_daily_work_hours: Assumed number of work hours in a standard day.
    |
    */
    'default_monthly_working_days' => 22,
    'default_daily_work_hours' => 8,

    /*
    |--------------------------------------------------------------------------
    | Leave Settings
    |--------------------------------------------------------------------------
    |
    | unpaid_leave_type_name: The name of the LeaveType model record that
    | represents unpaid leave. This is used to identify unpaid leave requests
    | for payroll deductions.
    |
    */
    'unpaid_leave_type_name' => 'Unpaid Leave',

    /*
    |--------------------------------------------------------------------------
    | Payroll Cycle Settings (Future Use)
    |--------------------------------------------------------------------------
    |
    | payroll_day_of_month: The typical day of the month payroll is processed.
    | pay_period_frequency: e.g., 'monthly', 'semi-monthly', 'weekly'.
    |
    */
    // 'payroll_day_of_month' => 28,
    // 'pay_period_frequency' => 'monthly',

];
