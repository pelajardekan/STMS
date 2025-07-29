<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PPUController;
use App\Http\Controllers\PropertyUnitController;
use App\Http\Controllers\PropertyUnitParameterController;
use App\Http\Controllers\RentalRequestController;
use App\Http\Controllers\BookingRequestController;

// Welcome page redirects to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin-only routes
    Route::middleware('auth')->group(function () {
        // User management
        Route::resource('admin/users', UserController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);
        
        // Property management
        Route::resource('admin/properties', PropertyController::class)->names([
            'index' => 'properties.index',
            'create' => 'properties.create',
            'store' => 'properties.store',
            'show' => 'properties.show',
            'edit' => 'properties.edit',
            'update' => 'properties.update',
            'destroy' => 'properties.destroy',
        ]);
        
        // Get property parameters for editing
        Route::get('/admin/properties/{property}/parameters', [PropertyController::class, 'getParameters'])->name('properties.parameters');
        
        // Get unit parameters for editing
        Route::get('/admin/units/{unit}/parameters', [UnitController::class, 'getParameters'])->name('units.parameters');
        Route::get('/admin/properties/{property}/units/{unit}/parameters', [PropertyUnitController::class, 'getParameters'])->name('properties.units.parameters');
        
        // Unit management
        Route::resource('admin/units', UnitController::class)->names([
            'index' => 'units.index',
            'create' => 'units.create',
            'store' => 'units.store',
            'show' => 'units.show',
            'edit' => 'units.edit',
            'update' => 'units.update',
            'destroy' => 'units.destroy',
        ]);

        // Property-specific unit management
        Route::resource('admin/properties/{property}/units', PropertyUnitController::class)->names([
            'index' => 'properties.units.index',
            'create' => 'properties.units.create',
            'store' => 'properties.units.store',
            'show' => 'properties.units.show',
            'edit' => 'properties.units.edit',
            'update' => 'properties.units.update',
            'destroy' => 'properties.units.destroy',
        ]);
        
        // Pricing management
        Route::resource('admin/pricings', PricingController::class)->names([
            'index' => 'pricings.index',
            'create' => 'pricings.create',
            'store' => 'pricings.store',
            'show' => 'pricings.show',
            'edit' => 'pricings.edit',
            'update' => 'pricings.update',
            'destroy' => 'pricings.destroy',
        ]);
        
        // Pricing API endpoints
        Route::get('admin/pricings/{id}/details', [PricingController::class, 'getPricingDetails'])->name('pricings.details');
        Route::post('admin/pricings/calculate', [PricingController::class, 'calculatePricing'])->name('pricings.calculate');
        
        // Service management
        Route::resource('admin/services', ServiceController::class)->names([
            'index' => 'services.index',
            'create' => 'services.create',
            'store' => 'services.store',
            'show' => 'services.show',
            'edit' => 'services.edit',
            'update' => 'services.update',
            'destroy' => 'services.destroy',
        ]);
        
        // Amenity management
        Route::resource('admin/amenities', AmenityController::class)->names([
            'index' => 'amenities.index',
            'create' => 'amenities.create',
            'store' => 'amenities.store',
            'show' => 'amenities.show',
            'edit' => 'amenities.edit',
            'update' => 'amenities.update',
            'destroy' => 'amenities.destroy',
        ]);
        
        // Tenant management (admin can view all tenants)
        Route::resource('admin/tenants', TenantController::class)->names([
            'index' => 'admin.tenants.index',
            'create' => 'admin.tenants.create',
            'store' => 'admin.tenants.store',
            'show' => 'admin.tenants.show',
            'edit' => 'admin.tenants.edit',
            'update' => 'admin.tenants.update',
            'destroy' => 'admin.tenants.destroy',
        ]);
        
        // Rental management
        Route::resource('admin/rentals', RentalController::class)->names([
            'index' => 'admin.rentals.index',
            'create' => 'admin.rentals.create',
            'store' => 'admin.rentals.store',
            'show' => 'admin.rentals.show',
            'edit' => 'admin.rentals.edit',
            'update' => 'admin.rentals.update',
            'destroy' => 'admin.rentals.destroy',
        ]);
        
        // Rental Request management
        Route::resource('admin/rental-requests', RentalRequestController::class)->names([
            'index' => 'admin.rental-requests.index',
            'create' => 'admin.rental-requests.create',
            'store' => 'admin.rental-requests.store',
            'show' => 'admin.rental-requests.show',
            'edit' => 'admin.rental-requests.edit',
            'update' => 'admin.rental-requests.update',
            'destroy' => 'admin.rental-requests.destroy',
        ]);
        
        // Booking management
        Route::resource('admin/bookings', BookingController::class)->names([
            'index' => 'admin.bookings.index',
            'create' => 'admin.bookings.create',
            'store' => 'admin.bookings.store',
            'show' => 'admin.bookings.show',
            'edit' => 'admin.bookings.edit',
            'update' => 'admin.bookings.update',
            'destroy' => 'admin.bookings.destroy',
        ]);
        
        // Booking Request management
        Route::resource('admin/booking-requests', BookingRequestController::class)->names([
            'index' => 'admin.booking-requests.index',
            'create' => 'admin.booking-requests.create',
            'store' => 'admin.booking-requests.store',
            'show' => 'admin.booking-requests.show',
            'edit' => 'admin.booking-requests.edit',
            'update' => 'admin.booking-requests.update',
            'destroy' => 'admin.booking-requests.destroy',
        ]);
        
        // Get available units for booking on a specific date
        Route::get('admin/booking-requests/available-units', [BookingRequestController::class, 'getAvailableUnitsForDate'])
            ->name('admin.booking-requests.available-units');
        
        // Invoice management
        Route::resource('admin/invoices', InvoiceController::class)->names([
            'index' => 'admin.invoices.index',
            'create' => 'admin.invoices.create',
            'store' => 'admin.invoices.store',
            'show' => 'admin.invoices.show',
            'edit' => 'admin.invoices.edit',
            'update' => 'admin.invoices.update',
            'destroy' => 'admin.invoices.destroy',
        ]);
        
        // Payment management
        Route::resource('admin/payments', PaymentController::class)->names([
            'index' => 'admin.payments.index',
            'create' => 'admin.payments.create',
            'store' => 'admin.payments.store',
            'show' => 'admin.payments.show',
            'edit' => 'admin.payments.edit',
            'update' => 'admin.payments.update',
            'destroy' => 'admin.payments.destroy',
        ]);
        
        // Property Unit Parameter management
        Route::get('admin/parameters', [PropertyUnitParameterController::class, 'index'])->name('parameters.index');
        Route::get('admin/parameters/create', [PropertyUnitParameterController::class, 'create'])->name('parameters.create');
        Route::post('admin/parameters', [PropertyUnitParameterController::class, 'store'])->name('parameters.store');
        Route::get('admin/parameters/edit', [PropertyUnitParameterController::class, 'edit'])->name('parameters.edit');
        Route::put('admin/parameters', [PropertyUnitParameterController::class, 'update'])->name('parameters.update');
        Route::delete('admin/parameters', [PropertyUnitParameterController::class, 'destroy'])->name('parameters.destroy');
        
        // Legacy Property Unit Parameter management (for modal functionality)
        Route::get('admin/properties/{property}/setup-parameter', [PPUController::class, 'setupParameter'])->name('properties.setup-parameter');
        Route::post('admin/properties/{property}/store-parameters', [PPUController::class, 'storeParameters'])->name('properties.store-parameters');
        Route::get('admin/properties/{property}/units-api', [PPUController::class, 'getPropertyUnits'])->name('properties.get-units');
        
        // API endpoints for modal data
        Route::get('admin/properties-api', [PPUController::class, 'getAllProperties'])->name('properties.api');
        Route::get('admin/pricings', [PPUController::class, 'getPricings'])->name('pricings.api');
        Route::get('admin/amenities', [PPUController::class, 'getAmenities'])->name('amenities.api');
        Route::get('admin/services', [PPUController::class, 'getServices'])->name('services.api');
    });

    // Tenant-only routes
    Route::middleware('auth')->group(function () {
        // Tenant can view their own profile
        Route::get('/profile', [TenantController::class, 'show'])->name('tenant.profile');
        Route::get('/profile/edit', [TenantController::class, 'edit'])->name('tenant.profile.edit');
        Route::put('/profile', [TenantController::class, 'update'])->name('tenant.profile.update');
        
        // Tenant can view available properties
        Route::get('/properties', [PropertyController::class, 'index'])->name('tenant.properties.index');
        Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('tenant.properties.show');
        
        // Tenant can view available units
        Route::get('/units', [UnitController::class, 'index'])->name('tenant.units.index');
        Route::get('/units/{unit}', [UnitController::class, 'show'])->name('tenant.units.show');
        
        // Tenant can manage their rental requests
        Route::resource('rental-requests', RentalController::class)->only(['index', 'create', 'store', 'show']);
        
        // Tenant can manage their booking requests
        Route::resource('booking-requests', BookingController::class)->only(['index', 'create', 'store', 'show']);
        
        // Tenant can view their invoices
        Route::get('/my-invoices', [InvoiceController::class, 'index'])->name('tenant.invoices.index');
        Route::get('/my-invoices/{invoice}', [InvoiceController::class, 'show'])->name('tenant.invoices.show');
        
        // Tenant can view their payments
        Route::get('/my-payments', [PaymentController::class, 'index'])->name('tenant.payments.index');
        Route::get('/my-payments/{payment}', [PaymentController::class, 'show'])->name('tenant.payments.show');
    });
});

Route::get('/dashboard-flow', function () {
    return view('dashboard-flow');
})->middleware('auth');








