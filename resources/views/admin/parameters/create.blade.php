@extends('layouts.sidebar')

@section('title', 'Create Parameter')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Create Parameter</h1>
        
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

        <form method="POST" action="{{ route('parameters.store') }}" class="space-y-6" onsubmit="return validateForm()">
            @csrf
            
            <!-- Parameter Type Selection -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Parameter Type</span>
                </label>
                <select name="parameter_type" id="parameter_type" class="select select-bordered w-full @error('parameter_type') select-error @enderror" required onchange="toggleParameterSections()">
                    <option value="">Select Parameter Type</option>
                    <option value="pricing" {{ old('parameter_type') == 'pricing' ? 'selected' : '' }}>Pricing</option>
                    <option value="service" {{ old('parameter_type') == 'service' ? 'selected' : '' }}>Service</option>
                    <option value="amenity" {{ old('parameter_type') == 'amenity' ? 'selected' : '' }}>Amenity</option>
                </select>
                @error('parameter_type')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Pricing Section -->
            <div id="pricing-section" class="hidden">
                <div class="card bg-base-200 p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        Pricing Configuration
                    </h2>
                    
                    <!-- Pricing Name -->
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Pricing Name</span>
                        </label>
                        <input
                            type="text"
                            name="pricing_name"
                            id="pricing_name"
                            placeholder="Enter pricing name"
                            class="input input-bordered w-full @error('pricing_name') input-error @enderror"
                            value="{{ old('pricing_name') }}"
                            oninput="validatePricingName(this)"
                        />
                        @error('pricing_name')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                        <span id="pricing-name-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                    </div>

                    <!-- Pricing Type -->
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Pricing Type</span>
                        </label>
                        <select name="pricing_type" id="pricing_type" class="select select-bordered w-full @error('pricing_type') select-error @enderror" onchange="updatePricingType()">
                            <option value="">Select Type</option>
                            <option value="booking" {{ old('pricing_type') == 'booking' ? 'selected' : '' }}>Booking</option>
                            <option value="rental" {{ old('pricing_type') == 'rental' ? 'selected' : '' }}>Rental</option>
                        </select>
                        @error('pricing_type')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Base Rates Section -->
                    <div id="base-rates-section" class="hidden mb-6">
                        <h3 class="text-lg font-medium mb-4">Base Rates</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Hourly Rate -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Hourly Rate (RM)</span>
                                </label>
                                <input
                                    type="number"
                                    name="base_hourly_rate"
                                    id="base_hourly_rate"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    class="input input-bordered w-full @error('base_hourly_rate') input-error @enderror"
                                    value="{{ old('base_hourly_rate') }}"
                                    oninput="validateBaseRate(this)"
                                />
                                @error('base_hourly_rate')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="hourly-rate-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>

                            <!-- Daily Rate -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Daily Rate (RM)</span>
                                </label>
                                <input
                                    type="number"
                                    name="base_daily_rate"
                                    id="base_daily_rate"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    class="input input-bordered w-full @error('base_daily_rate') input-error @enderror"
                                    value="{{ old('base_daily_rate') }}"
                                    oninput="validateBaseRate(this)"
                                />
                                @error('base_daily_rate')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="daily-rate-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>

                            <!-- Monthly Rate -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Monthly Rate (RM)</span>
                                </label>
                                <input
                                    type="number"
                                    name="base_monthly_rate"
                                    id="base_monthly_rate"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    class="input input-bordered w-full @error('base_monthly_rate') input-error @enderror"
                                    value="{{ old('base_monthly_rate') }}"
                                    oninput="validateBaseRate(this)"
                                />
                                @error('base_monthly_rate')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="monthly-rate-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>

                            <!-- Yearly Rate -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Yearly Rate (RM)</span>
                                </label>
                                <input
                                    type="number"
                                    name="base_yearly_rate"
                                    id="base_yearly_rate"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    class="input input-bordered w-full @error('base_yearly_rate') input-error @enderror"
                                    value="{{ old('base_yearly_rate') }}"
                                    oninput="validateBaseRate(this)"
                                />
                                @error('base_yearly_rate')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="yearly-rate-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Discount Section -->
                    <div id="daily-discount-section" class="hidden mb-6">
                        <h3 class="text-lg font-medium mb-4">Daily Booking Discount</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Hours Threshold -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Daily Hours Threshold</span>
                                </label>
                                <input
                                    type="number"
                                    name="daily_hours_threshold"
                                    id="daily_hours_threshold"
                                    placeholder="8"
                                    min="1"
                                    class="input input-bordered w-full @error('daily_hours_threshold') input-error @enderror"
                                    value="{{ old('daily_hours_threshold') }}"
                                    oninput="validateHoursThreshold(this)"
                                />
                                @error('daily_hours_threshold')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="hours-threshold-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>

                            <!-- Daily Discount Percentage -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text whitespace-nowrap">Daily Discount (%)</span>
                                </label>
                                <input
                                    type="number"
                                    name="daily_discount_percentage"
                                    id="daily_discount_percentage"
                                    placeholder="0"
                                    min="0"
                                    max="100"
                                    class="input input-bordered w-full @error('daily_discount_percentage') input-error @enderror"
                                    value="{{ old('daily_discount_percentage') }}"
                                    oninput="validateDiscountPercentage(this)"
                                />
                                @error('daily_discount_percentage')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="daily-discount-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Special Customer Discounts Section -->
                    <div id="special-customer-discounts-section" class="hidden mb-6">
                        <h3 class="text-lg font-medium mb-4">Special Customer Discounts</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Educational Discount -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text whitespace-nowrap">Educational Discount (%)</span>
                                </label>
                                <input
                                    type="number"
                                    name="educational_discount_percentage"
                                    id="educational_discount_percentage"
                                    placeholder="0"
                                    min="0"
                                    max="100"
                                    class="input input-bordered w-full @error('educational_discount_percentage') input-error @enderror"
                                    value="{{ old('educational_discount_percentage') }}"
                                    oninput="validateDiscountPercentage(this)"
                                />
                                @error('educational_discount_percentage')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="educational-discount-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>

                            <!-- Corporate Discount -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text whitespace-nowrap">Corporate Discount (%)</span>
                                </label>
                                <input
                                    type="number"
                                    name="corporate_discount_percentage"
                                    id="corporate_discount_percentage"
                                    placeholder="0"
                                    min="0"
                                    max="100"
                                    class="input input-bordered w-full @error('corporate_discount_percentage') input-error @enderror"
                                    value="{{ old('corporate_discount_percentage') }}"
                                    oninput="validateDiscountPercentage(this)"
                                />
                                @error('corporate_discount_percentage')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="corporate-discount-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>

                            <!-- Student Discount -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text whitespace-nowrap">Student Discount (%)</span>
                                </label>
                                <input
                                    type="number"
                                    name="student_discount_percentage"
                                    id="student_discount_percentage"
                                    placeholder="0"
                                    min="0"
                                    max="100"
                                    class="input input-bordered w-full @error('student_discount_percentage') input-error @enderror"
                                    value="{{ old('student_discount_percentage') }}"
                                    oninput="validateDiscountPercentage(this)"
                                />
                                @error('student_discount_percentage')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="student-discount-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Requirements Section -->
                    <div id="booking-requirements-section" class="hidden mb-6">
                        <h3 class="text-lg font-medium mb-4">Booking Requirements</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Minimum Booking Hours -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Minimum Booking Hours</span>
                                </label>
                                <input
                                    type="number"
                                    name="minimum_booking_hours"
                                    id="minimum_booking_hours"
                                    placeholder="1"
                                    min="1"
                                    class="input input-bordered w-full @error('minimum_booking_hours') input-error @enderror"
                                    value="{{ old('minimum_booking_hours') }}"
                                    oninput="validateBookingHours(this)"
                                />
                                @error('minimum_booking_hours')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="minimum-hours-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>

                            <!-- Maximum Booking Hours -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Maximum Booking Hours</span>
                                </label>
                                <input
                                    type="number"
                                    name="maximum_booking_hours"
                                    id="maximum_booking_hours"
                                    placeholder="24"
                                    min="1"
                                    class="input input-bordered w-full @error('maximum_booking_hours') input-error @enderror"
                                    value="{{ old('maximum_booking_hours') }}"
                                    oninput="validateBookingHours(this)"
                                />
                                @error('maximum_booking_hours')
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                @enderror
                                <span id="maximum-hours-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Duration Section -->
                    <div id="rental-duration-section" class="hidden mb-6">
                        <h3 class="text-lg font-medium mb-4">Rental Duration</h3>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Rental Duration (Months)</span>
                            </label>
                            <input
                                type="number"
                                name="rental_duration_months"
                                id="rental_duration_months"
                                placeholder="12"
                                min="1"
                                class="input input-bordered w-full @error('rental_duration_months') input-error @enderror"
                                value="{{ old('rental_duration_months') }}"
                                oninput="validateRentalDuration(this)"
                            />
                            @error('rental_duration_months')
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            @enderror
                            <span id="rental-duration-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                        </div>
                    </div>

                    <!-- Pricing Notes -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Notes</span>
                        </label>
                        <textarea
                            name="pricing_notes"
                            placeholder="Additional notes about this pricing"
                            class="textarea textarea-bordered w-full @error('pricing_notes') textarea-error @enderror"
                            rows="3"
                        >{{ old('pricing_notes') }}</textarea>
                        @error('pricing_notes')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Service Section -->
            <div id="service-section" class="hidden">
                <div class="card bg-base-200 p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Service Configuration
                    </h2>
                    
                    <!-- Service Name -->
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Service Name</span>
                        </label>
                        <input
                            type="text"
                            name="service_name"
                            id="service_name"
                            placeholder="Enter service name"
                            class="input input-bordered w-full @error('service_name') input-error @enderror"
                            value="{{ old('service_name') }}"
                            oninput="validateServiceName(this)"
                        />
                        @error('service_name')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                        <span id="service-name-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                    </div>

                    <!-- Service Description -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Description</span>
                        </label>
                        <textarea
                            name="service_description"
                            placeholder="Enter service description"
                            class="textarea textarea-bordered w-full @error('service_description') textarea-error @enderror"
                            rows="3"
                        >{{ old('service_description') }}</textarea>
                        @error('service_description')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Amenity Section -->
            <div id="amenity-section" class="hidden">
                <div class="card bg-base-200 p-6">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        Amenity Configuration
                    </h2>
                    
                    <!-- Amenity Name -->
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Amenity Name</span>
                        </label>
                        <input
                            type="text"
                            name="amenity_name"
                            id="amenity_name"
                            placeholder="Enter amenity name"
                            class="input input-bordered w-full @error('amenity_name') input-error @enderror"
                            value="{{ old('amenity_name') }}"
                            oninput="validateAmenityName(this)"
                        />
                        @error('amenity_name')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                        <span id="amenity-name-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                    </div>

                    <!-- Amenity Description -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Description</span>
                        </label>
                        <textarea
                            name="amenity_description"
                            placeholder="Enter amenity description"
                            class="textarea textarea-bordered w-full @error('amenity_description') textarea-error @enderror"
                            rows="3"
                        >{{ old('amenity_description') }}</textarea>
                        @error('amenity_description')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-control mt-8">
                <button type="submit" class="btn btn-primary w-full">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Parameter
                </button>
            </div>
        </form>
        
        <div class="mt-6">
            <a href="{{ route('parameters.index') }}" class="btn btn-outline w-full" id="backButton">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Manage Property/Unit Parameter
            </a>
        </div>
    </div>
