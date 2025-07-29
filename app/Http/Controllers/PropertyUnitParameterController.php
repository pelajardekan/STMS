<?php

namespace App\Http\Controllers;

use App\Models\PropertyUnitParameter;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Pricing;
use App\Models\Amenity;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PropertyUnitParameterController extends Controller
{
    use ValidatesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pricings = Pricing::orderBy('created_at', 'desc')->paginate(10);
        $amenities = Amenity::orderBy('created_at', 'desc')->paginate(10);
        $services = Service::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.parameters.index', compact('pricings', 'amenities', 'services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.parameters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $parameterType = $request->input('parameter_type');

        try {
            DB::beginTransaction();

            switch ($parameterType) {
                case 'pricing':
                    $this->validate($request, [
                        'pricing_name' => 'required|string|max:255',
                        'pricing_type' => 'required|string|max:20',
                        'base_hourly_rate' => 'nullable|numeric|min:0',
                        'base_daily_rate' => 'nullable|numeric|min:0',
                        'base_monthly_rate' => 'nullable|numeric|min:0',
                        'base_yearly_rate' => 'nullable|numeric|min:0',
                        'daily_hours_threshold' => 'nullable|integer|min:1',
                        'daily_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'educational_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'corporate_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'student_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'off_peak_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'minimum_booking_hours' => 'nullable|integer|min:1',
                        'maximum_booking_hours' => 'nullable|integer|min:1',
                        'rental_duration_months' => 'nullable|integer|min:1',
                        'pricing_notes' => 'nullable|string|max:1000',
                    ]);

                    // Handle rates (empty string = NULL, 0 = free rate)
                    $baseHourlyRate = $request->base_hourly_rate === '' ? null : $request->base_hourly_rate;
                    $baseDailyRate = $request->base_daily_rate === '' ? null : $request->base_daily_rate;
                    $baseMonthlyRate = $request->base_monthly_rate === '' ? null : $request->base_monthly_rate;
                    $baseYearlyRate = $request->base_yearly_rate === '' ? null : $request->base_yearly_rate;

                    // Create pricing with full complex structure
                    Pricing::create([
                        'name' => $request->pricing_name,
                        'pricing_type' => $request->pricing_type,
                        'price_amount' => $baseHourlyRate ?? $baseDailyRate ?? $baseMonthlyRate ?? $baseYearlyRate ?? 0, // Legacy field
                        'duration_type' => $request->pricing_type === 'booking' ? 'hourly' : 'monthly', // Legacy field
                        'discount' => 0, // Legacy field
                        'base_hourly_rate' => $baseHourlyRate,
                        'base_daily_rate' => $baseDailyRate,
                        'base_monthly_rate' => $baseMonthlyRate,
                        'base_yearly_rate' => $baseYearlyRate,
                        'daily_hours_threshold' => $request->daily_hours_threshold,
                        'daily_discount_percentage' => $request->daily_discount_percentage,
                        'educational_discount_percentage' => $request->educational_discount_percentage,
                        'corporate_discount_percentage' => $request->corporate_discount_percentage,
                        'student_discount_percentage' => $request->student_discount_percentage,
                        'off_peak_discount_percentage' => $request->off_peak_discount_percentage,
                        'minimum_booking_hours' => $request->minimum_booking_hours,
                        'maximum_booking_hours' => $request->maximum_booking_hours,
                        'rental_duration_months' => $request->pricing_type === 'rental' ? $request->rental_duration_months : null,
                        'special_rates' => null, // Can be used for custom rates
                        'is_active' => true,
                        'notes' => $request->pricing_notes,
                    ]);
                    break;

                case 'amenity':
                    $this->validate($request, [
                        'amenity_name' => 'required|string|max:255',
                        'amenity_description' => 'nullable|string|max:1000',
                    ]);

                    Amenity::create([
                        'name' => $request->amenity_name,
                        'description' => $request->amenity_description,
                    ]);
                    break;

                case 'service':
                    $this->validate($request, [
                        'service_name' => 'required|string|max:255',
                        'service_description' => 'nullable|string|max:1000',
                    ]);

                    Service::create([
                        'name' => $request->service_name,
                        'description' => $request->service_description,
                    ]);
                    break;

                default:
                    throw new \Exception('Invalid parameter type');
            }

            DB::commit();

            return redirect()->route('parameters.index', ['tab' => $parameterType === 'amenity' ? 'amenities' : ($parameterType === 'service' ? 'services' : $parameterType)])
                ->with('success', ucfirst($parameterType) . ' created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create parameter: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to create ' . $parameterType . '. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');

        switch ($type) {
            case 'pricing':
                $pricing = Pricing::findOrFail($id);
                return view('admin.parameters.edit', compact('pricing', 'type'));
            case 'amenity':
                $amenity = Amenity::findOrFail($id);
                return view('admin.parameters.edit', compact('amenity', 'type'));
            case 'service':
                $service = Service::findOrFail($id);
                return view('admin.parameters.edit', compact('service', 'type'));
            default:
                abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');

        try {
            DB::beginTransaction();

            switch ($type) {
                case 'pricing':
                    $this->validate($request, [
                        'pricing_name' => 'required|string|max:255',
                        'pricing_type' => 'required|string|max:20',
                        'base_hourly_rate' => 'nullable|numeric|min:0',
                        'base_daily_rate' => 'nullable|numeric|min:0',
                        'base_monthly_rate' => 'nullable|numeric|min:0',
                        'base_yearly_rate' => 'nullable|numeric|min:0',
                        'daily_hours_threshold' => 'nullable|integer|min:1',
                        'daily_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'educational_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'corporate_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'student_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'off_peak_discount_percentage' => 'nullable|numeric|min:0|max:100',
                        'minimum_booking_hours' => 'nullable|integer|min:1',
                        'maximum_booking_hours' => 'nullable|integer|min:1',
                        'rental_duration_months' => 'nullable|integer|min:1',
                        'pricing_notes' => 'nullable|string|max:1000',
                    ]);

                    // Handle rates (empty string = NULL, 0 = free rate)
                    $baseHourlyRate = $request->base_hourly_rate === '' ? null : $request->base_hourly_rate;
                    $baseDailyRate = $request->base_daily_rate === '' ? null : $request->base_daily_rate;
                    $baseMonthlyRate = $request->base_monthly_rate === '' ? null : $request->base_monthly_rate;
                    $baseYearlyRate = $request->base_yearly_rate === '' ? null : $request->base_yearly_rate;

                    $pricing = Pricing::findOrFail($id);
                    $pricing->update([
                        'name' => $request->pricing_name,
                        'pricing_type' => $request->pricing_type,
                        'price_amount' => $baseHourlyRate ?? $baseDailyRate ?? $baseMonthlyRate ?? $baseYearlyRate ?? 0, // Legacy field
                        'duration_type' => $request->pricing_type === 'booking' ? 'hourly' : 'monthly', // Legacy field
                        'discount' => 0, // Legacy field
                        'base_hourly_rate' => $baseHourlyRate,
                        'base_daily_rate' => $baseDailyRate,
                        'base_monthly_rate' => $baseMonthlyRate,
                        'base_yearly_rate' => $baseYearlyRate,
                        'daily_hours_threshold' => $request->daily_hours_threshold,
                        'daily_discount_percentage' => $request->daily_discount_percentage,
                        'educational_discount_percentage' => $request->educational_discount_percentage,
                        'corporate_discount_percentage' => $request->corporate_discount_percentage,
                        'student_discount_percentage' => $request->student_discount_percentage,
                        'off_peak_discount_percentage' => $request->off_peak_discount_percentage,
                        'minimum_booking_hours' => $request->minimum_booking_hours,
                        'maximum_booking_hours' => $request->maximum_booking_hours,
                        'rental_duration_months' => $request->pricing_type === 'rental' ? $request->rental_duration_months : null,
                        'special_rates' => null, // Can be used for custom rates
                        'is_active' => true,
                        'notes' => $request->pricing_notes,
                    ]);
                    break;

                case 'amenity':
                    $this->validate($request, [
                        'amenity_name' => 'required|string|max:255',
                        'amenity_description' => 'nullable|string|max:1000',
                    ]);

                    $amenity = Amenity::findOrFail($id);
                    $amenity->update([
                        'name' => $request->amenity_name,
                        'description' => $request->amenity_description,
                    ]);
                    break;

                case 'service':
                    $this->validate($request, [
                        'service_name' => 'required|string|max:255',
                        'service_description' => 'nullable|string|max:1000',
                    ]);

                    $service = Service::findOrFail($id);
                    $service->update([
                        'name' => $request->service_name,
                        'description' => $request->service_description,
                    ]);
                    break;

                default:
                    throw new \Exception('Invalid parameter type');
            }

            DB::commit();

            return redirect()->route('parameters.index', ['tab' => $type === 'amenity' ? 'amenities' : ($type === 'service' ? 'services' : $type)])
                ->with('success', ucfirst($type) . ' updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update parameter: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to update ' . $type . '. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        try {
            DB::beginTransaction();

            switch ($type) {
                case 'pricing':
                    $pricing = Pricing::findOrFail($id);
                    $pricing->delete();
                    break;
                case 'amenity':
                    $amenity = Amenity::findOrFail($id);
                    $amenity->delete();
                    break;
                case 'service':
                    $service = Service::findOrFail($id);
                    $service->delete();
                    break;
                default:
                    throw new \Exception('Invalid parameter type');
            }

            DB::commit();

            return redirect()->route('parameters.index')
                ->with('success', ucfirst($type) . ' deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete parameter: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete ' . $type . '. Please try again.');
        }
    }
} 