@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Attendance Adjustments</h1>
        <a href="{{ route('admin.attendance-adjustments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Adjustment
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adjustments as $adjustment)
                            <tr>
                                <td>{{ $adjustment->employee->name }}</td>
                                <td>{{ $adjustment->date->format('d M Y') }}</td>
                                <td>
                                    @if($adjustment->type === 'half_day')
                                        <span class="badge badge-warning">Half Day</span>
                                    @else
                                        <span class="badge badge-info">Reimbursement</span>
                                    @endif
                                </td>
                                <td>
                                    @if($adjustment->type === 'reimbursement')
                                        {{ number_format($adjustment->amount, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $adjustment->description ?? 'N/A' }}</td>
                                <td>
                                    @if($adjustment->is_approved)
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$adjustment->is_approved)
                                        <form action="{{ route('admin.attendance-adjustments.approve', $adjustment) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.attendance-adjustments.destroy', $adjustment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No adjustments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $adjustments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
