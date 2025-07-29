<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Pricing;
use App\Models\Amenity;
use App\Models\Service;
use App\Models\PropertyUnitParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PPUController extends Controller
{
    /**
     * Show the setup parameter modal for a property
     */
    public function setupParameter($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $units = Unit::where('property_id', $propertyId)->get();
        $pricings = Pricing::all();
        $amenities = Amenity::all();
        $services = Service::all();
        
        // Get existing parameters for this property
        $existingParameters = PropertyUnitParameter::where('property_id', $propertyId)->get();
        
        return view('properties.setup-parameter', compact(
            'property', 
            'units', 
            'pricings', 
            'amenities', 
            'services', 
            'existingParameters'
        ));
    }

    /**
     * Store the setup parameters
     */
    public function storeParameters(Request $request, $propertyId)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'nullable|exists:units,unit_id',
            'pricing_id' => 'required|exists:pricings,pricing_id',
            'amenity_ids' => 'array',
            'amenity_ids.*' => 'exists:amenities,amenity_id',
            'service_ids' => 'array',
            'service_ids.*' => 'exists:services,service_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Delete existing parameters for this property/unit combination
            PropertyUnitParameter::where('property_id', $propertyId)
                ->when($request->unit_id, function($query, $unitId) {
                    return $query->where('unit_id', $unitId);
                })
                ->delete();

            // Create new parameters
            $amenityIds = $request->amenity_ids ?? [];
            $serviceIds = $request->service_ids ?? [];

            // Create parameter for pricing
            PropertyUnitParameter::create([
                'property_id' => $propertyId,
                'unit_id' => $request->unit_id,
                'pricing_id' => $request->pricing_id,
                'amenity_id' => null,
                'service_id' => null,
            ]);

            // Create parameters for amenities
            foreach ($amenityIds as $amenityId) {
                PropertyUnitParameter::create([
                    'property_id' => $propertyId,
                    'unit_id' => $request->unit_id,
                    'pricing_id' => null,
                    'amenity_id' => $amenityId,
                    'service_id' => null,
                ]);
            }

            // Create parameters for services
            foreach ($serviceIds as $serviceId) {
                PropertyUnitParameter::create([
                    'property_id' => $propertyId,
                    'unit_id' => $request->unit_id,
                    'pricing_id' => null,
                    'amenity_id' => null,
                    'service_id' => $serviceId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Property parameters have been set up successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set up property parameters. Please try again.'
            ], 500);
        }
    }

    /**
     * Get parameters for a specific property/unit
     */
    public function getParameters($propertyId, $unitId = null)
    {
        $query = PropertyUnitParameter::where('property_id', $propertyId);
        
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        $parameters = $query->get();

        return response()->json([
            'pricing' => $parameters->whereNotNull('pricing_id')->first(),
            'amenities' => $parameters->whereNotNull('amenity_id')->pluck('amenity_id'),
            'services' => $parameters->whereNotNull('service_id')->pluck('service_id'),
        ]);
    }

    /**
     * Get units for a specific property
     */
    public function getPropertyUnits($propertyId)
    {
        $units = Unit::where('property_id', $propertyId)->get();
        return response()->json($units);
    }

    /**
     * Get all pricings for API
     */
    public function getPricings()
    {
        $pricings = Pricing::all();
        return response()->json($pricings);
    }

    /**
     * Get all amenities for API
     */
    public function getAmenities()
    {
        $amenities = Amenity::all();
        return response()->json($amenities);
    }

    /**
     * Get all services for API
     */
    public function getServices()
    {
        $services = Service::all();
        return response()->json($services);
    }

    /**
     * Get all properties for API
     */
    public function getAllProperties()
    {
        $properties = Property::all();
        return response()->json($properties);
    }
} 