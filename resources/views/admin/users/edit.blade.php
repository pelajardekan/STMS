@extends('layouts.sidebar')

@section('title', 'Edit User')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Edit User</h1>
        
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

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Name</span>
                    <div class="relative">
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            placeholder="Full Name" 
                            class="input input-bordered w-full pl-10 @error('name') input-error @enderror" 
                            value="{{ old('name', $user->name) }}" 
                            required 
                            oninput="validateName(this)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none z-10">
                            <svg id="name-valid-icon" class="h-5 w-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg id="name-invalid-icon" class="h-5 w-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    @error('name')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                    <span id="name-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Email</span>
                    <div class="relative">
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            placeholder="Email Address" 
                            class="input input-bordered w-full pl-10 @error('email') input-error @enderror" 
                            value="{{ old('email', $user->email) }}" 
                            required 
                            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                            oninput="validateEmail(this)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none z-10">
                            <svg id="email-valid-icon" class="h-5 w-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg id="email-invalid-icon" class="h-5 w-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                    <span id="email-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Phone Number</span>
                    <div class="relative">
                        <input 
                            type="tel" 
                            name="phone_number" 
                            id="phone_number"
                            placeholder="Phone Number" 
                            class="input input-bordered w-full pl-10 @error('phone_number') input-error @enderror" 
                            value="{{ old('phone_number', $user->phone_number) }}" 
                            required 
                            oninput="validatePhone(this)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none z-10">
                            <svg id="phone-valid-icon" class="h-5 w-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg id="phone-invalid-icon" class="h-5 w-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    @error('phone_number')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                    <span id="phone-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Role</span>
                    <select name="role" class="select select-bordered w-full @error('role') select-error @enderror" required>
                        <option value="">Select Role</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="tenant" {{ old('role', $user->role) == 'tenant' ? 'selected' : '' }}>Tenant</option>
                    </select>
                    @error('role')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Password <span class="opacity-60">(leave blank to keep current)</span></span>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            placeholder="New Password" 
                            class="input input-bordered w-full pl-10 pr-10 @error('password') input-error @enderror" 
                            minlength="8"
                            oninput="validatePassword(this)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none z-10">
                            <svg id="password-valid-icon" class="h-5 w-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg id="password-invalid-icon" class="h-5 w-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 pr-12 flex items-center z-10"
                            onclick="togglePassword()"
                        >
                            <svg id="eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-slash-icon" class="h-5 w-5 text-gray-400 hover:text-gray-300 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                    <span id="password-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                </label>
            </div>
            
            <div>
                <label class="form-control w-full">
                    <span class="label-text">Confirm Password</span>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            placeholder="Confirm New Password" 
                            class="input input-bordered w-full pl-10 pr-10" 
                            minlength="8"
                            oninput="validateConfirmPassword(this)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none z-10">
                            <svg id="confirm-password-valid-icon" class="h-5 w-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg id="confirm-password-invalid-icon" class="h-5 w-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 pr-12 flex items-center z-10"
                            onclick="toggleConfirmPassword()"
                        >
                            <svg id="eye-confirm-icon" class="h-5 w-5 text-gray-400 hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-slash-confirm-icon" class="h-5 w-5 text-gray-400 hover:text-gray-300 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    <span id="confirm-password-validation-message" class="label-text-alt text-gray-400 hidden"></span>
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
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeSlashIcon = document.getElementById('eye-slash-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
    }
}

function toggleConfirmPassword() {
    const passwordInput = document.getElementById('password_confirmation');
    const eyeIcon = document.getElementById('eye-confirm-icon');
    const eyeSlashIcon = document.getElementById('eye-slash-confirm-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
    }
}

function validateName(input) {
    const nameInput = input;
    const nameValidIcon = document.getElementById('name-valid-icon');
    const nameInvalidIcon = document.getElementById('name-invalid-icon');
    const nameValidationMessage = document.getElementById('name-validation-message');

    const nameRegex = /^[a-zA-Z\s]+$/; // Only letters and spaces
    const isValid = nameRegex.test(nameInput.value);

    if (isValid) {
        nameInput.classList.remove('input-error');
        nameValidIcon.classList.remove('hidden');
        nameInvalidIcon.classList.add('hidden');
        nameValidationMessage.classList.add('hidden');
    } else {
        nameInput.classList.add('input-error');
        nameValidIcon.classList.add('hidden');
        nameInvalidIcon.classList.remove('hidden');
        nameValidationMessage.classList.remove('hidden');
        nameValidationMessage.textContent = 'Name must only contain letters and spaces.';
        nameValidationMessage.classList.remove('text-green-400');
        nameValidationMessage.classList.add('text-red-400');
    }
}

function validateEmail(input) {
    const emailInput = input;
    const emailValidIcon = document.getElementById('email-valid-icon');
    const emailInvalidIcon = document.getElementById('email-invalid-icon');
    const emailValidationMessage = document.getElementById('email-validation-message');

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = emailRegex.test(emailInput.value);

    if (isValid) {
        emailInput.classList.remove('input-error');
        emailValidIcon.classList.remove('hidden');
        emailInvalidIcon.classList.add('hidden');
        emailValidationMessage.classList.add('hidden');
    } else {
        emailInput.classList.add('input-error');
        emailValidIcon.classList.add('hidden');
        emailInvalidIcon.classList.remove('hidden');
        emailValidationMessage.classList.remove('hidden');
        emailValidationMessage.textContent = 'Please enter a valid email address (example@email.com)';
        emailValidationMessage.classList.remove('text-green-400');
        emailValidationMessage.classList.add('text-red-400');
    }
}

