@extends('layouts.sidebar')
@section('title', 'Add Property')
@section('content')
<x-form wire:submit="store" no-separator>
    <x-card title="Add New Property">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input label="Property Name" wire:model="name" placeholder="Enter property name" />
            
            <x-select label="Property Type" wire:model="type" placeholder="Select property type">
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="condo">Condo</option>
                <option value="office">Office</option>
                <option value="commercial">Commercial</option>
                <option value="industrial">Industrial</option>
            </x-select>
            
            <x-select label="Status" wire:model="status" placeholder="Select status">
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Maintenance</option>
                <option value="unavailable">Unavailable</option>
            </x-select>
            
            <x-input label="Address" wire:model="address" placeholder="Enter property address" />
        </div>
        
        <x-textarea label="Description" wire:model="description" placeholder="Enter property description" rows="4" />
        
        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('properties.index') }}" class="btn-outline" />
            <x-button label="Create Property" type="submit" class="btn-primary" spinner="store" />
        </x-slot:actions>
    </x-card>
</x-form>
@endsection 