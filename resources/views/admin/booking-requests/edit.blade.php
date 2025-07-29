@extends('layouts.sidebar')

@section('title', 'Edit Booking Request')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Edit Booking Request</h1>
        
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

        <form method="POST" action="{{ route('admin.booking-requests.update', $bookingRequest) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Read-only Information Display -->
            <div class="bg-base-200 p-6 rounded-lg space-y-4">
                <h3 class="text-lg font-semibold text-base-content">Booking Request Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Tenant</span>
                        </label>
                        <div class="text-base-content">{{ $bookingRequest->tenant->user->name ?? 'N/A' }} ({{ $bookingRequest->tenant->user->email ?? 'N/A' }})</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Property</span>
                        </label>
                        <div class="text-base-content">{{ $bookingRequest->property->name }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Unit</span>
                        </label>
                        <div class="text-base-content">{{ $bookingRequest->unit->name ?? 'N/A' }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Date</span>
                        </label>
                        <div class="text-base-content">{{ $bookingRequest->date ? $bookingRequest->date->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Time</span>
                        </label>
                        <div class="text-base-content">
                            {{ $bookingRequest->start_time ? \Carbon\Carbon::parse($bookingRequest->start_time)->format('H:i') : 'N/A' }} - 
                            {{ $bookingRequest->end_time ? \Carbon\Carbon::parse($bookingRequest->end_time)->format('H:i') : 'N/A' }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Duration</span>
                        </label>
                        <div class="text-base-content">{{ $bookingRequest->duration }} {{ $bookingRequest->duration_type }}</div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for required data -->
            <input type="hidden" name="tenant_id" value="{{ $bookingRequest->tenant_id }}">
            <input type="hidden" name="property_id" value="{{ $bookingRequest->property_id }}">
            <input type="hidden" name="unit_id" value="{{ $bookingRequest->unit_id }}">
            <input type="hidden" name="date" value="{{ $bookingRequest->date ? $bookingRequest->date->format('Y-m-d') : '' }}">
            <input type="hidden" name="start_time" value="{{ $bookingRequest->start_time ? \Carbon\Carbon::parse($bookingRequest->start_time)->format('H:i') : '' }}">
            <input type="hidden" name="end_time" value="{{ $bookingRequest->end_time ? \Carbon\Carbon::parse($bookingRequest->end_time)->format('H:i') : '' }}">
            <input type="hidden" name="duration_type" value="{{ $bookingRequest->duration_type }}">
            <input type="hidden" name="duration" value="{{ $bookingRequest->duration }}">

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
                    >{{ old('notes', $bookingRequest->notes) }}</textarea>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Booking Request
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
            const option = document.createElement('option');
            option.value = unit.unit_id;
            option.textContent = unit.name;
            // Check if this is the currently selected unit
            if (unit.unit_id == {{ $bookingRequest->unit_id }}) {
                option.selected = true;
            }
            unitSelect.appendChild(option);
        });
    }
}

async function updateAvailableUnits() {
    const propertyId = document.getElementById('property_id').value;
    const date = document.getElementById('date').value;
    const unitSelect = document.getElementById('unit_id');
    const currentUnitId = {{ $bookingRequest->unit_id }};
    
    // Clear current options
    unitSelect.innerHTML = '<option value="">Loading available units...</option>';
    
    if (propertyId && date) {
        try {
            const response = await fetch(`/admin/booking-requests/available-units?property_id=${propertyId}&date=${date}`);
            const units = await response.json();
            
            // Clear and populate options
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            
            units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit.unit_id;
                option.textContent = unit.name;
                // Check if this is the currently selected unit
                if (unit.unit_id == currentUnitId) {
                    option.selected = true;
                }
                unitSelect.appendChild(option);
            });
            
            if (units.length === 0) {
                unitSelect.innerHTML = '<option value="">No available units for this date</option>';
            }
        } catch (error) {
            console.error('Error loading available units:', error);
            unitSelect.innerHTML = '<option value="">Error loading units</option>';
        }
    } else {
        unitSelect.innerHTML = '<option value="">Select Property and Date First</option>';
    }
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load units for the current property
    updateUnits();
    
    // Add event listeners for dynamic unit loading
    document.getElementById('property_id').addEventListener('change', updateAvailableUnits);
    document.getElementById('date').addEventListener('change', updateAvailableUnits);
    
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