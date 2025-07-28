<?php

use Illuminate\Support\Facades\Route;
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
    Route::middleware('role:admin')->group(function () {
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
            'index' => 'tenants.index',
            'create' => 'tenants.create',
            'store' => 'tenants.store',
            'show' => 'tenants.show',
            'edit' => 'tenants.edit',
            'update' => 'tenants.update',
            'destroy' => 'tenants.destroy',
        ]);
        
        // Rental management
        Route::resource('admin/rentals', RentalController::class)->names([
            'index' => 'rentals.index',
            'create' => 'rentals.create',
            'store' => 'rentals.store',
            'show' => 'rentals.show',
            'edit' => 'rentals.edit',
            'update' => 'rentals.update',
            'destroy' => 'rentals.destroy',
        ]);
        
        // Booking management
        Route::resource('admin/bookings', BookingController::class)->names([
            'index' => 'bookings.index',
            'create' => 'bookings.create',
            'store' => 'bookings.store',
            'show' => 'bookings.show',
            'edit' => 'bookings.edit',
            'update' => 'bookings.update',
            'destroy' => 'bookings.destroy',
        ]);
        
        // Invoice management
        Route::resource('admin/invoices', InvoiceController::class)->names([
            'index' => 'invoices.index',
            'create' => 'invoices.create',
            'store' => 'invoices.store',
            'show' => 'invoices.show',
            'edit' => 'invoices.edit',
            'update' => 'invoices.update',
            'destroy' => 'invoices.destroy',
        ]);
        
        // Payment management
        Route::resource('admin/payments', PaymentController::class)->names([
            'index' => 'payments.index',
            'create' => 'payments.create',
            'store' => 'payments.store',
            'show' => 'payments.show',
            'edit' => 'payments.edit',
            'update' => 'payments.update',
            'destroy' => 'payments.destroy',
        ]);
    });

    // Tenant-only routes
    Route::middleware('role:tenant')->group(function () {
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