</div>

<script>
function toggleParameterSections() {
    const parameterType = document.getElementById('parameter_type').value;
    const pricingSection = document.getElementById('pricing-section');
    const serviceSection = document.getElementById('service-section');
    const amenitySection = document.getElementById('amenity-section');
    
    // Hide all sections first
    pricingSection.classList.add('hidden');
    serviceSection.classList.add('hidden');
    amenitySection.classList.add('hidden');
    
    // Show the selected section
    if (parameterType === 'pricing') {
        pricingSection.classList.remove('hidden');
    } else if (parameterType === 'service') {
        serviceSection.classList.remove('hidden');
    } else if (parameterType === 'amenity') {
        amenitySection.classList.remove('hidden');
    }
}

function updatePricingType() {
    const pricingType = document.getElementById('pricing_type').value;
    const baseRatesSection = document.getElementById('base-rates-section');
    const dailyDiscountSection = document.getElementById('daily-discount-section');
    const specialCustomerDiscountsSection = document.getElementById('special-customer-discounts-section');
    const bookingRequirementsSection = document.getElementById('booking-requirements-section');
    const rentalDurationSection = document.getElementById('rental-duration-section');
    
    // Get individual rate fields
    const hourlyRateField = document.getElementById('base_hourly_rate').closest('.form-control');
    const dailyRateField = document.getElementById('base_daily_rate').closest('.form-control');
    const monthlyRateField = document.getElementById('base_monthly_rate').closest('.form-control');
    const yearlyRateField = document.getElementById('base_yearly_rate').closest('.form-control');
    
    // Hide all sections first
    baseRatesSection.classList.add('hidden');
    dailyDiscountSection.classList.add('hidden');
    specialCustomerDiscountsSection.classList.add('hidden');
    bookingRequirementsSection.classList.add('hidden');
    rentalDurationSection.classList.add('hidden');
    
    // Hide all rate fields first
    hourlyRateField.classList.add('hidden');
    dailyRateField.classList.add('hidden');
    monthlyRateField.classList.add('hidden');
    yearlyRateField.classList.add('hidden');
    
    if (pricingType === 'booking') {
        baseRatesSection.classList.remove('hidden');
        dailyDiscountSection.classList.remove('hidden');
        specialCustomerDiscountsSection.classList.remove('hidden');
        bookingRequirementsSection.classList.remove('hidden');
        
        // Show only hourly and daily rates for booking
        hourlyRateField.classList.remove('hidden');
        dailyRateField.classList.remove('hidden');
        
    } else if (pricingType === 'rental') {
        baseRatesSection.classList.remove('hidden');
        specialCustomerDiscountsSection.classList.remove('hidden');
        rentalDurationSection.classList.remove('hidden');
        
        // Show only monthly and yearly rates for rental
        monthlyRateField.classList.remove('hidden');
        yearlyRateField.classList.remove('hidden');
    }
}

