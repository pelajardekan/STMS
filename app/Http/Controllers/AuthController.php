<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.regex' => 'Please enter a valid email address format.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email address.',
            ])->onlyInput('email');
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Incorrect password.',
            ])->onlyInput('email');
        }

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:20|unique:users|regex:/^(\+?6?01)[0-9]{7,9}$/',
            'IC_number' => 'required|string|max:20|unique:tenants|regex:/^(\d{6,8})[-\/](\d{2})[-\/](\d{4})$/',
            'address' => 'nullable|string|max:255',
            'emergency_contact' => 'required|string|max:20|regex:/^(\+?6?01)[0-9]{7,9}$/',
            'additional_info' => 'nullable|string',
        ], [
            'name.required' => 'Full name is required.',
            'name.regex' => 'Name must only contain letters and spaces.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Please enter a valid email address format.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'This phone number is already registered.',
            'phone_number.regex' => 'Please enter a valid Malaysian phone number.',
            'IC_number.required' => 'IC number is required.',
            'IC_number.unique' => 'This IC number is already registered.',
            'IC_number.regex' => 'Please enter a valid Malaysian IC number format (e.g., 900101-01-1234).',
            'emergency_contact.required' => 'Emergency contact is required.',
            'emergency_contact.regex' => 'Please enter a valid Malaysian phone number for emergency contact.',
        ]);

        // Custom validation: Check if phone number and emergency contact are different
        if ($request->phone_number === $request->emergency_contact) {
            return back()->withErrors([
                'emergency_contact' => 'Emergency contact cannot be the same as phone number.',
            ])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role' => 'tenant', // Default role for new registrations
        ]);

        $tenant = Tenant::create([
            'user_id' => $user->id,
            'IC_number' => $request->IC_number,
            'address' => $request->address,
            'emergency_contact' => $request->emergency_contact,
            'additional_info' => $request->additional_info,
        ]);

        // Set success message and redirect back to register page to show modal
        return redirect()->route('register')->with('registration_success', true);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Show the dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        return view('dashboard', compact('user', 'tenant'));
    }
}
