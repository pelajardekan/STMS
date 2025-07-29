@extends('layouts.sidebar')

@section('title', $unit->name)

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('tenant.units.index') }}" class="btn btn-ghost btn-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Units
                    </a>
                </div>
                <h1 class="text-3xl font-bold text-base-content">{{ $unit->name }}</h1>
                <p class="text-base-content/60 mt-1">{{ $unit->property->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="badge badge-{{ $unit->leasing_type === 'rental' ? 'primary' : 'secondary' }} badge-lg">
                    {{ ucfirst($unit->leasing_type) }}
                </div>
                <div class="badge badge-success badge-lg">{{ ucfirst($unit->status) }}</div>
                @if($unit->leasing_type === 'rental')
                    <div class="badge badge-{{ $unit->availability === 'available' ? 'success' : 'warning' }} badge-lg">
                        {{ ucfirst($unit->availability) }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Unit Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Unit Information -->
            <div class="lg:col-span-2">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Unit Information</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-base-content">Unit Type</h3>
                                    <p class="text-base-content/70">{{ ucfirst($unit->type) }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-base-content">Property</h3>
                                    <p class="text-base-content/70">{{ $unit->property->name }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-base-content">Property Address</h3>
                                    <p class="text-base-content/70">{{ $unit->property->address }}</p>
                                </div>
                            </div>

                            @if($unit->description)
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-primary mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-base-content">Description</h3>
                                        <p class="text-base-content/70">{{ $unit->description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Quick Actions</h2>
                        
                        <div class="space-y-4">
                            @if($unit->leasing_type === 'rental' && $unit->availability === 'available')
                                <a href="{{ route('tenant.rental-requests.create', ['unit_id' => $unit->unit_id]) }}" class="btn btn-primary w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Request Rental
                                </a>
                            @elseif($unit->leasing_type === 'booking')
                                <a href="{{ route('tenant.booking-requests.create', ['unit_id' => $unit->unit_id]) }}" class="btn btn-primary w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Request Booking
                                </a>
                            @else
                                <div class="alert alert-warning">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <span>This unit is currently not available for requests.</span>
                                </div>
                            @endif

                            <a href="{{ route('tenant.properties.show', $unit->property) }}" class="btn btn-outline w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                View Property
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Unit Status -->
                <div class="card bg-base-200 mt-4">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Unit Status</h2>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-base-content/70">Status:</span>
                                <span class="badge badge-success">{{ ucfirst($unit->status) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-base-content/70">Type:</span>
                                <span class="badge badge-primary">{{ ucfirst($unit->type) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-base-content/70">Leasing Type:</span>
                                <span class="badge badge-{{ $unit->leasing_type === 'rental' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst($unit->leasing_type) }}
                                </span>
                            </div>

                            @if($unit->leasing_type === 'rental')
                                <div class="flex justify-between items-center">
                                    <span class="text-base-content/70">Availability:</span>
                                    <span class="badge badge-{{ $unit->availability === 'available' ? 'success' : 'warning' }}">
                                        {{ ucfirst($unit->availability) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unit Parameters (if any) -->
        @if($unit->propertyUnitParameters->count() > 0)
            <div class="card bg-base-200 mb-8">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">Unit Features & Pricing</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($unit->propertyUnitParameters as $parameter)
                            <div class="card bg-base-100">
                                <div class="card-body">
                                    @if($parameter->pricing)
                                        <h3 class="font-semibold text-base-content mb-2">Pricing Information</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-base-content/70">Base Rate:</span>
                                                <span class="font-semibold">RM {{ number_format($parameter->pricing->base_rate, 2) }}</span>
                                            </div>
                                            @if($parameter->pricing->peak_hour_rate)
                                                <div class="flex justify-between">
                                                    <span class="text-base-content/70">Peak Hour Rate:</span>
                                                    <span class="font-semibold">RM {{ number_format($parameter->pricing->peak_hour_rate, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($parameter->pricing->rental_duration)
                                                <div class="flex justify-between">
                                                    <span class="text-base-content/70">Duration:</span>
                                                    <span class="font-semibold">{{ $parameter->pricing->rental_duration }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($parameter->amenity)
                                        <h3 class="font-semibold text-base-content mb-2">Amenity</h3>
                                        <p class="text-base-content/70">{{ $parameter->amenity->name }}</p>
                                        @if($parameter->amenity->description)
                                            <p class="text-sm text-base-content/60 mt-1">{{ $parameter->amenity->description }}</p>
                                        @endif
                                    @endif

                                    @if($parameter->service)
                                        <h3 class="font-semibold text-base-content mb-2">Service</h3>
                                        <p class="text-base-content/70">{{ $parameter->service->name }}</p>
                                        @if($parameter->service->description)
                                            <p class="text-sm text-base-content/60 mt-1">{{ $parameter->service->description }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 