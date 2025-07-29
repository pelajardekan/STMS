@extends('layouts.sidebar')

@section('title', 'My Rental Requests')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">My Rental Requests</h1>
                <p class="text-base-content/60 mt-1">View and manage your rental requests</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.rental-requests.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Rental Request
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success mb-6" id="successAlert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('successAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error mb-6" id="errorAlert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $errors->first() }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('errorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Rental Requests Table -->
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Property</th>
                        <th>Unit</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rentalRequests as $rentalRequest)
                        <tr>
                            <td>{{ $rentalRequest->rental_request_id }}</td>
                            <td>
                                <div class="font-semibold">{{ $rentalRequest->property->name }}</div>
                                <div class="text-sm text-base-content/60">{{ Str::limit($rentalRequest->property->address, 30) }}</div>
                            </td>
                            <td>
                                <div class="font-semibold">{{ $rentalRequest->unit->name }}</div>
                                <div class="text-sm text-base-content/60">{{ ucfirst($rentalRequest->unit->type) }}</div>
                            </td>
                            <td>{{ $rentalRequest->start_date->format('M d, Y') }}</td>
                            <td>{{ $rentalRequest->end_date->format('M d, Y') }}</td>
                            <td>{{ $rentalRequest->duration }} {{ $rentalRequest->duration_type }}</td>
                            <td>
                                @if($rentalRequest->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($rentalRequest->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($rentalRequest->status === 'rejected')
                                    <span class="badge badge-error">Rejected</span>
                                @else
                                    <span class="badge badge-info">{{ ucfirst($rentalRequest->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $rentalRequest->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('tenant.rental-requests.show', $rentalRequest) }}" class="btn btn-ghost btn-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-base-content mb-2">No Rental Requests</h3>
                                    <p class="text-base-content/60 mb-4">You haven't submitted any rental requests yet.</p>
                                    <a href="{{ route('tenant.rental-requests.create') }}" class="btn btn-primary">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Create Your First Request
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rentalRequests->hasPages())
            <div class="mt-8">
                {{ $rentalRequests->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function hideAlert(alertId) {
    document.getElementById(alertId).style.display = 'none';
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 5000);
});
</script>
@endsection 