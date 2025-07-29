<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants.
     */
    public function index()
    {
        $tenants = Tenant::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        return view('admin.tenants.create');
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'IC_number' => 'nullable|string|max:50',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                'unique:users',
                'regex:/^(\+?6?01)[0-9]{7,9}$/'
            ],
            'emergency_contact' => 'nullable|string|max:20|regex:/^(\+?6?01)[0-9]{7,9}$/',
            'address' => 'nullable|string|max:500',
            'additional_info' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Tenant name is required.',
            'name.regex' => 'Name must only contain letters and spaces.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already registered.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'This phone number is already registered.',
            'phone_number.regex' => 'Please enter a valid Malaysian phone number.',
            'emergency_contact.regex' => 'Please enter a valid Malaysian phone number.',
        ]);

        try {
            // Create user first
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make('password123'), // Default password
                'role' => 'tenant',
            ]);

            // Create tenant
            $tenant = Tenant::create([
                'user_id' => $user->id,
                'IC_number' => $request->IC_number,
                'emergency_contact' => $request->emergency_contact,
                'address' => $request->address,
                'additional_info' => $request->additional_info,
            ]);

            return redirect()->route('admin.tenants.index')
                ->with('success', 'Tenant created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create tenant: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show(string $id)
    {
        $tenant = Tenant::with(['user', 'rentalRequests.property', 'rentalRequests.unit', 'bookingRequests.property', 'bookingRequests.unit'])
            ->findOrFail($id);
        return view('admin.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $user = $tenant->user;

        $request->validate([
            'user_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'IC_number' => 'nullable|string|max:50',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
                'regex:/^(\+?6?01)[0-9]{7,9}$/'
            ],
            'emergency_contact' => 'nullable|string|max:20|regex:/^(\+?6?01)[0-9]{7,9}$/',
            'address' => 'nullable|string|max:500',
            'additional_info' => 'nullable|string|max:1000',
        ], [
            'user_name.required' => 'Tenant name is required.',
            'user_name.regex' => 'Name must only contain letters and spaces.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'This phone number is already registered.',
            'phone_number.regex' => 'Please enter a valid Malaysian phone number.',
            'emergency_contact.regex' => 'Please enter a valid Malaysian phone number.',
        ]);

        try {
            // Update user information
            $user->update([
                'name' => $request->user_name,
                'phone_number' => $request->phone_number,
            ]);

            // Update tenant information
            $tenant->update([
                'IC_number' => $request->IC_number,
                'emergency_contact' => $request->emergency_contact,
                'address' => $request->address,
                'additional_info' => $request->additional_info,
            ]);

            return redirect()->route('admin.users.show', $user->id)->with('success', 'Tenant profile updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update tenant profile. Please try again.'])->withInput();
        }
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(string $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $user = $tenant->user;
            
            // Delete tenant first
            $tenant->delete();
            
            // Delete associated user
            $user->delete();
            
            return redirect()->route('admin.tenants.index')
                ->with('success', 'Tenant deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tenant: ' . $e->getMessage());
        }
    }
}