// Validation functions
function validatePricingName(input) {
    const nameInput = input;
    const validationMessage = document.getElementById('pricing-name-validation-message');
    
    if (nameInput.value.trim() === '') {
        nameInput.classList.add('input-error');
        validationMessage.classList.remove('hidden');
        validationMessage.textContent = 'Pricing name is required.';
        validationMessage.classList.remove('text-green-400');
        validationMessage.classList.add('text-red-400');
        return false;
    } else {
        nameInput.classList.remove('input-error');
        validationMessage.classList.add('hidden');
        return true;
    }
}

function validateServiceName(input) {
    const nameInput = input;
    const validationMessage = document.getElementById('service-name-validation-message');
    
    if (nameInput.value.trim() === '') {
        nameInput.classList.add('input-error');
        validationMessage.classList.remove('hidden');
        validationMessage.textContent = 'Service name is required.';
        validationMessage.classList.remove('text-green-400');
        validationMessage.classList.add('text-red-400');
        return false;
    } else {
        nameInput.classList.remove('input-error');
        validationMessage.classList.add('hidden');
        return true;
    }
}

function validateAmenityName(input) {
    const nameInput = input;
    const validationMessage = document.getElementById('amenity-name-validation-message');
    
    if (nameInput.value.trim() === '') {
        nameInput.classList.add('input-error');
        validationMessage.classList.remove('hidden');
        validationMessage.textContent = 'Amenity name is required.';
        validationMessage.classList.remove('text-green-400');
        validationMessage.classList.add('text-red-400');
        return false;
    } else {
        nameInput.classList.remove('input-error');
        validationMessage.classList.add('hidden');
        return true;
    }
}

