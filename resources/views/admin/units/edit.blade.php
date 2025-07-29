@extends('layouts.sidebar')

@section('title', 'Edit Unit')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Edit Unit</h1>
        
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

        <form method="POST" action="{{ route('units.update', $unit->unit_id) }}" class="space-y-6" id="unitForm">
            @csrf
            @method('PUT')
            
            <!-- Unit Information Grid (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Unit Name -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Unit Name *</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="name"
                            id="name"
                            placeholder="Enter unit name"
                            class="input input-bordered w-full pl-10 @error('name') input-error @enderror"
                            value="{{ old('name', $unit->name) }}"
                            required
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    @error('name')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Property -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Property *</span>
                    </label>
                    <div class="relative">
                        <select
                            name="property_id"
                            id="property_id"
                            class="select select-bordered w-full pl-10 @error('property_id') select-error @enderror"
                            required
                        >
                            <option value="">Select a property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->property_id }}" 
                                    {{ old('property_id', $unit->property_id) == $property->property_id ? 'selected' : '' }}>
                                    {{ $property->name }} - {{ $property->address }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    @error('property_id')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Unit Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Unit Type</span>
                    </label>
                    <div class="relative">
                        <select name="type" id="type" class="select select-bordered w-full pl-10 @error('type') select-error @enderror" onchange="toggleCustomUnitType()">
                            <option value="">Select unit type</option>
                            <option value="custom" {{ (old('type', $unit->type) == 'custom' || !in_array($unit->type, ['studio', 'single_story_house', 'room', 'small_unit_house', 'kitchen', 'event_hall', 'restaurant'])) ? 'selected' : '' }}>Custom Type</option>
                            <option value="studio" {{ (old('type', $unit->type) == 'studio') ? 'selected' : '' }}>Studio</option>
                            <option value="single_story_house" {{ (old('type', $unit->type) == 'single_story_house') ? 'selected' : '' }}>Single Story House</option>
                            <option value="room" {{ (old('type', $unit->type) == 'room') ? 'selected' : '' }}>Room</option>
                            <option value="small_unit_house" {{ (old('type', $unit->type) == 'small_unit_house') ? 'selected' : '' }}>Small Unit House</option>
                            <option value="kitchen" {{ (old('type', $unit->type) == 'kitchen') ? 'selected' : '' }}>Kitchen</option>
                            <option value="event_hall" {{ (old('type', $unit->type) == 'event_hall') ? 'selected' : '' }}>Event Hall</option>
                            <option value="restaurant" {{ (old('type', $unit->type) == 'restaurant') ? 'selected' : '' }}>Restaurant</option>
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
                
                <!-- Status -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Status</span>
                    </label>
                    <div class="relative">
                        <select name="status" id="status" class="select select-bordered w-full pl-10 @error('status') select-error @enderror" required>
                            <option value="">Select status</option>
                            <option value="active" {{ (old('status', $unit->status) == 'active') ? 'selected' : '' }}>Active</option>
                            <option value="unactive" {{ (old('status', $unit->status) == 'unactive') ? 'selected' : '' }}>Unactive</option>
                        </select>
                        <!-- Icon -->
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    @error('status')
                        <div class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Leasing Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Leasing Type</span>
                    </label>
                    <div class="relative">
                        <select name="leasing_type" id="leasing_type" class="select select-bordered w-full pl-10 @error('leasing_type') select-error @enderror" required>
                            <option value="">Select leasing type</option>
                            <option value="rental" {{ (old('leasing_type', $unit->leasing_type) == 'rental') ? 'selected' : '' }}>Rental</option>
                            <option value="booking" {{ (old('leasing_type', $unit->leasing_type) == 'booking') ? 'selected' : '' }}>Booking</option>
                        </select>
                        <!-- Icon -->
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    @error('leasing_type')
                        <div class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>
            
            <!-- Description -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea
                    name="description"
                    id="description"
                    placeholder="Enter unit description"
                    class="textarea textarea-bordered w-full @error('description') textarea-error @enderror"
                    rows="4"
                >{{ old('description', $unit->description) }}</textarea>
                @error('description')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Leasing Type and Availability -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Leasing Type -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Leasing Type *</span>
                    </label>
                    <div class="relative">
                        <select
                            name="leasing_type"
                            id="leasing_type"
                            class="select select-bordered w-full pl-10 @error('leasing_type') select-error @enderror"
                            required
                            onchange="toggleAvailabilityField()"
                        >
                            <option value="">Select leasing type</option>
                            <option value="rental" {{ old('leasing_type', $unit->leasing_type) == 'rental' ? 'selected' : '' }}>Rental</option>
                            <option value="booking" {{ old('leasing_type', $unit->leasing_type) == 'booking' ? 'selected' : '' }}>Booking</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    @error('leasing_type')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Availability -->
                <div class="form-control" id="availabilityField" style="display: {{ $unit->leasing_type == 'rental' ? 'block' : 'none' }};">
                    <label class="label">
                        <span class="label-text">Availability *</span>
                    </label>
                    <div class="relative">
                        <select
                            name="availability"
                            id="availability"
                            class="select select-bordered w-full pl-10 @error('availability') select-error @enderror"
                            required="{{ $unit->leasing_type == 'rental' ? 'true' : 'false' }}"
                        >
                            <option value="">Select availability</option>
                            <option value="available" {{ old('availability', $unit->availability) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="not_available" {{ old('availability', $unit->availability) == 'not_available' ? 'selected' : '' }}>Not Available</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    @error('availability')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex flex-col space-y-4">
                <label for="setupParamModal" class="btn btn-secondary w-full cursor-pointer">
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
                    Save Changes
                </button>
                <a href="{{ route('units.index') }}" class="btn btn-outline w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Manage Units
                </a>
            </div>
        </form>

        <!-- Setup Parameter Modal -->
        <input type="checkbox" id="setupParamModal" class="modal-toggle" />
        <div class="modal">
            <div class="modal-box w-full max-w-2xl">
                <h3 class="font-bold text-lg mb-4">Setup Unit Parameters</h3>
                <form id="paramForm" class="space-y-8" onsubmit="handleParamSubmit(event)">
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
                        <button type="button" onclick="closeSetupParamModal()" class="btn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openSetupParamModal() {
        document.getElementById('setupParamModal').checked = true;
    }

    function closeSetupParamModal() {
        document.getElementById('setupParamModal').checked = false;
    }

    function handleParamSubmit(event) {
        event.preventDefault();
        
        // Get form data
        const globalPricing = document.getElementById('globalPricingSelect').value;
        
        // Validate that at least one pricing option is selected
        if (!globalPricing) {
            showToast('Please select a global pricing structure or "No Pricing".', 'error');
            return;
        }
        
        // Store parameter data for later use when unit is updated
        window.unitParameters = {
            globalPricingId: globalPricing,
            services: getSelectedServices(),
            amenities: getSelectedAmenities()
        };
        
        // Close modal and show success message
        closeSetupParamModal();
        
        // Show toast notification
        showToast('Parameters configured successfully! They will be applied when you update the unit.', 'success');
    }

    function getSelectedServices() {
        const services = [];
        const serviceSelects = document.querySelectorAll('#servicesList select');
        serviceSelects.forEach(select => {
            if (select.value && select.value !== '') {
                services.push(select.value);
            }
        });
        return services;
    }

    function getSelectedAmenities() {
        const amenities = [];
        const amenitySelects = document.querySelectorAll('#amenitiesList select');
        amenitySelects.forEach(select => {
            if (select.value && select.value !== '') {
                amenities.push(select.value);
            }
        });
        return amenities;
    }

    function showParamSuccessMessage() {
        // Create success alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success mb-6';
        alertDiv.id = 'paramSuccessAlert';
        alertDiv.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Unit parameters have been set successfully!</span>
            <button class="btn btn-sm btn-ghost" onclick="hideAlert('paramSuccessAlert')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        
        // Insert after the form
        const form = document.querySelector('form');
        form.parentNode.insertBefore(alertDiv, form.nextSibling);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            hideAlert('paramSuccessAlert');
        }, 5000);
    }

    function addService() {
        const servicesList = document.getElementById('servicesList');
        const newService = document.createElement('div');
        newService.className = 'flex gap-2 mt-2';
        newService.innerHTML = `
            <select class="select select-bordered w-full">
                <option value="">-- Select Service --</option>
            </select>
            <button type="button" class="btn btn-error" onclick="removeService(this)">Remove</button>
        `;
        servicesList.appendChild(newService);
        loadServicesOptions(newService.querySelector('select'));
    }

    function removeService(button) {
        button.parentElement.remove();
    }

    function addAmenity() {
        const amenitiesList = document.getElementById('amenitiesList');
        const newAmenity = document.createElement('div');
        newAmenity.className = 'flex gap-2 mt-2';
        newAmenity.innerHTML = `
            <select class="select select-bordered w-full">
                <option value="">-- Select Amenity --</option>
            </select>
            <button type="button" class="btn btn-error" onclick="removeAmenity(this)">Remove</button>
        `;
        amenitiesList.appendChild(newAmenity);
        loadAmenitiesOptions(newAmenity.querySelector('select'));
    }

    function removeAmenity(button) {
        button.parentElement.remove();
    }

    // Load data from models
    function loadPricingOptions() {
        fetch('{{ route("pricings.api") }}')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('globalPricingSelect');
                select.innerHTML = '<option value="">-- Select --</option>';
                select.innerHTML += '<option value="no-pricing">No Pricing</option>';
                data.forEach(pricing => {
                    select.innerHTML += `<option value="${pricing.pricing_id}">
                        ${pricing.name} - ${pricing.pricing_type} - RM${pricing.base_hourly_rate || pricing.base_monthly_rate || pricing.price_amount}
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

    // Load options when modal is opened
    document.getElementById('setupParamModal').addEventListener('change', function() {
        if (this.checked) {
            loadPricingOptions();
            loadServicesOptions();
            loadAmenitiesOptions();
            // Populate existing parameters after options are loaded
            setTimeout(() => {
                populateExistingParameters();
            }, 100);
        }
    });

    // Populate existing parameters
    function populateExistingParameters() {
        // Fetch existing parameters for this unit
        fetch(`/admin/units/{{ $unit->unit_id }}/parameters`)
            .then(response => response.json())
            .then(data => {
                console.log('Existing parameters:', data);
                
                // Set pricing
                const pricingSelect = document.getElementById('globalPricingSelect');
                if (data.pricing && data.pricing.length > 0) {
                    pricingSelect.value = data.pricing[0].pricing_id;
                } else {
                    pricingSelect.value = 'no-pricing';
                }
                
                // Clear existing services and amenities
                const servicesList = document.getElementById('servicesList');
                const amenitiesList = document.getElementById('amenitiesList');
                
                // Clear all existing rows
                const serviceRows = servicesList.querySelectorAll('.flex.gap-2');
                const amenityRows = amenitiesList.querySelectorAll('.flex.gap-2');
                
                serviceRows.forEach(row => row.remove());
                amenityRows.forEach(row => row.remove());
                
                // Set services
                if (data.services && data.services.length > 0) {
                    console.log('Setting services:', data.services);
                    
                    // Create all service rows first
                    const servicePromises = [];
                    data.services.forEach((service, index) => {
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
                
                // Set amenities
                if (data.amenities && data.amenities.length > 0) {
                    console.log('Setting amenities:', data.amenities);
                    
                    // Create all amenity rows first
                    const amenityPromises = [];
                    data.amenities.forEach((amenity, index) => {
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
            })
            .catch(error => {
                console.error('Error loading existing parameters:', error);
            });
    }

    // Handle unit form submission to include parameters
    document.querySelector('form').addEventListener('submit', function(e) {
        // Include unit parameters if they exist
        if (window.unitParameters) {
            // Create a hidden input to pass the parameters
            const paramInput = document.createElement('input');
            paramInput.type = 'hidden';
            paramInput.name = 'unit_parameters';
            paramInput.value = JSON.stringify(window.unitParameters);
            this.appendChild(paramInput);
        }
    });

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

    function toggleAvailabilityField() {
        const leasingTypeSelect = document.getElementById('leasing_type');
        const availabilityField = document.getElementById('availabilityField');
        const availabilitySelect = document.getElementById('availability');

        if (leasingTypeSelect.value === 'booking') {
            availabilityField.style.display = 'none';
            availabilitySelect.required = false;
            availabilitySelect.value = 'available'; // Set default to available for booking
        } else {
            availabilityField.style.display = 'block';
            availabilitySelect.required = true;
        }
    }

    // Handle form submission to ensure booking units have availability set to 'available'
    document.getElementById('unitForm').addEventListener('submit', function(e) {
        const leasingTypeSelect = document.getElementById('leasing_type');
        const availabilitySelect = document.getElementById('availability');

        if (leasingTypeSelect.value === 'booking') {
            availabilitySelect.value = 'available';
        }
    });

    function toggleCustomUnitType() {
        const unitTypeSelect = document.getElementById('type');
        const customOption = unitTypeSelect.querySelector('option[value="custom"]');
        const otherOptions = unitTypeSelect.querySelectorAll('option:not([value="custom"])');

        if (customOption.selected) {
            // If custom type is selected, show the custom type input
            document.getElementById('customTypeInput').style.display = 'block';
            // Optionally, you might want to clear other options if they are not relevant for custom
            otherOptions.forEach(option => option.style.display = 'none');
        } else {
            // If a specific type is selected, hide the custom type input
            document.getElementById('customTypeInput').style.display = 'none';
            // Optionally, you might want to re-enable other options if they are relevant for custom
            otherOptions.forEach(option => option.style.display = 'block');
        }
    }

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
</script>
@endsection 