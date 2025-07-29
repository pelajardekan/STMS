<?php

namespace App\Http\Controllers;

use App\Models\Pricing;
use App\Services\PricingCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PricingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get pricing details for API calls
     */
    public function getPricingDetails(string $id): JsonResponse
    {
        try {
            $pricing = Pricing::findOrFail($id);
            
            $details = [
                'pricing_id' => $pricing->pricing_id,
                'name' => $pricing->name,
                'pricing_type' => $pricing->pricing_type,
                'base_rate' => $pricing->pricing_type === 'booking' ? 
                    ($pricing->base_hourly_rate ?? 0) : 
                    ($pricing->base_monthly_rate ?? 0),
                'duration_type' => $pricing->pricing_type === 'booking' ? 'hour' : 'month',
                'discounts' => $this->getDiscountsText($pricing),
                'notes' => $pricing->notes,
                'is_active' => $pricing->is_active,
            ];

            return response()->json($details);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Pricing not found or error occurred',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Calculate pricing for a specific booking
     */
    public function calculatePricing(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'pricing_id' => 'required|exists:pricings,pricing_id',
                'hours' => 'required|integer|min:1',
                'customer_type' => 'nullable|string|max:20',
                'is_peak_hour' => 'nullable|boolean',
            ]);

            $pricing = Pricing::findOrFail($request->pricing_id);
            
            $options = [
                'hours' => $request->hours,
                'customer_type' => $request->customer_type ?? 'regular',
                'is_peak_hour' => $request->is_peak_hour ?? false,
            ];

            $calculation = PricingCalculator::calculatePrice($pricing, $options);

            return response()->json($calculation);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error calculating pricing',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get formatted discounts text
     */
    private function getDiscountsText(Pricing $pricing): string
    {
        $discounts = [];

        if ($pricing->educational_discount_percentage !== null) {
            $discounts[] = $pricing->educational_discount_percentage . '% educational';
        }

        if ($pricing->corporate_discount_percentage !== null) {
            $discounts[] = $pricing->corporate_discount_percentage . '% corporate';
        }

        if ($pricing->student_discount_percentage !== null) {
            $discounts[] = $pricing->student_discount_percentage . '% student';
        }

        if ($pricing->daily_discount_percentage !== null) {
            $discounts[] = $pricing->daily_discount_percentage . '% daily (8+ hours)';
        }

        return empty($discounts) ? 'None' : implode(', ', $discounts);
    }
}
