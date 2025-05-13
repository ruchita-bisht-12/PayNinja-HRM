<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function show()
    {
        // Display logged-in employee data
        $employee = auth()->user();  // Get the logged-in employee
        return view('employee.profile', compact('employee'));
    }
}
