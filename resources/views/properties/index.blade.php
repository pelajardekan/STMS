@extends('layouts.sidebar')
@section('title', 'Properties')
@section('content')
<!-- Search and Filters -->
<x-card>
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex flex-col md:flex-row gap-4 flex-1">
            <x-input placeholder="Search properties..." class="w-full md:w-64" />
            <x-select placeholder="Filter by type" class="w-full md:w-48">
                <option value="">All Types</option>
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="condo">Condo</option>
                <option value="office">Office</option>
            </x-select>
            <x-select placeholder="Filter by status" class="w-full md:w-48">
                <option value="">All Status</option>
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Maintenance</option>
            </x-select>
        </div>
        <x-button label="Add Property" link="{{ route('properties.create') }}" class="btn-primary" />
    </div>
</x-card>

<!-- Success/Error Messages -->
@if(session('success'))
    <x-alert title="Success!" description="{{ session('success') }}" icon="o-check-circle" class="alert-success" />
@endif

@if($errors->any())
    <x-alert title="Error!" description="Please fix the following errors:" icon="o-exclamation-triangle" class="alert-error">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

<!-- Properties Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
    @forelse($properties as $property)
        <x-card>
            <x-slot:figure>
                <div class="h-48 bg-base-200 rounded-t-lg flex items-center justify-center">
                    <x-icon name="o-building-office" class="w-16 h-16 text-base-content/40" />
                </div>
            </x-slot:figure>
            
            <x-slot:body>
                <h3 class="text-lg font-semibold">{{ $property->name }}</h3>
                <p class="text-base-content/60 text-sm">{{ $property->address }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <x-badge value="{{ ucfirst($property->type) }}" class="badge-primary" />
                    <x-badge value="{{ ucfirst($property->status) }}" class="badge-{{ $property->status === 'available' ? 'success' : ($property->status === 'occupied' ? 'warning' : 'error') }}" />
                </div>
                @if($property->description)
                    <p class="text-base-content/70 text-sm mt-2 line-clamp-2">{{ $property->description }}</p>
                @endif
            </x-slot:body>
            
            <x-slot:actions>
                <x-button label="View" link="{{ route('properties.show', $property->property_id) }}" class="btn-primary btn-sm" />
                <x-button label="Edit" link="{{ route('properties.edit', $property->property_id) }}" class="btn-outline btn-sm" />
                <form method="POST" action="{{ route('properties.destroy', $property->property_id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this property?')">
                    @csrf
                    @method('DELETE')
                    <x-button label="Delete" type="submit" class="btn-error btn-sm" />
                </form>
            </x-slot:actions>
        </x-card>
    @empty
        <div class="col-span-full">
            <x-card>
                <div class="text-center py-8">
                    <x-icon name="o-building-office" class="w-16 h-16 text-base-content/40 mx-auto mb-4" />
                    <h3 class="text-lg font-semibold mb-2">No Properties Found</h3>
                    <p class="text-base-content/60 mb-4">Get started by adding your first property.</p>
                    <x-button label="Add Property" link="{{ route('properties.create') }}" class="btn-primary" />
                </div>
            </x-card>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($properties->hasPages())
    <div class="mt-6">
        {{ $properties->links() }}
    </div>
@endif
@endsection 