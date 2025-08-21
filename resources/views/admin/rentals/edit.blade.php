@extends('layouts.sidebar')

@section('title', 'Edit Rental')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Edit Rental</h1>
                <p class="text-base-content/60 mt-1">Modify rental details and status</p>
            </div>
            <a href="{{ route('admin.rentals.index') }}" class="btn btn-outline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Rentals
            </a>
        </div>

        <!-- Rental Information -->
        <div class="bg-base-200 rounded-xl p-6 mb-8">
            <h2 class="text-xl font-semibold text-base-content mb-4">Rental Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="font-medium text-base-content/70">Rental ID:</span>
                    <span class="text-base-content ml-2">#{{ $rental->rental_id }}</span>
                </div>
                <div>
                    <span class="font-medium text-base-content/70">Tenant:</span>
                    <span class="text-base-content ml-2">
                        @if($rental->tenant())
                            {{ $rental->tenant()->name }}
                        @else
                            <span class="text-error">No tenant assigned</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="font-medium text-base-content/70">Property:</span>
                    <span class="text-base-content ml-2">
                        @if($rental->rentalRequest && $rental->rentalRequest->property)
                            {{ $rental->rentalRequest->property->name }}
                        @else
                            <span class="text-error">No property</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="font-medium text-base-content/70">Unit:</span>
                    <span class="text-base-content ml-2">
                        @if($rental->rentalRequest && $rental->rentalRequest->unit)
                            {{ $rental->rentalRequest->unit->unit_number }}
                        @else
                            <span class="text-error">No unit</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('admin.rentals.update', $rental->rental_id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Start Date</span>
                    </label>
                    <input type="date" name="start_date" value="{{ $rental->start_date ? $rental->start_date->format('Y-m-d') : '' }}" 
                           class="input input-bordered focus:input-primary @error('start_date') input-error @enderror" required>
                    @error('start_date')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- End Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">End Date</span>
                    </label>
                    <input type="date" name="end_date" value="{{ $rental->end_date ? $rental->end_date->format('Y-m-d') : '' }}" 
                           class="input input-bordered focus:input-primary @error('end_date') input-error @enderror" required>
                    @error('end_date')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Duration -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Duration (days)</span>
                    </label>
                    <input type="number" name="duration" value="{{ $rental->duration }}" 
                           class="input input-bordered focus:input-primary @error('duration') input-error @enderror" min="1">
                    @error('duration')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Status</span>
                    </label>
                    <select name="status" class="select select-bordered focus:select-primary @error('status') select-error @enderror" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ $rental->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ $rental->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ $rental->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $rental->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Rental
                </button>
                <a href="{{ route('admin.rentals.index') }}" class="btn btn-ghost">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-calculate duration when dates change
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const durationInput = document.querySelector('input[name="duration"]');

    function calculateDuration() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            durationInput.value = diffDays;
        }
    }

    startDateInput.addEventListener('change', calculateDuration);
    endDateInput.addEventListener('change', calculateDuration);
});
</script>
@endsection
