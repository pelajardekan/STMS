@extends('layouts.sidebar')
@section('title', 'Dashboard')
@section('content')
@if(auth()->user()->isAdmin())
    <!-- Admin Dashboard -->
    <!-- Summary Statistics (Top Row - Four Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6">
        <!-- Total Revenue -->
        <x-card class="bg-base-100 border border-base-300">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 flex items-center justify-center">
                    <x-icon name="o-currency-dollar" class="w-6 h-6 text-purple-500" />
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/60">Total Revenue</p>
                    <p class="text-2xl font-bold text-base-content break-all">$459,498,283.14</p>
                </div>
            </div>
        </x-card>

        <!-- Active Rentals -->
        <x-card class="bg-base-100 border border-base-300">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 flex items-center justify-center">
                    <x-icon name="o-document-text" class="w-6 h-6 text-purple-500" />
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/60">Active Rentals</p>
                    <p class="text-2xl font-bold text-base-content">156</p>
                </div>
            </div>
        </x-card>

        <!-- Total Tenants -->
        <x-card class="bg-base-100 border border-base-300">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 flex items-center justify-center">
                    <x-icon name="o-users" class="w-6 h-6 text-purple-500" />
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/60">Total Tenants</p>
                    <p class="text-2xl font-bold text-base-content">488</p>
                </div>
            </div>
        </x-card>

        <!-- Properties -->
        <x-card class="bg-base-100 border border-base-300">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 flex items-center justify-center">
                    <x-icon name="o-building-office" class="w-6 h-6 text-purple-500" />
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/60">Properties</p>
                    <p class="text-2xl font-bold text-base-content">24</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Horizontal Line Separator -->
    <div class="border-b border-base-300"></div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
        <!-- Revenue Chart -->
        <x-card title="Revenue" class="bg-base-100 border border-base-300">
            <div class="h-64 bg-base-200 rounded-lg flex items-center justify-center">
                <div class="w-full h-full flex items-end justify-between px-4 pb-4">
                    <!-- Simple line chart representation -->
                    <div class="flex items-end space-x-1">
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 20px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 35px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 25px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 40px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 30px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 60px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 80px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 45px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 70px;"></div>
                        <div class="w-2 bg-purple-400 rounded-t" style="height: 90px;"></div>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Property Types Chart -->
        <x-card title="Property Types" class="bg-base-100 border border-base-300">
            <div class="flex">
                <!-- Legend -->
                <div class="w-1/2 space-y-2 pr-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-sm text-base-content">Residential</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                        <span class="text-sm text-base-content">Commercial</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm text-base-content">Industrial</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-base-content">Mixed Use</span>
                    </div>
                </div>
                <!-- Donut Chart Placeholder -->
                <div class="w-1/2 h-64 bg-base-200 rounded-lg flex items-center justify-center">
                    <div class="w-32 h-32 rounded-full border-8 border-red-500 border-r-orange-500 border-b-yellow-500 border-l-green-500 relative">
                        <div class="absolute inset-2 bg-base-100 rounded-full"></div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Horizontal Line Separator -->
    <div class="border-b border-base-300"></div>

    <!-- Bottom Cards Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
        <!-- Top Tenants -->
        <x-card title="Top Tenants" class="bg-base-100 border border-base-300">
            <x-slot:action>
                <x-button label="Tenants →" link="/admin/tenants" class="btn-primary btn-sm bg-gradient-to-r from-purple-500 to-blue-500 border-0" />
            </x-slot:action>
            
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="avatar placeholder">
                        <div class="bg-blue-500 text-white rounded-full w-10">
                            <span>A</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-base-content">Abel Cremingg</div>
                        <div class="text-sm text-base-content/60">Brazil</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-base-content">$229,744,336.30</div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="avatar placeholder">
                        <div class="bg-gray-500 text-white rounded-full w-10">
                            <span>A</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-base-content">Abigale Feest</div>
                        <div class="text-sm text-base-content/60">France</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-base-content">$229,713,688.92</div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="avatar placeholder">
                        <div class="bg-brown-500 text-white rounded-full w-10">
                            <span>A</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-base-content">Abigale Bergnaum</div>
                        <div class="text-sm text-base-content/60">United States</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-base-content">$31,479.64</div>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Recent Rentals -->
        <x-card title="Recent Rentals" class="bg-base-100 border border-base-300">
            <x-slot:action>
                <x-button label="Rentals →" link="/admin/rentals" class="btn-primary btn-sm bg-gradient-to-r from-purple-500 to-blue-500 border-0" />
            </x-slot:action>
            
            <div class="overflow-x-auto">
                <table class="table table-zebra bg-base-200">
                    <thead>
                        <tr class="text-base-content/60">
                            <th>#</th>
                            <th>Date</th>
                            <th>Tenant</th>
                            <th>Property</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-base-content">
                        <tr>
                            <td>201</td>
                            <td>Jun 27, 2025</td>
                            <td>Abigale Bergnaum</td>
                            <td>Sunset Apartments</td>
                            <td><span class="badge badge-warning badge-sm">Active</span></td>
                        </tr>
                        <tr>
                            <td>202</td>
                            <td>Jun 27, 2025</td>
                            <td>Abigale Ortiz</td>
                            <td>Downtown Plaza</td>
                            <td><span class="badge badge-success badge-sm">Completed</span></td>
                        </tr>
                        <tr>
                            <td>203</td>
                            <td>Jun 28, 2025</td>
                            <td>John Smith</td>
                            <td>Riverside Complex</td>
                            <td><span class="badge badge-info badge-sm">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
@else
    <!-- Tenant Dashboard -->
    <!-- Summary Statistics (Top Row - Four Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6">
        <!-- My Rentals -->
        <x-card class="bg-base-100 border border-base-300">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 flex items-center justify-center">
                    <x-icon name="o-document-text" class="w-6 h-6 text-purple-500" />
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/60">My Rentals</p>
                    <p class="text-2xl font-bold text-base-content">3</p>
                </div>
            </div>
        </x-card>

        <!-- My Bookings -->
        <x-card class="bg-base-100 border border-base-300">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 flex items-center justify-center">
                    <x-icon name="o-calendar" class="w-6 h-6 text-purple-500" />
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/60">My Bookings</p>
                    <p class="text-2xl font-bold text-base-content">7</p>
                </div>
            </div>
        </x-card>

        <!-- Pending Bills -->
        <x-card class="bg-base-100 border border-base-300">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 flex items-center justify-center">
                    <x-icon name="o-currency-dollar" class="w-6 h-6 text-purple-500" />
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/60">Pending Bills</p>
                    <p class="text-2xl font-bold text-base-content">$2,450</p>
                </div>
            </div>
        </x-card>

        <!-- Favorites -->
        <x-card class="bg-base-100 border border-base-300">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 flex items-center justify-center">
                    <x-icon name="o-heart" class="w-6 h-6 text-purple-500" />
                </div>
                <div class="text-right">
                    <p class="text-sm text-base-content/60">Favorites</p>
                    <p class="text-2xl font-bold text-base-content">12</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Horizontal Line Separator -->
    <div class="border-b border-base-300"></div>

    <!-- Tenant Welcome Card -->
    <div class="p-6">
        <x-card class="bg-base-100 border border-base-300">
            <h3 class="text-lg font-semibold mb-4 text-base-content">Welcome back, {{ auth()->user()->name }}!</h3>
            <p class="text-base-content/60">You have 3 active rentals and 2 pending bookings. Check out the latest available properties or manage your current rentals.</p>
        </x-card>
    </div>
@endif
@endsection 