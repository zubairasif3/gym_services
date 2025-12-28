@extends('web.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<style>
    .profile-edit-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .profile-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 20px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1), 0 15px 30px rgba(0, 0, 0, 0.08);
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-title i {
        color: #007bff;
        font-size: 1.1rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-control, .form-select {
        border: 1.5px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
        outline: none;
    }
    
    .file-upload-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    
    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .file-upload-label:hover {
        border-color: #007bff;
        background: #f0f7ff;
    }
    
    .file-upload-label i {
        font-size: 2.5rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .file-upload-label span {
        color: #495057;
        font-weight: 500;
    }
    
    .file-upload-label small {
        color: #6c757d;
        margin-top: 0.25rem;
    }
    
    .file-upload-input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    
    .image-preview {
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1rem;
        position: relative;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .image-preview img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .avatar-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .char-counter {
        text-align: right;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    
    .char-counter.warning {
        color: #ffc107;
    }
    
    .char-counter.danger {
        color: #dc3545;
    }
    
    .btn-enhanced {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-enhanced:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .input-group-text {
        background: #f8f9fa;
        border: 1.5px solid #dee2e6;
        border-right: none;
        border-radius: 8px 0 0 8px;
        color: #6c757d;
        font-weight: 500;
    }
    
    .input-group .form-control {
        border-left: none;
        border-radius: 0 8px 8px 0;
    }
    
    .input-group .form-control:focus {
        border-left: 1.5px solid #007bff;
    }
    
    .alert-enhanced {
        border-radius: 12px;
        border: none;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .page-header {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .page-header h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }
    
    .page-header p {
        color: #6c757d;
        margin: 0;
    }
</style>

<section class="profile-edit-container">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard_navigationbar dn db-1024 mb-3">
                    <div class="dropdown">
                        <button onclick="myFunction()" class="dropbtn"><i class="fa fa-bars pr10"></i> Dashboard Navigation</button>
                        <ul id="myDropdown" class="dropdown-content">
                            <li><a href="{{ route('filament.admin.pages.dashboard') }}"><span class="flaticon-home mr10"></span>Dashboard</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="active"><span class="flaticon-user mr10"></span>Edit Profile</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <!-- Page Header -->
                <div class="page-header">
                    <h2><i class="far fa-user-edit me-2"></i> Edit Profile</h2>
                    <p>Update your profile information and make it stand out</p>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-enhanced">
                        <i class="far fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Images Section -->
                    <div class="profile-card">
                        <h5 class="section-title">
                            <i class="far fa-images"></i>
                            Profile Images
                        </h5>
                        
                        <div class="row">
                            <!-- Wallpaper Image -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Profile Wallpaper</label>
                                @if($user->profile && $user->profile->wallpaper_image)
                                    <div class="image-preview">
                                        <img src="{{ asset('storage/' . $user->profile->wallpaper_image) }}" 
                                             id="wallpaper-preview"
                                             alt="Current Wallpaper">
                                    </div>
                                @else
                                    <div class="image-preview" id="wallpaper-preview-container" style="display: none;">
                                        <img id="wallpaper-preview" alt="Wallpaper Preview">
                                    </div>
                                @endif
                                <div class="file-upload-wrapper">
                                    <label for="wallpaper_image" class="file-upload-label">
                                        <i class="far fa-image"></i>
                                        <span>Upload Wallpaper</span>
                                        <small>Recommended: 1920x350px (JPG, PNG)</small>
                                    </label>
                                    <input type="file" 
                                           class="file-upload-input" 
                                           id="wallpaper_image" 
                                           name="wallpaper_image" 
                                           accept="image/*"
                                           onchange="previewImage(this, 'wallpaper-preview', 'wallpaper-preview-container')">
                                </div>
                                @error('wallpaper_image')
                                    <div class="text-danger small mt-2"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Avatar Image -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Profile Picture</label>
                                <div class="d-flex flex-column align-items-center">
                                    @if($user->avatar_url)
                                        <img src="{{ asset('storage/' . $user->avatar_url) }}" 
                                             class="avatar-preview mb-3" 
                                             id="avatar-preview"
                                             alt="Current Avatar">
                                    @else
                                        <img src="" 
                                             class="avatar-preview mb-3" 
                                             id="avatar-preview"
                                             style="display: none;"
                                             alt="Avatar Preview">
                                    @endif
                                    <div class="file-upload-wrapper" style="max-width: 300px;">
                                        <label for="avatar" class="file-upload-label">
                                            <i class="far fa-user-circle"></i>
                                            <span>Upload Avatar</span>
                                            <small>Square image recommended</small>
                                        </label>
                                        <input type="file" 
                                               class="file-upload-input" 
                                               id="avatar" 
                                               name="avatar" 
                                               accept="image/*"
                                               onchange="previewImage(this, 'avatar-preview', null, true)">
                                    </div>
                                </div>
                                @error('avatar')
                                    <div class="text-danger small mt-2 text-center"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Basic Information Section -->
                    <div class="profile-card">
                        <h5 class="section-title">
                            <i class="far fa-id-card"></i>
                            Basic Information
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="far fa-user me-1"></i> First Name
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required
                                       placeholder="Enter your first name">
                                @error('name')
                                    <div class="text-danger small mt-1"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="surname" class="form-label">
                                    <i class="far fa-user me-1"></i> Last Name
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="surname" 
                                       name="surname" 
                                       value="{{ old('surname', $user->surname) }}"
                                       placeholder="Enter your last name">
                                @error('surname')
                                    <div class="text-danger small mt-1"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="far fa-at me-1"></i> Username
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', $user->username) }}" 
                                       required
                                       placeholder="username">
                            </div>
                            @error('username')
                                <div class="text-danger small mt-1"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="far fa-envelope me-1"></i> Email Address
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required
                                   placeholder="your.email@example.com">
                            @error('email')
                                <div class="text-danger small mt-1"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Profile Details Section -->
                    <div class="profile-card">
                        <h5 class="section-title">
                            <i class="far fa-file-alt"></i>
                            Profile Details
                        </h5>
                        
                        <div class="mb-4">
                            <label for="bio" class="form-label">
                                <i class="far fa-quote-left me-1"></i> Bio (Short description)
                            </label>
                            <textarea class="form-control" 
                                      id="bio" 
                                      name="bio" 
                                      rows="3" 
                                      maxlength="160"
                                      placeholder="Write a short bio that describes you...">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                            <div class="char-counter" id="bio-counter">
                                <span id="bio-count">0</span>/160 characters
                            </div>
                            @error('bio')
                                <div class="text-danger small mt-1"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="about" class="form-label">
                                <i class="far fa-align-left me-1"></i> About (Detailed description)
                            </label>
                            <textarea class="form-control" 
                                      id="about" 
                                      name="about" 
                                      rows="6"
                                      placeholder="Tell people more about yourself, your experience, and what you offer...">{{ old('about', $user->profile->about ?? '') }}</textarea>
                            @error('about')
                                <div class="text-danger small mt-1"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="skills" class="form-label">
                                <i class="far fa-star me-1"></i> Skills
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="skills" 
                                   name="skills" 
                                   value="{{ old('skills', is_array($user->profile->skills ?? null) ? implode(', ', json_decode($user->profile->skills, true) ?? []) : '') }}"
                                   placeholder="e.g., Personal Training, Nutrition, Yoga, Fitness Coaching">
                            <small class="text-muted d-block mt-1">
                                <i class="far fa-info-circle me-1"></i> Enter skills separated by commas
                            </small>
                            @error('skills')
                                <div class="text-danger small mt-1"><i class="far fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="profile-card">
                        <div class="d-flex flex-wrap gap-3 justify-content-end">
                            <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-outline-secondary btn-enhanced">
                                <i class="far fa-times"></i> Cancel
                            </a>
                            @if($user->isProfessional())
                                <a href="{{ route('professional.preview') }}" class="btn btn-outline-primary btn-enhanced" target="_blank">
                                    <i class="far fa-eye"></i> Preview Profile
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary btn-enhanced">
                                <i class="far fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    // Image preview function
    function previewImage(input, previewId, containerId = null, isAvatar = false) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block';
                
                if (containerId) {
                    const container = document.getElementById(containerId);
                    if (container) {
                        container.style.display = 'block';
                    }
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Bio character counter
    const bioTextarea = document.getElementById('bio');
    const bioCounter = document.getElementById('bio-counter');
    const bioCount = document.getElementById('bio-count');
    
    if (bioTextarea && bioCounter && bioCount) {
        function updateBioCounter() {
            const length = bioTextarea.value.length;
            bioCount.textContent = length;
            
            bioCounter.classList.remove('warning', 'danger');
            if (length > 140) {
                bioCounter.classList.add('danger');
            } else if (length > 120) {
                bioCounter.classList.add('warning');
            }
        }
        
        // Set initial count
        updateBioCounter();
        
        // Update on input
        bioTextarea.addEventListener('input', updateBioCounter);
    }
</script>
@endsection

