@extends('layouts.sidebar')

@section('title', 'Unit Details')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Unit Details</h1>
                <p class="text-base-content/60 mt-1">View unit information</p>
            </div>
            <div class="flex items-center gap-3 mt-4 md:mt-0">
                <a href="{{ route('units.edit', $unit->unit_id) }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Unit
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

        @if(session('error'))
            <div class="alert alert-error mb-6" id="errorAlert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('errorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Unit Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Basic Information -->
            <div class="space-y-6">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl font-semibold mb-4">Basic Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-base-content/60">Unit Name</label>
                                <p class="text-lg font-semibold text-base-content">{{ $unit->name }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-base-content/60">Property</label>
                                <p class="text-lg font-semibold text-base-content">
                                    <a href="{{ route('properties.show', $unit->property->property_id) }}" class="text-primary hover:underline">
                                        {{ $unit->property->name ?? 'N/A' }}
                                    </a>
                                </p>
                                @if($unit->property)
                                    <p class="text-sm text-base-content/60">{{ $unit->property->address }}</p>
                                @endif
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-base-content/60">Unit Type</label>
                                <p class="text-lg font-semibold text-base-content">{{ ucfirst($unit->type) }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-base-content/60">Status</label>
                                <div class="mt-1">
                                    @if($unit->status === 'available')
                                        <span class="badge badge-success badge-lg">Available</span>
                                    @elseif($unit->status === 'occupied')
                                        <span class="badge badge-warning badge-lg">Occupied</span>
                                    @elseif($unit->status === 'maintenance')
                                        <span class="badge badge-error badge-lg">Maintenance</span>
                                    @else
                                        <span class="badge badge-neutral badge-lg">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="space-y-6">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl font-semibold mb-4">Additional Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-base-content/60">Description</label>
                                <p class="text-base-content">
                                    {{ $unit->description ?: 'No description provided' }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-base-content/60">Created At</label>
                                <p class="text-base-content">{{ $unit->created_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-base-content/60">Last Updated</label>
                                <p class="text-base-content">{{ $unit->updated_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl font-semibold mb-4">Quick Actions</h2>
                        
                        <div class="flex flex-col space-y-3">
                            <a href="{{ route('units.edit', $unit->unit_id) }}" class="btn btn-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Unit
                            </a>
                            
                            <button onclick="confirmDelete({{ $unit->unit_id }})" class="btn btn-error">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Unit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="{{ route('units.index') }}" class="btn btn-outline w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Manage Units
            </a>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Confirm Delete</h3>
        <p class="py-4">Are you sure you want to delete this unit? This action cannot be undone.</p>
        <div class="modal-action">
            <button class="btn btn-ghost" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
// Delete confirmation
function confirmDelete(unitId) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = `/admin/units/${unitId}`;
    modal.classList.add('modal-open');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('modal-open');
}

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }
        }, 5000);
    });
});

function hideAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.remove();
            }
        }, 500);
    }
}
</script>
@endsection 