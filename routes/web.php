<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
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
use App\Http\Controllers\TenantPropertyController;
use App\Http\Controllers\TenantUnitController;
use App\Http\Controllers\TenantRentalRequestController;
use App\Http\Controllers\TenantBookingRequestController;
use App\Http\Controllers\TenantRentalController;
use App\Http\Controllers\TenantBookingController;
use App\Http\Controllers\TenantInvoiceController;
use App\Http\Controllers\TenantPaymentController;

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
        
        // Temporary debug route
        Route::get('admin/payments/debug/invoices', [PaymentController::class, 'debug'])->name('admin.payments.debug');
        
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
    Route::middleware(['auth', 'role:tenant'])->group(function () {
        // Tenant can view their own profile
        Route::get('/profile', [TenantController::class, 'show'])->name('tenant.profile');
        Route::get('/profile/edit', [TenantController::class, 'edit'])->name('tenant.profile.edit');
        Route::put('/profile', [TenantController::class, 'update'])->name('tenant.profile.update');
        
        // Tenant can view available properties
        Route::get('/properties', [TenantPropertyController::class, 'index'])->name('tenant.properties.index');
        Route::get('/properties/{property}', [TenantPropertyController::class, 'show'])->name('tenant.properties.show');
        
        // Tenant can view available units
        Route::get('/units', [TenantUnitController::class, 'index'])->name('tenant.units.index');
        Route::get('/units/{unit}', [TenantUnitController::class, 'show'])->name('tenant.units.show');
        
        // Tenant can manage their rental requests
        Route::resource('rental-requests', TenantRentalRequestController::class)->names([
            'index' => 'tenant.rental-requests.index',
            'create' => 'tenant.rental-requests.create',
            'store' => 'tenant.rental-requests.store',
            'show' => 'tenant.rental-requests.show',
        ]);
        
        // Tenant can manage their booking requests
        Route::resource('booking-requests', TenantBookingRequestController::class)->names([
            'index' => 'tenant.booking-requests.index',
            'create' => 'tenant.booking-requests.create',
            'store' => 'tenant.booking-requests.store',
            'show' => 'tenant.booking-requests.show',
        ]);
        
        // Get available units for booking on a specific date
        Route::get('booking-requests/available-units', [TenantBookingRequestController::class, 'getAvailableUnitsForDate'])
            ->name('tenant.booking-requests.available-units');
        
        // Tenant can view their rentals
        Route::resource('rentals', TenantRentalController::class)->names([
            'index' => 'tenant.rentals.index',
            'show' => 'tenant.rentals.show',
        ]);
        
        // Tenant can view their bookings
        Route::resource('bookings', TenantBookingController::class)->names([
            'index' => 'tenant.bookings.index',
            'show' => 'tenant.bookings.show',
        ]);
        
        // Tenant can view their invoices
        Route::resource('invoices', TenantInvoiceController::class)->names([
            'index' => 'tenant.invoices.index',
            'show' => 'tenant.invoices.show',
        ]);
        
        // Tenant can view and create payments
        Route::resource('payments', TenantPaymentController::class)->names([
            'index' => 'tenant.payments.index',
            'create' => 'tenant.payments.create',
            'store' => 'tenant.payments.store',
            'show' => 'tenant.payments.show',
        ]);
    });
});

Route::get('/dashboard-flow', function () {
    return view('dashboard-flow');
})->middleware('auth');

// Database setup and test routes for Azure deployment
Route::get('/setup-database', function () {
    try {
        echo "<h2>🚀 STMS Database Setup</h2>";
        echo "<pre>";
        
        // Test database connection
        echo "📋 Testing database connection...\n";
        DB::connection()->getPdo();
        echo "✅ Database connection successful!\n\n";
        
        // Check migration status
        echo "📋 Checking migration status...\n";
        Artisan::call('migrate:status');
        echo Artisan::output() . "\n";
        
        // Run migrations
        echo "🔄 Running migrations...\n";
        Artisan::call('migrate', ['--force' => true]);
        echo Artisan::output() . "\n";
        
        // Run seeders
        echo "🌱 Running seeders...\n";
        Artisan::call('db:seed', ['--force' => true]);
        echo Artisan::output() . "\n";
        
        echo "✅ Database setup completed successfully!\n";
        echo "</pre>";
        
    } catch (\Exception $e) {
        echo "<h2>❌ Database Setup Error</h2>";
        echo "<pre style='color: red;'>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "</pre>";
    }
});

