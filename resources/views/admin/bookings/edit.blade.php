@extends('layouts.sidebar')

@section('title', 'Edit Booking')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Edit Booking</h1>
                <p class="text-base-content/70 mt-1">Update booking details and information</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Bookings
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="font-bold">Please fix the following errors:</h4>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Booking Information Summary -->
        <div class="card bg-base-200 p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4">Booking Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="stat bg-base-100 rounded-lg p-4">
                    <div class="stat-title">Booking ID</div>
                    <div class="stat-value text-primary">B-{{ str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="stat bg-base-100 rounded-lg p-4">
                    <div class="stat-title">Tenant</div>
                    <div class="stat-value text-secondary">{{ $booking->bookingRequest->tenant->name }}</div>
                    <div class="stat-desc">{{ $booking->bookingRequest->tenant->email }}</div>
                </div>
                <div class="stat bg-base-100 rounded-lg p-4">
                    <div class="stat-title">Property/Unit</div>
                    <div class="stat-value text-accent">{{ $booking->bookingRequest->property->name }}</div>
                    <div class="stat-desc">{{ $booking->bookingRequest->unit->name }}</div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Booking Details -->
            <div class="card bg-base-200 p-6">
                <h3 class="text-lg font-semibold mb-4">Booking Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Booking Date</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="date" name="date" value="{{ old('date', $booking->date->format('Y-m-d')) }}" 
                               class="input input-bordered w-full" required>
                    </div>

                    <!-- Duration Type -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Duration Type</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <select name="duration_type" id="duration_type" class="select select-bordered w-full" required>
                            <option value="">Select duration type...</option>
                            <option value="hourly" {{ old('duration_type', $booking->duration_type) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="daily" {{ old('duration_type', $booking->duration_type) == 'daily' ? 'selected' : '' }}>Daily</option>
                        </select>
                    </div>

                    <!-- Duration -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Duration</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration', $booking->duration) }}" 
                               class="input input-bordered w-full" required min="1" 
                               placeholder="Enter duration">
                        <label class="label">
                            <span class="label-text-alt" id="duration_label">
                                {{ $booking->duration_type === 'hourly' ? 'hours' : 'days' }}
                            </span>
                        </label>
                    </div>

                    <!-- Status -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Status</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <select name="status" class="select select-bordered w-full" required>
                            <option value="">Select status...</option>
                            <option value="active" {{ old('status', $booking->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Start Time -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Start Time</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($booking->start_time)->format('H:i')) }}" 
                               class="input input-bordered w-full" required>
                    </div>

                    <!-- End Time -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">End Time</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($booking->end_time)->format('H:i')) }}" 
                               class="input input-bordered w-full" required>
                    </div>
                </div>
            </div>

            <!-- Current Pricing Information -->
            <div class="card bg-base-200 p-6">
                <h3 class="text-lg font-semibold mb-4">Current Pricing Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="stat bg-base-100 rounded-lg p-4">
                        <div class="stat-title">Duration</div>
                        <div class="stat-value text-primary">{{ $booking->duration }} {{ $booking->duration_type === 'hourly' ? 'hours' : 'days' }}</div>
                        <div class="stat-desc">{{ ucfirst($booking->duration_type) }} booking</div>
                    </div>
                    <div class="stat bg-base-100 rounded-lg p-4">
                        <div class="stat-title">Time Range</div>
                        <div class="stat-value text-secondary">
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                        </div>
                        <div class="stat-desc">{{ $booking->date->format('M d, Y') }}</div>
                    </div>
                </div>
                
                <!-- Pricing calculation -->
                <div class="mt-4 p-4 bg-base-100 rounded-lg">
                    <h4 class="font-semibold mb-2">Pricing Breakdown</h4>
                    <div class="text-sm text-base-content/70" id="pricing_breakdown">
                        <p><strong>Base Rate:</strong> <span id="base_rate">Loading...</span></p>
                        <p><strong>Total Amount:</strong> <span id="total_amount">Loading...</span></p>
                        <p><strong>Discounts Applied:</strong> <span id="discounts">Loading...</span></p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Booking
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const durationTypeSelect = document.getElementById('duration_type');
    const durationInput = document.getElementById('duration');
    const durationLabel = document.getElementById('duration_label');

    // Update duration label based on duration type
    durationTypeSelect.addEventListener('change', function() {
        const durationType = this.value;
        if (durationType === 'hourly') {
            durationLabel.textContent = 'hours';
            durationInput.placeholder = 'Enter hours (e.g., 2)';
        } else if (durationType === 'daily') {
            durationLabel.textContent = 'days';
            durationInput.placeholder = 'Enter days (e.g., 1)';
        } else {
            durationLabel.textContent = 'hours/days';
            durationInput.placeholder = 'Enter duration';
        }
    });

    // Initialize duration label on page load
    if (durationTypeSelect.value) {
        durationTypeSelect.dispatchEvent(new Event('change'));
    }

    // Load pricing information on page load
    loadPricingInformation();
});

// Function to load pricing information
function loadPricingInformation() {
    // Get booking data from the page
    const duration = parseInt(document.getElementById('duration').value) || 0;
    const durationType = document.getElementById('duration_type').value;
    const hours = durationType === 'hourly' ? duration : (duration * 24);
    
    // For now, we'll use a placeholder pricing ID
    // In a real implementation, you'd get this from the booking request data
    const pricingId = '1'; // This should come from the booking request's property unit parameters
    
    if (pricingId && hours > 0) {
        // Load pricing details
        fetch(`{{ route('pricings.details', '') }}/${pricingId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('base_rate').textContent = `RM ${parseFloat(data.base_rate).toFixed(2)} per ${data.duration_type}`;
                document.getElementById('discounts').textContent = data.discounts || 'None';
            })
            .catch(error => {
                console.error('Error loading pricing details:', error);
                document.getElementById('base_rate').textContent = 'Error loading';
                document.getElementById('discounts').textContent = 'Error loading';
            });
        
        // Calculate pricing
        fetch('{{ route("pricings.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                pricing_id: pricingId,
                hours: hours,
                customer_type: 'regular',
                is_peak_hour: false
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.final_price !== undefined) {
                document.getElementById('total_amount').textContent = `RM ${parseFloat(data.final_price).toFixed(2)}`;
            }
        })
        .catch(error => {
            console.error('Error calculating pricing:', error);
            document.getElementById('total_amount').textContent = 'Error calculating';
        });
    } else {
        document.getElementById('base_rate').textContent = 'No pricing configured';
        document.getElementById('total_amount').textContent = 'No pricing configured';
        document.getElementById('discounts').textContent = 'No pricing configured';
    }
}
</script>
@endsection 