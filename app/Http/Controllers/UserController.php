<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use App\Models\RentalRequest;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'phone_number' => 'required|string|max:20|unique:users|regex:/^(\+?6?01)[0-9]{7,9}$/',
            'role' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ];

        $validationMessages = [
            'name.required' => 'Full name is required.',
            'name.regex' => 'Name must only contain letters and spaces.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Please enter a valid email address format.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'This phone number is already registered.',
            'phone_number.regex' => 'Please enter a valid Malaysian phone number.',
            'role.required' => 'User role is required.',
            'role.in' => 'Please select a valid role.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];

        // Add tenant-specific validation rules if role is tenant
        if ($request->role === 'tenant') {
            $validationRules['emergency_contact'] = 'nullable|string|max:20';
            $validationRules['IC_number'] = 'required|string|max:50|regex:/^(\d{6,8})[-\/](\d{2})[-\/](\d{4})$/';
            $validationRules['address'] = 'nullable|string|max:500';
            $validationRules['additional_info'] = 'nullable|string|max:1000';

            $validationMessages['IC_number.required'] = 'IC number is required.';
            $validationMessages['IC_number.regex'] = 'Please enter a valid Malaysian IC number (e.g., 900101-01-1234).';
        }

        $request->validate($validationRules, $validationMessages);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);

            // If user is a tenant, create tenant profile with all the details
            if ($request->role === 'tenant') {
                Tenant::create([
                    'user_id' => $user->id,
                    'IC_number' => $request->IC_number,
                    'address' => $request->address ?? null,
                    'emergency_contact' => $request->emergency_contact ?? null,
                    'additional_info' => $request->additional_info ?? null,
                ]);
            }

            return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('User creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create user. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        
        // If user is a tenant, always show tenant profile
        if ($user->role === 'tenant') {
            $tenant = $user->tenant;
            if ($tenant) {
                return view('admin.tenants.show', compact('tenant'));
            } else {
                // If tenant profile doesn't exist, create it
                $tenant = Tenant::create([
                    'user_id' => $user->id,
                    'IC_number' => null,
                    'address' => null,
                    'emergency_contact' => null,
                    'additional_info' => null,
                ]);
                return view('admin.tenants.show', compact('tenant'));
            }
        }
        
        // For admin users, redirect to users index with message
        return redirect()->route('admin.users.index')->with('info', 'Admin users do not have detailed profiles.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
                'regex:/^(\+?6?01)[0-9]{7,9}$/'
            ],
            'role' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Full name is required.',
            'name.regex' => 'Name must only contain letters and spaces.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Please enter a valid email address format.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'This phone number is already registered.',
            'phone_number.regex' => 'Please enter a valid Malaysian phone number.',
            'role.required' => 'User role is required.',
            'role.in' => 'Please select a valid role.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'role' => $request->role,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // If role changed to tenant and no tenant profile exists, create one
            if ($request->role === 'tenant' && !$user->tenant) {
                Tenant::create([
                    'user_id' => $user->id,
                    'IC_number' => null,
                    'address' => null,
                    'emergency_contact' => null,
                    'additional_info' => null,
                ]);
            }

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update user. Please try again.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === Auth::user()->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        // Prevent deletion of other admin users
        if ($user->role === 'admin') {
            return back()->withErrors(['error' => 'Admin users cannot be deleted.']);
        }

        // Check if tenant has rental requests or booking requests
        if ($user->role === 'tenant') {
            $hasRentalRequests = \App\Models\RentalRequest::where('tenant_id', $user->tenant->id ?? 0)->exists();
            $hasBookingRequests = \App\Models\BookingRequest::where('tenant_id', $user->tenant->id ?? 0)->exists();
            
            if ($hasRentalRequests || $hasBookingRequests) {
                $message = 'Cannot delete tenant with active ';
                if ($hasRentalRequests && $hasBookingRequests) {
                    $message .= 'rental and booking requests.';
                } elseif ($hasRentalRequests) {
                    $message .= 'rental requests.';
                } else {
                    $message .= 'booking requests.';
                }
                return back()->withErrors(['error' => $message]);
            }
        }

        try {
            // Delete user (tenant profile will be automatically deleted via cascade)
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            Log::error('User deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete user. Please try again.']);
        }
    }
} 