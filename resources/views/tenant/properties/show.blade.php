@extends('layouts.sidebar')

@section('title', $property->name)

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('tenant.properties.index') }}" class="btn btn-ghost btn-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Properties
                    </a>
                </div>
                <h1 class="text-3xl font-bold text-base-content">{{ $property->name }}</h1>
                <p class="text-base-content/60 mt-1">{{ $property->address }}</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="badge badge-primary badge-lg">{{ ucfirst($property->type) }}</div>
                <div class="badge badge-success badge-lg">{{ ucfirst($property->status) }}</div>
            </div>
        </div>

        <!-- Property Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Property Information -->
            <div class="lg:col-span-2">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Property Information</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-base-content">Address</h3>
                                    <p class="text-base-content/70">{{ $property->address }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-primary mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-base-content">Property Type</h3>
                                    <p class="text-base-content/70">{{ ucfirst($property->type) }}</p>
                                </div>
                            </div>

                            @if($property->description)
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-primary mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <h3 class="font-semibold text-base-content">Description</h3>
                                        <p class="text-base-content/70">{{ $property->description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="lg:col-span-1">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Quick Stats</h2>
                        
                        <div class="space-y-4">
                            <div class="stat">
                                <div class="stat-title">Total Units</div>
                                <div class="stat-value text-primary">{{ $property->units->count() }}</div>
                            </div>
                            
                            <div class="stat">
                                <div class="stat-title">Rental Units</div>
                                <div class="stat-value text-secondary">{{ $property->units->where('leasing_type', 'rental')->count() }}</div>
                            </div>
                            
                            <div class="stat">
                                <div class="stat-title">Booking Units</div>
                                <div class="stat-value text-accent">{{ $property->units->where('leasing_type', 'booking')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Units -->
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title text-xl mb-6">Available Units</h2>
                
                @if($property->units->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($property->units as $unit)
                            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                                <div class="card-body">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="card-title text-lg">{{ $unit->name }}</h3>
                                        <div class="badge badge-{{ $unit->leasing_type === 'rental' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($unit->leasing_type) }}
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm text-base-content/70">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ ucfirst($unit->type) }}
                                        </div>
                                        
                                        <div class="flex items-center text-sm text-base-content/70">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ ucfirst($unit->status) }}
                                        </div>

                                        @if($unit->leasing_type === 'rental')
                                            <div class="flex items-center text-sm text-base-content/70">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ ucfirst($unit->availability) }}
                                            </div>
                                        @endif
                                    </div>

                                    @if($unit->description)
                                        <p class="text-sm text-base-content/60 mb-4">
                                            {{ Str::limit($unit->description, 80) }}
                                        </p>
                                    @endif

                                    <div class="card-actions justify-end">
                                        <a href="{{ route('tenant.units.show', $unit) }}" class="btn btn-primary btn-sm">
                                            View Details
                                        </a>
                                        @if($unit->leasing_type === 'rental' && $unit->availability === 'available')
                                            <a href="{{ route('tenant.rental-requests.create', ['unit_id' => $unit->unit_id]) }}" class="btn btn-secondary btn-sm">
                                                Request Rental
                                            </a>
                                        @elseif($unit->leasing_type === 'booking')
                                            <a href="{{ route('tenant.booking-requests.create', ['unit_id' => $unit->unit_id]) }}" class="btn btn-secondary btn-sm">
                                                Request Booking
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-base-content mb-2">No Units Available</h3>
                        <p class="text-base-content/60">This property currently has no units available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 