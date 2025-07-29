<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Property;
use App\Models\PropertyUnitParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::with('property')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        return view('admin.units.create', compact('properties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Custom validation for unit type
        $typeValidation = 'required|string|max:255';
        $customTypeValidation = 'nullable|string|max:255';
        
        // If type is 'custom', then custom_type is required
        if ($request->input('type') === 'custom') {
            $typeValidation = 'required|string|max:255';
            $customTypeValidation = 'required|string|max:255';
        }
        
        $request->validate([
            'property_id' => 'required|exists:properties,property_id',
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
                'property_id' => $request->property_id,
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
                        'property_id' => $request->property_id,
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
                            'property_id' => $request->property_id,
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
                            'property_id' => $request->property_id,
                            'unit_id' => $unit->unit_id,
                            'pricing_id' => null,
                            'amenity_id' => $amenityId,
                            'service_id' => null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('units.index')
                ->with('success', 'Unit created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create unit: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to create unit. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unit = Unit::with('property')->findOrFail($id);
        return view('admin.units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $unit = Unit::findOrFail($id);
        $properties = Property::where('status', 'active')->get();
        return view('admin.units.edit', compact('unit', 'properties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unit = Unit::findOrFail($id);

        // Custom validation for unit type
        $typeValidation = 'required|string|max:255';
        $customTypeValidation = 'nullable|string|max:255';
        
        // If type is 'custom', then custom_type is required
        if ($request->input('type') === 'custom') {
            $typeValidation = 'required|string|max:255';
            $customTypeValidation = 'required|string|max:255';
        }
        
        $request->validate([
            'property_id' => 'required|exists:properties,property_id',
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
                'property_id' => $request->property_id,
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
                PropertyUnitParameter::where('property_id', $request->property_id)
                    ->where('unit_id', $id)
                    ->delete();
                
                // Create pricing parameter if global pricing is selected
                if (!empty($unitParameters['globalPricingId'])) {
                    PropertyUnitParameter::create([
                        'property_id' => $request->property_id,
                        'unit_id' => $id,
                        'pricing_id' => $unitParameters['globalPricingId'],
                        'amenity_id' => null,
                        'service_id' => null,
                    ]);
                }

                // Create service parameters
                if (!empty($unitParameters['services'])) {
                    foreach ($unitParameters['services'] as $serviceId) {
                        PropertyUnitParameter::create([
                            'property_id' => $request->property_id,
                            'unit_id' => $id,
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
                            'property_id' => $request->property_id,
                            'unit_id' => $id,
                            'pricing_id' => null,
                            'amenity_id' => $amenityId,
                            'service_id' => null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('units.index')
                ->with('success', 'Unit updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update unit: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to update unit. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();
            
            return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('units.index')->with('error', 'Failed to delete unit.');
        }
    }

    /**
     * Get unit parameters for editing
     */
    public function getParameters(string $id)
    {
        try {
            $unit = Unit::with('propertyUnitParameters.pricing', 'propertyUnitParameters.service', 'propertyUnitParameters.amenity')->findOrFail($id);
            
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

            Log::info('Unit parameters fetched successfully', ['unit_id' => $id, 'parameters' => $parameters]);
            
            return response()->json($parameters);
        } catch (\Exception $e) {
            Log::error('Error fetching unit parameters', ['unit_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch unit parameters'], 500);
        }
    }
}
