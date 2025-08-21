@extends('layouts.sidebar')

@section('title', 'Edit Invoice')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Edit Invoice</h1>
                <p class="text-base-content/60 mt-1">Modify invoice details and status</p>
            </div>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Invoices
            </a>
        </div>

        <!-- Invoice Information -->
        <div class="bg-base-200 rounded-xl p-6 mb-8">
            <h2 class="text-xl font-semibold text-base-content mb-4">Invoice Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="font-medium text-base-content/70">Invoice ID:</span>
                    <span class="text-base-content ml-2">#{{ $invoice->invoice_id }}</span>
                </div>
                <div>
                    <span class="font-medium text-base-content/70">Type:</span>
                    <span class="text-base-content ml-2">
                        @if($invoice->rental_id)
                            <span class="badge badge-info">Rental Invoice</span>
                        @elseif($invoice->booking_id)
                            <span class="badge badge-secondary">Booking Invoice</span>
                        @else
                            <span class="badge badge-warning">Unknown Type</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="font-medium text-base-content/70">Tenant:</span>
                    <span class="text-base-content ml-2">
                        @php $tenant = $invoice->tenant(); @endphp
                        @if($tenant)
                            {{ $tenant->name }}
                        @else
                            <span class="text-error">No tenant assigned</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="font-medium text-base-content/70">Current Status:</span>
                    <span class="ml-2">
                        @if($invoice->status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($invoice->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($invoice->status === 'overdue')
                            <span class="badge badge-error">Overdue</span>
                        @else
                            <span class="badge badge-ghost">{{ ucfirst($invoice->status) }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('admin.invoices.update', $invoice->invoice_id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Amount -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Amount (RM)</span>
                    </label>
                    <input type="number" name="amount" value="{{ $invoice->amount }}" step="0.01" min="0"
                           class="input input-bordered focus:input-primary @error('amount') input-error @enderror" required>
                    @error('amount')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Status</span>
                    </label>
                    <select name="status" class="select select-bordered focus:select-primary @error('status') select-error @enderror" required>
                        <option value="">Select Status</option>
                        <option value="pending" {{ $invoice->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ $invoice->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Issue Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Issue Date</span>
                    </label>
                    <input type="date" name="issue_date" value="{{ $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : '' }}" 
                           class="input input-bordered focus:input-primary @error('issue_date') input-error @enderror" required>
                    @error('issue_date')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Due Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Due Date</span>
                    </label>
                    <input type="date" name="due_date" value="{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}" 
                           class="input input-bordered focus:input-primary @error('due_date') input-error @enderror" required>
                    @error('due_date')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>
            </div>

            <!-- Related Record Info (Read-only) -->
            <div class="bg-base-300 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-base-content mb-4">Related Record</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    @if($invoice->rental_id)
                        <div>
                            <span class="font-medium text-base-content/70">Rental ID:</span>
                            <span class="text-base-content ml-2">#{{ $invoice->rental_id }}</span>
                        </div>
                        @if($invoice->rental)
                            <div>
                                <span class="font-medium text-base-content/70">Rental Period:</span>
                                <span class="text-base-content ml-2">
                                    {{ $invoice->rental->start_date ? $invoice->rental->start_date->format('M d, Y') : 'N/A' }} - 
                                    {{ $invoice->rental->end_date ? $invoice->rental->end_date->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                        @endif
                    @elseif($invoice->booking_id)
                        <div>
                            <span class="font-medium text-base-content/70">Booking ID:</span>
                            <span class="text-base-content ml-2">#{{ $invoice->booking_id }}</span>
                        </div>
                        @if($invoice->booking)
                            <div>
                                <span class="font-medium text-base-content/70">Booking Date:</span>
                                <span class="text-base-content ml-2">
                                    {{ $invoice->booking->date ? $invoice->booking->date->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="alert alert-info mt-4">
                    <svg class="stroke-current shrink-0 w-6 h-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>The rental/booking association cannot be changed from this form. Contact system administrator if needed.</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Invoice
                </button>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-ghost">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update status based on dates
    const issueDateInput = document.querySelector('input[name="issue_date"]');
    const dueDateInput = document.querySelector('input[name="due_date"]');
    const statusSelect = document.querySelector('select[name="status"]');

    function checkOverdue() {
        if (dueDateInput.value && statusSelect.value === 'pending') {
            const dueDate = new Date(dueDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (dueDate < today) {
                if (confirm('This invoice appears to be overdue. Would you like to update the status to "Overdue"?')) {
                    statusSelect.value = 'overdue';
                }
            }
        }
    }

    // Set minimum due date to issue date
    issueDateInput.addEventListener('change', function() {
        if (this.value) {
            dueDateInput.min = this.value;
            if (dueDateInput.value && dueDateInput.value < this.value) {
                dueDateInput.value = this.value;
            }
        }
    });

    dueDateInput.addEventListener('change', checkOverdue);
});
</script>
@endsection
