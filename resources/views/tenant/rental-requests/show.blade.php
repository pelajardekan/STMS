@extends('layouts.sidebar')

@section('title', 'Rental Request Details')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('tenant.rental-requests.index') }}" class="btn btn-ghost btn-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Rental Requests
                    </a>
                </div>
                <h1 class="text-3xl font-bold text-base-content">Rental Request #{{ $rentalRequest->rental_request_id }}</h1>
                <p class="text-base-content/60 mt-1">View rental request details and status</p>
            </div>
            <div class="flex items-center gap-3">
                @if($rentalRequest->status === 'pending')
                    <div class="badge badge-warning badge-lg">Pending</div>
                @elseif($rentalRequest->status === 'approved')
                    <div class="badge badge-success badge-lg">Approved</div>
                @elseif($rentalRequest->status === 'rejected')
                    <div class="badge badge-error badge-lg">Rejected</div>
                @else
                    <div class="badge badge-info badge-lg">{{ ucfirst($rentalRequest->status) }}</div>
                @endif
            </div>
        </div>

        <!-- Rental Request Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Request Information -->
            <div class="lg:col-span-2">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Request Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <h3 class="font-semibold text-base-content mb-2">Property Details</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Property:</span>
                                            <span class="font-semibold">{{ $rentalRequest->property->name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Address:</span>
                                            <span class="text-right">{{ $rentalRequest->property->address }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Type:</span>
                                            <span>{{ ucfirst($rentalRequest->property->type) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-base-content mb-2">Unit Details</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Unit:</span>
                                            <span class="font-semibold">{{ $rentalRequest->unit->name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Type:</span>
                                            <span>{{ ucfirst($rentalRequest->unit->type) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Leasing Type:</span>
                                            <span>{{ ucfirst($rentalRequest->unit->leasing_type) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h3 class="font-semibold text-base-content mb-2">Rental Period</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Start Date:</span>
                                            <span class="font-semibold">{{ $rentalRequest->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">End Date:</span>
                                            <span class="font-semibold">{{ $rentalRequest->end_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Duration:</span>
                                            <span>{{ $rentalRequest->duration }} {{ $rentalRequest->duration_type }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-base-content mb-2">Request Details</h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Status:</span>
                                            @if($rentalRequest->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($rentalRequest->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($rentalRequest->status === 'rejected')
                                                <span class="badge badge-error">Rejected</span>
                                            @else
                                                <span class="badge badge-info">{{ ucfirst($rentalRequest->status) }}</span>
                                            @endif
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-base-content/70">Submitted:</span>
                                            <span>{{ $rentalRequest->created_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        @if($rentalRequest->updated_at != $rentalRequest->created_at)
                                            <div class="flex justify-between">
                                                <span class="text-base-content/70">Last Updated:</span>
                                                <span>{{ $rentalRequest->updated_at->format('M d, Y H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($rentalRequest->notes)
                            <div class="mt-6">
                                <h3 class="font-semibold text-base-content mb-2">Additional Notes</h3>
                                <div class="bg-base-100 p-4 rounded-lg">
                                    <p class="text-base-content/80">{{ $rentalRequest->notes }}</p>
                                </div>
                            </div>
                        @endif

                        @if($rentalRequest->agreement_file_path)
                            <div class="mt-6">
                                <h3 class="font-semibold text-base-content mb-2">Agreement File</h3>
                                <div class="bg-base-100 p-4 rounded-lg">
                                    <a href="{{ Storage::url($rentalRequest->agreement_file_path) }}" target="_blank" class="btn btn-outline btn-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download Agreement
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Quick Actions</h2>
                        
                        <div class="space-y-4">
                            @if($rentalRequest->status === 'approved' && $rentalRequest->rental)
                                <a href="{{ route('tenant.rentals.show', $rentalRequest->rental) }}" class="btn btn-primary w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    View Rental
                                </a>
                            @endif

                            <a href="{{ route('tenant.properties.show', $rentalRequest->property) }}" class="btn btn-outline w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                View Property
                            </a>

                            <a href="{{ route('tenant.units.show', $rentalRequest->unit) }}" class="btn btn-outline w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                View Unit
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="card bg-base-200 mt-4">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Status Timeline</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-3 h-3 bg-success rounded-full mt-2"></div>
                                <div>
                                    <div class="font-semibold text-base-content">Request Submitted</div>
                                    <div class="text-sm text-base-content/60">{{ $rentalRequest->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>

                            @if($rentalRequest->status !== 'pending')
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 bg-{{ $rentalRequest->status === 'approved' ? 'success' : 'error' }} rounded-full mt-2"></div>
                                    <div>
                                        <div class="font-semibold text-base-content">Request {{ ucfirst($rentalRequest->status) }}</div>
                                        <div class="text-sm text-base-content/60">{{ $rentalRequest->updated_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($rentalRequest->status === 'approved' && $rentalRequest->rental)
                                <div class="flex items-start gap-3">
                                    <div class="w-3 h-3 bg-success rounded-full mt-2"></div>
                                    <div>
                                        <div class="font-semibold text-base-content">Rental Created</div>
                                        <div class="text-sm text-base-content/60">{{ $rentalRequest->rental->created_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 