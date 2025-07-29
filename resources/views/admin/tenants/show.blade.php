@extends('layouts.sidebar')

@section('title', 'Tenant Profile')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Tenant Profile</h1>
                <p class="text-base-content/60 mt-1">View tenant details and information</p>
            </div>
            <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Users
                </a>
                                            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Profile
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

                            <!-- Tenant Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Basic Information -->
                        <div class="card bg-base-200">
                            <div class="card-body">
                                <h2 class="card-title text-xl mb-4">Basic Information</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-base-content/60">Tenant Name</label>
                                        <p class="text-base-content font-semibold">{{ $tenant->user->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-base-content/60">IC Number</label>
                                        <p class="text-base-content font-semibold">{{ $tenant->IC_number ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-base-content/60">Phone Number</label>
                                        <p class="text-base-content font-semibold">{{ $tenant->user->phone_number ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card bg-base-200">
                            <div class="card-body">
                                <h2 class="card-title text-xl mb-4">Contact Information</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-base-content/60">Emergency Contact Number</label>
                                        <p class="text-base-content font-semibold">{{ $tenant->emergency_contact ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-base-content/60">Address</label>
                                        <p class="text-base-content font-semibold">{{ $tenant->address ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-base-content/60">Additional Information</label>
                                        <p class="text-base-content font-semibold">{{ $tenant->additional_info ?? 'No additional information' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

        

        
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
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

// Manual hide alert function
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