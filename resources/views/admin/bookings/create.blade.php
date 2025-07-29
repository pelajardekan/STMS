@extends('layouts.sidebar')

@section('title', 'Create Booking')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Create New Booking</h1>
                <p class="text-base-content/70 mt-1">Create a new booking from approved booking requests</p>
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

        <form action="{{ route('admin.bookings.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Booking Request Selection -->
            <div class="card bg-base-200 p-6">
                <h3 class="text-lg font-semibold mb-4">Select Booking Request</h3>
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">Booking Request</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <select name="booking_request_id" id="booking_request_id" class="select select-bordered w-full" required>
                        <option value="">Select a booking request...</option>
                        @foreach($bookingRequests as $request)
                            <option value="{{ $request->booking_request_id }}" 
                                    data-tenant="{{ $request->tenant->name }}"
                                    data-property="{{ $request->property->name }}"
                                    data-unit="{{ $request->unit->name }}"
                                    data-pricing="{{ $request->property->propertyUnitParameters->first()?->pricing_id ?? '' }}">
                                BR-{{ str_pad($request->booking_request_id, 3, '0', STR_PAD_LEFT) }} - 
                                {{ $request->tenant->name }} - 
                                {{ $request->property->name }} ({{ $request->unit->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

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
                        <input type="date" name="date" value="{{ old('date') }}" 
                               class="input input-bordered w-full" required 
                               min="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Duration Type -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Duration Type</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <select name="duration_type" id="duration_type" class="select select-bordered w-full" required>
                            <option value="">Select duration type...</option>
                            <option value="hourly" {{ old('duration_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="daily" {{ old('duration_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                        </select>
                    </div>

                    <!-- Duration -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Duration</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration') }}" 
                               class="input input-bordered w-full" required min="1" 
                               placeholder="Enter duration">
                        <label class="label">
                            <span class="label-text-alt" id="duration_label">hours/days</span>
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
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Start Time -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Start Time</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" 
                               class="input input-bordered w-full" required>
                    </div>

                    <!-- End Time -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">End Time</span>
                            <span class="label-text-alt text-error">*</span>
                        </label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" 
                               class="input input-bordered w-full" required>
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="card bg-base-200 p-6" id="pricing_info" style="display: none;">
                <h3 class="text-lg font-semibold mb-4">Pricing Information</h3>
                <div id="pricing_details" class="space-y-4">
                    <!-- Pricing details will be loaded here via JavaScript -->
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
                    Create Booking
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
    const bookingRequestSelect = document.getElementById('booking_request_id');
    const pricingInfo = document.getElementById('pricing_info');
    const pricingDetails = document.getElementById('pricing_details');

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

    // Load pricing information when booking request is selected
    bookingRequestSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const pricingId = selectedOption.dataset.pricing;
        
        if (pricingId) {
            // Show pricing info section
            pricingInfo.style.display = 'block';
            
                         // Load pricing details via AJAX
             fetch(`{{ route('pricings.details', '') }}/${pricingId}`)
                .then(response => response.json())
                .then(data => {
                    pricingDetails.innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="stat bg-base-100 rounded-lg p-4">
                                <div class="stat-title">Base Rate</div>
                                <div class="stat-value text-primary">RM ${data.base_rate}</div>
                                <div class="stat-desc">per ${data.duration_type}</div>
                            </div>
                            <div class="stat bg-base-100 rounded-lg p-4">
                                <div class="stat-title">Estimated Total</div>
                                <div class="stat-value text-secondary" id="estimated_total">RM 0.00</div>
                                <div class="stat-desc">Based on duration</div>
                            </div>
                        </div>
                        <div class="text-sm text-base-content/70">
                            <p><strong>Discounts:</strong> ${data.discounts || 'None'}</p>
                            <p><strong>Notes:</strong> ${data.notes || 'No additional notes'}</p>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error loading pricing details:', error);
                    pricingDetails.innerHTML = '<p class="text-error">Error loading pricing information</p>';
                });
        } else {
            pricingInfo.style.display = 'none';
        }
    });

    // Calculate estimated total when duration changes
    durationInput.addEventListener('input', function() {
        const duration = parseInt(this.value) || 0;
        const durationType = durationTypeSelect.value;
        const selectedOption = bookingRequestSelect.options[bookingRequestSelect.selectedIndex];
        const pricingId = selectedOption.dataset.pricing;
        
        if (duration > 0 && pricingId && durationType) {
            // Calculate estimated total via AJAX
            const hours = durationType === 'hourly' ? duration : (duration * 24);
            
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
                const estimatedTotal = document.getElementById('estimated_total');
                if (estimatedTotal && data.final_price !== undefined) {
                    estimatedTotal.textContent = `RM ${parseFloat(data.final_price).toFixed(2)}`;
                }
            })
            .catch(error => {
                console.error('Error calculating pricing:', error);
                const estimatedTotal = document.getElementById('estimated_total');
                if (estimatedTotal) {
                    estimatedTotal.textContent = 'Error calculating';
                }
            });
        }
    });
});
</script>
@endsection 