Route::get('/test-db', function () {
    try {
        echo "<h2>🧪 Database Connection Test</h2>";
        echo "<pre>";
        
        // Test connection
        DB::connection()->getPdo();
        echo "✅ Database connection successful!\n";
        
        // Show database info
        $result = DB::select('SELECT DATABASE() as db_name, USER() as db_user, VERSION() as db_version');
        echo "Database: " . $result[0]->db_name . "\n";
        echo "User: " . $result[0]->db_user . "\n";
        echo "Version: " . $result[0]->db_version . "\n";
        
        // Show tables
        $tables = DB::select('SHOW TABLES');
        echo "\nTables (" . count($tables) . "):\n";
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "- " . $tableName . "\n";
        }
        
        echo "</pre>";
    } catch (\Exception $e) {
        echo "<h2>❌ Database Connection Error</h2>";
        echo "<pre style='color: red;'>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "</pre>";
    }
});

Route::get('/seed-database', function () {
    try {
        echo "<h2>🌱 Database Seeding</h2>";
        echo "<pre>";
        
        // Run seeders manually
        echo "Running AdminUserSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'AdminUserSeeder', '--force' => true]);
        echo "✅ AdminUserSeeder completed\n\n";
        
        echo "Running ParameterSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'ParameterSeeder', '--force' => true]);
        echo "✅ ParameterSeeder completed\n\n";
        
        echo "Running PropertySeeder...\n";
        Artisan::call('db:seed', ['--class' => 'PropertySeeder', '--force' => true]);
        echo "✅ PropertySeeder completed\n\n";
        
        echo "Running UnitSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'UnitSeeder', '--force' => true]);
        echo "✅ UnitSeeder completed\n\n";
        
        // Create test user directly
        echo "Creating test user...\n";
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ]
        );
        echo "✅ Test user created: {$user->name} ({$user->email})\n\n";
        
        echo "🎉 All seeding completed successfully!\n";
        echo "</pre>";
    } catch (\Exception $e) {
        echo "<h2>❌ Seeding Error</h2>";
        echo "<pre style='color: red;'>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "</pre>";
    }
});

Route::get('/check-data', function () {
    try {
        echo "<h2>📊 Database Data Check</h2>";
        echo "<pre>";
        
        // Check users table
        $userCount = DB::table('users')->count();
        echo "Users: $userCount\n";
        
        if ($userCount > 0) {
            $users = DB::table('users')->select('id', 'name', 'email', 'role')->get();
            foreach ($users as $user) {
                echo "  - {$user->name} ({$user->email}) - Role: {$user->role}\n";
            }
        }
        
        // Check other key tables
        $tables = ['tenants', 'properties', 'units', 'services', 'amenities'];
        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            echo "{$table}: $count records\n";
        }
        
        echo "</pre>";
    } catch (\Exception $e) {
        echo "<h2>❌ Data Check Error</h2>";
        echo "<pre style='color: red;'>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "</pre>";
    }
});

// Temporary debug route - remove after fixing
Route::get('/debug/invoice/{id}', function ($id) {
    $invoice = \App\Models\Invoice::with(['rental.rentalRequest.tenant.user', 'booking.bookingRequest.tenant.user'])
        ->find($id);
    
    if (!$invoice) {
        return response()->json(['error' => 'Invoice not found']);
    }
    
    $debug = $invoice->debugTenant();
    $tenant = $invoice->tenant();
    
    return response()->json([
        'invoice_debug' => $debug,
        'tenant_result' => $tenant ? [
            'name' => $tenant->name,
            'email' => $tenant->email,
            'tenant_id' => $tenant->tenant_id,
        ] : null,
        'type' => $invoice->type,
    ]);
})->name('debug.invoice');