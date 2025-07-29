@extends('layouts.sidebar')

@section('title', 'View Booking')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Booking Details</h1>
                <p class="text-base-content/70 mt-1">View detailed information about this booking</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Booking
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-ghost">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Bookings
                </a>
            </div>
        </div>

        <!-- Booking Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Booking Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Summary -->
                <div class="card bg-base-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Booking Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="stat bg-base-100 rounded-lg p-4">
                            <div class="stat-title">Booking ID</div>
                            <div class="stat-value text-primary">B-{{ str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="stat bg-base-100 rounded-lg p-4">
                            <div class="stat-title">Status</div>
                            <div class="stat-value">
                                @if($booking->status === 'active')
                                    <span class="badge badge-success gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Active
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge badge-neutral gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Completed
                                    </span>
                                @else
                                    <span class="badge badge-error gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Cancelled
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="stat bg-base-100 rounded-lg p-4">
                            <div class="stat-title">Date</div>
                            <div class="stat-value text-secondary">{{ $booking->date->format('M d, Y') }}</div>
                        </div>
                        <div class="stat bg-base-100 rounded-lg p-4">
                            <div class="stat-title">Duration</div>
                            <div class="stat-value text-accent" id="booking-duration" data-duration="{{ $booking->duration }}">{{ $booking->duration }} {{ $booking->duration_type === 'hourly' ? 'hours' : 'days' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Time Information -->
                <div class="card bg-base-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Time Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="stat bg-base-100 rounded-lg p-4">
                            <div class="stat-title">Start Time</div>
                            <div class="stat-value text-primary">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</div>
                        </div>
                        <div class="stat bg-base-100 rounded-lg p-4">
                            <div class="stat-title">End Time</div>
                            <div class="stat-value text-secondary">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Tenant Information -->
                <div class="card bg-base-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Tenant Information</h3>
                    <div class="flex items-center gap-4">
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-16">
                                <span class="text-xl">{{ substr($booking->bookingRequest->tenant->name, 0, 2) }}</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold">{{ $booking->bookingRequest->tenant->name }}</h4>
                            <p class="text-base-content/70">{{ $booking->bookingRequest->tenant->email }}</p>
                            <p class="text-sm text-base-content/60">{{ $booking->bookingRequest->tenant->phone_number ?? 'No phone number' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Property Information -->
                <div class="card bg-base-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Property Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-base-content">{{ $booking->bookingRequest->property->name }}</h4>
                            <p class="text-base-content/70">{{ $booking->bookingRequest->property->address ?? 'No address' }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-base-content">{{ $booking->bookingRequest->unit->name }}</h4>
                            <p class="text-base-content/70">{{ $booking->bookingRequest->unit->description ?? 'No description' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pricing Information -->
                <div class="card bg-base-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Pricing Information</h3>
                    <div class="space-y-4">
                        <div class="stat bg-base-100 rounded-lg p-4">
                            <div class="stat-title">Duration Type</div>
                            <div class="stat-value text-primary" id="booking-duration-type" data-duration-type="{{ $booking->duration_type }}">{{ ucfirst($booking->duration_type) }}</div>
                        </div>
                        <div class="stat bg-base-100 rounded-lg p-4">
                            <div class="stat-title">Total Duration</div>
                            <div class="stat-value text-secondary">{{ $booking->duration }} {{ $booking->duration_type === 'hourly' ? 'hours' : 'days' }}</div>
                        </div>
                        <div class="p-4 bg-base-100 rounded-lg">
                            <h4 class="font-semibold mb-2">Pricing Breakdown</h4>
                            <div class="text-sm text-base-content/70">
                                <p><strong>Base Rate:</strong> <span id="base_rate">Loading...</span></p>
                                <p><strong>Total Amount:</strong> <span id="total_amount">Loading...</span></p>
                                <p><strong>Discounts Applied:</strong> <span id="discounts">Loading...</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Request Information -->
                <div class="card bg-base-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Booking Request</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-base-content/60">Request ID:</span>
                            <p class="font-semibold">BR-{{ str_pad($booking->bookingRequest->booking_request_id, 3, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-base-content/60">Request Date:</span>
                            <p class="font-semibold">{{ $booking->bookingRequest->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-base-content/60">Request Status:</span>
                            <p class="font-semibold">
                                <span class="badge badge-success">Approved</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card bg-base-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-primary w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Booking
                        </a>
                        <button onclick="openDeleteModal('{{ $booking->booking_id }}', '{{ $booking->bookingRequest->tenant->name }}')" class="btn btn-error w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Delete Booking</h3>
        <p class="py-4">Are you sure you want to delete the booking for <span id="deleteTenantName" class="font-semibold"></span>? This action cannot be undone.</p>
        <div class="modal-action">
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error">Delete</button>
            </form>
            <button onclick="closeDeleteModal()" class="btn">Cancel</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load pricing information on page load
    loadPricingInformation();
});

// Function to load pricing information
function loadPricingInformation() {
    // Get booking data from data attributes
    const durationElement = document.getElementById('booking-duration');
    const durationTypeElement = document.getElementById('booking-duration-type');
    
    if (!durationElement || !durationTypeElement) {
        console.error('Booking data elements not found');
        return;
    }
    
    const duration = parseInt(durationElement.dataset.duration) || 0;
    const durationType = durationTypeElement.dataset.durationType || 'hourly';
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

function openDeleteModal(id, tenantName) {
    document.getElementById('deleteTenantName').textContent = tenantName;
    document.getElementById('deleteForm').action = `/admin/bookings/${id}`;
    document.getElementById('deleteModal').classList.add('modal-open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('modal-open');
}
</script>
@endsection 