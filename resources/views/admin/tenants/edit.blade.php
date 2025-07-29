@extends('layouts.sidebar')

@section('title', 'Edit Tenant Profile')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Edit Tenant Profile</h1>
        
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

        <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Tenant Name</span>
                    <input type="text" name="user_name" placeholder="Full Name" class="input input-bordered w-full @error('user_name') input-error @enderror" value="{{ old('user_name', $tenant->user->name) }}" required />
                    @error('user_name')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">IC Number</span>
                    <input type="text" name="IC_number" placeholder="IC Number" class="input input-bordered w-full @error('IC_number') input-error @enderror" value="{{ old('IC_number', $tenant->IC_number) }}" />
                    @error('IC_number')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Phone Number</span>
                    <input type="tel" name="phone_number" placeholder="Phone Number" class="input input-bordered w-full @error('phone_number') input-error @enderror" value="{{ old('phone_number', $tenant->user->phone_number) }}" required />
                    @error('phone_number')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Emergency Contact Number</span>
                    <input type="tel" name="emergency_contact" placeholder="Emergency Contact Number" class="input input-bordered w-full @error('emergency_contact') input-error @enderror" value="{{ old('emergency_contact', $tenant->emergency_contact) }}" />
                    @error('emergency_contact')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Address</span>
                    <textarea name="address" placeholder="Full Address" class="textarea textarea-bordered w-full @error('address') textarea-error @enderror" rows="3">{{ old('address', $tenant->address) }}</textarea>
                    @error('address')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Additional Information</span>
                    <textarea name="additional_info" placeholder="Additional Information" class="textarea textarea-bordered w-full @error('additional_info') textarea-error @enderror" rows="3">{{ old('additional_info', $tenant->additional_info) }}</textarea>
                    @error('additional_info')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary w-full mt-2">Save Changes</button>
        </form>
        
        <div class="mt-6">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Manage Users
            </a>
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