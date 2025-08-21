@extends('layouts.sidebar')

@section('title', 'Create Booking Request')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Create Booking Request</h1>
        
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

        <form method="POST" action="{{ route('admin.booking-requests.store') }}" class="space-y-6">
            @csrf
            
            <!-- Tenant and Property Selection Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tenant Selection -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Tenant</span>
                    </label>
                    <div class="relative">
                        <select name="tenant_id" id="tenant_id" class="select select-bordered w-full pl-10 @error('tenant_id') select-error @enderror" required>
                            <option value="">Select Tenant</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->tenant_id }}" {{ old('tenant_id') == $tenant->tenant_id ? 'selected' : '' }}>
                                    {{ $tenant->user->name ?? 'N/A' }} ({{ $tenant->user->email ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    @error('tenant_id')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Property Selection -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Property</span>
                    </label>
                    <div class="relative">
                        <select name="property_id" id="property_id" class="select select-bordered w-full pl-10 @error('property_id') select-error @enderror" required onchange="updateUnits()">
                            <option value="">Select Property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->property_id }}" {{ old('property_id') == $property->property_id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    @error('property_id')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Unit Selection -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Unit</span>
                </label>
                <div class="relative">
                    <select name="unit_id" id="unit_id" class="select select-bordered w-full pl-10 @error('unit_id') select-error @enderror" required>
                        <option value="">Select Property First</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                        </svg>
                    </div>
                </div>
                @error('unit_id')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Date and Time Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Date</span>
                    </label>
                    <div class="relative">
                        <input
                            type="date"
                            name="date"
                            id="date"
                            class="input input-bordered w-full pl-10 @error('date') input-error @enderror"
                            value="{{ old('date') }}"
                            required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    @error('date')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Duration Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Duration Type</span>
                    </label>
                    <div class="relative">
                        <select name="duration_type" id="duration_type" class="select select-bordered w-full pl-10 @error('duration_type') select-error @enderror" required>
                            <option value="">Select Duration Type</option>
                            <option value="hourly" {{ old('duration_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="daily" {{ old('duration_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    @error('duration_type')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Time Range Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Time -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Start Time</span>
                    </label>
                    <div class="relative">
                        <input
                            type="time"
                            name="start_time"
                            id="start_time"
                            class="input input-bordered w-full pl-10 @error('start_time') input-error @enderror"
                            value="{{ old('start_time') }}"
                            required
                            onchange="calculateDuration()"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    @error('start_time')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- End Time -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">End Time</span>
                    </label>
                    <div class="relative">
                        <input
                            type="time"
                            name="end_time"
                            id="end_time"
                            class="input input-bordered w-full pl-10 @error('end_time') input-error @enderror"
                            value="{{ old('end_time') }}"
                            required
                            onchange="calculateDuration()"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    @error('end_time')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Duration (Auto-calculated, Read-only) -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Duration (Auto-calculated)</span>
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="duration_display"
                        id="duration_display"
                        placeholder="Select start and end time to calculate duration"
                        class="input input-bordered w-full pl-10 bg-base-200"
                        readonly
                    />
                    <input
                        type="hidden"
                        name="duration"
                        id="duration"
                        value="{{ old('duration') }}"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                @error('duration')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Notes -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Notes</span>
                </label>
                <div class="relative">
                    <textarea 
                        name="notes"
                        id="notes"
                        placeholder="Enter any additional notes or special requirements" 
                        class="textarea textarea-bordered w-full pl-10 @error('notes') textarea-error @enderror"
                        rows="3"
                    >{{ old('notes') }}</textarea>
                    <div class="absolute top-3 left-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                @error('notes')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-control mt-8">
                <button type="submit" class="btn btn-primary w-full">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Booking Request
                </button>
            </div>
        </form>
        
        <div class="mt-6">
            <a href="{{ route('admin.booking-requests.index') }}" class="btn btn-outline w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Booking Requests
            </a>
        </div>
    </div>
</div>

<script>
// Store units data for dynamic loading
const unitsData = @json($units->groupBy('property_id'));

function updateUnits() {
    const propertyId = document.getElementById('property_id').value;
    const unitSelect = document.getElementById('unit_id');
    
    // Clear current options
    unitSelect.innerHTML = '<option value="">Select Unit</option>';
    
    if (propertyId && unitsData[propertyId]) {
        unitsData[propertyId].forEach(unit => {
            // Only show booking units
            if (unit.leasing_type === 'booking') {
                const option = document.createElement('option');
                option.value = unit.unit_id;
                option.textContent = unit.name;
                unitSelect.appendChild(option);
            }
        });
        
        if (unitSelect.children.length === 1) {
            unitSelect.innerHTML = '<option value="">No booking units available for this property</option>';
        }
    }
}

function calculateDuration() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    const durationDisplay = document.getElementById('duration_display');
    const durationHidden = document.getElementById('duration');
    const durationType = document.getElementById('duration_type').value;
    
    if (startTime && endTime) {
        const start = new Date(`2000-01-01T${startTime}:00`);
        const end = new Date(`2000-01-01T${endTime}:00`);
        
        // Handle overnight bookings
        if (end < start) {
            end.setDate(end.getDate() + 1);
        }
        
        const diffMs = end - start;
        const diffHours = diffMs / (1000 * 60 * 60);
        
        if (diffHours > 0) {
            if (durationType === 'hourly') {
                const hours = Math.ceil(diffHours);
                durationDisplay.value = `${hours} hour${hours !== 1 ? 's' : ''}`;
                durationHidden.value = hours;
            } else if (durationType === 'daily') {
                const days = Math.ceil(diffHours / 24);
                durationDisplay.value = `${days} day${days !== 1 ? 's' : ''}`;
                durationHidden.value = days;
            } else {
                // Default to hours if duration type not selected
                const hours = Math.ceil(diffHours);
                durationDisplay.value = `${hours} hour${hours !== 1 ? 's' : ''} (Please select duration type)`;
                durationHidden.value = hours;
            }
        } else {
            durationDisplay.value = 'End time must be after start time';
            durationHidden.value = '';
        }
    } else {
        durationDisplay.value = 'Select start and end time to calculate duration';
        durationHidden.value = '';
    }
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('date').min = tomorrow.toISOString().split('T')[0];
    
    // Add event listener for duration type change
    document.getElementById('duration_type').addEventListener('change', calculateDuration);
    
    // Calculate initial duration if values are present (for form validation errors)
    calculateDuration();
    
    // Auto-hide alerts after 5 seconds
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