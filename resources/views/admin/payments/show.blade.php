@extends('layouts.sidebar')

@section('title', 'Payment Details')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Payment Details</h1>
                <p class="text-base-content/60 mt-1">Payment #{{ $payment->payment_id }}</p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Payments
            </a>
        </div>

        <!-- Payment Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Payment Details -->
            <div class="bg-base-200 rounded-xl p-6">
                <h2 class="text-xl font-semibold text-base-content mb-4">Payment Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="font-medium text-base-content/70">Payment ID:</span>
                        <span class="text-base-content ml-2">#{{ $payment->payment_id }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-base-content/70">Amount:</span>
                        <span class="text-base-content ml-2 font-bold">RM{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-base-content/70">Payment Date:</span>
                        <span class="text-base-content ml-2">{{ $payment->payment_date ? $payment->payment_date->format('F j, Y') : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-base-content/70">Payment Method:</span>
                        <span class="text-base-content ml-2">
                            @if($payment->payment_method)
                                {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                            @else
                                Not specified
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-base-content/70">Status:</span>
                        <span class="ml-2">
                            @if($payment->status === 'completed')
                                <span class="badge badge-success">Completed</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($payment->status === 'failed')
                                <span class="badge badge-error">Failed</span>
                            @else
                                <span class="badge badge-ghost">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </span>
                    </div>
                    @if($payment->reference_number)
                    <div>
                        <span class="font-medium text-base-content/70">Reference Number:</span>
                        <span class="text-base-content ml-2 font-mono">{{ $payment->reference_number }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="font-medium text-base-content/70">Created:</span>
                        <span class="text-base-content ml-2">{{ $payment->created_at->format('F j, Y g:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="bg-base-200 rounded-xl p-6">
                <h2 class="text-xl font-semibold text-base-content mb-4">Invoice Information</h2>
                @if($payment->invoice)
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-base-content/70">Invoice ID:</span>
                            <span class="text-base-content ml-2">#{{ $payment->invoice->invoice_id }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-base-content/70">Invoice Type:</span>
                            <span class="text-base-content ml-2">
                                @if($payment->invoice->rental_id)
                                    <span class="badge badge-info">Rental Invoice</span>
                                @elseif($payment->invoice->booking_id)
                                    <span class="badge badge-secondary">Booking Invoice</span>
                                @else
                                    <span class="badge badge-warning">Unknown Type</span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-base-content/70">Tenant:</span>
                            <span class="text-base-content ml-2">
                                @php $tenant = $payment->invoice->tenant(); @endphp
                                @if($tenant)
                                    {{ $tenant->name }}
                                @else
                                    <span class="text-error">No tenant information</span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-base-content/70">Invoice Status:</span>
                            <span class="ml-2">
                                @if($payment->invoice->status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @elseif($payment->invoice->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($payment->invoice->status === 'overdue')
                                    <span class="badge badge-error">Overdue</span>
                                @else
                                    <span class="badge badge-ghost">{{ ucfirst($payment->invoice->status) }}</span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-base-content/70">Invoice Amount:</span>
                            <span class="text-base-content ml-2">RM{{ number_format($payment->invoice->amount, 2) }}</span>
                        </div>
                    </div>
                @else
                    <div class="alert alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span>Associated invoice not found</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Notes Section -->
        @if($payment->notes)
        <div class="mt-8">
            <div class="bg-base-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-base-content mb-3">Notes</h3>
                <p class="text-base-content/80">{{ $payment->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
