<?php

use Illuminate\Support\Facades\Route;
use App\Models\Invoice;

// Temporary debug route - remove after fixing
Route::get('/debug/invoice/{id}', function ($id) {
    $invoice = Invoice::with(['rental.rentalRequest.tenant.user', 'booking.bookingRequest.tenant.user'])
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
