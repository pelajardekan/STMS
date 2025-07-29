@extends('layouts.sidebar')

@section('title', 'Edit Property')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Edit Property</h1>
        
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

        <form method="POST" action="{{ route('properties.update', $property) }}" class="space-y-6" id="propertyForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="property_parameters" id="propertyParametersInput">
            
            <!-- Property Information Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Property Name -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Property Name</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="name"
                            id="name"
                            placeholder="Enter property name"
                            class="input input-bordered w-full pl-10 @error('name') input-error @enderror"
                            value="{{ old('name', $property->name) }}"
                            required
                            onblur="validatePropertyName(this.value)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    @error('name')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Property Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Property Type</span>
                    </label>
                    <div class="relative">
                        <select name="type" id="type" class="select select-bordered w-full pl-10 @error('type') select-error @enderror" onchange="toggleCustomPropertyType()">
                            <option value="">Select property type</option>
                            <option value="custom" {{ (old('type', $property->type) == 'custom' || !in_array($property->type, ['residential', 'commercial', 'industrial'])) ? 'selected' : '' }}>Custom Type</option>
                            <option value="residential" {{ (old('type', $property->type) == 'residential') ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ (old('type', $property->type) == 'commercial') ? 'selected' : '' }}>Commercial</option>
                            <option value="industrial" {{ (old('type', $property->type) == 'industrial') ? 'selected' : '' }}>Industrial</option>
                        </select>
                        <!-- Icon -->
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    @error('type')
                        <div class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Custom Property Type -->
                <div class="form-control" id="customPropertyTypeField" style="display: none;">
                    <label class="label">
                        <span class="label-text">Custom Property Type</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="custom_type" id="custom_type" placeholder="Enter custom property type" 
                               class="input input-bordered w-full pl-10 @error('custom_type') input-error @enderror" 
                               value="{{ old('custom_type', (!in_array($property->type, ['residential', 'commercial', 'industrial']) ? $property->type : '')) }}" />
                        <!-- Icon -->
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    @error('custom_type')
                        <div class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Status and Address Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Status</span>
                    </label>
                    <div class="relative">
                        <select
                            name="status"
                            id="status"
                            class="select select-bordered w-full pl-10 @error('status') select-error @enderror"
                            required
                        >
                            <option value="">Select status</option>
                            <option value="active" {{ old('status', $property->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $property->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                
                <!-- Address -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Address</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="address"
                            id="address"
                            placeholder="Enter property address"
                            class="input input-bordered w-full pl-10 @error('address') input-error @enderror"
                            value="{{ old('address', $property->address) }}"
                            required
                            onblur="validateAddress(this.value)"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    @error('address')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea
                    name="description"
                    id="description"
                    placeholder="Enter property description"
                    class="textarea textarea-bordered w-full @error('description') textarea-error @enderror"
                    rows="4"
                >{{ old('description', $property->description) }}</textarea>
                @error('description')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col space-y-4">
                <label for="setupParameterModal" class="btn btn-secondary w-full cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Setup Parameter
                </label>
                <button type="submit" class="btn btn-primary w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Property
                </button>
                <a href="{{ route('properties.index') }}" class="btn btn-outline w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Manage Properties
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Setup Parameter Modal -->
<input type="checkbox" id="setupParameterModal" class="modal-toggle" />
<div class="modal">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="font-bold text-lg mb-4">Setup Property Parameters</h3>
        <form id="paramForm" class="space-y-8">
            <!-- Price Section -->
            <div class="space-y-2">
                <div class="font-semibold text-base mb-1">Price</div>
                <div class="space-y-3">
                    <label class="form-control w-full">
                        <span class="label-text">Select Global Pricing Structure</span>
                        <select class="select select-bordered w-full" id="globalPricingSelect">
                            <option value="">-- Select --</option>
                            <option value="no-pricing">No Pricing</option>
                        </select>
                    </label>
                </div>
            </div>
            <!-- Services Section -->
            <div class="space-y-2">
                <div class="font-semibold text-base mb-1">Services</div>
                <div id="servicesList" class="space-y-2">
                    <div class="flex gap-2">
                        <select class="select select-bordered w-full" id="serviceSelect">
                            <option value="">-- Select Service --</option>
                        </select>
                        <button type="button" class="btn btn-success" onclick="addService()">Add</button>
                    </div>
                </div>
            </div>
            <!-- Amenities Section -->
            <div class="space-y-2">
                <div class="font-semibold text-base mb-1">Amenities</div>
                <div id="amenitiesList" class="space-y-2">
                    <div class="flex gap-2">
                        <select class="select select-bordered w-full" id="amenitySelect">
                            <option value="">-- Select Amenity --</option>
                        </select>
                        <button type="button" class="btn btn-success" onclick="addAmenity()">Add</button>
                    </div>
                </div>
            </div>
            
            <!-- Horizontal Bar with Create Parameter Link -->
            <div class="flex items-center my-4">
                <div class="flex-grow border-t"></div>
                <span class="mx-4 text-sm opacity-70">or</span>
                <div class="flex-grow border-t"></div>
            </div>
            <div class="text-center">
                <a href="{{ route('parameters.create') }}" class="link link-primary text-sm">
                    Don't see the parameter you want to add? Create a new parameter
                </a>
            </div>
            <div class="modal-action flex justify-end gap-2 pt-6">
                <label for="setupParameterModal" class="btn">Cancel</label>
                <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 5000);

    // Manual hide alert function
    function hideAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.style.display = 'none';
        }
    }

    // Validation functions
    function validatePropertyName(value) {
        if (value.length < 3) {
            // Add validation logic if needed
        }
    }

    function validateAddress(value) {
        if (value.length < 5) {
            // Add validation logic if needed
        }
    }

    // Toggle custom property type field
    function toggleCustomPropertyType() {
        const typeSelect = document.getElementById('type');
        const customTypeField = document.getElementById('customPropertyTypeField');
        const customTypeInput = document.getElementById('custom_type');

        if (typeSelect.value === 'custom') {
            customTypeField.style.display = 'block';
            customTypeInput.setAttribute('required', 'required');
        } else {
            customTypeField.style.display = 'none';
            customTypeInput.removeAttribute('required');
            customTypeInput.value = ''; // Clear custom type if not selected
        }
    }



    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleCustomPropertyType();
    });

    function showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(toastContainer);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} shadow-lg max-w-sm transform transition-all duration-300 translate-x-full`;
        
        // Set icon based on type
        let icon = '';
        if (type === 'success') {
            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        } else if (type === 'error') {
            icon = '<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        } else {
            icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }

        toast.innerHTML = `
            ${icon}
            <span>${message}</span>
            <button class="btn btn-sm btn-ghost" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;

        // Add toast to container
        toastContainer.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 5000);
    }

    // Setup Parameter Modal functions (MockUI style)
    document.getElementById('paramForm').onsubmit = function(e) {
        e.preventDefault();
        saveParameters();
    };

    function toggleCustomPricing() {
        var globalSelect = document.getElementById('globalPricingSelect');
        var customPrice = document.getElementById('customPriceInput');
        var customType = document.getElementById('customPricingType');
        var disabled = globalSelect.value !== '';
        customPrice.disabled = disabled;
        customType.disabled = disabled;
    }

    function addService() {
        const list = document.getElementById('servicesList');
        const div = document.createElement('div');
        div.className = 'flex gap-2 mt-2';
        div.innerHTML = `<select class='select select-bordered w-full'>
            <option value="">-- Select Service --</option>
        </select>
        <button type='button' class='btn btn-error' onclick='this.parentNode.remove()'>Remove</button>`;
        list.appendChild(div);
        loadServicesOptions(div.querySelector('select'));
    }

    function addAmenity() {
        const list = document.getElementById('amenitiesList');
        const div = document.createElement('div');
        div.className = 'flex gap-2 mt-2';
        div.innerHTML = `<select class='select select-bordered w-full'>
            <option value="">-- Select Amenity --</option>
        </select>
        <button type='button' class='btn btn-error' onclick='this.parentNode.remove()'>Remove</button>`;
        list.appendChild(div);
        loadAmenitiesOptions(div.querySelector('select'));
    }

    function loadPricingOptions() {
        fetch('/admin/pricings')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('globalPricingSelect');
                select.innerHTML = '<option value="">-- Select --</option>';
                select.innerHTML += '<option value="no-pricing">No Pricing</option>';
                data.forEach(pricing => {
                    select.innerHTML += `<option value="${pricing.pricing_id}">
                        ${pricing.name} - ${pricing.pricing_type} - RM${pricing.price_amount}
                        ${pricing.duration_type ? ' / ' + pricing.duration_type : ''}
                    </option>`;
                });
            })
            .catch(error => {
                console.error('Error loading pricing options:', error);
            });
    }

    function loadServicesOptions(selectElement = null) {
        return fetch('/admin/services')
            .then(response => response.json())
            .then(data => {
                const select = selectElement || document.getElementById('serviceSelect');
                select.innerHTML = '<option value="">-- Select Service --</option>';
                data.forEach(service => {
                    select.innerHTML += `<option value="${service.service_id}">${service.name}</option>`;
                });
                return data;
            })
            .catch(error => {
                console.error('Error loading services options:', error);
            });
    }

    function loadAmenitiesOptions(selectElement = null) {
        return fetch('/admin/amenities')
            .then(response => response.json())
            .then(data => {
                const select = selectElement || document.getElementById('amenitySelect');
                select.innerHTML = '<option value="">-- Select Amenity --</option>';
                data.forEach(amenity => {
                    select.innerHTML += `<option value="${amenity.amenity_id}">${amenity.name}</option>`;
                });
                return data;
            })
            .catch(error => {
                console.error('Error loading amenities options:', error);
            });
    }

    function saveParameters() {
        // Get pricing selection
        const globalPricingId = document.getElementById('globalPricingSelect').value;
        
        // Get selected services
        const serviceSelects = document.querySelectorAll('#servicesList select');
        const selectedServices = Array.from(serviceSelects)
            .map(select => select.value)
            .filter(value => value !== '');
        
        // Get selected amenities
        const amenitySelects = document.querySelectorAll('#amenitiesList select');
        const selectedAmenities = Array.from(amenitySelects)
            .map(select => select.value)
            .filter(value => value !== '');
        
        // Get custom property type if applicable
        const customType = document.getElementById('custom_type').value;

        // Store the parameters for later use when property is updated
        window.propertyParameters = {
            globalPricingId: globalPricingId,
            services: selectedServices,
            amenities: selectedAmenities,
            customType: customType // Add custom type to parameters
        };
        
        // Close modal
        document.getElementById('setupParameterModal').checked = false;
        
        // Show toast notification
        showToast('Parameters configured successfully! They will be applied when you update the property.', 'success');
    }

    // Load options when modal is opened
    document.querySelector('label[for="setupParameterModal"]').addEventListener('click', function() {
        console.log('Setup Parameter modal opened');
        loadPricingOptions();
        loadServicesOptions();
        loadAmenitiesOptions();
        
        // Pre-populate with existing parameters after options are loaded
        setTimeout(() => {
            populateExistingParameters();
        }, 500);
    });

    // Pre-populate modal with existing parameters
    function populateExistingParameters() {
        // Get existing parameters for this property via AJAX
        const propertyId = '{{ $property->property_id }}';
        
        console.log('Populating existing parameters for property:', propertyId);
        
        fetch(`/admin/properties/${propertyId}/parameters`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                if (data.success && data.parameters) {
                    const existingParams = data.parameters;
                    console.log('Existing parameters:', existingParams);
                    
                    // Pre-populate pricing
                    if (existingParams.pricing) {
                        console.log('Setting pricing:', existingParams.pricing);
                        const pricingSelect = document.getElementById('globalPricingSelect');
                        const pricingOption = Array.from(pricingSelect.options).find(option => 
                            option.value == existingParams.pricing.pricing_id
                        );
                        if (pricingOption) {
                            pricingSelect.value = existingParams.pricing.pricing_id;
                        }
                    } else {
                        // No pricing selected
                        console.log('No pricing selected for this property');
                        const pricingSelect = document.getElementById('globalPricingSelect');
                        pricingSelect.value = 'no-pricing';
                    }
                    
                    // Pre-populate services
                    if (existingParams.services && existingParams.services.length > 0) {
                        console.log('Setting services:', existingParams.services);
                        const servicesList = document.getElementById('servicesList');
                        
                        // Clear all existing service rows
                        const serviceRows = servicesList.querySelectorAll('.flex.gap-2');
                        serviceRows.forEach(row => row.remove());
                        
                        // Create all service rows first
                        const servicePromises = [];
                        existingParams.services.forEach((service, index) => {
                            if (index === 0) {
                                // Add the first row with Add button
                                const div = document.createElement('div');
                                div.className = 'flex gap-2';
                                div.innerHTML = `<select class='select select-bordered w-full' id='serviceSelect'>
                                    <option value="">-- Select Service --</option>
                                </select>
                                <button type='button' class='btn btn-success' onclick='addService()'>Add</button>`;
                                servicesList.appendChild(div);
                                const promise = loadServicesOptions(div.querySelector('select'));
                                servicePromises.push({ select: div.querySelector('select'), service: service, promise });
                            } else {
                                // Add additional rows with Remove button
                                const div = document.createElement('div');
                                div.className = 'flex gap-2 mt-2';
                                div.innerHTML = `<select class='select select-bordered w-full'>
                                    <option value="">-- Select Service --</option>
                                </select>
                                <button type='button' class='btn btn-error' onclick='this.parentNode.remove()'>Remove</button>`;
                                servicesList.appendChild(div);
                                const promise = loadServicesOptions(div.querySelector('select'));
                                servicePromises.push({ select: div.querySelector('select'), service: service, promise });
                            }
                        });
                        
                        // Wait for all options to load, then set values
                        Promise.all(servicePromises.map(item => item.promise)).then(() => {
                            servicePromises.forEach(item => {
                                item.select.value = item.service.service_id;
                                console.log(`Set service to:`, item.service.service_id, 'Name:', item.service.name);
                            });
                        });
                    }
                    
                    // Pre-populate amenities
                    if (existingParams.amenities && existingParams.amenities.length > 0) {
                        console.log('Setting amenities:', existingParams.amenities);
                        const amenitiesList = document.getElementById('amenitiesList');
                        
                        // Clear all existing amenity rows
                        const amenityRows = amenitiesList.querySelectorAll('.flex.gap-2');
                        amenityRows.forEach(row => row.remove());
                        
                        // Create all amenity rows first
                        const amenityPromises = [];
                        existingParams.amenities.forEach((amenity, index) => {
                            if (index === 0) {
                                // Add the first row with Add button
                                const div = document.createElement('div');
                                div.className = 'flex gap-2';
                                div.innerHTML = `<select class='select select-bordered w-full' id='amenitySelect'>
                                    <option value="">-- Select Amenity --</option>
                                </select>
                                <button type='button' class='btn btn-success' onclick='addAmenity()'>Add</button>`;
                                amenitiesList.appendChild(div);
                                const promise = loadAmenitiesOptions(div.querySelector('select'));
                                amenityPromises.push({ select: div.querySelector('select'), amenity: amenity, promise });
                            } else {
                                // Add additional rows with Remove button
                                const div = document.createElement('div');
                                div.className = 'flex gap-2 mt-2';
                                div.innerHTML = `<select class='select select-bordered w-full'>
                                    <option value="">-- Select Amenity --</option>
                                </select>
                                <button type='button' class='btn btn-error' onclick='this.parentNode.remove()'>Remove</button>`;
                                amenitiesList.appendChild(div);
                                const promise = loadAmenitiesOptions(div.querySelector('select'));
                                amenityPromises.push({ select: div.querySelector('select'), amenity: amenity, promise });
                            }
                        });
                        
                        // Wait for all options to load, then set values
                        Promise.all(amenityPromises.map(item => item.promise)).then(() => {
                            amenityPromises.forEach(item => {
                                item.select.value = item.amenity.amenity_id;
                                console.log(`Set amenity to:`, item.amenity.amenity_id, 'Name:', item.amenity.name);
                            });
                        });
                    }

                    // Pre-populate custom property type
                    if (existingParams.customType) {
                        console.log('Setting custom property type:', existingParams.customType);
                        const typeSelect = document.getElementById('type');
                        typeSelect.value = existingParams.customType;
                        toggleCustomPropertyType(); // Ensure custom field is shown
                    }
                } else {
                    console.log('No existing parameters found or invalid response');
                }
            })
            .catch(error => {
                console.error('Error loading existing parameters:', error);
            });
    }

    // Handle property form submission
    document.getElementById('propertyForm').addEventListener('submit', function(e) {
        // Include property parameters if they exist
        if (window.propertyParameters) {
            document.getElementById('propertyParametersInput').value = JSON.stringify(window.propertyParameters);
        }
    });
</script>
@endsection 