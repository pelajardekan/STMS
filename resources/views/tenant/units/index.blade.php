@extends('layouts.sidebar')

@section('title', 'Available Units')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Available Units</h1>
                <p class="text-base-content/60 mt-1">Browse and explore available units for rental and booking</p>
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="flex-1">
                <div class="form-control">
                    <div class="input-group">
                        <input type="text" placeholder="Search units..." class="input input-bordered flex-1" id="searchInput" />
                        <button class="btn btn-square">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <select class="select select-bordered" id="propertyFilter">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->property_id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
                <select class="select select-bordered" id="leasingTypeFilter">
                    <option value="">All Types</option>
                    <option value="rental">Rental</option>
                    <option value="booking">Booking</option>
                </select>
                <select class="select select-bordered" id="sortBy">
                    <option value="">Sort By</option>
                    <option value="name">Name</option>
                    <option value="type">Type</option>
                    <option value="property">Property</option>
                    <option value="created_at">Date Added</option>
                </select>
            </div>
        </div>

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

        <!-- Units Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="unitsGrid">
            @forelse($units as $unit)
                <div class="card bg-base-200 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="card-title text-lg">{{ $unit->name }}</h2>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $unit->property->name }}
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
                                {{ Str::limit($unit->description, 100) }}
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
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-base-content mb-2">No Units Available</h3>
                        <p class="text-base-content/60">There are currently no units available for viewing.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($units->hasPages())
            <div class="mt-8">
                {{ $units->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function hideAlert(alertId) {
    document.getElementById(alertId).style.display = 'none';
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 5000);
});

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterUnits);
document.getElementById('propertyFilter').addEventListener('change', filterUnits);
document.getElementById('leasingTypeFilter').addEventListener('change', filterUnits);
document.getElementById('sortBy').addEventListener('change', filterUnits);

function filterUnits() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const propertyFilter = document.getElementById('propertyFilter').value;
    const leasingTypeFilter = document.getElementById('leasingTypeFilter').value.toLowerCase();
    const sortBy = document.getElementById('sortBy').value;
    
    const units = document.querySelectorAll('#unitsGrid > div');
    
    units.forEach(function(unit) {
        const name = unit.querySelector('.card-title').textContent.toLowerCase();
        const propertyName = unit.querySelectorAll('.flex.items-center.text-sm.text-base-content\\/70')[1].textContent.trim();
        const leasingType = unit.querySelector('.badge').textContent.toLowerCase();
        
        const matchesSearch = name.includes(searchTerm);
        const matchesProperty = !propertyFilter || propertyName.includes(propertyFilter);
        const matchesLeasingType = !leasingTypeFilter || leasingType.includes(leasingTypeFilter);
        
        if (matchesSearch && matchesProperty && matchesLeasingType) {
            unit.style.display = 'block';
        } else {
            unit.style.display = 'none';
        }
    });
}
</script>
@endsection 