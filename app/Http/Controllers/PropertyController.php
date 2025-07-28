<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::latest()->paginate(10);
        return view('properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('properties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:residential,commercial,industrial,mixed',
            'address' => 'required|string|max:500',
            'status' => 'required|in:available,occupied,maintenance,unavailable',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Property name is required.',
            'name.max' => 'Property name cannot exceed 255 characters.',
            'type.required' => 'Property type is required.',
            'type.in' => 'Please select a valid property type.',
            'address.required' => 'Property address is required.',
            'address.max' => 'Property address cannot exceed 500 characters.',
            'status.required' => 'Property status is required.',
            'status.in' => 'Please select a valid property status.',
            'description.max' => 'Description cannot exceed 1000 characters.',
        ]);

        try {
            Property::create($request->all());
            return redirect()->route('properties.index')->with('success', 'Property created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create property. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $property = Property::findOrFail($id);
        $units = $property->units()->paginate(10);
        return view('properties.show', compact('property', 'units'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $property = Property::findOrFail($id);
        return view('properties.edit', compact('property'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $property = Property::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:residential,commercial,industrial,mixed',
            'address' => 'required|string|max:500',
            'status' => 'required|in:available,occupied,maintenance,unavailable',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Property name is required.',
            'name.max' => 'Property name cannot exceed 255 characters.',
            'type.required' => 'Property type is required.',
            'type.in' => 'Please select a valid property type.',
            'address.required' => 'Property address is required.',
            'address.max' => 'Property address cannot exceed 500 characters.',
            'status.required' => 'Property status is required.',
            'status.in' => 'Please select a valid property status.',
            'description.max' => 'Description cannot exceed 1000 characters.',
        ]);

        try {
            $property->update($request->all());
            return redirect()->route('properties.index')->with('success', 'Property updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update property. Please try again.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $property = Property::findOrFail($id);

        try {
            // Check if property has units
            if ($property->units()->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete property with existing units. Please remove all units first.']);
            }

            $property->delete();
            return redirect()->route('properties.index')->with('success', 'Property deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete property. Please try again.']);
        }
    }
}