function validateBaseRate(input) {
    const rateInput = input;
    const value = parseFloat(rateInput.value);
    const pricingType = document.getElementById('pricing_type').value;
    
    // Get validation message element
    let validationMessage;
    if (rateInput.id === 'base_hourly_rate') {
        validationMessage = document.getElementById('hourly-rate-validation-message');
    } else if (rateInput.id === 'base_daily_rate') {
        validationMessage = document.getElementById('daily-rate-validation-message');
    } else if (rateInput.id === 'base_monthly_rate') {
        validationMessage = document.getElementById('monthly-rate-validation-message');
    } else if (rateInput.id === 'base_yearly_rate') {
        validationMessage = document.getElementById('yearly-rate-validation-message');
    }
    
    if (rateInput.value === '' || value < 0) {
        rateInput.classList.add('input-error');
        if (validationMessage) {
            validationMessage.classList.add('hidden');
        }
        return false;
    } else {
        rateInput.classList.remove('input-error');
        if (validationMessage) {
            validationMessage.classList.add('hidden');
        }
        
        // Check if we need to clear the "at least one rate required" error
        if (pricingType === 'booking' && (rateInput.id === 'base_hourly_rate' || rateInput.id === 'base_daily_rate')) {
            const hourlyRate = parseFloat(document.getElementById('base_hourly_rate').value) || 0;
            const dailyRate = parseFloat(document.getElementById('base_daily_rate').value) || 0;
            
            if (hourlyRate > 0 || dailyRate > 0) {
                // Clear error states for both fields
                document.getElementById('base_hourly_rate').classList.remove('input-error');
                document.getElementById('base_daily_rate').classList.remove('input-error');
                document.getElementById('hourly-rate-validation-message').classList.add('hidden');
                document.getElementById('daily-rate-validation-message').classList.add('hidden');
            }
        } else if (pricingType === 'rental' && (rateInput.id === 'base_monthly_rate' || rateInput.id === 'base_yearly_rate')) {
            const monthlyRate = parseFloat(document.getElementById('base_monthly_rate').value) || 0;
            const yearlyRate = parseFloat(document.getElementById('base_yearly_rate').value) || 0;
            
            if (monthlyRate > 0 || yearlyRate > 0) {
                // Clear error states for both fields
                document.getElementById('base_monthly_rate').classList.remove('input-error');
                document.getElementById('base_yearly_rate').classList.remove('input-error');
                document.getElementById('monthly-rate-validation-message').classList.add('hidden');
                document.getElementById('yearly-rate-validation-message').classList.add('hidden');
            }
        }
        
        return true;
    }
}

