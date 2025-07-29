@extends('layouts.sidebar')

@section('title', 'Create Rental Request')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('tenant.rental-requests.index') }}" class="btn btn-ghost btn-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Rental Requests
                    </a>
                </div>
                <h1 class="text-3xl font-bold text-base-content">Create Rental Request</h1>
                <p class="text-base-content/60 mt-1">Submit a new rental request for an available unit</p>
            </div>
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

        @if($errors->any())
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Rental Request Form -->
        <form action="{{ route('tenant.rental-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Property Selection -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Property *</span>
                    </label>
                    <select name="property_id" class="select select-bordered w-full" required>
                        <option value="">Select a property</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->property_id }}" {{ old('property_id') == $property->property_id ? 'selected' : '' }}>
                                {{ $property->name }} - {{ $property->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Unit Selection -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Unit *</span>
                    </label>
                    <select name="unit_id" class="select select-bordered w-full" required>
                        <option value="">Select a unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->unit_id }}" {{ old('unit_id') == $unit->unit_id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->property->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Start Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Start Date *</span>
                    </label>
                    <input type="date" name="start_date" class="input input-bordered w-full" value="{{ old('start_date') }}" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    @error('start_date')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- End Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">End Date *</span>
                    </label>
                    <input type="date" name="end_date" class="input input-bordered w-full" value="{{ old('end_date') }}" required min="{{ date('Y-m-d', strtotime('+2 days')) }}">
                    @error('end_date')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Duration Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Duration Type *</span>
                    </label>
                    <select name="duration_type" class="select select-bordered w-full" required>
                        <option value="">Select duration type</option>
                        <option value="days" {{ old('duration_type') == 'days' ? 'selected' : '' }}>Days</option>
                        <option value="weeks" {{ old('duration_type') == 'weeks' ? 'selected' : '' }}>Weeks</option>
                        <option value="months" {{ old('duration_type') == 'months' ? 'selected' : '' }}>Months</option>
                        <option value="years" {{ old('duration_type') == 'years' ? 'selected' : '' }}>Years</option>
                    </select>
                    @error('duration_type')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Duration -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Duration *</span>
                    </label>
                    <input type="number" name="duration" class="input input-bordered w-full" value="{{ old('duration') }}" required min="1" placeholder="Enter duration">
                    @error('duration')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
            </div>

            <!-- Agreement File -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Agreement File (Optional)</span>
                </label>
                <input type="file" name="agreement_file" class="file-input file-input-bordered w-full" accept=".pdf,.doc,.docx">
                <label class="label">
                    <span class="label-text-alt">Accepted formats: PDF, DOC, DOCX (Max: 2MB)</span>
                </label>
                @error('agreement_file')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Notes -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Additional Notes (Optional)</span>
                </label>
                <textarea name="notes" class="textarea textarea-bordered h-24" placeholder="Enter any additional notes or special requirements...">{{ old('notes') }}</textarea>
                @error('notes')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('tenant.rental-requests.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Submit Rental Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-calculate duration when dates change
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const durationInput = document.querySelector('input[name="duration"]');
    const durationTypeSelect = document.querySelector('select[name="duration_type"]');

    function calculateDuration() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            durationInput.value = diffDays;
            durationTypeSelect.value = 'days';
        }
    }

    startDateInput.addEventListener('change', calculateDuration);
    endDateInput.addEventListener('change', calculateDuration);
});
</script>
@endsection 