function validatePhone(input) {
    const phoneInput = input;
    const phoneValidIcon = document.getElementById('phone-valid-icon');
    const phoneInvalidIcon = document.getElementById('phone-invalid-icon');
    const phoneValidationMessage = document.getElementById('phone-validation-message');

    const phoneRegex = /^(\+?6?01)[0-9]{7,9}$/; // Malaysian phone number format
    const isValid = phoneRegex.test(phoneInput.value);

    if (isValid) {
        phoneInput.classList.remove('input-error');
        phoneValidIcon.classList.remove('hidden');
        phoneInvalidIcon.classList.add('hidden');
        phoneValidationMessage.classList.add('hidden');
    } else {
        phoneInput.classList.add('input-error');
        phoneValidIcon.classList.add('hidden');
        phoneInvalidIcon.classList.remove('hidden');
        phoneValidationMessage.classList.remove('hidden');
        phoneValidationMessage.textContent = 'Please enter a valid Malaysian phone number (e.g., 012-3456789, 011-12345678, +6012-3456789).';
        phoneValidationMessage.classList.remove('text-green-400');
        phoneValidationMessage.classList.add('text-red-400');
    }
}

function validatePassword(input) {
    const passwordInput = input;
    const passwordValidIcon = document.getElementById('password-valid-icon');
    const passwordInvalidIcon = document.getElementById('password-invalid-icon');
    const passwordValidationMessage = document.getElementById('password-validation-message');

    // If password is empty, it's valid (optional field)
    if (passwordInput.value === '') {
        passwordInput.classList.remove('input-error');
        passwordValidIcon.classList.add('hidden');
        passwordInvalidIcon.classList.add('hidden');
        passwordValidationMessage.classList.add('hidden');
        return;
    }

    const minLength = 8;
    const isValid = passwordInput.value.length >= minLength;

    if (isValid) {
        passwordInput.classList.remove('input-error');
        passwordValidIcon.classList.remove('hidden');
        passwordInvalidIcon.classList.add('hidden');
        passwordValidationMessage.classList.add('hidden');
    } else {
        passwordInput.classList.add('input-error');
        passwordValidIcon.classList.add('hidden');
        passwordInvalidIcon.classList.remove('hidden');
        passwordValidationMessage.classList.remove('hidden');
        passwordValidationMessage.textContent = `Password must be at least ${minLength} characters.`;
        passwordValidationMessage.classList.remove('text-green-400');
        passwordValidationMessage.classList.add('text-red-400');
    }
}

function validateConfirmPassword(input) {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = input;
    const confirmPasswordValidIcon = document.getElementById('confirm-password-valid-icon');
    const confirmPasswordInvalidIcon = document.getElementById('confirm-password-invalid-icon');
    const confirmPasswordValidationMessage = document.getElementById('confirm-password-validation-message');

    // If confirm password is empty, it's valid (optional field)
    if (confirmPasswordInput.value === '') {
        confirmPasswordInput.classList.remove('input-error');
        confirmPasswordValidIcon.classList.add('hidden');
        confirmPasswordInvalidIcon.classList.add('hidden');
        confirmPasswordValidationMessage.classList.add('hidden');
        return;
    }

    // If password is empty but confirm password is not, show error
    if (passwordInput.value === '' && confirmPasswordInput.value !== '') {
        confirmPasswordInput.classList.add('input-error');
        confirmPasswordValidIcon.classList.add('hidden');
        confirmPasswordInvalidIcon.classList.remove('hidden');
        confirmPasswordValidationMessage.classList.remove('hidden');
        confirmPasswordValidationMessage.textContent = 'Please enter a password first.';
        confirmPasswordValidationMessage.classList.remove('text-green-400');
        confirmPasswordValidationMessage.classList.add('text-red-400');
        return;
    }

    if (confirmPasswordInput.value === passwordInput.value) {
        confirmPasswordInput.classList.remove('input-error');
        confirmPasswordValidIcon.classList.remove('hidden');
        confirmPasswordInvalidIcon.classList.add('hidden');
        confirmPasswordValidationMessage.classList.add('hidden');
    } else {
        confirmPasswordInput.classList.add('input-error');
        confirmPasswordValidIcon.classList.add('hidden');
        confirmPasswordInvalidIcon.classList.remove('hidden');
        confirmPasswordValidationMessage.classList.remove('hidden');
        confirmPasswordValidationMessage.textContent = 'Passwords do not match.';
        confirmPasswordValidationMessage.classList.remove('text-green-400');
        confirmPasswordValidationMessage.classList.add('text-red-400');
    }
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    // Initialize validation on page load
    validateName(document.getElementById('name'));
    validateEmail(document.getElementById('email'));
    validatePhone(document.getElementById('phone_number'));
    validatePassword(document.getElementById('password'));
    validateConfirmPassword(document.getElementById('password_confirmation'));
    
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