function validateHoursThreshold(input) {
    const thresholdInput = input;
    const value = parseInt(thresholdInput.value);
    const validationMessage = document.getElementById('hours-threshold-validation-message');
    
    if (thresholdInput.value === '' || value < 1) {
        thresholdInput.classList.add('input-error');
        validationMessage.classList.remove('hidden');
        validationMessage.textContent = 'Hours threshold must be at least 1.';
        validationMessage.classList.remove('text-green-400');
        validationMessage.classList.add('text-red-400');
        return false;
    } else {
        thresholdInput.classList.remove('input-error');
        validationMessage.classList.add('hidden');
        return true;
    }
}

function validateDiscountPercentage(input) {
    const discountInput = input;
    const value = parseFloat(discountInput.value);
    
    if (discountInput.value === '' || value < 0 || value > 100) {
        discountInput.classList.add('input-error');
        return false;
    } else {
        discountInput.classList.remove('input-error');
        return true;
    }
}

function validateBookingHours(input) {
    const hoursInput = input;
    const value = parseInt(hoursInput.value);
    const minHours = document.getElementById('minimum_booking_hours');
    const maxHours = document.getElementById('maximum_booking_hours');
    
    // For minimum booking hours, allow empty but if provided, must be at least 1
    if (hoursInput.id === 'minimum_booking_hours') {
        if (hoursInput.value !== '' && value < 1) {
            hoursInput.classList.add('input-error');
            return false;
        }
    } else {
        // For maximum booking hours, allow empty but if provided, must be at least 1
        if (hoursInput.value !== '' && value < 1) {
            hoursInput.classList.add('input-error');
            return false;
        }
    }
    
    // Check if minimum is less than maximum (only if both have values)
    if (minHours.value && maxHours.value && parseInt(minHours.value) >= parseInt(maxHours.value)) {
        minHours.classList.add('input-error');
        maxHours.classList.add('input-error');
        return false;
    } else {
        minHours.classList.remove('input-error');
        maxHours.classList.remove('input-error');
    }
    
    hoursInput.classList.remove('input-error');
    return true;
}

