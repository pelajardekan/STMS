@extends('layouts.sidebar')

@section('title', 'Manage Property/Unit Parameters')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Manage Property/Unit Parameters</h1>
                <p class="text-base-content/60 mt-1">Manage pricing, amenities, and services for properties and units</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('parameters.create') }}?tab={{ request()->get('tab', 'pricing') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Parameter
                </a>
            </div>
        </div>

        <!-- Success/Error Messages with Auto-hide -->
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

        @if(session('info'))
            <div class="alert alert-info mb-6" id="infoAlert">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('info') }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('infoAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error mb-6" id="errorAlert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('errorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Tabs -->
        <div role="tablist" class="tabs tabs-boxed mb-6">
            <a role="tab" class="tab {{ request()->get('tab', 'pricing') == 'pricing' ? 'tab-active' : '' }}" id="tab-pricing" onclick="showMainTab('pricing')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Pricing
            </a>
            <a role="tab" class="tab {{ request()->get('tab') == 'amenities' ? 'tab-active' : '' }}" id="tab-amenities" onclick="showMainTab('amenities')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                Amenities
            </a>
            <a role="tab" class="tab {{ request()->get('tab') == 'services' ? 'tab-active' : '' }}" id="tab-services" onclick="showMainTab('services')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Services
            </a>
        </div>

        <!-- Search and Filters Section -->
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="flex-1">
                <div class="form-control">
                    <div class="input-group">
                        <input type="text" placeholder="Search parameters..." class="input input-bordered flex-1" id="searchInput" />
                        <button class="btn btn-square">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <select class="select select-bordered" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="booking">Booking</option>
                    <option value="rental">Rental</option>
                </select>
                <select class="select select-bordered" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select class="select select-bordered" id="sortBy">
                    <option value="">Sort By</option>
                    <option value="name">Name</option>
                    <option value="type">Type</option>
                    <option value="status">Status</option>
                    <option value="created_at">Date Created</option>
                </select>
            </div>
        </div>

        <!-- Pricing Tab -->
        <div id="pricing-section" class="{{ request()->get('tab', 'pricing') == 'pricing' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Pricing Details</th>
                            <th class="text-base-content font-semibold">Type & Status</th>
                            <th class="text-base-content font-semibold">Rates Summary</th>
                            <th class="text-base-content font-semibold">Notes</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pricings as $pricing)
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td>
                                    <div>
                                        <div class="font-bold text-primary hover:text-primary-focus hover:underline cursor-pointer" onclick='showPricingDetails(@json($pricing))'>
                                            {{ $pricing->name }}
                                        </div>
                                        <div class="text-sm text-base-content/60">ID: {{ $pricing->pricing_id }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-col gap-2">
                                        <span class="badge {{ $pricing->pricing_type === 'booking' ? 'badge-primary' : 'badge-secondary' }} gap-1">
                                            @if($pricing->pricing_type === 'booking')
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            {{ ucfirst($pricing->pricing_type) }}
                                        </span>
                                        <span class="badge {{ $pricing->is_active ? 'badge-success' : 'badge-error' }} gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $pricing->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-base-content/80">
                                        {{ \App\Services\PricingCalculator::getPricingSummary($pricing) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-base-content/60">
                                        {{ Str::limit($pricing->notes, 50) ?: 'No notes' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('parameters.edit', ['type' => 'pricing', 'id' => $pricing->pricing_id]) }}?tab=pricing" 
                                           class="btn btn-ghost btn-sm" 
                                           title="Edit Pricing">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button class="btn btn-ghost btn-sm text-error" 
                                                onclick="openDeleteModal('pricing', '{{ $pricing->pricing_id }}', '{{ $pricing->name }}')"
                                                title="Delete Pricing">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        <div>
                                            <h3 class="text-lg font-semibold text-base-content">No pricing records found</h3>
                                            <p class="text-base-content/60">Get started by creating your first pricing parameter.</p>
                                        </div>
                                        <a href="{{ route('parameters.create') }}?tab=pricing" class="btn btn-primary">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Create First Pricing
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Amenities Tab -->
        <div id="amenities-section" class="{{ request()->get('tab') == 'amenities' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Amenity Details</th>
                            <th class="text-base-content font-semibold">Description</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($amenities as $amenity)
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td>
                                    <div>
                                        <div class="font-bold text-primary">{{ $amenity->name }}</div>
                                        <div class="text-sm text-base-content/60">ID: {{ $amenity->amenity_id }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-base-content/80">
                                        {{ Str::limit($amenity->description, 100) ?: 'No description' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('parameters.edit', ['type' => 'amenity', 'id' => $amenity->amenity_id]) }}?tab=amenities" 
                                           class="btn btn-ghost btn-sm" 
                                           title="Edit Amenity">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button class="btn btn-ghost btn-sm text-error" 
                                                onclick="openDeleteModal('amenity', '{{ $amenity->amenity_id }}', '{{ $amenity->name }}')"
                                                title="Delete Amenity">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                        <div>
                                            <h3 class="text-lg font-semibold text-base-content">No amenity records found</h3>
                                            <p class="text-base-content/60">Get started by creating your first amenity parameter.</p>
                                        </div>
                                        <a href="{{ route('parameters.create') }}?tab=amenities" class="btn btn-primary">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Create First Amenity
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Services Tab -->
        <div id="services-section" class="{{ request()->get('tab') == 'services' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Service Details</th>
                            <th class="text-base-content font-semibold">Description</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td>
                                    <div>
                                        <div class="font-bold text-primary">{{ $service->name }}</div>
                                        <div class="text-sm text-base-content/60">ID: {{ $service->service_id }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-base-content/80">
                                        {{ Str::limit($service->description, 100) ?: 'No description' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('parameters.edit', ['type' => 'service', 'id' => $service->service_id]) }}?tab=services" 
                                           class="btn btn-ghost btn-sm" 
                                           title="Edit Service">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button class="btn btn-ghost btn-sm text-error" 
                                                onclick="openDeleteModal('service', '{{ $service->service_id }}', '{{ $service->name }}')"
                                                title="Delete Service">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <div>
                                            <h3 class="text-lg font-semibold text-base-content">No service records found</h3>
                                            <p class="text-base-content/60">Get started by creating your first service parameter.</p>
                                        </div>
                                        <a href="{{ route('parameters.create') }}?tab=services" class="btn btn-primary">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Create First Service
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-base-300">
            <!-- Results Info -->
            <div class="text-sm text-base-content/60">
                Showing {{ $pricings->firstItem() ?? 0 }} to {{ $pricings->lastItem() ?? 0 }} of {{ $pricings->total() }} results
            </div>
            
            <!-- Pagination Links -->
            @if($pricings->hasPages())
                <div class="join">
                    {{-- Previous Page --}}
                    @if ($pricings->onFirstPage())
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $pricings->previousPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($pricings->getUrlRange(1, $pricings->lastPage()) as $page => $url)
                        @if ($page == $pricings->currentPage())
                            <button class="join-item btn btn-sm btn-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($pricings->hasMorePages())
                        <a href="{{ $pricings->nextPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @else
                <!-- Single page indicator -->
                <div class="text-sm text-base-content/60">
                    Page 1 of 1
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Delete Parameter</h3>
        <p class="py-4">Are you sure you want to delete <span id="deleteParameterName" class="font-semibold"></span>? This action cannot be undone.</p>
        <div class="modal-action">
            <button class="btn btn-ghost" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error">Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- Pricing Details Modal -->
<div id="pricingDetailsModal" class="modal">
    <div class="modal-box max-w-2xl">
        <h3 class="font-bold text-lg mb-4">Pricing Details</h3>
        <div id="pricingDetailsContent" class="space-y-3 text-sm"></div>
        <div class="modal-action">
            <button class="btn btn-ghost" onclick="closePricingDetailsModal()">Close</button>
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
    
    // Get tab from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    
    // Only set tab if there's a valid tab parameter, otherwise let server-side handle it
    if (tabParam && ['pricing', 'amenities', 'services'].includes(tabParam)) {
        showMainTab(tabParam, false);
    }
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

// Tab functionality
function showMainTab(tab, updateUrl = true) {
    document.getElementById('pricing-section').classList.add('hidden');
    document.getElementById('amenities-section').classList.add('hidden');
    document.getElementById('services-section').classList.add('hidden');
    document.getElementById('tab-pricing').classList.remove('tab-active');
    document.getElementById('tab-amenities').classList.remove('tab-active');
    document.getElementById('tab-services').classList.remove('tab-active');
    
    if(tab === 'pricing') {
        document.getElementById('pricing-section').classList.remove('hidden');
        document.getElementById('tab-pricing').classList.add('tab-active');
    } else if(tab === 'amenities') {
        document.getElementById('amenities-section').classList.remove('hidden');
        document.getElementById('tab-amenities').classList.add('tab-active');
    } else if(tab === 'services') {
        document.getElementById('services-section').classList.remove('hidden');
        document.getElementById('tab-services').classList.add('tab-active');
    }
    
    // Only update URL if explicitly requested (for user clicks)
    if (updateUrl) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('tab', tab);
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        
        // Use actual navigation instead of replaceState to update referrer
        if (window.location.href !== newUrl) {
            window.location.href = newUrl;
        }
    }
}

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const currentSection = document.querySelector('[id$="-section"]:not(.hidden)');
        const rows = currentSection.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// Type filter functionality
const typeFilter = document.getElementById('typeFilter');
if (typeFilter) {
    typeFilter.addEventListener('change', function() {
        const selectedType = this.value.toLowerCase();
        const rows = document.querySelectorAll('#pricing-section tbody tr');
        
        rows.forEach(row => {
            const typeCell = row.querySelector('td:nth-child(2)');
            if (typeCell) {
                const typeText = typeCell.textContent.toLowerCase();
                if (selectedType === '' || typeText.includes(selectedType)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
}

// Status filter functionality
const statusFilter = document.getElementById('statusFilter');
if (statusFilter) {
    statusFilter.addEventListener('change', function() {
        const selectedStatus = this.value.toLowerCase();
        const rows = document.querySelectorAll('#pricing-section tbody tr');
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(2)');
            if (statusCell) {
                const statusText = statusCell.textContent.toLowerCase();
                if (selectedStatus === '' || statusText.includes(selectedStatus)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
}

// Sort functionality
const sortBy = document.getElementById('sortBy');
if (sortBy) {
    sortBy.addEventListener('change', function() {
        const sortField = this.value;
        const currentSection = document.querySelector('[id$="-section"]:not(.hidden)');
        const tbody = currentSection.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(sortField) {
                case 'name':
                    aValue = a.querySelector('td:nth-child(1)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(1)').textContent.trim();
                    break;
                case 'type':
                    aValue = a.querySelector('td:nth-child(2)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(2)').textContent.trim();
                    break;
                case 'status':
                    aValue = a.querySelector('td:nth-child(2)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(2)').textContent.trim();
                    break;
                default:
                    return 0;
            }
            
            return aValue.localeCompare(bValue);
        });
        
        rows.forEach(row => tbody.appendChild(row));
    });
}

// Delete modal functionality
let deleteType = null;
let deleteId = null;

function openDeleteModal(type, id, name) {
    deleteType = type;
    deleteId = id;
    document.getElementById('deleteParameterName').textContent = name;
    document.getElementById('deleteForm').action = '{{ route("parameters.destroy") }}';
    document.getElementById('deleteModal').classList.add('modal-open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('modal-open');
}

// Add hidden inputs to delete form
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Add hidden inputs
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = deleteType;
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = deleteId;
    
    this.appendChild(typeInput);
    this.appendChild(idInput);
    
    // Submit the form
    this.submit();
});

// Pricing details modal functionality
function showPricingDetails(pricing) {
    let html = '';
    html += `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;
    html += `<div><b>Name:</b> ${pricing.name}</div>`;
    html += `<div><b>Type:</b> ${pricing.pricing_type}</div>`;
    html += `<div><b>Base Hourly Rate:</b> RM ${pricing.base_hourly_rate ?? '-'}</div>`;
    html += `<div><b>Base Daily Rate:</b> RM ${pricing.base_daily_rate ?? '-'}</div>`;
    html += `<div><b>Base Monthly Rate:</b> RM ${pricing.base_monthly_rate ?? '-'}</div>`;
    html += `<div><b>Base Yearly Rate:</b> RM ${pricing.base_yearly_rate ?? '-'}</div>`;
    html += `<div><b>Daily Hours Threshold:</b> ${pricing.daily_hours_threshold ?? '-'}</div>`;
    html += `<div><b>Daily Discount %:</b> ${pricing.daily_discount_percentage ?? '-'}%</div>`;
    html += `<div><b>Educational Discount %:</b> ${pricing.educational_discount_percentage ?? '-'}%</div>`;
    html += `<div><b>Corporate Discount %:</b> ${pricing.corporate_discount_percentage ?? '-'}%</div>`;
    html += `<div><b>Student Discount %:</b> ${pricing.student_discount_percentage ?? '-'}%</div>`;
    
    html += `<div><b>Min Booking Hours:</b> ${pricing.minimum_booking_hours ?? '-'}</div>`;
    html += `<div><b>Max Booking Hours:</b> ${pricing.maximum_booking_hours ?? '-'}</div>`;
    html += `<div><b>Status:</b> ${pricing.is_active ? 'Active' : 'Inactive'}</div>`;
    html += `</div>`;
    html += `<div class="mt-4"><b>Notes:</b> ${pricing.notes ?? 'No notes'}</div>`;
    
    document.getElementById('pricingDetailsContent').innerHTML = html;
    document.getElementById('pricingDetailsModal').classList.add('modal-open');
}

function closePricingDetailsModal() {
    document.getElementById('pricingDetailsModal').classList.remove('modal-open');
}
</script>
@endsection 