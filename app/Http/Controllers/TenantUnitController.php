<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantUnitController extends Controller
{
    /**
     * Display a listing of available units for tenants.
     */
    public function index(Request $request)
    {
        $query = Unit::with('property')
            ->where('status', 'active');

        // Filter by leasing type if specified
        if ($request->has('leasing_type') && in_array($request->leasing_type, ['rental', 'booking'])) {
            $query->where('leasing_type', $request->leasing_type);
        }

        // Filter by property if specified
        if ($request->has('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $units = $query->orderBy('created_at', 'desc')->paginate(12);
        $properties = Property::where('status', 'active')->get();

        return view('tenant.units.index', compact('units', 'properties'));
    }

    /**
     * Display the specified unit.
     */
    public function show(Unit $unit)
    {
        // Load the unit with its property and parameters
        $unit->load([
            'property',
            'propertyUnitParameters.pricing',
            'propertyUnitParameters.amenity',
            'propertyUnitParameters.service'
        ]);

        return view('tenant.units.show', compact('unit'));
    }
} 