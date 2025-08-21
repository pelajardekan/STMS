<?php

namespace App\Services;

use App\Models\Pricing;

class PricingCalculator
{
    /**
     * Calculate pricing based on complex rules
     */
    public static function calculatePrice(Pricing $pricing, array $options = [])
    {
        $pricingType = $pricing->pricing_type;
        $customerType = $options['customer_type'] ?? 'regular';
        $isPeakHour = $options['is_peak_hour'] ?? false;
        
        if ($pricingType === 'booking') {
            return self::calculateBookingPrice($pricing, $options);
        } else {
            return self::calculateRentalPrice($pricing, $options);
        }
    }
    
    /**
     * Calculate booking pricing (hourly/daily)
     */
    private static function calculateBookingPrice(Pricing $pricing, array $options = [])
    {
        $hours = $options['hours'] ?? 1;
        $customerType = $options['customer_type'] ?? 'regular';
        $isPeakHour = $options['is_peak_hour'] ?? false;
        
        // Start with base hourly rate (NULL = not applicable)
        if ($pricing->base_hourly_rate === null) {
            throw new \InvalidArgumentException('Hourly rate is not configured for this pricing');
        }
        $baseRate = $pricing->base_hourly_rate;
        
        // Calculate base price
        $basePrice = $baseRate * $hours;
        
        // Apply daily discount if booking meets threshold and daily rate exists
        if ($hours >= ($pricing->daily_hours_threshold ?? 8) && $pricing->daily_discount_percentage !== null) {
            $basePrice = $basePrice * (1 - ($pricing->daily_discount_percentage / 100));
        }
        
        // Apply customer type discounts (NULL = no discount)
        $customerDiscount = 0;
        switch ($customerType) {
            case 'educational':
                $customerDiscount = $pricing->educational_discount_percentage ?? 0;
                break;
            case 'corporate':
                $customerDiscount = $pricing->corporate_discount_percentage ?? 0;
                break;
            case 'student':
                $customerDiscount = $pricing->student_discount_percentage ?? 0;
                break;
        }
        
        // Apply customer discount
        $finalPrice = $basePrice * (1 - ($customerDiscount / 100));
        
        return [
            'base_price' => $basePrice,
            'customer_discount' => $customerDiscount,
            'final_price' => $finalPrice,
            'breakdown' => [
                'base_rate' => $baseRate,
                'hours' => $hours,
                'base_calculation' => $baseRate * $hours,
                'daily_discount_applied' => $hours >= ($pricing->daily_hours_threshold ?? 8) && $pricing->daily_discount_percentage !== null,
                'daily_discount_percentage' => $pricing->daily_discount_percentage ?? 0,
                'customer_type' => $customerType,
                'customer_discount_percentage' => $customerDiscount,
                'is_peak_hour' => $isPeakHour,
            ]
        ];
    }
    
    /**
     * Calculate rental pricing (monthly/yearly)
     */
    private static function calculateRentalPrice(Pricing $pricing, array $options = [])
    {
        $duration = $options['duration'] ?? 'monthly';
        $customerType = $options['customer_type'] ?? 'regular';
        
        // Get base rate based on duration (NULL = not applicable)
        $baseRate = null;
        if ($duration === 'monthly') {
            if ($pricing->base_monthly_rate === null) {
                throw new \InvalidArgumentException('Monthly rate is not configured for this pricing');
            }
            $baseRate = $pricing->base_monthly_rate;
        } elseif ($duration === 'yearly') {
            if ($pricing->base_yearly_rate === null) {
                throw new \InvalidArgumentException('Yearly rate is not configured for this pricing');
            }
            $baseRate = $pricing->base_yearly_rate;
        }
        
        $basePrice = $baseRate;
        
        // Apply customer type discounts (NULL = no discount)
        $customerDiscount = 0;
        switch ($customerType) {
            case 'educational':
                $customerDiscount = $pricing->educational_discount_percentage ?? 0;
                break;
            case 'corporate':
                $customerDiscount = $pricing->corporate_discount_percentage ?? 0;
                break;
            case 'student':
                $customerDiscount = $pricing->student_discount_percentage ?? 0;
                break;
        }
        
        // Apply customer discount
        $finalPrice = $basePrice * (1 - ($customerDiscount / 100));
        
        return [
            'base_price' => $basePrice,
            'customer_discount' => $customerDiscount,
            'final_price' => $finalPrice,
            'breakdown' => [
                'base_rate' => $baseRate,
                'duration' => $duration,
                'customer_type' => $customerType,
                'customer_discount_percentage' => $customerDiscount,
            ]
        ];
    }
    
    /**
     * Get pricing summary for display
     */
    public static function getPricingSummary(Pricing $pricing)
    {
        $summary = [];
        
        if ($pricing->pricing_type === 'booking') {
            // Booking pricing (hourly/daily)
            if ($pricing->base_hourly_rate !== null) {
                $rate = $pricing->base_hourly_rate == 0 ? "Free" : "RM" . number_format($pricing->base_hourly_rate, 2);
                $summary[] = $rate . "/hour";
            }
            
            if ($pricing->base_daily_rate !== null) {
                $rate = $pricing->base_daily_rate == 0 ? "Free" : "RM" . number_format($pricing->base_daily_rate, 2);
                $summary[] = $rate . "/day";
            }
            
            // Add daily discount information
            if ($pricing->daily_discount_percentage !== null) {
                $summary[] = $pricing->daily_discount_percentage . "% off for " . ($pricing->daily_hours_threshold ?? 8) . "+ hours";
            }
        } else {
            // Rental pricing (monthly/yearly)
            if ($pricing->base_monthly_rate !== null) {
                $rate = $pricing->base_monthly_rate == 0 ? "Free" : "RM" . number_format($pricing->base_monthly_rate, 2);
                $summary[] = $rate . "/month";
            }
            
            if ($pricing->base_yearly_rate !== null) {
                $rate = $pricing->base_yearly_rate == 0 ? "Free" : "RM" . number_format($pricing->base_yearly_rate, 2);
                $summary[] = $rate . "/year";
            }
        }
        
        // Add customer discount information
        if ($pricing->educational_discount_percentage !== null) {
            $summary[] = $pricing->educational_discount_percentage . "% off for educational classes";
        }
        
        if ($pricing->corporate_discount_percentage !== null) {
            $summary[] = $pricing->corporate_discount_percentage . "% off for corporate clients";
        }
        
        if ($pricing->student_discount_percentage !== null) {
            $summary[] = $pricing->student_discount_percentage . "% off for students";
        }
        
        return implode(", ", $summary);
    }
    
    /**
     * Validate booking hours against pricing rules
     */
    public static function validateBookingHours(Pricing $pricing, int $hours)
    {
        $minHours = $pricing->minimum_booking_hours ?? 1;
        $maxHours = $pricing->maximum_booking_hours;
        
        if ($hours < $minHours) {
            return [
                'valid' => false,
                'message' => "Minimum booking is {$minHours} hour(s)"
            ];
        }
        
        if ($maxHours && $hours > $maxHours) {
            return [
                'valid' => false,
                'message' => "Maximum booking is {$maxHours} hour(s)"
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Booking hours are valid'
        ];
    }
} 