@extends('layouts.sidebar')

@section('title', 'Manage Invoices & Payments')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Manage Invoices & Payments</h1>
                <p class="text-base-content/60 mt-1">View and manage all invoices and payment records</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.payments.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Make Payment
                </a>
            </div>
        </div>

        <!-- Success/Error Messages with Auto-hide -->
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

        @if(session('info'))
            <div class="alert alert-info mb-6" id="infoAlert">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('info') }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('infoAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error mb-6" id="errorAlert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $errors->first() }}</span>
                <button class="btn btn-sm btn-ghost" onclick="hideAlert('errorAlert')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Main Tabs -->
        <div role="tablist" class="tabs tabs-boxed mb-6">
            <a role="tab" class="tab tab-active" id="tab-invoices" onclick="showMainTab('invoices')">Invoices</a>
            <a role="tab" class="tab" href="{{ route('admin.payments.index') }}" id="tab-payments">Payments</a>
        </div>

        <!-- Invoices Section -->
        <div id="invoices-section">
            <!-- Invoice Sub Tabs -->
            <div role="tablist" class="tabs tabs-bordered mb-4">
                <a role="tab" class="tab tab-active" id="tab-invoices-all" onclick="showSubTab('invoices', 'all')">All</a>
                <a role="tab" class="tab" id="tab-invoices-paid" onclick="showSubTab('invoices', 'paid')">Paid</a>
                <a role="tab" class="tab" id="tab-invoices-unpaid" onclick="showSubTab('invoices', 'unpaid')">Unpaid</a>
            </div>

            <!-- Search and Filters Section -->
            <div class="flex flex-col lg:flex-row gap-4 mb-6">
                <div class="flex-1">
                    <div class="form-control">
                        <div class="input-group">
                            <input type="text" placeholder="Search invoices..." class="input input-bordered flex-1" id="searchInput" />
                            <button class="btn btn-square">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <select class="select select-bordered" id="leaseTypeFilter">
                        <option value="">All Types</option>
                        <option value="rental">Rental</option>
                        <option value="booking">Booking</option>
                    </select>
                    <select class="select select-bordered" id="sortBy">
                        <option value="">Sort By</option>
                        <option value="tenant">Tenant</option>
                        <option value="amount">Amount</option>
                        <option value="status">Status</option>
                        <option value="created_at">Date Created</option>
                    </select>
                </div>
            </div>

            <!-- All Invoices Table -->
            <div id="invoices-all">
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr class="bg-base-200">
                                <th class="text-base-content font-semibold">Invoice ID</th>
                                <th class="text-base-content font-semibold">Tenant</th>
                                <th class="text-base-content font-semibold">Type</th>
                                <th class="text-base-content font-semibold">Amount</th>
                                <th class="text-base-content font-semibold">Due Date</th>
                                <th class="text-base-content font-semibold">Status</th>
                                <th class="text-base-content font-semibold">Created</th>
                                <th class="text-base-content font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-base-200/50 transition-colors" data-lease-type="{{ $invoice->rental_id ? 'rental' : ($invoice->booking_id ? 'booking' : 'unknown') }}">
                                    <td>
                                        <div>
                                            <div class="font-bold text-primary">INV-{{ str_pad($invoice->invoice_id, 3, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-sm text-base-content/60">ID: {{ $invoice->invoice_id }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @php $tenant = $invoice->tenant(); @endphp
                                            @if($tenant)
                                                <div class="font-medium text-base-content">{{ $tenant->name }}</div>
                                                <div class="text-sm text-base-content/60">{{ $tenant->email }}</div>
                                            @else
                                                <div class="font-medium text-error">No Tenant</div>
                                                <div class="text-sm text-base-content/60">
                                                    @if($invoice->rental_id)
                                                        Rental ID: {{ $invoice->rental_id }}
                                                        @if(!$invoice->rental) | Missing Rental @endif
                                                    @elseif($invoice->booking_id)
                                                        Booking ID: {{ $invoice->booking_id }}
                                                        @if(!$invoice->booking) | Missing Booking @endif
                                                    @else
                                                        No rental_id or booking_id
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($invoice->rental_id)
                                            <span class="badge badge-primary gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                                Rental
                                            </span>
                                        @else
                                            <span class="badge badge-secondary gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Booking
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-medium text-base-content">MYR {{ number_format($invoice->amount, 2) }}</div>
                                            <div class="text-sm text-base-content/60">Issued: {{ $invoice->issue_date->format('M d, Y') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-base-content/60">
                                            {{ $invoice->due_date->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($invoice->status === 'paid')
                                            <span class="badge badge-success gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Paid
                                            </span>
                                        @elseif($invoice->status === 'unpaid')
                                            <span class="badge badge-warning gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Unpaid
                                            </span>
                                        @else
                                            <span class="badge badge-error gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                                Overdue
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-sm text-base-content/60">
                                            {{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex justify-center gap-1">
                                            <a href="{{ route('admin.invoices.edit', $invoice) }}" 
                                               class="btn btn-ghost btn-sm" 
                                               title="Edit Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <button class="btn btn-ghost btn-sm text-error" 
                                                    onclick="openDeleteModal({{ $invoice->invoice_id }}, '{{ $invoice->tenant() ? $invoice->tenant()->name : 'No Tenant' }}')"
                                                    title="Delete Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <h3 class="text-lg font-semibold text-base-content">No invoices found</h3>
                                                <p class="text-base-content/60">Get started by creating your first invoice.</p>
                                            </div>
                                            <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Add First Invoice
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paid Invoices Table -->
            <div id="invoices-paid" class="hidden">
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr class="bg-base-200">
                                <th class="text-base-content font-semibold">Invoice ID</th>
                                <th class="text-base-content font-semibold">Tenant</th>
                                <th class="text-base-content font-semibold">Type</th>
                                <th class="text-base-content font-semibold">Amount</th>
                                <th class="text-base-content font-semibold">Due Date</th>
                                <th class="text-base-content font-semibold">Status</th>
                                <th class="text-base-content font-semibold">Created</th>
                                <th class="text-base-content font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $hasPaidInvoices = false; @endphp
                            @foreach($invoices as $invoice)
                                @if($invoice->status === 'paid')
                                    @php $hasPaidInvoices = true; @endphp
                                    <tr class="hover:bg-base-200/50 transition-colors">
                                        <td>
                                            <div>
                                                <div class="font-bold text-primary">INV-{{ str_pad($invoice->invoice_id, 3, '0', STR_PAD_LEFT) }}</div>
                                                <div class="text-sm text-base-content/60">ID: {{ $invoice->invoice_id }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                @php $tenant = $invoice->tenant(); @endphp
                                                @if($tenant)
                                                    <div class="font-medium text-base-content">{{ $tenant->name }}</div>
                                                    <div class="text-sm text-base-content/60">{{ $tenant->email }}</div>
                                                @else
                                                    <div class="font-medium text-base-content">Unknown Tenant</div>
                                                    <div class="text-sm text-base-content/60">No Email</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($invoice->rental_id)
                                                <span class="badge badge-primary gap-1">Rental</span>
                                            @else
                                                <span class="badge badge-secondary gap-1">Booking</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <div class="font-medium text-base-content">MYR {{ number_format($invoice->amount, 2) }}</div>
                                                <div class="text-sm text-base-content/60">Issued: {{ $invoice->issue_date->format('M d, Y') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm text-base-content/60">
                                                {{ $invoice->due_date->format('M d, Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-success gap-1">Paid</span>
                                        </td>
                                        <td>
                                            <div class="text-sm text-base-content/60">
                                                {{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('admin.invoices.edit', $invoice) }}" 
                                                   class="btn btn-ghost btn-sm" 
                                                   title="View Invoice">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            @if(!$hasPaidInvoices)
                                <tr>
                                    <td colspan="8" class="text-center py-12">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <h3 class="text-lg font-semibold text-base-content">No paid invoices</h3>
                                                <p class="text-base-content/60">Paid invoices will appear here when payments are processed.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Unpaid Invoices Table -->
            <div id="invoices-unpaid" class="hidden">
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr class="bg-base-200">
                                <th class="text-base-content font-semibold">Invoice ID</th>
                                <th class="text-base-content font-semibold">Tenant</th>
                                <th class="text-base-content font-semibold">Type</th>
                                <th class="text-base-content font-semibold">Amount</th>
                                <th class="text-base-content font-semibold">Due Date</th>
                                <th class="text-base-content font-semibold">Status</th>
                                <th class="text-base-content font-semibold">Created</th>
                                <th class="text-base-content font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $hasUnpaidInvoices = false; @endphp
                            @foreach($invoices as $invoice)
                                @if($invoice->status === 'unpaid' || $invoice->status === 'overdue')
                                    @php $hasUnpaidInvoices = true; @endphp
                                    <tr class="hover:bg-base-200/50 transition-colors">
                                        <td>
                                            <div>
                                                <div class="font-bold text-primary">INV-{{ str_pad($invoice->invoice_id, 3, '0', STR_PAD_LEFT) }}</div>
                                                <div class="text-sm text-base-content/60">ID: {{ $invoice->invoice_id }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                @php $tenant = $invoice->tenant(); @endphp
                                                @if($tenant)
                                                    <div class="font-medium text-base-content">{{ $tenant->name }}</div>
                                                    <div class="text-sm text-base-content/60">{{ $tenant->email }}</div>
                                                @else
                                                    <div class="font-medium text-base-content">Unknown Tenant</div>
                                                    <div class="text-sm text-base-content/60">No Email</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($invoice->rental_id)
                                                <span class="badge badge-primary gap-1">Rental</span>
                                            @else
                                                <span class="badge badge-secondary gap-1">Booking</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <div class="font-medium text-base-content">MYR {{ number_format($invoice->amount, 2) }}</div>
                                                <div class="text-sm text-base-content/60">Issued: {{ $invoice->issue_date->format('M d, Y') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm text-base-content/60">
                                                {{ $invoice->due_date->format('M d, Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($invoice->status === 'unpaid')
                                                <span class="badge badge-warning gap-1">Unpaid</span>
                                            @else
                                                <span class="badge badge-error gap-1">Overdue</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-sm text-base-content/60">
                                                {{ $invoice->created_at ? $invoice->created_at->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex justify-center gap-1">
                                                <a href="{{ route('admin.invoices.edit', $invoice) }}" 
                                                   class="btn btn-ghost btn-sm" 
                                                   title="Edit Invoice">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            @if(!$hasUnpaidInvoices)
                                <tr>
                                    <td colspan="8" class="text-center py-12">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <h3 class="text-lg font-semibold text-base-content">No unpaid invoices</h3>
                                                <p class="text-base-content/60">All invoices have been paid. Great job!</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payments Section -->
        <div id="payments-section" class="hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Payment ID</th>
                            <th class="text-base-content font-semibold">Tenant</th>
                            <th class="text-base-content font-semibold">Invoice ID</th>
                            <th class="text-base-content font-semibold">Amount</th>
                            <th class="text-base-content font-semibold">Payment Date</th>
                            <th class="text-base-content font-semibold">Status</th>
                            <th class="text-base-content font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center gap-4">
                                    <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-semibold text-base-content">No payments found</h3>
                                        <p class="text-base-content/60">Payment records will appear here when payments are recorded.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-base-300">
            <!-- Results Info -->
            <div class="text-sm text-base-content/60">
                Showing {{ $invoices->firstItem() ?? 0 }} to {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }} results
            </div>
            
            <!-- Pagination Links -->
            @if($invoices->hasPages())
                <div class="join">
                    {{-- Previous Page --}}
                    @if ($invoices->onFirstPage())
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $invoices->previousPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($invoices->getUrlRange(1, $invoices->lastPage()) as $page => $url)
                        @if ($page == $invoices->currentPage())
                            <button class="join-item btn btn-sm btn-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($invoices->hasMorePages())
                        <a href="{{ $invoices->nextPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @else
                <!-- Single page indicator -->
                <div class="text-sm text-base-content/60">
                    Page 1 of 1
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Delete Invoice</h3>
        <p class="py-4">Are you sure you want to delete the invoice for <span id="deleteTenantName" class="font-semibold"></span>? This action cannot be undone.</p>
        <div class="modal-action">
            <button class="btn btn-ghost" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }
        }, 5000);
    });
});

// Manual hide alert function
function hideAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.remove();
            }
        }, 500);
    }
}

// Main tab functionality
function showMainTab(tab) {
    // Hide all main sections
    document.getElementById('invoices-section').classList.add('hidden');
    document.getElementById('payments-section').classList.add('hidden');
    
    // Remove active class from all main tabs
    document.getElementById('tab-invoices').classList.remove('tab-active');
    document.getElementById('tab-payments').classList.remove('tab-active');
    
    // Show selected main section and add active class
    if(tab === 'invoices') {
        document.getElementById('invoices-section').classList.remove('hidden');
        document.getElementById('tab-invoices').classList.add('tab-active');
        showSubTab('invoices', 'all');
    } else {
        document.getElementById('payments-section').classList.remove('hidden');
        document.getElementById('tab-payments').classList.add('tab-active');
    }
}

// Sub tab functionality
function showSubTab(main, sub) {
    const sections = ['all', 'paid', 'unpaid'];
    sections.forEach(s => {
        document.getElementById(`${main}-${s}`).classList.add('hidden');
        document.getElementById(`tab-${main}-${s}`).classList.remove('tab-active');
    });
    document.getElementById(`${main}-${sub}`).classList.remove('hidden');
    document.getElementById(`tab-${main}-${sub}`).classList.add('tab-active');
}

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// Lease type filter functionality
const leaseTypeFilter = document.getElementById('leaseTypeFilter');
if (leaseTypeFilter) {
    leaseTypeFilter.addEventListener('change', function() {
        const selectedType = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr[data-lease-type]');
        
        rows.forEach(row => {
            const leaseType = row.getAttribute('data-lease-type');
            if (selectedType === '' || leaseType === selectedType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// Sort functionality
const sortBy = document.getElementById('sortBy');
if (sortBy) {
    sortBy.addEventListener('change', function() {
        const sortField = this.value;
        const tbody = document.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(sortField) {
                case 'tenant':
                    aValue = a.querySelector('td:nth-child(2)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(2)').textContent.trim();
                    break;
                case 'amount':
                    aValue = parseFloat(a.querySelector('td:nth-child(4)').textContent.replace(/[^0-9.]/g, ''));
                    bValue = parseFloat(b.querySelector('td:nth-child(4)').textContent.replace(/[^0-9.]/g, ''));
                    break;
                case 'status':
                    aValue = a.querySelector('td:nth-child(6)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(6)').textContent.trim();
                    break;
                case 'created_at':
                    aValue = a.querySelector('td:nth-child(7)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(7)').textContent.trim();
                    break;
                default:
                    return 0;
            }
            
            if (sortField === 'amount') {
                return aValue - bValue;
            } else {
                return aValue.localeCompare(bValue);
            }
        });
        
        rows.forEach(row => tbody.appendChild(row));
    });
}

// Delete modal functionality
function openDeleteModal(invoiceId, tenantName) {
    document.getElementById('deleteTenantName').textContent = tenantName;
    document.getElementById('deleteForm').action = `/admin/invoices/${invoiceId}`;
    document.getElementById('deleteModal').classList.add('modal-open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('modal-open');
}

// Initialize tabs on page load
document.addEventListener('DOMContentLoaded', function() {
    showMainTab('invoices');
});
</script>
@endsection 