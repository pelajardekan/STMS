<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantPropertyController extends Controller
{
    /**
     * Display a listing of available properties for tenants.
     */
    public function index()
    {
        $properties = Property::where('status', 'active')
            ->with(['units' => function($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('tenant.properties.index', compact('properties'));
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property)
    {
        // Load the property with its units and amenities
        $property->load([
            'units' => function($query) {
                $query->where('status', 'active');
            },
            'units.propertyUnitParameters.pricing',
            'units.propertyUnitParameters.amenity',
            'units.propertyUnitParameters.service'
        ]);

        return view('tenant.properties.show', compact('property'));
    }
} 