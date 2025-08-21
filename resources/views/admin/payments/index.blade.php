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
            <a role="tab" class="tab" href="{{ route('admin.invoices.index') }}" id="tab-invoices">Invoices</a>
            <a role="tab" class="tab tab-active" id="tab-payments" onclick="showMainTab('payments')">Payments</a>
        </div>

        <!-- Payments Section -->
        <div id="payments-section">
            <!-- Payment Sub Tabs -->
            <div role="tablist" class="tabs tabs-bordered mb-4">
                <a role="tab" class="tab tab-active" id="tab-payments-all" onclick="showSubTab('payments', 'all')">All</a>
                <a role="tab" class="tab" id="tab-payments-completed" onclick="showSubTab('payments', 'completed')">Completed</a>
                <a role="tab" class="tab" id="tab-payments-pending" onclick="showSubTab('payments', 'pending')">Pending</a>
            </div>

            <!-- Search and Filters Section -->
            <div class="flex flex-col lg:flex-row gap-4 mb-6">
                <div class="flex-1">
                    <div class="form-control">
                        <div class="input-group">
                            <input type="text" placeholder="Search payments..." class="input input-bordered flex-1" id="searchInput" />
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
                        <option value="created_at">Sort by Date</option>
                        <option value="amount">Sort by Amount</option>
                        <option value="payment_date">Sort by Payment Date</option>
                    </select>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="stat bg-base-200 rounded-xl">
                    <div class="stat-title">Total Payments</div>
                    <div class="stat-value text-primary">{{ $payments->total() }}</div>
                    <div class="stat-desc">All payment records</div>
                </div>
                <div class="stat bg-base-200 rounded-xl">
                    <div class="stat-title">Rental Payments</div>
                    <div class="stat-value text-success">{{ $payments->filter(function($payment) { return $payment->invoice && $payment->invoice->rental_id; })->count() }}</div>
                    <div class="stat-desc">Rental-based payments</div>
                </div>
                <div class="stat bg-base-200 rounded-xl">
                    <div class="stat-title">Booking Payments</div>
                    <div class="stat-value text-accent">{{ $payments->filter(function($payment) { return $payment->invoice && $payment->invoice->booking_id; })->count() }}</div>
                    <div class="stat-desc">Booking-based payments</div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="text-base-content font-semibold">Payment ID</th>
                            <th class="text-base-content font-semibold">Tenant</th>
                            <th class="text-base-content font-semibold">Invoice ID</th>
                            <th class="text-base-content font-semibold">Lease Type</th>
                            <th class="text-base-content font-semibold">Amount</th>
                            <th class="text-base-content font-semibold">Payment Date</th>
                            <th class="text-base-content font-semibold">Status</th>
                            <th class="text-base-content font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="hover:bg-base-200/50" data-lease-type="{{ $payment->invoice && $payment->invoice->rental_id ? 'rental' : ($payment->invoice && $payment->invoice->booking_id ? 'booking' : 'unknown') }}">
                                <td class="font-medium text-base-content">
                                    #{{ $payment->payment_id }}
                                </td>
                                <td class="text-base-content">
                                    @php $tenant = $payment->invoice ? $payment->invoice->tenant() : null; @endphp
                                    @if($tenant)
                                        {{ $tenant->name }}
                                    @else
                                        <span class="text-error">No Tenant</span>
                                    @endif
                                </td>
                                <td class="text-base-content">
                                    @if($payment->invoice)
                                        <span class="font-mono">#{{ $payment->invoice->invoice_id }}</span>
                                    @else
                                        <span class="text-error">N/A</span>
                                    @endif
                                </td>
                                <td class="text-base-content">
                                    @if($payment->invoice)
                                        @if($payment->invoice->rental_id)
                                            <span class="badge badge-primary">Rental</span>
                                        @elseif($payment->invoice->booking_id)
                                            <span class="badge badge-secondary">Booking</span>
                                        @else
                                            <span class="badge badge-ghost">Unknown</span>
                                        @endif
                                    @else
                                        <span class="text-error">N/A</span>
                                    @endif
                                </td>
                                <td class="font-semibold text-base-content">
                                    RM{{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="text-base-content">
                                    {{ $payment->payment_date ? $payment->payment_date->format('M j, Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if($payment->status === 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($payment->status === 'failed')
                                        <span class="badge badge-error">Failed</span>
                                    @else
                                        <span class="badge badge-ghost">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.payments.show', $payment->payment_id) }}" 
                                           class="btn btn-sm btn-outline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
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
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-base-300">
            <!-- Results Info -->
            <div class="text-sm text-base-content/60">
                Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} results
            </div>
            
            <!-- Pagination Links -->
            @if($payments->hasPages())
                <div class="join">
                    {{-- Previous Page --}}
                    @if ($payments->onFirstPage())
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $payments->previousPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                        @if ($page == $payments->currentPage())
                            <button class="join-item btn btn-sm btn-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($payments->hasMorePages())
                        <a href="{{ $payments->nextPageUrl() }}" class="join-item btn btn-sm">
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
            @endif
        </div>
    </div>
</div>

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        ['successAlert', 'infoAlert', 'errorAlert'].forEach(id => {
            const alert = document.getElementById(id);
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 5000);

    function hideAlert(id) {
        const alert = document.getElementById(id);
        if (alert) {
            alert.style.transition = 'opacity 0.3s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }
    }

    function showMainTab(tab) {
        // Since this is payments index, all sections are payments
        document.getElementById('payments-section').classList.remove('hidden');
    }

    function showSubTab(mainTab, subTab) {
        // Remove active class from all sub tabs
        document.querySelectorAll('#payments-section .tabs .tab').forEach(t => {
            t.classList.remove('tab-active');
        });
        
        // Add active class to clicked tab
        document.getElementById(`tab-payments-${subTab}`).classList.add('tab-active');
        
        // Filter payments based on status
        filterPaymentsByStatus(subTab);
    }

    function filterPaymentsByStatus(status) {
        const rows = document.querySelectorAll('#payments-section tbody tr[data-lease-type]');
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (status === 'all') {
                row.style.display = '';
                visibleCount++;
            } else {
                const statusBadges = row.querySelectorAll('.badge');
                let paymentStatus = '';
                statusBadges.forEach(badge => {
                    if (badge.textContent.toLowerCase().includes('completed') || 
                        badge.textContent.toLowerCase().includes('pending') || 
                        badge.textContent.toLowerCase().includes('failed')) {
                        paymentStatus = badge.textContent.toLowerCase().trim();
                    }
                });
                if (paymentStatus === status) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        // Show/hide placeholder based on visible rows
        updatePaymentPlaceholder(status, visibleCount);
    }

    function filterPaymentsByLeaseType(leaseType) {
        const rows = document.querySelectorAll('#payments-section tbody tr[data-lease-type]');
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (leaseType === 'all' || leaseType === '') {
                row.style.display = '';
                visibleCount++;
            } else {
                const rowLeaseType = row.getAttribute('data-lease-type');
                if (rowLeaseType === leaseType) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        // Show/hide placeholder based on visible rows
        updatePaymentPlaceholder(leaseType, visibleCount);
    }

    function updatePaymentPlaceholder(filterType, visibleCount) {
        const tbody = document.querySelector('#payments-section tbody');
        const existingPlaceholder = tbody.querySelector('.dynamic-placeholder');
        
        // Remove existing dynamic placeholder
        if (existingPlaceholder) {
            existingPlaceholder.remove();
        }
        
        // Add placeholder if no visible rows
        if (visibleCount === 0) {
            let message = '';
            let subMessage = '';
            
            if (filterType === 'pending') {
                message = 'No pending payments';
                subMessage = 'All payments have been completed or there are no pending payments at this time.';
            } else if (filterType === 'completed') {
                message = 'No completed payments';
                subMessage = 'Completed payment records will appear here when payments are processed.';
            } else if (filterType === 'rental') {
                message = 'No rental payments';
                subMessage = 'Payment records for rental invoices will appear here.';
            } else if (filterType === 'booking') {
                message = 'No booking payments';
                subMessage = 'Payment records for booking invoices will appear here.';
            } else {
                message = 'No payments found';
                subMessage = 'Payment records will appear here when payments are recorded.';
            }
            
            const placeholderRow = document.createElement('tr');
            placeholderRow.className = 'dynamic-placeholder';
            placeholderRow.innerHTML = `
                <td colspan="8" class="text-center py-12">
                    <div class="flex flex-col items-center gap-4">
                        <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-base-content">${message}</h3>
                            <p class="text-base-content/60">${subMessage}</p>
                        </div>
                    </div>
                </td>
            `;
            tbody.appendChild(placeholderRow);
        }
    }

    // Lease type filter dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const leaseTypeFilter = document.getElementById('leaseTypeFilter');
        if (leaseTypeFilter) {
            leaseTypeFilter.addEventListener('change', function() {
                const selectedType = this.value;
                filterPaymentsByLeaseType(selectedType);
                
                // Reset status tabs to "All" when filtering by lease type
                if (selectedType) {
                    document.querySelectorAll('#payments-section .tabs .tab').forEach(t => {
                        t.classList.remove('tab-active');
                    });
                    document.getElementById('tab-payments-all').classList.add('tab-active');
                }
            });
        }
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#payments-section tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endsection
