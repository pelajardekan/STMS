<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyUnitParameter;
use App\Models\Pricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::orderBy('created_at', 'desc')->paginate(10);
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
        // Custom validation for property type
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
            'address' => 'required|string|max:500',
            'status' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Property name is required.',
            'name.max' => 'Property name cannot exceed 255 characters.',
            'type.required' => 'Property type is required.',
            'custom_type.required' => 'Custom property type is required when Custom Type is selected.',
            'address.required' => 'Property address is required.',
            'address.max' => 'Property address cannot exceed 500 characters.',
            'status.required' => 'Property status is required.',
            'status.max' => 'Status cannot exceed 20 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
        ]);

        try {
            DB::beginTransaction();
            
            Log::info('Starting property creation with data:', $request->all());
            
            // Prepare property data
            $propertyData = $request->all();
            
            // If custom type is selected, use the custom_type value
            if ($request->input('type') === 'custom') {
                $propertyData['type'] = $request->input('custom_type');
            }
            
            // Remove custom_type from data as it's not a database field
            unset($propertyData['custom_type']);
            
            // Create the property
            $property = Property::create($propertyData);
            
            Log::info('Property created successfully:', ['property_id' => $property->property_id]);
            
            // Handle PropertyUnitParameter creation if parameters were configured
            if ($request->has('property_parameters') && !empty($request->input('property_parameters'))) {
                Log::info('Property parameters received:', ['data' => $request->input('property_parameters')]);
                
                $parameters = json_decode($request->input('property_parameters'), true);
                
                Log::info('Decoded parameters:', ['parameters' => $parameters]);
                
                if ($parameters) {
                    // Handle pricing
                    if (!empty($parameters['globalPricingId']) && $parameters['globalPricingId'] !== 'no-pricing') {
                        Log::info('Creating PropertyUnitParameter for global pricing:', ['pricing_id' => $parameters['globalPricingId']]);
                        // Use existing pricing
                        PropertyUnitParameter::create([
                            'property_id' => $property->property_id,
                            'unit_id' => null, // Property-wide
                            'pricing_id' => $parameters['globalPricingId'],
                            'amenity_id' => null,
                            'service_id' => null,
                        ]);
                        Log::info('PropertyUnitParameter created for global pricing successfully');
                    } elseif ($parameters['globalPricingId'] === 'no-pricing') {
                        Log::info('No pricing selected for property');
                        // No pricing parameter will be created (pricing_id will be null)
                    }
                    
                    // Handle services - create separate records for each service
                    if (!empty($parameters['services']) && is_array($parameters['services'])) {
                        Log::info('Creating PropertyUnitParameters for services:', ['services' => $parameters['services']]);
                        foreach ($parameters['services'] as $serviceId) {
                            if (!empty($serviceId)) {
                                PropertyUnitParameter::create([
                                    'property_id' => $property->property_id,
                                    'unit_id' => null, // Property-wide
                                    'pricing_id' => null,
                                    'amenity_id' => null,
                                    'service_id' => $serviceId,
                                ]);
                            }
                        }
                        Log::info('PropertyUnitParameters created for services successfully');
                    }
                    
                    // Handle amenities - create separate records for each amenity
                    if (!empty($parameters['amenities']) && is_array($parameters['amenities'])) {
                        Log::info('Creating PropertyUnitParameters for amenities:', ['amenities' => $parameters['amenities']]);
                        foreach ($parameters['amenities'] as $amenityId) {
                            if (!empty($amenityId)) {
                                PropertyUnitParameter::create([
                                    'property_id' => $property->property_id,
                                    'unit_id' => null, // Property-wide
                                    'pricing_id' => null,
                                    'amenity_id' => $amenityId,
                                    'service_id' => null,
                                ]);
                            }
                        }
                        Log::info('PropertyUnitParameters created for amenities successfully');
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('properties.index')->with('success', 'Property created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Property creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Failed to create property: ' . $e->getMessage()])->withInput();
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
        
        // Load existing property parameters
        $propertyParameters = $property->propertyUnitParameters()
            ->with(['pricing', 'service', 'amenity'])
            ->get();
        
        // Organize parameters by type
        $existingParameters = [
            'pricing' => null,
            'services' => [],
            'amenities' => []
        ];
        
        foreach ($propertyParameters as $param) {
            if ($param->pricing_id) {
                $existingParameters['pricing'] = $param->pricing;
            }
            if ($param->service_id) {
                $existingParameters['services'][] = $param->service;
            }
            if ($param->amenity_id) {
                $existingParameters['amenities'][] = $param->amenity;
            }
        }
        
        return view('properties.edit', compact('property', 'existingParameters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $property = Property::findOrFail($id);

        // Custom validation for property type
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
            'address' => 'required|string|max:500',
            'status' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Property name is required.',
            'name.max' => 'Property name cannot exceed 255 characters.',
            'type.required' => 'Property type is required.',
            'custom_type.required' => 'Custom property type is required when Custom Type is selected.',
            'address.required' => 'Property address is required.',
            'address.max' => 'Property address cannot exceed 500 characters.',
            'status.required' => 'Property status is required.',
            'status.max' => 'Status cannot exceed 20 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
        ]);

        try {
            DB::beginTransaction();
            
            // Prepare property data
            $propertyData = $request->all();
            
            // If custom type is selected, use the custom_type value
            if ($request->input('type') === 'custom') {
                $propertyData['type'] = $request->input('custom_type');
            }
            
            // Remove custom_type from data as it's not a database field
            unset($propertyData['custom_type']);
            
            // Update the property
            $property->update($propertyData);
            
            // Handle PropertyUnitParameter updates if parameters were configured
            if ($request->has('property_parameters') && !empty($request->input('property_parameters'))) {
                // Delete existing property parameters
                $property->propertyUnitParameters()->delete();
                
                $parameters = json_decode($request->input('property_parameters'), true);
                
                if ($parameters) {
                    // Handle pricing
                    if (!empty($parameters['globalPricingId']) && $parameters['globalPricingId'] !== 'no-pricing') {
                        // Use existing pricing
                        PropertyUnitParameter::create([
                            'property_id' => $property->property_id,
                            'unit_id' => null, // Property-wide
                            'pricing_id' => $parameters['globalPricingId'],
                            'amenity_id' => null,
                            'service_id' => null,
                        ]);
                    } elseif ($parameters['globalPricingId'] === 'no-pricing') {
                        // No pricing parameter will be created (pricing_id will be null)
                    }
                    
                    // Handle services
                    if (!empty($parameters['services']) && is_array($parameters['services'])) {
                        foreach ($parameters['services'] as $serviceId) {
                            if (!empty($serviceId)) {
                                PropertyUnitParameter::create([
                                    'property_id' => $property->property_id,
                                    'unit_id' => null, // Property-wide
                                    'pricing_id' => null,
                                    'amenity_id' => null,
                                    'service_id' => $serviceId,
                                ]);
                            }
                        }
                    }
                    
                    // Handle amenities
                    if (!empty($parameters['amenities']) && is_array($parameters['amenities'])) {
                        foreach ($parameters['amenities'] as $amenityId) {
                            if (!empty($amenityId)) {
                                PropertyUnitParameter::create([
                                    'property_id' => $property->property_id,
                                    'unit_id' => null, // Property-wide
                                    'pricing_id' => null,
                                    'amenity_id' => $amenityId,
                                    'service_id' => null,
                                ]);
                            }
                        }
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('properties.index')->with('success', 'Property updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update property. Please try again.'])->withInput();
        }
    }

    /**
     * Get property parameters for editing.
     */
    public function getParameters(string $id)
    {
        $property = Property::findOrFail($id);
        
        // Load existing property parameters
        $propertyParameters = $property->propertyUnitParameters()
            ->with(['pricing', 'service', 'amenity'])
            ->get();
        
        Log::info('Property parameters found:', [
            'property_id' => $id,
            'count' => $propertyParameters->count(),
            'parameters' => $propertyParameters->toArray()
        ]);
        
        // Organize parameters by type
        $existingParameters = [
            'pricing' => null,
            'services' => [],
            'amenities' => []
        ];
        
        foreach ($propertyParameters as $param) {
            if ($param->pricing_id) {
                $existingParameters['pricing'] = $param->pricing;
            }
            if ($param->service_id) {
                $existingParameters['services'][] = $param->service;
            }
            if ($param->amenity_id) {
                $existingParameters['amenities'][] = $param->amenity;
            }
        }
        
        Log::info('Organized parameters:', $existingParameters);
        
        return response()->json([
            'success' => true,
            'parameters' => $existingParameters
        ]);
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
