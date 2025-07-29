<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Property;
use App\Models\PropertyUnitParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyUnitController extends Controller
{
    /**
     * Display a listing of units for a specific property.
     */
    public function index(string $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $units = Unit::where('property_id', $propertyId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.properties.units.index', compact('units', 'property'));
    }

    /**
     * Show the form for creating a new unit for a specific property.
     */
    public function create(string $propertyId)
    {
        $property = Property::findOrFail($propertyId);
        return view('admin.properties.units.create', compact('property'));
    }

    /**
     * Store a newly created unit for a specific property.
     */
    public function store(Request $request, string $propertyId)
    {
        $property = Property::findOrFail($propertyId);

        // Custom validation for unit type
        $typeValidation = 'required|string|max:255';
        $customTypeValidation = 'nullable|string|max:255';
        
        // If type is 'custom', then custom_type is required
        if ($request->input('type') === 'custom') {
            $typeValidation = 'required|string|max:255';
            $customTypeValidation = 'required|string|max:255';
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => $typeValidation,
            'custom_type' => $customTypeValidation,
            'status' => 'required|in:active,unactive',
            'description' => 'nullable|string|max:1000',
            'leasing_type' => 'required|in:rental,booking',
        ], [
            'custom_type.required' => 'Custom unit type is required when Custom Type is selected.',
            'status.in' => 'Status must be either active or unactive.',
        ]);

        try {
            DB::beginTransaction();

            // Prepare unit data
            $unitData = [
                'property_id' => $property->property_id,
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'description' => $request->description,
                'leasing_type' => $request->leasing_type,
            ];
            
            // If custom type is selected, use the custom_type value
            if ($request->input('type') === 'custom') {
                $unitData['type'] = $request->input('custom_type');
            }

            $unit = Unit::create($unitData);

            // Handle unit parameters if provided
            if ($request->has('unit_parameters')) {
                $unitParameters = json_decode($request->unit_parameters, true);
                
                // Create pricing parameter if global pricing is selected
                if (!empty($unitParameters['globalPricingId'])) {
                    PropertyUnitParameter::create([
                        'property_id' => $propertyId,
                        'unit_id' => $unit->unit_id,
                        'pricing_id' => $unitParameters['globalPricingId'],
                        'amenity_id' => null,
                        'service_id' => null,
                    ]);
                }

                // Create service parameters
                if (!empty($unitParameters['services'])) {
                    foreach ($unitParameters['services'] as $serviceId) {
                        PropertyUnitParameter::create([
                            'property_id' => $propertyId,
                            'unit_id' => $unit->unit_id,
                            'pricing_id' => null,
                            'amenity_id' => null,
                            'service_id' => $serviceId,
                        ]);
                    }
                }

                // Create amenity parameters
                if (!empty($unitParameters['amenities'])) {
                    foreach ($unitParameters['amenities'] as $amenityId) {
                        PropertyUnitParameter::create([
                            'property_id' => $propertyId,
                            'unit_id' => $unit->unit_id,
                            'pricing_id' => null,
                            'amenity_id' => $amenityId,
                            'service_id' => null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('properties.units.index', $propertyId)
                ->with('success', 'Unit created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create unit: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to create unit. Please try again.');
        }
    }

    /**
     * Display the specified unit for a specific property.
     */
    public function show(string $propertyId, string $unitId)
    {
        $property = Property::findOrFail($propertyId);
        $unit = Unit::where('property_id', $propertyId)
            ->where('unit_id', $unitId)
            ->firstOrFail();

        return view('admin.properties.units.show', compact('unit', 'property'));
    }

    /**
     * Show the form for editing a unit for a specific property.
     */
    public function edit(string $propertyId, string $unitId)
    {
        $property = Property::findOrFail($propertyId);
        $unit = Unit::where('property_id', $propertyId)
            ->where('unit_id', $unitId)
            ->firstOrFail();

        return view('admin.properties.units.edit', compact('unit', 'property'));
    }

    /**
     * Update the specified unit for a specific property.
     */
    public function update(Request $request, string $propertyId, string $unitId)
    {
        $property = Property::findOrFail($propertyId);
        $unit = Unit::where('property_id', $propertyId)
            ->where('unit_id', $unitId)
            ->firstOrFail();

        // Custom validation for unit type
        $typeValidation = 'required|string|max:255';
        $customTypeValidation = 'nullable|string|max:255';
        
        // If type is 'custom', then custom_type is required
        if ($request->input('type') === 'custom') {
            $typeValidation = 'required|string|max:255';
            $customTypeValidation = 'required|string|max:255';
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => $typeValidation,
            'custom_type' => $customTypeValidation,
            'status' => 'required|in:active,unactive',
            'description' => 'nullable|string|max:1000',
            'leasing_type' => 'required|in:rental,booking',
        ], [
            'custom_type.required' => 'Custom unit type is required when Custom Type is selected.',
            'status.in' => 'Status must be either active or unactive.',
        ]);

        try {
            DB::beginTransaction();

            // Prepare unit data
            $unitData = [
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'description' => $request->description,
                'leasing_type' => $request->leasing_type,
            ];
            
            // If custom type is selected, use the custom_type value
            if ($request->input('type') === 'custom') {
                $unitData['type'] = $request->input('custom_type');
            }

            $unit->update($unitData);

            // Handle unit parameters if provided
            if ($request->has('unit_parameters')) {
                $unitParameters = json_decode($request->unit_parameters, true);
                
                // Delete existing parameters for this unit
                PropertyUnitParameter::where('property_id', $propertyId)
                    ->where('unit_id', $unitId)
                    ->delete();
                
                // Create pricing parameter if global pricing is selected
                if (!empty($unitParameters['globalPricingId'])) {
                    PropertyUnitParameter::create([
                        'property_id' => $propertyId,
                        'unit_id' => $unitId,
                        'pricing_id' => $unitParameters['globalPricingId'],
                        'amenity_id' => null,
                        'service_id' => null,
                    ]);
                }

                // Create service parameters
                if (!empty($unitParameters['services'])) {
                    foreach ($unitParameters['services'] as $serviceId) {
                        PropertyUnitParameter::create([
                            'property_id' => $propertyId,
                            'unit_id' => $unitId,
                            'pricing_id' => null,
                            'amenity_id' => null,
                            'service_id' => $serviceId,
                        ]);
                    }
                }

                // Create amenity parameters
                if (!empty($unitParameters['amenities'])) {
                    foreach ($unitParameters['amenities'] as $amenityId) {
                        PropertyUnitParameter::create([
                            'property_id' => $propertyId,
                            'unit_id' => $unitId,
                            'pricing_id' => null,
                            'amenity_id' => $amenityId,
                            'service_id' => null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('properties.units.index', $propertyId)
                ->with('success', 'Unit updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update unit: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to update unit. Please try again.');
        }
    }

    /**
     * Remove the specified unit for a specific property.
     */
    public function destroy(string $propertyId, string $unitId)
    {
        $property = Property::findOrFail($propertyId);
        $unit = Unit::where('property_id', $propertyId)
            ->where('unit_id', $unitId)
            ->firstOrFail();

        // Check if unit has associated rental requests or booking requests
        if ($unit->rentalRequests()->exists() || $unit->bookingRequests()->exists()) {
            return back()->with('error', 'Cannot delete unit. It has associated rental or booking requests.');
        }

        try {
            DB::beginTransaction();

            // Delete associated property unit parameters
            $unit->propertyUnitParameters()->delete();

            // Delete the unit
            $unit->delete();

            DB::commit();

            return redirect()->route('properties.units.index', $propertyId)
                ->with('success', 'Unit deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete unit: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete unit. Please try again.');
        }
    }

    /**
     * Get unit parameters for editing
     */
    public function getParameters(string $propertyId, string $unitId)
    {
        try {
            $unit = Unit::where('property_id', $propertyId)
                ->with('propertyUnitParameters.pricing', 'propertyUnitParameters.service', 'propertyUnitParameters.amenity')
                ->findOrFail($unitId);
            
            $parameters = [
                'pricing' => [],
                'services' => [],
                'amenities' => []
            ];

            foreach ($unit->propertyUnitParameters as $param) {
                if ($param->pricing_id && $param->pricing) {
                    $parameters['pricing'][] = $param->pricing;
                }
                if ($param->service_id && $param->service) {
                    $parameters['services'][] = $param->service;
                }
                if ($param->amenity_id && $param->amenity) {
                    $parameters['amenities'][] = $param->amenity;
                }
            }

            Log::info('Unit parameters fetched successfully', ['property_id' => $propertyId, 'unit_id' => $unitId, 'parameters' => $parameters]);
            
            return response()->json($parameters);
        } catch (\Exception $e) {
            Log::error('Error fetching unit parameters', ['property_id' => $propertyId, 'unit_id' => $unitId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch unit parameters'], 500);
        }
    }
} 