<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayrollPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Admin')) { // Or your equivalent super admin role
            return true;
        }
        return null; // Let other methods handle it
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'company_admin']); // Corrected role
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payroll $payroll): bool
    {
        return ($user->hasRole(['admin', 'company_admin'])) &&
               $user->company_id === $payroll->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'company_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payroll $payroll): bool
    {
        return ($user->hasRole(['admin', 'company_admin'])) &&
               $user->company_id === $payroll->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payroll $payroll): bool
    {
        return ($user->hasRole(['admin', 'company_admin'])) &&
               $user->company_id === $payroll->company_id &&
               $payroll->status !== 'paid'; // Cannot delete paid payrolls
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payroll $payroll): bool
    {
        // Assuming only admins with specific permissions can restore
        return ($user->hasRole(['admin'])) &&
               $user->company_id === $payroll->company_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payroll $payroll): bool
    {
        // Assuming only admins with specific permissions can force delete
        return ($user->hasRole(['admin'])) &&
               $user->company_id === $payroll->company_id;
    }
}
