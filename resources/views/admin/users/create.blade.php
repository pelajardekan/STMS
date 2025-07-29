@extends('layouts.sidebar')

@section('title', 'Add User')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Add User</h1>
        
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

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf
            
            <!-- Personal Information Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Full Name</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="name"
                            id="name"
                            placeholder="Enter your full name"
                            class="input input-bordered w-full pl-10 @error('name') input-error @enderror"
                            value="{{ old('name') }}"
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
                </div>
                
                <!-- Email -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            name="email"
                            id="email"
                            placeholder="Enter your email"
                            class="input input-bordered w-full pl-10 @error('email') input-error @enderror"
                            value="{{ old('email') }}"
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
                </div>
            </div>

            <!-- Contact Information Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone Number -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone Number</span>
                    </label>
                    <div class="relative">
                        <input
                            type="tel"
                            name="phone_number"
                            id="phone_number"
                            placeholder="Enter your phone number"
                            class="input input-bordered w-full pl-10 @error('phone_number') input-error @enderror"
                            value="{{ old('phone_number') }}"
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
                </div>
                
                <!-- Emergency Contact -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Emergency Contact</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="tel" 
                            name="emergency_contact"
                            id="emergency_contact"
                            placeholder="Enter emergency contact number" 
                            class="input input-bordered w-full pl-10 @error('emergency_contact') input-error @enderror"
                            value="{{ old('emergency_contact') }}"
                            oninput="validateEmergencyContact(this)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none z-10">
                            <svg id="emergency-valid-icon" class="h-5 w-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg id="emergency-invalid-icon" class="h-5 w-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    @error('emergency_contact')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                    <span id="emergency-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                </div>
            </div>

            <!-- Identity and Address Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- IC Number -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">IC Number</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="IC_number"
                            id="IC_number"
                            placeholder="Enter your IC number (e.g., 900101-01-1234)"
                            class="input input-bordered w-full pl-10 @error('IC_number') input-error @enderror"
                            value="{{ old('IC_number') }}"
                            maxlength="14"
                            oninput="validateIC(this)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none z-10">
                            <svg id="ic-valid-icon" class="h-5 w-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg id="ic-invalid-icon" class="h-5 w-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    @error('IC_number')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                    <span id="ic-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                </div>
                
                <!-- Address -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Address</span>
                    </label>
                    <div class="relative">
                        <textarea 
                            name="address"
                            placeholder="Enter your address" 
                            class="textarea textarea-bordered w-full pl-10 @error('address') textarea-error @enderror"
                            rows="3"
                        >{{ old('address') }}</textarea>
                        <div class="absolute top-3 left-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    @error('address')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Role Selection -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Role</span>
                </label>
                <select name="role" id="role" class="select select-bordered w-full @error('role') select-error @enderror" required onchange="toggleTenantFields()">
                    <option value="">Select Role</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="tenant" {{ old('role') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                </select>
                @error('role')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Additional Information (Tenant Only) -->
            <div id="tenant-fields" class="hidden">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Additional Information</span>
                    </label>
                    <textarea 
                        name="additional_info"
                        placeholder="Additional Information" 
                        class="textarea textarea-bordered w-full @error('additional_info') textarea-error @enderror"
                        rows="3"
                    >{{ old('additional_info') }}</textarea>
                    @error('additional_info')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Password Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Password -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your password"
                            class="input input-bordered w-full pl-10 pr-10 @error('password') input-error @enderror"
                            required
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
                </div>
                
                <!-- Confirm Password -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Confirm Password</span>
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Confirm your password"
                            class="input input-bordered w-full pl-10 pr-10"
                            required
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
                </div>
            </div>
            
            <div class="form-control mt-8">
                <button type="submit" class="btn btn-primary w-full">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Add User
                </button>
            </div>
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
function toggleTenantFields() {
    const roleSelect = document.getElementById('role');
    const tenantFields = document.getElementById('tenant-fields');
    const emergencyContact = document.getElementById('emergency_contact');
    const icNumber = document.getElementById('IC_number');
    const address = document.querySelector('textarea[name="address"]');
    
    if (roleSelect.value === 'tenant') {
        tenantFields.classList.remove('hidden');
        // Only IC number is required for tenants
        icNumber.required = true;
        emergencyContact.required = false;
        address.required = false;
    } else {
        tenantFields.classList.add('hidden');
        // Remove required from all tenant fields
        emergencyContact.required = false;
        icNumber.required = false;
        address.required = false;
    }
}

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
    const emergencyContactInput = document.getElementById('emergency_contact');

    const phoneRegex = /^(\+?6?01)[0-9]{7,9}$/; // Malaysian phone number format
    const isValid = phoneRegex.test(phoneInput.value);

    if (isValid) {
        // Check if phone number matches emergency contact
        if (emergencyContactInput.value && phoneInput.value === emergencyContactInput.value) {
            phoneInput.classList.add('input-error');
            phoneValidIcon.classList.add('hidden');
            phoneInvalidIcon.classList.remove('hidden');
            phoneValidationMessage.classList.remove('hidden');
            phoneValidationMessage.textContent = 'Phone number cannot be the same as emergency contact.';
            phoneValidationMessage.classList.remove('text-green-400');
            phoneValidationMessage.classList.add('text-red-400');
        } else {
            phoneInput.classList.remove('input-error');
            phoneValidIcon.classList.remove('hidden');
            phoneInvalidIcon.classList.add('hidden');
            phoneValidationMessage.classList.add('hidden');
        }
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

function validateIC(input) {
    const icInput = input;
    const icValidIcon = document.getElementById('ic-valid-icon');
    const icInvalidIcon = document.getElementById('ic-invalid-icon');
    const icValidationMessage = document.getElementById('ic-validation-message');

    const icRegex = /^(\d{6,8})[-\/](\d{2})[-\/](\d{4})$/; // Malaysian IC number format (e.g., 900101-01-1234)
    const isValid = icRegex.test(icInput.value);

    if (isValid) {
        icInput.classList.remove('input-error');
        icValidIcon.classList.remove('hidden');
        icInvalidIcon.classList.add('hidden');
        icValidationMessage.classList.add('hidden');
    } else {
        icInput.classList.add('input-error');
        icValidIcon.classList.add('hidden');
        icInvalidIcon.classList.remove('hidden');
        icValidationMessage.classList.remove('hidden');
        icValidationMessage.innerHTML = 'Please enter a valid Malaysian IC number<br>(e.g., 900101-01-1234).';
        icValidationMessage.classList.remove('text-green-400');
        icValidationMessage.classList.add('text-red-400');
    }
}

function validateEmergencyContact(input) {
    const emergencyContactInput = input;
    const emergencyContactValidIcon = document.getElementById('emergency-valid-icon');
    const emergencyContactInvalidIcon = document.getElementById('emergency-invalid-icon');
    const emergencyContactValidationMessage = document.getElementById('emergency-validation-message');
    const phoneInput = document.getElementById('phone_number');

    // If empty, it's valid (nullable field)
    if (emergencyContactInput.value === '') {
        emergencyContactInput.classList.remove('input-error');
        emergencyContactValidIcon.classList.add('hidden');
        emergencyContactInvalidIcon.classList.add('hidden');
        emergencyContactValidationMessage.classList.add('hidden');
        return;
    }

    const emergencyContactRegex = /^(\+?6?01)[0-9]{7,9}$/; // Malaysian phone number format
    const isValid = emergencyContactRegex.test(emergencyContactInput.value);

    if (isValid) {
        // Check if emergency contact matches phone number
        if (phoneInput.value && emergencyContactInput.value === phoneInput.value) {
            emergencyContactInput.classList.add('input-error');
            emergencyContactValidIcon.classList.add('hidden');
            emergencyContactInvalidIcon.classList.remove('hidden');
            emergencyContactValidationMessage.classList.remove('hidden');
            emergencyContactValidationMessage.textContent = 'Emergency contact cannot be the same as phone number.';
            emergencyContactValidationMessage.classList.remove('text-green-400');
            emergencyContactValidationMessage.classList.add('text-red-400');
        } else {
            emergencyContactInput.classList.remove('input-error');
            emergencyContactValidIcon.classList.remove('hidden');
            emergencyContactInvalidIcon.classList.add('hidden');
            emergencyContactValidationMessage.classList.add('hidden');
        }
    } else {
        emergencyContactInput.classList.add('input-error');
        emergencyContactValidIcon.classList.add('hidden');
        emergencyContactInvalidIcon.classList.remove('hidden');
        emergencyContactValidationMessage.classList.remove('hidden');
        emergencyContactValidationMessage.textContent = 'Please enter a valid Malaysian phone number (e.g., 012-3456789, 011-12345678, +6012-3456789).';
        emergencyContactValidationMessage.classList.remove('text-green-400');
        emergencyContactValidationMessage.classList.add('text-red-400');
    }
}

function validatePassword(input) {
    const passwordInput = input;
    const passwordValidIcon = document.getElementById('password-valid-icon');
    const passwordInvalidIcon = document.getElementById('password-invalid-icon');
    const passwordValidationMessage = document.getElementById('password-validation-message');

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

    if (confirmPasswordInput.value === '') {
        confirmPasswordInput.classList.remove('input-error');
        confirmPasswordValidIcon.classList.add('hidden');
        confirmPasswordInvalidIcon.classList.add('hidden');
        confirmPasswordValidationMessage.classList.add('hidden');
    } else if (confirmPasswordInput.value === passwordInput.value) {
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

// Initialize tenant fields visibility on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleTenantFields();
        
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