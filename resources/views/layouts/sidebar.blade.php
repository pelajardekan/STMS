<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'STMS') }} - @yield('title', 'Dashboard')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Livewire Styles -->
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-container { height: 100vh; }
        .sidebar-content { height: calc(100vh - 80px); }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-base-300"
      x-data="{ 
        sidebarExpanded: JSON.parse(localStorage.getItem('sidebarExpanded') ?? 'false'),
      }"
      x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebarExpanded', JSON.stringify(value)))">
    <div class="flex h-screen">
        <!-- SIDEBAR -->
        <aside class="bg-base-300 border-r border-base-300 flex flex-col h-full sidebar-container transition-all duration-300"
               :class="sidebarExpanded ? 'w-64' : 'w-20'">
            <!-- TOP SECTION -->
            <div class="flex-shrink-0">
                <!-- BRAND -->
                <div class="p-4 pt-6 h-20 flex items-center" :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
                    <div class="flex items-center" :class="sidebarExpanded ? 'space-x-3' : ''">
                        <!-- Logo stays fixed size -->
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-500 rounded-lg flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <template x-if="sidebarExpanded">
                            <div class="flex flex-col justify-center">
                                <span class="text-lg font-bold text-base-content leading-tight">STMS</span>
                                <span class="text-xs text-base-content/60 leading-tight">Scalable Tenant Management System</span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="border-b border-base-content/20 mx-4"></div>

                <!-- USER PROFILE -->
                @if($user = auth()->user())
                    <div class="px-4 py-4 h-20 flex items-center" :class="sidebarExpanded ? 'justify-start' : 'justify-center'" x-data="{ userMenuOpen: false }">
                        <div class="flex items-center w-full" :class="sidebarExpanded ? 'space-x-3' : ''">
                            <div class="avatar placeholder flex-shrink-0">
                                <div class="bg-gradient-to-r from-purple-500 to-blue-500 text-white rounded-full w-12 h-12 flex items-center justify-center">
                                    <span class="w-12 h-12 flex items-center justify-center text-lg font-bold text-center">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <template x-if="sidebarExpanded">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-base-content truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-base-content/60 truncate">{{ $user->email }}</p>
                                </div>
                            </template>
                            <template x-if="sidebarExpanded">
                                <div class="relative">
                                    <button @click="userMenuOpen = !userMenuOpen" class="text-base-content/60 hover:text-base-content">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition
                                         class="absolute right-0 top-full mt-2 w-48 bg-base-200 rounded-lg shadow-lg border border-base-300 z-50">
                                        <div class="p-2">
                                            <a href="#" class="flex items-center px-3 py-2 text-sm text-base-content hover:bg-base-300 rounded-md">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Settings
                                            </a>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="w-full flex items-center px-3 py-2 text-sm text-base-content hover:bg-base-300 rounded-md">
                                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                    </svg>
                                                    Logout
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="border-b border-base-content/20 mx-4"></div>
                @endif
            </div>

            <!-- MIDDLE SECTION - NAVIGATION -->
            <div class="flex-1 px-2 py-4 overflow-y-auto sidebar-content">
                <nav class="space-y-2">
                    @if(auth()->user()->isAdmin())
                        <!-- Admin Navigation (from mock UI, adjusted) -->
                        <a href="/dashboard" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Dashboard</span></template>
                        </a>
                        <a href="/admin/users" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Manage Users</span></template>
                        </a>
                        <!-- Properties & Units Menu -->
                        <div class="relative" x-data="{ 
                            propertiesExpanded: {{ request()->is('admin/properties*') || request()->is('admin/units*') ? 'true' : 'false' }}
                        }">
                            <button @click="
                                if (!sidebarExpanded) {
                                    sidebarExpanded = true;
                                    setTimeout(() => { propertiesExpanded = true; }, 100);
                                } else {
                                    propertiesExpanded = !propertiesExpanded;
                                }
                            " 
                                    class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                                    :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <template x-if="sidebarExpanded">
                                    <div class="flex items-center justify-between w-full">
                                        <span class="ml-3 text-left">Manage Properties & Units</span>
                                        <svg class="w-4 h-4 transition-transform duration-200" 
                                             :class="propertiesExpanded ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </template>
                            </button>
                            
                            <!-- Sub-menu items -->
                            <div x-show="propertiesExpanded && sidebarExpanded" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="ml-4 mt-1 space-y-1">
                                <div class="border-l-2 border-base-content/20 pl-4 space-y-1">
                                    <a href="/admin/properties" class="btn btn-ghost btn-sm w-full text-base-content hover:bg-base-200 h-10 justify-start">
                                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="ml-3 text-left">Manage Properties</span>
                                    </a>
                                    <a href="/admin/units" class="btn btn-ghost btn-sm w-full text-base-content hover:bg-base-200 h-10 justify-start">
                                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="ml-3 text-left">Manage Units</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <a href="/admin/parameters" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Manage Property / Unit Parameters</span></template>
                        </a>
                        <a href="/admin/rental-requests" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Manage Rental Requests</span></template>
                        </a>
                        <a href="/admin/booking-requests" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Manage Booking Requests</span></template>
                        </a>
                        <a href="/admin/rentals" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Manage Rentals</span></template>
                        </a>
                        <a href="/admin/bookings" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Manage Bookings</span></template>
                        </a>
                        <a href="/admin/invoices" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Manage Invoices & Payments</span></template>
                        </a>
                    @else
                        <!-- Tenant Navigation (from mock UI, adjusted) -->
                        <a href="/dashboard" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Dashboard</span></template>
                        </a>
                        <a href="/properties" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">View Properties</span></template>
                        </a>
                        <a href="/rental-requests" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">My Rental Requests</span></template>
                        </a>
                        <a href="/booking-requests" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">My Booking Requests</span></template>
                        </a>
                        <a href="/rentals" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">My Rentals</span></template>
                        </a>
                        <a href="/bookings" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">My Bookings</span></template>
                        </a>
                        <a href="/invoices" class="btn btn-ghost w-full text-base-content hover:bg-base-200 h-12"
                           :class="sidebarExpanded ? 'justify-start items-center' : 'justify-center'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" /></svg>
                            <template x-if="sidebarExpanded"><span class="ml-3 text-left">Invoices & Payments</span></template>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- BOTTOM SECTION -->
            <div class="flex-shrink-0 mt-auto">
                <div class="border-b border-base-content/20 mx-4"></div>

                <!-- TOGGLE BUTTON -->
                <div class="p-4">
                    <button @click="sidebarExpanded = !sidebarExpanded"
                            class="btn btn-ghost w-full text-base-content hover:text-base-content h-12"
                            :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <template x-if="sidebarExpanded"><span class="ml-3">Collapse</span></template>
                    </button>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast -->
    <x-toast />

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