function validateRentalDuration(input) {
    const durationInput = input;
    const value = parseInt(durationInput.value);
    const validationMessage = document.getElementById('rental-duration-validation-message');
    
    // Allow empty values (nullable field)
    if (durationInput.value === '') {
        durationInput.classList.remove('input-error');
        validationMessage.classList.add('hidden');
        return true;
    }
    
    // If a value is provided, it must be at least 1
    if (value < 1) {
        durationInput.classList.add('input-error');
        validationMessage.classList.remove('hidden');
        validationMessage.textContent = 'Rental duration must be at least 1 month.';
        validationMessage.classList.remove('text-green-400');
        validationMessage.classList.add('text-red-400');
        return false;
    } else {
        durationInput.classList.remove('input-error');
        validationMessage.classList.add('hidden');
        return true;
    }
}

function validateForm() {
    const parameterType = document.getElementById('parameter_type').value;
    let isValid = true;
    
    if (parameterType === 'pricing') {
        // Validate pricing name
        const pricingName = document.getElementById('pricing_name');
        if (!validatePricingName(pricingName)) {
            isValid = false;
        }
        
        // Validate pricing type
        const pricingType = document.getElementById('pricing_type').value;
        if (!pricingType) {
            document.getElementById('pricing_type').classList.add('select-error');
            isValid = false;
        } else {
            document.getElementById('pricing_type').classList.remove('select-error');
        }
        
        // Validate at least one base rate based on pricing type
        if (pricingType === 'booking') {
            const hourlyRate = parseFloat(document.getElementById('base_hourly_rate').value) || 0;
            const dailyRate = parseFloat(document.getElementById('base_daily_rate').value) || 0;
            const hourlyRateField = document.getElementById('base_hourly_rate');
            const dailyRateField = document.getElementById('base_daily_rate');
            const hourlyValidationMessage = document.getElementById('hourly-rate-validation-message');
            const dailyValidationMessage = document.getElementById('daily-rate-validation-message');
            
            if (hourlyRate === 0 && dailyRate === 0) {
                // Highlight both fields and show error messages
                hourlyRateField.classList.add('input-error');
                dailyRateField.classList.add('input-error');
                hourlyValidationMessage.classList.remove('hidden');
                dailyValidationMessage.classList.remove('hidden');
                hourlyValidationMessage.textContent = 'For booking pricing, at least one of hourly rate or daily rate must be provided.';
                dailyValidationMessage.textContent = 'For booking pricing, at least one of hourly rate or daily rate must be provided.';
                hourlyValidationMessage.classList.remove('text-gray-400');
                dailyValidationMessage.classList.remove('text-gray-400');
                hourlyValidationMessage.classList.add('text-error');
                dailyValidationMessage.classList.add('text-error');
                isValid = false;
            } else {
                // Clear error states if at least one rate is provided
                hourlyRateField.classList.remove('input-error');
                dailyRateField.classList.remove('input-error');
                hourlyValidationMessage.classList.add('hidden');
                dailyValidationMessage.classList.add('hidden');
            }
        } else if (pricingType === 'rental') {
            const monthlyRate = parseFloat(document.getElementById('base_monthly_rate').value) || 0;
            const yearlyRate = parseFloat(document.getElementById('base_yearly_rate').value) || 0;
            const monthlyRateField = document.getElementById('base_monthly_rate');
            const yearlyRateField = document.getElementById('base_yearly_rate');
            const monthlyValidationMessage = document.getElementById('monthly-rate-validation-message');
            const yearlyValidationMessage = document.getElementById('yearly-rate-validation-message');
            
            if (monthlyRate === 0 && yearlyRate === 0) {
                // Highlight both fields and show error messages
                monthlyRateField.classList.add('input-error');
                yearlyRateField.classList.add('input-error');
                monthlyValidationMessage.classList.remove('hidden');
                yearlyValidationMessage.classList.remove('hidden');
                monthlyValidationMessage.textContent = 'For rental pricing, at least one of monthly rate or yearly rate must be provided.';
                yearlyValidationMessage.textContent = 'For rental pricing, at least one of monthly rate or yearly rate must be provided.';
                monthlyValidationMessage.classList.remove('text-gray-400');
                yearlyValidationMessage.classList.remove('text-gray-400');
                monthlyValidationMessage.classList.add('text-error');
                yearlyValidationMessage.classList.add('text-error');
                isValid = false;
            } else {
                // Clear error states if at least one rate is provided
                monthlyRateField.classList.remove('input-error');
                yearlyRateField.classList.remove('input-error');
                monthlyValidationMessage.classList.add('hidden');
                yearlyValidationMessage.classList.add('hidden');
            }
        }
        
        // Validate rental duration for rental type
        if (pricingType === 'rental') {
            const rentalDuration = document.getElementById('rental_duration_months');
            if (!validateRentalDuration(rentalDuration)) {
                isValid = false;
            }
        }
        
        // Validate hours threshold if daily rate is provided
        if (pricingType === 'booking') {
            const dailyRate = parseFloat(document.getElementById('base_daily_rate').value) || 0;
            if (dailyRate > 0) {
                const hoursThreshold = document.getElementById('daily_hours_threshold');
                if (!validateHoursThreshold(hoursThreshold)) {
                    isValid = false;
                }
            }
        }
        
        // Validate booking hours for booking type
        if (pricingType === 'booking') {
            const minHours = document.getElementById('minimum_booking_hours');
            const maxHours = document.getElementById('maximum_booking_hours');
            
            // Only validate minimum hours as required
            if (!validateBookingHours(minHours)) {
                isValid = false;
            }
            
            // Validate maximum hours only if it has a value
            if (maxHours.value && !validateBookingHours(maxHours)) {
                isValid = false;
            }
        }
        
    } else if (parameterType === 'service') {
        const serviceName = document.getElementById('service_name');
        if (!validateServiceName(serviceName)) {
            isValid = false;
        }
    } else if (parameterType === 'amenity') {
        const amenityName = document.getElementById('amenity_name');
        if (!validateAmenityName(amenityName)) {
            isValid = false;
        }
    }
    
    // If validation fails, prevent form submission and scroll to first error
    if (!isValid) {
        // Find the first error field and scroll to it
        const firstErrorField = document.querySelector('.input-error, .select-error');
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
    }
    
    return true;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleParameterSections();
    
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

// Update back button URL based on current tab
document.addEventListener('DOMContentLoaded', function() {
    const backButton = document.getElementById('backButton');
    if (backButton) {
        // Get the tab parameter from the current URL
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        // Set the back button to go to the exact tab that was used to navigate here
        if (tab === 'amenities') {
            backButton.href = '{{ route("parameters.index") }}?tab=amenities';
        } else if (tab === 'services') {
            backButton.href = '{{ route("parameters.index") }}?tab=services';
        } else if (tab === 'pricing') {
            backButton.href = '{{ route("parameters.index") }}?tab=pricing';
        } else {
            // If no tab parameter, try to get it from the referrer URL
            const referrer = document.referrer;
            if (referrer && referrer.includes('parameters')) {
                const referrerUrl = new URL(referrer);
                const referrerTab = referrerUrl.searchParams.get('tab');
                if (referrerTab) {
                    backButton.href = '{{ route("parameters.index") }}?tab=' + referrerTab;
                } else {
                    backButton.href = '{{ route("parameters.index") }}?tab=pricing';
                }
            } else {
                backButton.href = '{{ route("parameters.index") }}?tab=pricing';
            }
        }
    }
});

// Initialize pricing type on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePricingType();
});
</script>
@endsection 