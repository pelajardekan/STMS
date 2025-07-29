@extends('layouts.sidebar')

@section('title', 'Edit Rental Request')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Edit Rental Request</h1>
        
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

        <form method="POST" action="{{ route('admin.rental-requests.update', $rentalRequest) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Read-only Information Display -->
            <div class="bg-base-200 p-6 rounded-lg space-y-4">
                <h3 class="text-lg font-semibold text-base-content">Rental Request Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Tenant</span>
                        </label>
                        <div class="text-base-content">{{ $rentalRequest->tenant->user->name ?? 'N/A' }} ({{ $rentalRequest->tenant->user->email ?? 'N/A' }})</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Property</span>
                        </label>
                        <div class="text-base-content">{{ $rentalRequest->property->name }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Unit</span>
                        </label>
                        <div class="text-base-content">{{ $rentalRequest->unit->name ?? 'N/A' }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Duration</span>
                        </label>
                        <div class="text-base-content">{{ $rentalRequest->duration }} {{ $rentalRequest->duration_type }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Start Date</span>
                        </label>
                        <div class="text-base-content">{{ $rentalRequest->start_date->format('M d, Y') }}</div>
                    </div>
                    
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">End Date</span>
                        </label>
                        <div class="text-base-content">{{ $rentalRequest->end_date->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for required data -->
            <input type="hidden" name="tenant_id" value="{{ $rentalRequest->tenant_id }}">
            <input type="hidden" name="property_id" value="{{ $rentalRequest->property_id }}">
            <input type="hidden" name="unit_id" value="{{ $rentalRequest->unit_id }}">
            <input type="hidden" name="start_date" value="{{ $rentalRequest->start_date->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $rentalRequest->end_date->format('Y-m-d') }}">
            <input type="hidden" name="duration_type" value="{{ $rentalRequest->duration_type }}">
            <input type="hidden" name="duration" value="{{ $rentalRequest->duration }}">

            <!-- Status -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Status</span>
                </label>
                <div class="relative">
                    <select name="status" id="status" class="select select-bordered w-full pl-10 @error('status') select-error @enderror" required>
                        <option value="">Select Status</option>
                        <option value="pending" {{ old('status', $rentalRequest->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status', $rentalRequest->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $rentalRequest->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                @error('status')
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
                    >{{ old('notes', $rentalRequest->notes) }}</textarea>
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

            <!-- Current Files -->
            @if($rentalRequest->agreement_file_path)
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Current Agreement File</span>
                    </label>
                    <div class="flex items-center gap-2 p-3 bg-base-200 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm">{{ basename($rentalRequest->agreement_file_path) }}</span>
                    </div>
                </div>
            @endif

            @if($rentalRequest->signed_agreement_file_path)
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Current Signed Agreement File</span>
                    </label>
                    <div class="flex items-center gap-2 p-3 bg-base-200 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm">{{ basename($rentalRequest->signed_agreement_file_path) }}</span>
                    </div>
                </div>
            @endif

            <!-- File Uploads -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Agreement File Upload -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">New Agreement File (Optional)</span>
                    </label>
                    <div class="relative">
                        <input
                            type="file"
                            name="agreement_file"
                            id="agreement_file"
                            class="file-input file-input-bordered w-full @error('agreement_file') file-input-error @enderror"
                            accept=".pdf,.doc,.docx"
                        />
                    </div>
                    <div class="label">
                        <span class="label-text-alt">Accepted formats: PDF, DOC, DOCX (Max: 2MB)</span>
                    </div>
                    @error('agreement_file')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Signed Agreement File Upload -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Signed Agreement File (Optional)</span>
                    </label>
                    <div class="relative">
                        <input
                            type="file"
                            name="signed_agreement_file"
                            id="signed_agreement_file"
                            class="file-input file-input-bordered w-full @error('signed_agreement_file') file-input-error @enderror"
                            accept=".pdf,.doc,.docx"
                        />
                    </div>
                    <div class="label">
                        <span class="label-text-alt">Accepted formats: PDF, DOC, DOCX (Max: 2MB)</span>
                    </div>
                    @error('signed_agreement_file')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-control mt-8">
                <button type="submit" class="btn btn-primary w-full">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Rental Request
                </button>
            </div>
        </form>
        
        <div class="mt-6">
            <a href="{{ route('admin.rental-requests.index') }}" class="btn btn-outline w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Rental Requests
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
    const currentUnitId = '{{ $rentalRequest->unit_id }}';
    
    // Clear current options
    unitSelect.innerHTML = '<option value="">Select Unit</option>';
    
    if (propertyId && unitsData[propertyId]) {
        unitsData[propertyId].forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.unit_id;
            option.textContent = unit.name;
            if (unit.unit_id == currentUnitId) {
                option.selected = true;
            }
            unitSelect.appendChild(option);
        });
    }
}

function updateEndDate() {
    const startDate = document.getElementById('start_date').value;
    const durationType = document.getElementById('duration_type').value;
    const duration = document.getElementById('duration').value;
    const endDateInput = document.getElementById('end_date');
    
    if (startDate && durationType && duration) {
        const start = new Date(startDate);
        let end = new Date(start);
        
        if (durationType === 'monthly') {
            end.setMonth(end.getMonth() + parseInt(duration));
        } else if (durationType === 'yearly') {
            end.setFullYear(end.getFullYear() + parseInt(duration));
        }
        
        // Subtract one day to get the end date
        end.setDate(end.getDate() - 1);
        
        endDateInput.value = end.toISOString().split('T')[0];
    }
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load units for the current property
    updateUnits();
    
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