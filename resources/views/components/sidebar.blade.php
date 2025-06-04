@auth
    @php
        // Helper function to check if a module is accessible for the current user role
        function hasModuleAccess($modules, $moduleName, $role) {
            // Company admin role always has access to all modules
            if ($role === 'company_admin') {
                return true;
            }
            
            // For other roles, check module settings if they exist
            return $modules[$moduleName][$role] ?? true; // Default to true if not set
        }
        
        // Get module access settings from session if available
        $modules = session('modules', []);
        $userRole = Auth::user()->role;
        
        // Debug information - remove after fixing
        // dd($modules, $userRole);
    @endphp
    <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <a href="">PayNinja</a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="">PayNinja</a>
            </div>
            <ul class="sidebar-menu">
                <li class="menu-header">Dashboard</li>
                <li class="{{ Request::is('home') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('home') }}"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                </li>
                @if (Auth::user()->hasRole('superadmin'))
                    <li class="menu-header">Companies</li>
                    <li class="{{ Request::is('hakakses') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('hakakses') }}"><i class="fas fa-user-shield"></i> <span>All
                                Users</span></a>
                    </li>
                    <li class="{{ Request::is('superadmin/companies') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('superadmin.companies.index') }}"><i class="fas fa-building"></i>
                            <span>Manage Companies</span></a>
                    </li>
                    <li class="menu-header">Company Admins</li>
                    <li class="{{ Request::is('superadmin/assigned-company-admins') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('superadmin.assigned-company-admins.index') }}"><i
                                class="fas fa-users-cog"></i> <span>Assign Company Admin</span></a>
                    </li>
                @endif


                <!-- {{-- or 'employee' --}} -->
                @if (Auth::user()->hasRole(['user', 'employee']))
                    <li class="menu-header">Profile</li>
                    <li class="{{ Request::is('employee/profile') ? 'active' : '' }}">
                        <!-- <a class="nav-link" href="{{ route('employee.profile') }}"><i class="far fa-user"></i> <span>My
                                Profile</span></a>
                    </li> -->
                    <li class="{{ Request::is('employee/colleagues') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('employee.colleagues') }}"><i class="fas fa-users"></i> <span>My
                                Colleagues</span></a>
                    </li>

                    @if(hasModuleAccess($modules, 'attendance', $userRole))
                    <li class="menu-header">Attendance</li>
                    <li class="{{ Request::is('attendance') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('attendance.dashboard') }}"><i class="fas fa-clock"></i>
                            <span>Attendance Dashboard</span></a>
                    </li>
                    <li class="{{ Request::is('attendance/check-in-out') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('attendance.check-in') }}"><i class="fas fa-sign-in-alt"></i>
                            <span>Check In/Out</span></a>
                    </li>
                    <li class="{{ Request::is('attendance/my-attendance') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('attendance.my-attendance') }}"><i
                                class="fas fa-calendar-check"></i> <span>My Attendance</span></a>
                    </li>
                    @endif

                    @if(hasModuleAccess($modules, 'leave', $userRole))
                    <li class="menu-header">Leave Management</li>
                    <li
                        class="{{ Request::is('leave-management/leave-requests') && !Request::is('leave-management/leave-requests/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('leave-management.leave-requests.index') }}"><i
                                class="fas fa-clipboard-list"></i> <span>My Leave Requests</span></a>
                    </li>
                    <li class="{{ Request::is('leave-management/leave-requests/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('leave-management.leave-requests.create') }}"><i
                                class="fas fa-calendar-plus"></i> <span>Apply for Leave</span></a>
                    </li>
                    <li class="{{ Request::is('leave-management/leave-requests/calendar') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('leave-management.leave-requests.calendar') }}"><i
                                class="fas fa-calendar-alt"></i> <span>Leave Calendar</span></a>
                    </li>
                    @endif
    
    @if(hasModuleAccess($modules, 'salary', $userRole))
    <li class="menu-header">Salary</li>
    <li class="{{ Request::is('employee/salary/details*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('employee.salary.details') }}">
            <i class="fas fa-money-check-alt"></i>
            <span>Salary Details</span>
        </a>
    </li>
    @if(isset(Auth::user()->employee) && Auth::user()->employee->currentSalary)
    <li class="{{ Request::is('employee/salary/payslips*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('employee.salary.payslips') }}">
            <i class="fas fa-file-pdf"></i>
            <span>My Payslips</span>
        </a>
    </li>
    @endif
    @endif
    
                    @if(hasModuleAccess($modules, 'reimbursement', $userRole))
                    <li class="menu-header">Reimbursements</li>
                    <li
                        class="{{ Request::is('reimbursements') && !Request::is('reimbursements/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('reimbursements.index') }}"><i class="fas fa-receipt"></i>
                            <span>My Reimbursements</span></a>
                    </li>
                    <li class="{{ Request::is('reimbursements/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('reimbursements.create') }}"><i class="fas fa-plus-circle"></i>
                            <span>Request Reimbursement</span></a>
                    </li>
                    @endif
                @endif

                {{-- Company Admin Routes --}}
                @if (Auth::user()->hasRole('company_admin') || Auth::user()->hasRole('admin'))
                    <li class="menu-header">Company Management</li>



                    <li class="{{ Request::is('company-admin/employees*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company-admin.employees.index') }}"><i
                                class="fas fa-users"></i> <span>Employee Management</span></a>
                    </li>

                    @if(auth()->user()->role === 'company_admin' || auth()->user()->role === 'admin')
                    <li class="menu-header">Settings</li>
                    @if(auth()->user()->role === 'company_admin')
                    <li class="dropdown {{ Request::is('company-admin/module-access*') ? 'active' : '' }}">
                        <a href="{{ route('company-admin.module-access.index') }}" class="nav-link">
                            <i class="fas fa-cogs"></i><span>Module Access</span>
                        </a>
                    </li>
                    @endif
                    @endif

                    <li class="{{ Request::is('company/companies/*/employees/create') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ route('company.employees.create', ['companyId' => Auth::user()->company_id]) }}"><i
                                class="fas fa-user-plus"></i> <span>Add Employee</span></a>
                    <li class="{{ Request::is('company-admin/settings*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company-admin.settings.index') }}"><i class="fas fa-cog"></i>
                            <span>Company Settings</span></a>
                    </li>
                    @if(hasModuleAccess($modules, 'designations', $userRole))
                    <li class="{{ Request::is('company/designations*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company.designations.index') }}"><i
                                class="fas fa-id-badge"></i> <span>Manage Designations</span></a>
                    @endif

                    @if(hasModuleAccess($modules, 'departments', $userRole))
                    <li class="{{ Request::is('company/departments*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company.departments.index') }}"><i class="fas fa-building"></i>
                            <span>Manage Departments</span></a>
                    </li>
                    @endif

                    @if(hasModuleAccess($modules, 'teams', $userRole))
                    <li class="{{ Request::is('company/teams*') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ route('company.teams.index', ['companyId' => Auth::user()->company_id]) }}"><i
                                class="fas fa-users-cog"></i> <span>Manage Teams</span></a>
                    </li>
                    @endif


                    @if(hasModuleAccess($modules, 'attendance', $userRole))
                    <li class="menu-header">Attendance Management</li>
                    <li class="{{ Request::is('admin/attendance') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.attendance.index') }}"><i
                                class="fas fa-user-clock"></i>
                            <span>Manage Attendance</span></a>
                    </li>
                    <li class="{{ Request::is('admin/attendance/summary') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.attendance.summary') }}"><i
                                class="fas fa-chart-pie"></i> <span>Attendance Summary</span></a>
                    </li>
                    <li class="{{ Request::is('attendance/check-in-out') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('attendance.check-in') }}"><i class="fas fa-sign-in-alt"></i>
                            <span>Check In/Out</span></a>
                    </li>
                    <li class="{{ Request::is('attendance/my-attendance') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('attendance.my-attendance') }}"><i
                                class="fas fa-calendar-check"></i> <span>My Attendance</span></a>
                    </li>
                    <li class="{{ Request::is('admin/attendance/settings*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.attendance.settings') }}"><i class="fas fa-cog"></i>
                            <span>Attendance Settings</span></a>
                    </li>
                    <li class="{{ Request::is('admin/shifts*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.shifts.index') }}"><i class="fas fa-clock"></i>
                            <span>Manage Shifts</span></a>
                    </li>
                    @endif

                    @if(hasModuleAccess($modules, 'leave', $userRole))
                    <li class="menu-header">Leave Management</li>
                    <li class="{{ Request::is('company/leave-types*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company.leave-types.index') }}"><i
                                class="fas fa-calendar-alt"></i> <span>Leave Types</span></a>
                    </li>
                    <li class="{{ Request::is('company/leave-balances*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company.leave-balances.index') }}"><i
                                class="fas fa-balance-scale"></i> <span>Leave Balances</span></a>
                    </li>
                    <li
                        class="{{ Request::is('company/leave-requests') && !Request::is('company/leave-requests/calendar') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company.leave-requests.index') }}"><i
                                class="fas fa-clipboard-list"></i> <span>Leave Requests</span></a>
                    </li>
                    <li class="{{ Request::is('company/leave-requests/calendar') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('company.leave-requests.calendar') }}"><i
                                class="fas fa-calendar-alt"></i> <span>Leave Calendar</span></a>
                    </li>
                    @endif

                    @if(hasModuleAccess($modules, 'salary', $userRole))
            <li class="menu-header">Salary Management</li>
            <li class="{{ Request::is('admin/salary*') && !Request::is('admin/salary/create*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.salary.index') }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Employee Salaries</span>
                </a>
            </li>
            <!-- <li class="{{ Request::is('admin/salary/create*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.salary.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Salary Record</span>
                </a>
            </li> -->
            <li class="{{ Request::is('employee/salary/details*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('employee.salary.details') }}">
                    <i class="fas fa-money-check-alt"></i>
                    <span>Salary Details</span>
                </a>
            </li>
            @if(isset(Auth::user()->employee) && Auth::user()->employee->currentSalary)
            <li class="{{ Request::is('employee/salary/payslips*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('employee.salary.payslips') }}">
                    <i class="fas fa-file-pdf"></i>
                    <span>My Payslips</span>
                </a>
            </li>
            @endif
            @endif

            @if(hasModuleAccess($modules, 'reimbursements', $userRole))
            <li class="menu-header">Reimbursements</li>
          
            <li class="{{ Request::is('reimbursements/create') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reimbursements.create') }}"><i class="fas fa-plus-circle"></i> <span>Request Reimbursement</span></a>
            </li>
            <li class="{{ Request::is('reimbursements') && !Request::is('reimbursements/create') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reimbursements.index') }}"><i class="fas fa-tasks"></i> <span>Pending Approvals</span></a>
            </li>
         @endif
           
         
            @endif











                <!-- profile ganti password -->
                <li class="menu-header">Profile</li>
                <li class="{{ Request::is('profile/edit') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('profile/edit') }}"><i class="far fa-user"></i>
                        <span>Profile</span></a>
                </li>
                <li class="{{ Request::is('profile/change-password') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('profile/change-password') }}"><i class="fas fa-key"></i>
                        <span>Change Password</span></a>
                </li>

            </ul>
        </aside>
    </div>
@endauth
