@extends('layouts.sidebar')

@section('title', 'Manage Units')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Manage Units</h1>
                <p class="text-base-content/60 mt-1">Manage all units in the system</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('units.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Unit
                </a>
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
                <select class="select select-bordered" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="unactive">Unactive</option>
                </select>
                <select class="select select-bordered" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="Kitchen">Kitchen</option>
                    <option value="Event Hall">Event Hall</option>
                    <option value="Room">Room</option>
                    <option value="Office">Office</option>
                    <option value="Shop">Shop</option>
                    <option value="Warehouse">Warehouse</option>
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

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th class="text-base-content font-semibold">Unit</th>
                        <th class="text-base-content font-semibold">Property</th>
                        <th class="text-base-content font-semibold">Type</th>
                        <th class="text-base-content font-semibold">Leasing Type</th>
                        <th class="text-base-content font-semibold">Availability</th>
                        <th class="text-base-content font-semibold">Status</th>
                        <th class="text-base-content font-semibold">Pricing</th>
                        <th class="text-base-content font-semibold">Services</th>
                        <th class="text-base-content font-semibold">Amenities</th>
                        <th class="text-base-content font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td>
                                <div>
                                    <div class="font-bold text-primary">{{ $unit->name }}</div>
                                    <div class="text-sm text-base-content/60">{{ $unit->description ?: 'No description' }}</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <a href="{{ route('properties.units.index', $unit->property->property_id) }}" class="font-medium text-primary hover:text-primary-focus hover:underline cursor-pointer">
                                        {{ $unit->property->name }}
                                    </a>
                                    <div class="text-sm text-base-content/60">{{ $unit->property->address }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-primary gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2h6v4H7V6zm8 8v2h1v-2h-1zm-2 2v2h1v-2h-1zm-2 2v2h1v-2h-1zm-2 2v2h1v-2h-1zm-2 2v2h1v-2h-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $unit->type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ ucfirst($unit->leasing_type) }}
                                </span>
                            </td>
                            <td>
                                @if($unit->leasing_type === 'booking')
                                    <span class="badge badge-success gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Always Available
                                    </span>
                                @elseif($unit->availability === 'available')
                                    <span class="badge badge-success gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Available
                                    </span>
                                @else
                                    <span class="badge badge-error gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Not Available
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($unit->status === 'active')
                                    <span class="badge badge-success gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Active
                                    </span>
                                @else
                                    <span class="badge badge-neutral gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Unactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $pricing = $unit->propertyUnitParameters->where('pricing_id', '!=', null)->first();
                                @endphp
                                @if($pricing && $pricing->pricing)
                                    <div class="text-sm">
                                        <div class="font-medium">{{ $pricing->pricing->name }}</div>
                                        <div class="text-base-content/60">RM{{ $pricing->pricing->price_amount }}</div>
                                    </div>
                                @else
                                    <span class="text-sm text-base-content/40">No pricing</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $services = $unit->propertyUnitParameters->where('service_id', '!=', null);
                                @endphp
                                @if($services->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($services as $serviceParam)
                                            @if($serviceParam->service)
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border border-base-300 bg-base-100 text-base-content whitespace-normal break-words max-w-full leading-tight">{{ $serviceParam->service->name }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-base-content/40">No services</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $amenities = $unit->propertyUnitParameters->where('amenity_id', '!=', null);
                                @endphp
                                @if($amenities->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($amenities as $amenityParam)
                                            @if($amenityParam->amenity)
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border border-base-300 bg-base-100 text-base-content whitespace-normal break-words max-w-full leading-tight">{{ $amenityParam->amenity->name }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-base-content/40">No amenities</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('units.edit', $unit->unit_id) }}" 
                                       class="btn btn-ghost btn-sm" 
                                       title="Edit Unit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button class="btn btn-ghost btn-sm text-error" 
                                            onclick="openDeleteModal('{{ $unit->unit_id }}')"
                                            title="Delete Unit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-12">
                                <div class="flex flex-col items-center gap-4">
                                    <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-semibold text-base-content">No units found</h3>
                                        <p class="text-base-content/60">Get started by creating your first unit.</p>
                                    </div>
                                    <a href="{{ route('units.create') }}" class="btn btn-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Add Unit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-base-300">
            <!-- Results Info -->
            <div class="text-sm text-base-content/60">
                Showing {{ $units->firstItem() ?? 0 }} to {{ $units->lastItem() ?? 0 }} of {{ $units->total() }} results
            </div>
            
            <!-- Pagination Links -->
            @if($units->hasPages())
                <div class="join">
                    {{-- Previous Page --}}
                    @if ($units->onFirstPage())
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $units->previousPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($units->getUrlRange(1, $units->lastPage()) as $page => $url)
                        @if ($page == $units->currentPage())
                            <button class="join-item btn btn-sm btn-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($units->hasMorePages())
                        <a href="{{ $units->nextPageUrl() }}" class="join-item btn btn-sm">
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
<input type="checkbox" id="deleteUnitModal" class="modal-toggle" />
<div class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Are you sure you want to delete this unit?</h3>
        <div class="modal-action">
            <label for="deleteUnitModal" class="btn">Cancel</label>
            <button class="btn btn-error" id="confirmDeleteUnitBtn">Yes</button>
        </div>
    </div>
