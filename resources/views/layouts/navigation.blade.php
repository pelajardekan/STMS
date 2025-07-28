<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
                        STMS
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        
                        @if(auth()->user()->isAdmin())
                            <x-nav-link href="{{ route('properties.index') }}" :active="request()->routeIs('properties.*')">
                                {{ __('Properties') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('tenants.index') }}" :active="request()->routeIs('tenants.*')">
                                {{ __('Tenants') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('invoices.index') }}" :active="request()->routeIs('invoices.*')">
                                {{ __('Invoices') }}
                            </x-nav-link>
                        @else
                            <x-nav-link href="{{ route('tenant.properties.index') }}" :active="request()->routeIs('tenant.properties.*')">
                                {{ __('Properties') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('tenant.invoices.index') }}" :active="request()->routeIs('tenant.invoices.*')">
                                {{ __('My Invoices') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    <div x-data="{ open: false }" class="ml-3 relative">
                        <div>
                            <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </div>

                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg">
                            <div class="py-1 rounded-md bg-white shadow-xs">
                                @if(auth()->user()->isTenant())
                                    <a href="{{ route('tenant.profile') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        {{ __('Profile') }}
                                    </a>
                                @endif
                                
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>
                    <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav> 