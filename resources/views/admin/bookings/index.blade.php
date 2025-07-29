@extends('layouts.sidebar')

@section('title', 'Manage Bookings')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Manage Bookings</h1>
                <p class="text-base-content/60 mt-1">View and manage all bookings</p>
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="flex-1">
                <div class="form-control">
                    <div class="input-group">
                        <input type="text" placeholder="Search bookings..." class="input input-bordered flex-1" id="searchInput" />
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
                <select class="select select-bordered" id="durationTypeFilter">
                    <option value="">All Duration Types</option>
                    <option value="hourly">Hourly</option>
                    <option value="daily">Daily</option>
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
            <a role="tab" class="tab {{ request()->get('tab', 'all') == 'all' ? 'tab-active' : '' }}" id="tab-all" onclick="showMainTab('all')">All Bookings</a>
            <a role="tab" class="tab {{ request()->get('tab') == 'active' ? 'tab-active' : '' }}" id="tab-active" onclick="showMainTab('active')">Active Bookings</a>
            <a role="tab" class="tab {{ request()->get('tab') == 'past' ? 'tab-active' : '' }}" id="tab-past" onclick="showMainTab('past')">Past Bookings</a>
        </div>

        <!-- All Bookings Section -->
        <div id="all-section" class="{{ request()->get('tab', 'all') == 'all' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Booking ID</th>
                            <th class="text-base-content font-semibold">Tenant</th>
                            <th class="text-base-content font-semibold">Property/Unit</th>
                            <th class="text-base-content font-semibold">Date & Time</th>
                            <th class="text-base-content font-semibold">Duration</th>
                            <th class="text-base-content font-semibold">Status</th>
                            <th class="text-base-content font-semibold">Created</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td>
                                    <div>
                                        <div class="font-bold text-primary">B-{{ str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-sm text-base-content/60">ID: {{ $booking->booking_id }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $booking->bookingRequest->tenant->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $booking->bookingRequest->tenant->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $booking->bookingRequest->property->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $booking->bookingRequest->unit->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $booking->date->format('M d, Y') }}</div>
                                        <div class="text-sm text-base-content/60">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium text-base-content">{{ $booking->duration }} {{ $booking->duration_type === 'hourly' ? 'hours' : 'days' }}</div>
                                        <div class="text-sm text-base-content/60">
                                            <span class="badge {{ $booking->duration_type === 'hourly' ? 'badge-primary' : 'badge-secondary' }} gap-1">
                                                @if($booking->duration_type === 'hourly')
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                                {{ ucfirst($booking->duration_type) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($booking->status === 'active')
                                        <span class="badge badge-success gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Active
                                        </span>
                                    @elseif($booking->status === 'completed')
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
                                        {{ $booking->created_at ? $booking->created_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('admin.bookings.edit', $booking) }}?tab={{ request()->get('tab', 'all') }}" 
                                           class="btn btn-ghost btn-sm" 
                                           title="Edit Booking">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button class="btn btn-ghost btn-sm text-error" 
                                                onclick="openDeleteModal({{ $booking->booking_id }}, '{{ $booking->bookingRequest->tenant->name }}')"
                                                title="Delete Booking">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <div class="text-base-content/60">No bookings found</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Active Bookings Table -->
        <div id="active" class="hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Booking ID</th>
                            <th class="text-base-content font-semibold">Tenant</th>
                            <th class="text-base-content font-semibold">Property/Unit</th>
                            <th class="text-base-content font-semibold">Date & Time</th>
                            <th class="text-base-content font-semibold">Duration</th>
                            <th class="text-base-content font-semibold">Status</th>
                            <th class="text-base-content font-semibold">Created</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            @if($booking->status === 'active')
                                <tr class="hover:bg-base-200/50 transition-colors">
                                    <td>
                                        <div>
                                            <div class="font-bold text-primary">B-{{ str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-sm text-base-content/60">ID: {{ $booking->booking_id }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">{{ $booking->bookingRequest->tenant->name }}</div>
                                            <div class="text-sm text-base-content/60">{{ $booking->bookingRequest->tenant->email }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">{{ $booking->bookingRequest->property->name }}</div>
                                            <div class="text-sm text-base-content/60">{{ $booking->bookingRequest->unit->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">{{ $booking->date->format('M d, Y') }}</div>
                                            <div class="text-sm text-base-content/60">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">{{ $booking->duration }} {{ $booking->duration_type === 'hourly' ? 'hours' : 'days' }}</div>
                                            <div class="text-sm text-base-content/60">
                                                <span class="badge {{ $booking->duration_type === 'hourly' ? 'badge-primary' : 'badge-secondary' }} gap-1">
                                                    @if($booking->duration_type === 'hourly')
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                    {{ ucfirst($booking->duration_type) }}
                                                </span>
                                            </div>
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
                                            {{ $booking->created_at ? $booking->created_at->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex justify-center gap-1">
                                            <a href="{{ route('admin.bookings.edit', $booking) }}" 
                                               class="btn btn-ghost btn-sm" 
                                               title="Edit Booking">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Past Bookings Table -->
        <div id="past" class="hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Booking ID</th>
                            <th class="text-base-content font-semibold">Tenant</th>
                            <th class="text-base-content font-semibold">Property/Unit</th>
                            <th class="text-base-content font-semibold">Date & Time</th>
                            <th class="text-base-content font-semibold">Duration</th>
                            <th class="text-base-content font-semibold">Status</th>
                            <th class="text-base-content font-semibold">Created</th>
                            <th class="text-base-content font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            @if($booking->status === 'completed' || $booking->status === 'cancelled')
                                <tr class="hover:bg-base-200/50 transition-colors">
                                    <td>
                                        <div>
                                            <div class="font-bold text-primary">B-{{ str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-sm text-base-content/60">ID: {{ $booking->booking_id }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">{{ $booking->bookingRequest->tenant->name }}</div>
                                            <div class="text-sm text-base-content/60">{{ $booking->bookingRequest->tenant->email }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">{{ $booking->bookingRequest->property->name }}</div>
                                            <div class="text-sm text-base-content/60">{{ $booking->bookingRequest->unit->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">{{ $booking->date->format('M d, Y') }}</div>
                                            <div class="text-sm text-base-content/60">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">{{ $booking->duration }} {{ $booking->duration_type === 'hourly' ? 'hours' : 'days' }}</div>
                                            <div class="text-sm text-base-content/60">
                                                <span class="badge {{ $booking->duration_type === 'hourly' ? 'badge-primary' : 'badge-secondary' }} gap-1">
                                                    @if($booking->duration_type === 'hourly')
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                    {{ ucfirst($booking->duration_type) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($booking->status === 'completed')
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
                                            {{ $booking->created_at ? $booking->created_at->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex justify-center gap-1">
                                            <a href="{{ route('admin.bookings.edit', $booking) }}" 
                                               class="btn btn-ghost btn-sm" 
                                               title="View Booking">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-base-300">
            <!-- Results Info -->
            <div class="text-sm text-base-content/60">
                Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} results
            </div>
            
            <!-- Pagination Links -->
            @if($bookings->hasPages())
                <div class="join">
                    {{-- Previous Page --}}
                    @if ($bookings->onFirstPage())
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $bookings->previousPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                        @if ($page == $bookings->currentPage())
                            <button class="join-item btn btn-sm btn-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($bookings->hasMorePages())
                        <a href="{{ $bookings->nextPageUrl() }}" class="join-item btn btn-sm">
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
        <h3 class="font-bold text-lg">Delete Booking</h3>
        <p class="py-4">Are you sure you want to delete the booking for <span id="deleteTenantName" class="font-semibold"></span>? This action cannot be undone.</p>
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

// Tab functionality
function showMainTab(tab) {
    // Hide all tab content
    document.getElementById('all-section').classList.add('hidden');
    document.getElementById('active').classList.add('hidden');
    document.getElementById('past').classList.add('hidden');
    
    // Remove active class from all tabs
    document.getElementById('tab-all').classList.remove('tab-active');
    document.getElementById('tab-active').classList.remove('tab-active');
    document.getElementById('tab-past').classList.remove('tab-active');
    
    // Show selected tab content and add active class
    if(tab === 'all') {
        document.getElementById('all-section').classList.remove('hidden');
        document.getElementById('tab-all').classList.add('tab-active');
    } else if(tab === 'active') {
        document.getElementById('active').classList.remove('hidden');
        document.getElementById('tab-active').classList.add('tab-active');
    } else {
        document.getElementById('past').classList.remove('hidden');
        document.getElementById('tab-past').classList.add('tab-active');
    }
}

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
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
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(6)');
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

// Duration type filter functionality
const durationTypeFilter = document.getElementById('durationTypeFilter');
if (durationTypeFilter) {
    durationTypeFilter.addEventListener('change', function() {
        const selectedDurationType = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const durationTypeCell = row.querySelector('td:nth-child(5)');
            if (durationTypeCell) {
                const durationTypeText = durationTypeCell.textContent.toLowerCase();
                if (selectedDurationType === '' || durationTypeText.includes(selectedDurationType)) {
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
        const tbody = document.querySelector('tbody');
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
                    aValue = a.querySelector('td:nth-child(6)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(6)').textContent.trim();
                    break;
                case 'created_at':
                    aValue = a.querySelector('td:nth-child(7)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(7)').textContent.trim();
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
function openDeleteModal(bookingId, tenantName) {
    document.getElementById('deleteTenantName').textContent = tenantName;
    document.getElementById('deleteForm').action = `/admin/bookings/${bookingId}`;
    document.getElementById('deleteModal').classList.add('modal-open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('modal-open');
}

// Initialize tabs on page load
document.addEventListener('DOMContentLoaded', function() {
    showMainTab('all');
});
</script>
@endsection 