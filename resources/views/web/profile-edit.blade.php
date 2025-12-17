@extends('web.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<section class="our-dashbord dashbord bgc-f7 pb50">
    <div class="container-fluid ovh">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard_navigationbar dn db-1024">
                    <div class="dropdown">
                        <button onclick="myFunction()" class="dropbtn"><i class="fa fa-bars pr10"></i> Dashboard Navigation</button>
                        <ul id="myDropdown" class="dropdown-content">
                            <li><a href="{{ route('admin') }}"><span class="flaticon-home mr10"></span>Dashboard</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="active"><span class="flaticon-user mr10"></span>Edit Profile</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="row">
                    <!-- Dashboard Title -->
                    <div class="col-lg-12 mb20">
                        <div class="dashboard_title_area">
                            <h2>Edit Profile</h2>
                            <p class="text">Update your profile information</p>
                        </div>
                    </div>
                    
                    <!-- Edit Form -->
                    <div class="col-lg-12">
                        <div class="dashboard_setting_box">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <!-- Wallpaper Image -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Profile Wallpaper</h5>
                                    @if($user->profile && $user->profile->wallpaper_image)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $user->profile->wallpaper_image) }}" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 200px;"
                                                 alt="Current Wallpaper">
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label for="wallpaper_image" class="form-label">Upload New Wallpaper (Recommended: 1920x350px)</label>
                                        <input type="file" class="form-control" id="wallpaper_image" name="wallpaper_image" accept="image/*">
                                        @error('wallpaper_image')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <!-- Avatar Image -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Profile Picture</h5>
                                    @if($user->avatar_url)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $user->avatar_url) }}" 
                                                 class="rounded-circle" 
                                                 width="100" 
                                                 height="100"
                                                 alt="Current Avatar">
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label for="avatar" class="form-label">Upload New Avatar</label>
                                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                        @error('avatar')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <!-- Basic Info -->
                                <h5 class="mb-3">Basic Information</h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="surname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="surname" name="surname" 
                                               value="{{ old('surname', $user->surname) }}">
                                        @error('surname')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="{{ old('username', $user->username) }}" required>
                                    </div>
                                    @error('username')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <hr class="my-4">
                                
                                <!-- Profile Details -->
                                <h5 class="mb-3">Profile Details</h5>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio (Short description)</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="2" maxlength="160">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                                    <small class="text-muted">Max 160 characters</small>
                                    @error('bio')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="about" class="form-label">About (Detailed description)</label>
                                    <textarea class="form-control" id="about" name="about" rows="5">{{ old('about', $user->profile->about ?? '') }}</textarea>
                                    @error('about')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="skills" class="form-label">Skills (comma-separated)</label>
                                    <input type="text" class="form-control" id="skills" name="skills" 
                                           value="{{ old('skills', is_array($user->profile->skills ?? null) ? implode(', ', json_decode($user->profile->skills, true) ?? []) : '') }}"
                                           placeholder="e.g., Web Design, PHP, Laravel">
                                    <small class="text-muted">Enter skills separated by commas</small>
                                    @error('skills')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <hr class="my-4">
                                
                                <!-- Submit Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="far fa-save me-1"></i> Save Changes
                                    </button>
                                    @if($user->isProfessional())
                                        <a href="{{ route('professional.preview') }}" class="btn btn-outline-primary" target="_blank">
                                            <i class="far fa-eye me-1"></i> Preview Profile
                                        </a>
                                    @endif
                                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="far fa-times me-1"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

