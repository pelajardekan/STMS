@extends('layouts.sidebar')

@section('title', 'Make Payment')

@section('content')
<div class="flex-1 flex flex-col items-center justify-center px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center">Make Payment</h1>
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success mb-6" id="successAlert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('successAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error mb-6" id="errorAlert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('errorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error mb-6" id="validationErrorAlert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <div class="font-bold">Validation Errors:</div>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('validationErrorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.payments.store') }}" class="space-y-6" id="paymentForm">
            @csrf
            
            <!-- Invoice Selection -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Select Invoice</span>
                </label>
                <div class="relative">
                    <select name="invoice_id" id="invoice_id" class="select select-bordered w-full pl-10 @error('invoice_id') select-error @enderror" required onchange="updatePaymentAmount()">
                        <option value="">Select an invoice</option>
                        @forelse($invoices as $invoice)
                            @php $tenant = $invoice->tenant(); @endphp
                            <option value="{{ $invoice->invoice_id }}" data-amount="{{ $invoice->amount }}">
                                Invoice #{{ $invoice->invoice_id }} - {{ $tenant ? $tenant->name : 'No Tenant' }} - RM{{ number_format($invoice->amount, 2) }}
                            </option>
                        @empty
                            <option value="" disabled>No unpaid invoices available</option>
                        @endforelse
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                @if($invoices->isEmpty())
                    <div class="alert alert-info mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>No unpaid invoices are currently available for payment.</span>
                    </div>
                @else
                    <div class="alert alert-info mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $invoices->count() }} invoice(s) available for payment.</span>
                    </div>
                @endif
                @error('invoice_id')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Amount -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Payment Amount (RM)</span>
                </label>
                <div class="relative">
                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        class="input input-bordered w-full pl-10 @error('amount') input-error @enderror"
                        value="{{ old('amount') }}"
                        required
                        readonly
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
                @error('amount')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Payment Method</span>
                </label>
                <div class="relative">
                    <select name="payment_method" id="payment_method" class="select select-bordered w-full pl-10 @error('payment_method') select-error @enderror" required>
                        <option value="">Select payment method</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                        <option value="online_payment" {{ old('payment_method') == 'online_payment' ? 'selected' : '' }}>Online Payment</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
                @error('payment_method')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Date -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Payment Date</span>
                </label>
                <div class="relative">
                    <input
                        type="date"
                        name="payment_date"
                        id="payment_date"
                        class="input input-bordered w-full pl-10 @error('payment_date') input-error @enderror"
                        value="{{ old('payment_date', date('Y-m-d')) }}"
                        required
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                @error('payment_date')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Reference Number -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Reference Number</span>
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="reference_number"
                        id="reference_number"
                        placeholder="Enter reference number (optional)"
                        class="input input-bordered w-full pl-10 @error('reference_number') input-error @enderror"
                        value="{{ old('reference_number') }}"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                @error('reference_number')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Notes -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Notes</span>
                </label>
                <textarea
                    name="notes"
                    id="notes"
                    placeholder="Enter any additional notes (optional)"
                    class="textarea textarea-bordered w-full @error('notes') textarea-error @enderror"
                    rows="3"
                >{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="label-text-alt text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                    Process Payment
                </button>
                <a href="{{ route('admin.invoices.index') }}?tab={{ request()->get('tab', 'invoices') }}" class="btn btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.display = 'none';
    });
}, 5000);

// Manual hide alert function
function hideAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.display = 'none';
    }
}

// Update payment amount when invoice is selected
function updatePaymentAmount() {
    const invoiceSelect = document.getElementById('invoice_id');
    const amountInput = document.getElementById('amount');
    
    if (invoiceSelect.value) {
        const selectedOption = invoiceSelect.options[invoiceSelect.selectedIndex];
        const amount = selectedOption.getAttribute('data-amount');
        amountInput.value = amount;
    } else {
        amountInput.value = '';
    }
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    updatePaymentAmount();
    
    // Add form submission debugging
    const form = document.getElementById('paymentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submission triggered');
            
            // Check required fields
            const requiredFields = ['invoice_id', 'amount', 'payment_method', 'payment_date'];
            const missingFields = [];
            
            requiredFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (!field || !field.value) {
                    missingFields.push(fieldName);
                }
            });
            
            if (missingFields.length > 0) {
                console.error('Missing required fields:', missingFields);
                alert('Please fill in all required fields: ' + missingFields.join(', '));
                e.preventDefault();
                return false;
            }
            
            console.log('Form data:', new FormData(form));
            console.log('Submitting to:', form.action);
        });
    }
});
</script>
@endsection 