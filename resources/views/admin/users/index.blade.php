@extends('layouts.sidebar')

@section('title', 'Manage Users')

@section('content')
<div class="flex-1 flex flex-col px-4 md:px-8 py-8 w-full">
    <div class="bg-base-100 shadow-xl rounded-2xl p-8 w-full mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-base-content">Manage Users</h1>
                <p class="text-base-content/60 mt-1">Manage all users in the system</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add User
                </a>
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="flex-1">
                <div class="form-control">
                    <div class="input-group">
                        <input type="text" placeholder="Search users..." class="input input-bordered flex-1" id="searchInput" />
                        <button class="btn btn-square">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <select class="select select-bordered" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="tenant">Tenant</option>
                </select>
                <select class="select select-bordered" id="sortBy">
                    <option value="">Sort By</option>
                    <option value="name">Name</option>
                    <option value="email">Email</option>
                    <option value="role">Role</option>
                    <option value="created_at">Date Created</option>
                </select>
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

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th class="text-base-content font-semibold">User</th>
                        <th class="text-base-content font-semibold">Contact</th>
                        <th class="text-base-content font-semibold">Role</th>
                        <th class="text-base-content font-semibold">Created</th>
                        <th class="text-base-content font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td>
                                <div>
                                    @if($user->role === 'tenant')
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="font-bold text-primary hover:text-primary-focus hover:underline cursor-pointer">
                                            {{ $user->name }}
                                        </a>
                                    @else
                                        <div class="font-bold text-base-content">{{ $user->name }}</div>
                                    @endif
                                    <div class="text-sm text-base-content/60">ID: {{ $user->id }}</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="font-medium text-base-content">{{ $user->email }}</div>
                                    <div class="text-sm text-base-content/60">{{ $user->phone_number }}</div>
                                </div>
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge badge-primary gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        Admin
                                    </span>
                                @else
                                    <span class="badge badge-secondary gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        Tenant
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm text-base-content/60">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                       class="btn btn-ghost btn-sm" 
                                       title="Edit User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button class="btn btn-ghost btn-sm text-error" 
                                            onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->role }}')"
                                            title="Delete User"
                                            @if($user->role === 'admin') disabled @endif>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center gap-4">
                                    <svg class="w-16 h-16 text-base-content/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4a4 4 0 10-8 0 4 4 0 008 0zm6 6v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2a6 6 0 0112 0z"/>
                                    </svg>
                                    <div>
                                        <h3 class="text-lg font-semibold text-base-content">No users found</h3>
                                        <p class="text-base-content/60">Get started by creating your first user.</p>
                                    </div>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Add First User
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-base-300">
            <!-- Results Info -->
            <div class="text-sm text-base-content/60">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
            </div>
            
            <!-- Pagination Links -->
            @if($users->hasPages())
                <div class="join">
                    {{-- Previous Page --}}
                    @if ($users->onFirstPage())
                        <button class="join-item btn btn-sm" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="join-item btn btn-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        @if ($page == $users->currentPage())
                            <button class="join-item btn btn-sm btn-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="join-item btn btn-sm">
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
            @else
                <!-- Single page indicator -->
                <div class="text-sm text-base-content/60">
                    Page 1 of 1
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Delete User</h3>
        <p class="py-4">Are you sure you want to delete <span id="deleteUserName" class="font-semibold"></span>? This action cannot be undone.</p>
        <div id="deleteRestrictionMessage" class="alert alert-warning mb-4 hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <span id="restrictionText"></span>
        </div>
        <div class="modal-action">
            <button class="btn btn-ghost" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error" id="confirmDeleteBtn">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }
        }, 5000);
    });
});

// Manual hide alert function
function hideAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.remove();
            }
        }, 500);
    }
}

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// Role filter functionality
const roleFilter = document.getElementById('roleFilter');
if (roleFilter) {
    roleFilter.addEventListener('change', function() {
        const selectedRole = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const roleCell = row.querySelector('td:nth-child(3)');
            if (roleCell) {
                const roleText = roleCell.textContent.toLowerCase();
                if (selectedRole === '' || roleText.includes(selectedRole)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
}

// Sort functionality
const sortBy = document.getElementById('sortBy');
if (sortBy) {
    sortBy.addEventListener('change', function() {
        const sortField = this.value;
        const tbody = document.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(sortField) {
                case 'name':
                    aValue = a.querySelector('td:nth-child(1)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(1)').textContent.trim();
                    break;
                case 'email':
                    aValue = a.querySelector('td:nth-child(2)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(2)').textContent.trim();
                    break;
                case 'role':
                    aValue = a.querySelector('td:nth-child(3)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(3)').textContent.trim();
                    break;
                case 'created_at':
                    aValue = a.querySelector('td:nth-child(4)').textContent.trim();
                    bValue = b.querySelector('td:nth-child(4)').textContent.trim();
                    break;
                default:
                    return 0;
            }
            
            return aValue.localeCompare(bValue);
        });
        
        rows.forEach(row => tbody.appendChild(row));
    });
}

// Delete modal functionality
function openDeleteModal(userId, userName, userRole) {
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteForm').action = `/admin/users/${userId}`;
    document.getElementById('deleteModal').classList.add('modal-open');

    const restrictionMessage = document.getElementById('deleteRestrictionMessage');
    const restrictionText = document.getElementById('restrictionText');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Handle restrictions based on user role
    if (userRole === 'admin') {
        restrictionMessage.classList.remove('hidden');
        restrictionText.textContent = 'Admin users cannot be deleted.';
        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.classList.add('btn-disabled');
    } else {
        restrictionMessage.classList.add('hidden');
        confirmDeleteBtn.disabled = false;
        confirmDeleteBtn.classList.remove('btn-disabled');
    }
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('modal-open');
}
</script>
@endsection 