</div>

<script>
    let deleteUnitId = null;

    function openDeleteModal(unitId) {
        deleteUnitId = unitId;
        document.getElementById('deleteUnitModal').checked = true;
    }

    document.getElementById('confirmDeleteUnitBtn').onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("units.destroy", ":unitId") }}'.replace(':unitId', deleteUnitId);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    };

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

    // Search, Filter, and Sort functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const typeFilter = document.getElementById('typeFilter');
        const sortBy = document.getElementById('sortBy');
        const tableRows = document.querySelectorAll('tbody tr');

        function filterAndSort() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const typeValue = typeFilter.value;
            const sortValue = sortBy.value;

            let visibleRows = Array.from(tableRows).filter(row => {
                const name = row.querySelector('td:first-child .font-bold')?.textContent.toLowerCase() || '';
                const description = row.querySelector('td:first-child .text-sm')?.textContent.toLowerCase() || '';
                const propertyName = row.querySelector('td:nth-child(2) .font-medium')?.textContent.toLowerCase() || '';
                const propertyAddress = row.querySelector('td:nth-child(2) .text-sm')?.textContent.toLowerCase() || '';
                const type = row.querySelector('td:nth-child(3) .badge')?.textContent.trim() || '';
                const status = row.querySelector('td:nth-child(4) .badge')?.textContent.trim() || '';

                // Search filter
                const matchesSearch = name.includes(searchTerm) || description.includes(searchTerm) || 
                                    propertyName.includes(searchTerm) || propertyAddress.includes(searchTerm);
                
                // Status filter
                const matchesStatus = !statusValue || status.toLowerCase().includes(statusValue);
                
                // Type filter
                const matchesType = !typeValue || type.toLowerCase().includes(typeValue);

                return matchesSearch && matchesStatus && matchesType;
            });

            // Sort rows
            if (sortValue) {
                visibleRows.sort((a, b) => {
                    let aValue, bValue;
                    
                    switch(sortValue) {
                        case 'name':
                            aValue = a.querySelector('td:first-child .font-bold')?.textContent || '';
                            bValue = b.querySelector('td:first-child .font-bold')?.textContent || '';
                            break;
                        case 'type':
                            aValue = a.querySelector('td:nth-child(3) .badge')?.textContent || '';
                            bValue = b.querySelector('td:nth-child(3) .badge')?.textContent || '';
                            break;
                        case 'status':
                            aValue = a.querySelector('td:nth-child(4) .badge')?.textContent || '';
                            bValue = b.querySelector('td:nth-child(4) .badge')?.textContent || '';
                            break;
                        default:
                            return 0;
                    }
                    
                    return aValue.localeCompare(bValue);
                });
            }

            // Show/hide rows
            tableRows.forEach(row => {
                row.style.display = 'none';
            });

            visibleRows.forEach(row => {
                row.style.display = '';
            });

            // Show empty state if no results
            const tbody = document.querySelector('tbody');
            const emptyRow = tbody.querySelector('tr[colspan]');
            if (emptyRow) {
                emptyRow.style.display = visibleRows.length === 0 ? '' : 'none';
            }
        }

        // Event listeners
        searchInput.addEventListener('input', filterAndSort);
        statusFilter.addEventListener('change', filterAndSort);
        typeFilter.addEventListener('change', filterAndSort);
        sortBy.addEventListener('change', filterAndSort);
    });
</script>
@endsection 