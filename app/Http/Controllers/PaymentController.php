<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['invoice.tenant'])->orderBy('created_at', 'desc')->get();
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Get unpaid invoices for payment
            $invoices = Invoice::with('tenant')
                ->where('status', 'unpaid')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            // If database connection fails, provide empty collection
            $invoices = collect([]);
        }
        
        return view('admin.payments.create', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,online_payment',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Create the payment
            $payment = Payment::create([
                'invoice_id' => $request->invoice_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            // Update the invoice status to paid
            $invoice = Invoice::find($request->invoice_id);
            $invoice->update(['status' => 'paid']);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to process payment. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['invoice.tenant'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payment = Payment::with(['invoice.tenant'])->findOrFail($id);
        $invoices = Invoice::with('tenant')->orderBy('created_at', 'desc')->get();
        return view('admin.payments.edit', compact('payment', 'invoices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,online_payment',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($id);
            $payment->update([
                'invoice_id' => $request->invoice_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to update payment. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($id);
            
            // Update the invoice status back to unpaid
            $invoice = $payment->invoice;
            $invoice->update(['status' => 'unpaid']);
            
            // Delete the payment
            $payment->delete();

            DB::commit();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete payment. Please try again.');
        }
    }
}
