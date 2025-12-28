<div class="profile-dropdown-content bg-white rounded shadow">
    <!-- User Info -->
    <div class="p-3 border-bottom">
        <div class="d-flex align-items-center">
            @if($user->avatar_url)
                <img src="{{ asset('storage/' . $user->avatar_url) }}" 
                     class="rounded-circle me-3" 
                     width="50" 
                     height="50">
            @else
                <div class="avatar-initials bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 50px; height: 50px; font-size: 18px;">
                    {{ $user->initials }}
                </div>
            @endif
            
            <div>
                <h6 class="mb-0 fw-semibold">{{ $user->name }} {{ $user->surname }}</h6>
                <p class="text-muted small mb-0">{{ $user->email }}</p>
                <span class="badge bg-{{ $user->user_type == 3 ? 'success' : ($user->user_type == 1 ? 'danger' : 'info') }} small">
                    {{ $user->user_type_label }}
                </span>
            </div>
        </div>
    </div>
    
    <!-- Menu Items -->
    <div class="py-2">
        @if($user->isProfessional())
            <a href="{{ route('professional.profile', $user->username) }}" 
               class="dropdown-item d-flex align-items-center py-2">
                <i class="far fa-eye me-3 text-primary" style="width: 20px;"></i>
                <span>My Public Profile</span>
            </a>
            
            {{-- <a href="{{ route('professional.preview') }}" 
               class="dropdown-item d-flex align-items-center py-2">
                <i class="far fa-user me-3 text-primary" style="width: 20px;"></i>
                <span>Preview Profile</span>
            </a> --}}

            <a href="{{ route('profile.edit') }}" 
               class="dropdown-item d-flex align-items-center py-2">
                <i class="far fa-edit me-3 text-primary" style="width: 20px;"></i>
                <span>Edit Profile</span>
            </a>
            
            <a href="{{ route('filament.admin.pages.dashboard') }}" 
               class="dropdown-item d-flex align-items-center py-2">
                <i class="far fa-tachometer-alt me-3 text-primary" style="width: 20px;"></i>
                <span>Dashboard</span>
            </a>
        @endif
        
        <a href="{{ route('following') }}" 
           class="dropdown-item d-flex align-items-center py-2">
            <i class="far fa-heart me-3 text-danger" style="width: 20px;"></i>
            <span>Following ({{ $user->following_count }})</span>
        </a>
        
        <a href="{{ route('notifications') }}" 
           class="dropdown-item d-flex align-items-center py-2">
            <i class="far fa-bell me-3 text-warning" style="width: 20px;"></i>
            <span>Notifications</span>
        </a>
    </div>
    
    <!-- Logout -->
    <div class="border-top">
        <button wire:click="logout" 
                class="dropdown-item d-flex align-items-center py-3 text-danger">
            <i class="far fa-sign-out-alt me-3" style="width: 20px;"></i>
            <span class="fw-semibold">Logout</span>
        </button>
    </div>
    
    <style>
    .profile-dropdown-content {
        min-width: 280px;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    </style>
</div>
