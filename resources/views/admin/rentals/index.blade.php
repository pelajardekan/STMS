@extends('layouts.sidebar')

@section('title', 'Manage Rentals')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Manage Rentals</h1>
                <p class="text-base-content/60 mt-1">View and manage all rentals</p>
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="flex-1">
                <div class="form-control">
                    <div class="input-group">
                        <input type="text" placeholder="Search rentals..." class="input input-bordered flex-1" id="searchInput" />
                        <button class="btn btn-square">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <select class="select select-bordered" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select class="select select-bordered" id="sortBy">
                    <option value="">Sort By</option>
                    <option value="tenant">Tenant</option>
                    <option value="property">Property</option>
                    <option value="status">Status</option>
                    <option value="created_at">Date Created</option>
                </select>
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

        <!-- Tabs -->
        <div role="tablist" class="tabs tabs-boxed mb-6">
            <a role="tab" class="tab {{ request()->get('tab', 'all') == 'all' ? 'tab-active' : '' }}" id="tab-all" onclick="showMainTab('all')">All Rentals</a>
            <a role="tab" class="tab {{ request()->get('tab') == 'active' ? 'tab-active' : '' }}" id="tab-active" onclick="showMainTab('active')">Active Rentals</a>
            <a role="tab" class="tab {{ request()->get('tab') == 'past' ? 'tab-active' : '' }}" id="tab-past" onclick="showMainTab('past')">Past Rentals</a>
        </div>

        <!-- All Rentals Section -->
        <div id="all-section" class="{{ request()->get('tab', 'all') == 'all' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Rental ID</th>
                            <th class="text-base-content font-semibold">Tenant</th>
                            <th class="text-base-content font-semibold">Property/Unit</th>
                            <th class="text-base-content font-semibold">Duration</th>
                            <th class="text-base-content font-semibold">Status</th>
                            <th class="text-base-content font-semibold">Created</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentals as $rental)
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td>
                                    <div>
                                        <div class="font-bold text-primary">R-{{ str_pad($rental->rental_id, 3, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-sm text-base-content/60">ID: {{ $rental->rental_id }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->rentalRequest->tenant->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->rentalRequest->tenant->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->rentalRequest->property->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->rentalRequest->unit->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->duration }} months</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->start_date->format('M d, Y') }} - {{ $rental->end_date->format('M d, Y') }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($rental->status === 'active')
                                        <span class="badge badge-success gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Active
                                        </span>
                                    @elseif($rental->status === 'completed')
                                        <span class="badge badge-neutral gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Completed
                                        </span>
                                    @else
                                        <span class="badge badge-error gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Cancelled
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-sm text-base-content/60">
                                        {{ $rental->created_at ? $rental->created_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('admin.rentals.edit', $rental) }}?tab={{ request()->get('tab', 'all') }}" 
                                           class="btn btn-ghost btn-sm" 
                                           title="Edit Rental">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button class="btn btn-ghost btn-sm text-error" 
                                                onclick="openDeleteModal({{ $rental->rental_id }}, '{{ $rental->rentalRequest->tenant->name }}')"
                                                title="Delete Rental">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <div class="text-base-content/60">No rentals found</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Active Rentals Section -->
        <div id="active-section" class="{{ request()->get('tab') == 'active' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Rental ID</th>
                            <th class="text-base-content font-semibold">Tenant</th>
                            <th class="text-base-content font-semibold">Property/Unit</th>
                            <th class="text-base-content font-semibold">Duration</th>
                            <th class="text-base-content font-semibold">Status</th>
                            <th class="text-base-content font-semibold">Created</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentals->where('status', 'active') as $rental)
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td>
                                    <div>
                                        <div class="font-bold text-primary">R-{{ str_pad($rental->rental_id, 3, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-sm text-base-content/60">ID: {{ $rental->rental_id }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->rentalRequest->tenant->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->rentalRequest->tenant->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->rentalRequest->property->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->rentalRequest->unit->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->duration }} months</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->start_date->format('M d, Y') }} - {{ $rental->end_date->format('M d, Y') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-success gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Active
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm text-base-content/60">
                                        {{ $rental->created_at ? $rental->created_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('admin.rentals.edit', $rental) }}?tab={{ request()->get('tab', 'all') }}" 
                                           class="btn btn-ghost btn-sm" 
                                           title="Edit Rental">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button class="btn btn-ghost btn-sm text-error" 
                                                onclick="openDeleteModal({{ $rental->rental_id }}, '{{ $rental->rentalRequest->tenant->name }}')"
                                                title="Delete Rental">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <div class="text-base-content/60">No active rentals found</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Past Rentals Section -->
        <div id="past-section" class="{{ request()->get('tab') == 'past' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Rental ID</th>
                            <th class="text-base-content font-semibold">Tenant</th>
                            <th class="text-base-content font-semibold">Property/Unit</th>
                            <th class="text-base-content font-semibold">Duration</th>
                            <th class="text-base-content font-semibold">Status</th>
                            <th class="text-base-content font-semibold">Created</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentals->whereIn('status', ['completed', 'cancelled']) as $rental)
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td>
                                    <div>
                                        <div class="font-bold text-primary">R-{{ str_pad($rental->rental_id, 3, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-sm text-base-content/60">ID: {{ $rental->rental_id }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->rentalRequest->tenant->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->rentalRequest->tenant->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->rentalRequest->property->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->rentalRequest->unit->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $rental->duration }} months</div>
                                        <div class="text-sm text-base-content/60">{{ $rental->start_date->format('M d, Y') }} - {{ $rental->end_date->format('M d, Y') }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($rental->status === 'completed')
                                        <span class="badge badge-neutral gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Completed
                                        </span>
                                    @else
                                        <span class="badge badge-error gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Cancelled
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-sm text-base-content/60">
                                        {{ $rental->created_at ? $rental->created_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('admin.rentals.edit', $rental) }}?tab={{ request()->get('tab', 'all') }}" 
                                           class="btn btn-ghost btn-sm" 
                                           title="Edit Rental">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button class="btn btn-ghost btn-sm text-error" 
                                                onclick="openDeleteModal({{ $rental->rental_id }}, '{{ $rental->rentalRequest->tenant->name }}')"
                                                title="Delete Rental">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <div class="text-base-content/60">No past rentals found</div>
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
                Showing {{ $rentals->firstItem() ?? 0 }} to {{ $rentals->lastItem() ?? 0 }} of {{ $rentals->total() }} results
            </div>
            
            <!-- Pagination Links -->
            @if($rentals->hasPages())
                <div class="join">
                    {{-- Previous Page --}}
                    @if ($rentals->onFirstPage())
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $rentals->previousPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($rentals->getUrlRange(1, $rentals->lastPage()) as $page => $url)
                        @if ($page == $rentals->currentPage())
                            <button class="join-item btn btn-sm btn-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($rentals->hasMorePages())
                        <a href="{{ $rentals->nextPageUrl() }}" class="join-item btn btn-sm">
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
        <h3 class="font-bold text-lg">Delete Rental</h3>
        <p class="py-4">Are you sure you want to delete the rental for <span id="deleteTenantName" class="font-semibold"></span>? This action cannot be undone.</p>
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

// Tab functionality
function showMainTab(tab, updateUrl = true) {
    document.getElementById('all-section').classList.add('hidden');
    document.getElementById('active-section').classList.add('hidden');
    document.getElementById('past-section').classList.add('hidden');
    document.getElementById('tab-all').classList.remove('tab-active');
    document.getElementById('tab-active').classList.remove('tab-active');
    document.getElementById('tab-past').classList.remove('tab-active');
    
    if(tab === 'all') {
        document.getElementById('all-section').classList.remove('hidden');
        document.getElementById('tab-all').classList.add('tab-active');
    } else if(tab === 'active') {
        document.getElementById('active-section').classList.remove('hidden');
        document.getElementById('tab-active').classList.add('tab-active');
    } else if(tab === 'past') {
        document.getElementById('past-section').classList.remove('hidden');
        document.getElementById('tab-past').classList.add('tab-active');
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

// Status filter functionality
const statusFilter = document.getElementById('statusFilter');
if (statusFilter) {
    statusFilter.addEventListener('change', function() {
        const selectedStatus = this.value.toLowerCase();
        const currentSection = document.querySelector('[id$="-section"]:not(.hidden)');
        const rows = currentSection.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(5)');
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
                case 'tenant':
                    aValue = a.querySelector('td:nth-child(2)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(2)').textContent.trim();
                    break;
                case 'property':
                    aValue = a.querySelector('td:nth-child(3)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(3)').textContent.trim();
                    break;
                case 'status':
                    aValue = a.querySelector('td:nth-child(5)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(5)').textContent.trim();
                    break;
                case 'created_at':
                    aValue = a.querySelector('td:nth-child(6)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(6)').textContent.trim();
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
function openDeleteModal(rentalId, tenantName) {
    document.getElementById('deleteTenantName').textContent = tenantName;
    document.getElementById('deleteForm').action = `/admin/rentals/${rentalId}`;
    document.getElementById('deleteModal').classList.add('modal-open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('modal-open');
}

// Initialize tabs on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get tab from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab') || 'all';
    
    // Show the appropriate tab without updating URL
    showMainTab(tabParam, false);
});
</script>
@endsection 