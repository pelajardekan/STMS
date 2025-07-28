@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen bg-gray-900 flex items-center justify-center">
    <div class="w-full max-w-sm px-4">
        <!-- STMS Logo with Purple-to-Blue Gradient - Left Aligned -->
        <div class="mb-8">
            <div class="flex items-start mb-2">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl mr-3 flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-500 to-blue-500 bg-clip-text text-transparent">STMS</h1>
                    <p class="text-sm text-gray-400">Scalable Tenant Management System</p>
                </div>
            </div>
        </div>
        
        <h2 class="text-2xl font-bold text-center mb-8 text-white">Login</h2>
        
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <div class="form-control">
                <label class="label">
                    <span class="label-text text-gray-300">E-mail</span>
                </label>
                <div class="relative">
                    <input 
                        type="email" 
                        name="email"
                        id="email"
                        placeholder="random@random.com" 
                        class="input input-bordered w-full pl-10 bg-gray-800 border-gray-600 text-white placeholder-gray-400 @error('email') border-red-500 @enderror"
                        value="{{ old('email') }}"
                        required
                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                        oninput="validateEmail(this)"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none z-10">
                        <svg id="email-valid-icon" class="h-5 w-5 text-green-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg id="email-invalid-icon" class="h-5 w-5 text-red-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                @error('email')
                    <label class="label">
                        <span class="label-text-alt text-red-400 font-medium">{{ $message }}</span>
                    </label>
                @enderror
                <label class="label">
                    <span id="email-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                </label>
            </div>
            
            <div class="form-control">
                <label class="label">
                    <span class="label-text text-gray-300">Password</span>
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password"
                        id="password"
                        placeholder="••••••" 
                        class="input input-bordered w-full pl-10 pr-10 bg-gray-800 border-gray-600 text-white placeholder-gray-400 @error('password') border-red-500 @enderror"
                        required
                        minlength="8"
                        oninput="validatePassword(this)"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <button 
                        type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center z-10"
                        onclick="togglePassword()"
                    >
                        <svg id="eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-slash-icon" class="h-5 w-5 text-gray-400 hover:text-gray-300 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <label class="label">
                        <span class="label-text-alt text-red-400 font-medium">{{ $message }}</span>
                    </label>
                @enderror
                <label class="label">
                    <span id="password-validation-message" class="label-text-alt text-gray-400 hidden"></span>
                </label>
            </div>
            
            <div class="form-control mt-8">
                <button type="submit" class="btn w-full bg-gradient-to-r from-purple-500 to-blue-500 border-0 hover:from-purple-600 hover:to-blue-600 text-white">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login
                </button>
            </div>
        </form>
        
        <div class="text-center mt-8">
            <p class="text-sm text-gray-400">Don't have an account? 
                <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-medium">Register</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeSlashIcon = document.getElementById('eye-slash-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
    }
}

function validateEmail(input) {
    const emailIcon = document.getElementById('email-valid-icon');
    const emailInvalidIcon = document.getElementById('email-invalid-icon');
    const emailValidationMessage = document.getElementById('email-validation-message');

    const email = input.value;
    const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;

    if (emailPattern.test(email)) {
        emailIcon.classList.remove('hidden');
        emailInvalidIcon.classList.add('hidden');
        emailValidationMessage.classList.add('hidden');
    } else {
        emailIcon.classList.add('hidden');
        emailInvalidIcon.classList.remove('hidden');
        emailValidationMessage.textContent = 'Please enter a valid email address (example@email.com)';
        emailValidationMessage.classList.remove('hidden');
        emailValidationMessage.classList.remove('text-green-400');
        emailValidationMessage.classList.add('text-red-400');
    }
}

function validatePassword(input) {
    const passwordValidationMessage = document.getElementById('password-validation-message');

    const password = input.value;
    const minLength = 8;

    if (password.length >= minLength) {
        passwordValidationMessage.classList.add('hidden');
    } else {
        passwordValidationMessage.textContent = `Password must be at least ${minLength} characters.`;
        passwordValidationMessage.classList.remove('hidden');
        passwordValidationMessage.classList.remove('text-green-400');
        passwordValidationMessage.classList.add('text-red-400');
    }
}
</script>
@endsection 