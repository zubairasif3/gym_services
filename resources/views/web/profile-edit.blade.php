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
        color: #00b3f1;
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
        border-color: #00b3f1;
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
        border-color: #00b3f1;
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
        border-left: 1.5px solid #00b3f1;
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
    
    /* Media Gallery Slider Styles */
    .media-slider-wrapper {
        position: relative;
        padding: 0 50px;
    }
    
    .media-slider-container {
        overflow: hidden;
        border-radius: 12px;
        background: #f8f9fa;
        padding: 20px 10px;
    }
    
    .media-slider-track {
        display: flex;
        gap: 20px;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 5px;
    }
    
    .media-slider-item {
        flex: 0 0 auto;
        width: 250px;
    }
    
    .slider-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: 2px solid #00b3f1;
        color: #00b3f1;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .slider-nav-btn:hover:not(:disabled) {
        background: #00b3f1;
        color: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 179, 241, 0.3);
    }
    
    .slider-nav-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
        border-color: #dee2e6;
        color: #6c757d;
    }
    
    .slider-nav-btn.prev {
        left: 0;
    }
    
    .slider-nav-btn.next {
        right: 0;
    }
    
    .media-card {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        aspect-ratio: 1;
        background: white;
    }
    
    .media-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
    
    .media-counter {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .media-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    
    .media-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(0, 179, 241, 0.9);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        backdrop-filter: blur(4px);
    }
    
    .media-badge.video-badge {
        background: rgba(220, 53, 69, 0.9);
    }
    
    .media-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .media-card:hover .media-overlay {
        opacity: 1;
    }
    
    .delete-media-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .delete-media-btn:hover {
        transform: scale(1.1);
    }
    
    .media-upload-label {
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 150px;
    }
    
    .media-upload-label:hover {
        border-color: #00b3f1;
        background: linear-gradient(135deg, #f0f7ff 0%, #e6f3ff 100%);
        transform: translateY(-2px);
    }
    
    .media-upload-label i {
        font-size: 2rem;
        color: #00b3f1;
    }
    
    .media-slider-item {
        animation: slideIn 0.4s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(30px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    /* Responsive slider */
    @media (max-width: 768px) {
        .media-slider-wrapper {
            padding: 0 40px;
        }
        
        .media-slider-item {
            width: 200px;
        }
        
        .slider-nav-btn {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }
    }
    
    @media (max-width: 480px) {
        .media-slider-wrapper {
            padding: 0 35px;
        }
        
        .media-slider-item {
            width: 180px;
        }
        
        .slider-nav-btn {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }
    }
    
    .progress {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .progress-bar {
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
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
                    
                    <!-- Media Gallery Section (Images & Videos) -->
                    <div class="profile-card">
                        <h5 class="section-title">
                            <i class="far fa-photo-video"></i>
                            Media Gallery
                        </h5>
                        <p class="text-muted mb-4">
                            <i class="far fa-info-circle me-1"></i> 
                            Upload images and short videos (max 30 seconds) to showcase your work. Unlimited media items.
                        </p>
                        
                        <!-- Media Slider -->
                        <div class="media-slider-wrapper mb-4">
                            <button type="button" class="slider-nav-btn prev" id="sliderPrev">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="media-slider-container">
                                <div id="media-gallery-grid" class="media-slider-track">
                                @foreach($user->activeProfileMedia as $media)
                                    <div class="media-slider-item media-item" data-media-id="{{ $media->id }}">
                                        <div class="media-card">
                                            @if($media->media_type === 'image')
                                                <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                     alt="Profile media" 
                                                     class="media-preview">
                                                <div class="media-badge">
                                                    <i class="far fa-image"></i> Image
                                                </div>
                                            @else
                                                <video src="{{ asset('storage/' . $media->file_path) }}" 
                                                       class="media-preview" 
                                                       muted 
                                                       loop
                                                       onmouseover="this.play()" 
                                                       onmouseout="this.pause()">
                                                </video>
                                                <div class="media-badge video-badge">
                                                    <i class="far fa-video"></i> Video
                                                    @if($media->duration)
                                                        <span class="ms-1">({{ $media->duration }}s)</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="media-overlay">
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger delete-media-btn" 
                                                        data-media-id="{{ $media->id }}"
                                                        title="Delete">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </div>
                                            <div class="media-counter">
                                                {{ $loop->iteration }}/{{ $user->activeProfileMedia->count() }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            <button type="button" class="slider-nav-btn next" id="sliderNext">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        
                        @if($user->activeProfileMedia && $user->activeProfileMedia->count() > 0)
                        <div class="text-center mb-3">
                            <small class="text-muted">
                                <i class="far fa-images me-1"></i>
                                <span id="media-count">{{ $user->activeProfileMedia->count() }}</span> media items
                            </small>
                        </div>
                        @endif
                        
                        <!-- Upload Section -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="far fa-images me-1"></i> Upload Images
                                </label>
                                <div class="file-upload-wrapper">
                                    <label for="media_images" class="file-upload-label media-upload-label">
                                        <i class="far fa-image"></i>
                                        <span>Click to upload images</span>
                                        <small>JPG, PNG, GIF (Max 10MB each)</small>
                                    </label>
                                    <input type="file" 
                                           class="file-upload-input" 
                                           id="media_images" 
                                           accept="image/jpeg,image/jpg,image/png,image/gif"
                                           multiple>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="far fa-video me-1"></i> Upload Videos
                                </label>
                                <div class="file-upload-wrapper">
                                    <label for="media_videos" class="file-upload-label media-upload-label">
                                        <i class="far fa-video"></i>
                                        <span>Click to upload videos</span>
                                        <small>MP4, MOV (Max 30 seconds, 20MB)</small>
                                    </label>
                                    <input type="file" 
                                           class="file-upload-input" 
                                           id="media_videos" 
                                           accept="video/mp4,video/mov,video/avi"
                                           multiple>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upload Progress -->
                        <div id="upload-progress" class="mt-3" style="display: none;">
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                                     role="progressbar" 
                                     id="progress-bar"
                                     style="width: 0%">
                                    <span id="progress-text">Uploading...</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upload Messages -->
                        <div id="upload-messages" class="mt-3"></div>
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
    
    // Media Gallery Upload Handling
    const mediaImagesInput = document.getElementById('media_images');
    const mediaVideosInput = document.getElementById('media_videos');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const uploadMessages = document.getElementById('upload-messages');
    const mediaGalleryGrid = document.getElementById('media-gallery-grid');
    
    if (mediaImagesInput) {
        mediaImagesInput.addEventListener('change', function(e) {
            handleMediaUpload(e.target.files, 'image');
        });
    }
    
    if (mediaVideosInput) {
        mediaVideosInput.addEventListener('change', function(e) {
            handleMediaUpload(e.target.files, 'video');
        });
    }
    
    // Delete media handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-media-btn')) {
            const btn = e.target.closest('.delete-media-btn');
            const mediaId = btn.dataset.mediaId;
            deleteMedia(mediaId);
        }
    });
    
    async function handleMediaUpload(files, mediaType) {
        if (!files || files.length === 0) return;
        
        // No limit on media count
        const filesToUpload = Array.from(files);
        
        uploadProgress.style.display = 'block';
        uploadMessages.innerHTML = '';
        
        let uploaded = 0;
        const total = filesToUpload.length;
        
        for (const file of filesToUpload) {
            // Validate file size
            const maxSize = 20 * 1024 * 1024; // 20MB
            if (file.size > maxSize) {
                showMessage('error', `${file.name} is too large. Max 20MB.`);
                continue;
            }
            
            // For videos, check duration
            if (mediaType === 'video') {
                const duration = await getVideoDuration(file);
                if (duration > 30) {
                    showMessage('error', `${file.name} is too long. Max 30 seconds.`);
                    continue;
                }
            }
            
            // Upload file
            const formData = new FormData();
            formData.append('media', file);
            formData.append('media_type', mediaType);
            
            if (mediaType === 'video') {
                const duration = await getVideoDuration(file);
                formData.append('duration', Math.round(duration));
            }
            
            try {
                const response = await fetch('{{ route('profile.media.upload') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    addMediaToGrid(data.media, data.url);
                    showMessage('success', `${file.name} uploaded successfully`);
                } else {
                    showMessage('error', data.error || 'Upload failed');
                }
            } catch (error) {
                console.error('Upload error:', error);
                showMessage('error', `Failed to upload ${file.name}`);
            }
            
            uploaded++;
            const progress = (uploaded / total) * 100;
            progressBar.style.width = progress + '%';
            progressText.textContent = `Uploading ${uploaded}/${total}...`;
        }
        
        // Hide progress after completion
        setTimeout(() => {
            uploadProgress.style.display = 'none';
            progressBar.style.width = '0%';
            mediaImagesInput.value = '';
            mediaVideosInput.value = '';
        }, 1500);
    }
    
    function getVideoDuration(file) {
        return new Promise((resolve) => {
            const video = document.createElement('video');
            video.preload = 'metadata';
            
            video.onloadedmetadata = function() {
                window.URL.revokeObjectURL(video.src);
                resolve(video.duration);
            };
            
            video.onerror = function() {
                resolve(0);
            };
            
            video.src = URL.createObjectURL(file);
        });
    }
    
    function addMediaToGrid(media, url) {
        const col = document.createElement('div');
        col.className = 'media-slider-item media-item';
        col.dataset.mediaId = media.id;
        
        const currentCount = mediaGalleryGrid.querySelectorAll('.media-item').length;
        
        let mediaContent = '';
        if (media.media_type === 'image') {
            mediaContent = `
                <img src="${url}" alt="Profile media" class="media-preview">
                <div class="media-badge">
                    <i class="far fa-image"></i> Image
                </div>
            `;
        } else {
            const durationText = media.duration ? `<span class="ms-1">(${media.duration}s)</span>` : '';
            mediaContent = `
                <video src="${url}" class="media-preview" muted loop onmouseover="this.play()" onmouseout="this.pause()"></video>
                <div class="media-badge video-badge">
                    <i class="far fa-video"></i> Video ${durationText}
                </div>
            `;
        }
        
        col.innerHTML = `
            <div class="media-card">
                ${mediaContent}
                <div class="media-overlay">
                    <button type="button" 
                            class="btn btn-sm btn-danger delete-media-btn" 
                            data-media-id="${media.id}"
                            title="Delete">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </div>
                <div class="media-counter">${currentCount + 1}/${currentCount + 1}</div>
            </div>
        `;
        
        mediaGalleryGrid.appendChild(col);
        updateMediaCounter();
        updateSliderButtons();
    }
    
    async function deleteMedia(mediaId) {
        if (!confirm('Are you sure you want to delete this media?')) {
            return;
        }
        
        try {
            const response = await fetch(`/profile/media/${mediaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const mediaItem = document.querySelector(`.media-item[data-media-id="${mediaId}"]`);
                if (mediaItem) {
                    mediaItem.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => {
                        mediaItem.remove();
                        updateMediaCounter();
                        updateSliderButtons();
                    }, 300);
                }
                showMessage('success', 'Media deleted successfully');
            } else {
                showMessage('error', 'Failed to delete media');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showMessage('error', 'Failed to delete media');
        }
    }
    
    // Media Slider functionality
    let sliderPosition = 0;
    const sliderPrevBtn = document.getElementById('sliderPrev');
    const sliderNextBtn = document.getElementById('sliderNext');
    
    if (sliderPrevBtn && sliderNextBtn) {
        sliderPrevBtn.addEventListener('click', () => slideMedia(-1));
        sliderNextBtn.addEventListener('click', () => slideMedia(1));
        
        // Initialize slider buttons
        updateSliderButtons();
    }
    
    function slideMedia(direction) {
        const track = mediaGalleryGrid;
        const items = track.querySelectorAll('.media-slider-item');
        if (items.length === 0) return;
        
        const itemWidth = items[0].offsetWidth;
        const gap = 20;
        const slideDistance = itemWidth + gap;
        const containerWidth = track.parentElement.offsetWidth;
        const totalWidth = (itemWidth + gap) * items.length - gap;
        const maxScroll = Math.max(0, totalWidth - containerWidth);
        
        sliderPosition += direction * slideDistance;
        sliderPosition = Math.max(0, Math.min(sliderPosition, maxScroll));
        
        track.style.transform = `translateX(-${sliderPosition}px)`;
        updateSliderButtons();
    }
    
    function updateSliderButtons() {
        if (!sliderPrevBtn || !sliderNextBtn) return;
        
        const track = mediaGalleryGrid;
        const items = track.querySelectorAll('.media-slider-item');
        
        if (items.length === 0) {
            sliderPrevBtn.disabled = true;
            sliderNextBtn.disabled = true;
            return;
        }
        
        const itemWidth = items[0].offsetWidth;
        const gap = 20;
        const containerWidth = track.parentElement.offsetWidth;
        const totalWidth = (itemWidth + gap) * items.length - gap;
        const maxScroll = Math.max(0, totalWidth - containerWidth);
        
        sliderPrevBtn.disabled = sliderPosition === 0;
        sliderNextBtn.disabled = sliderPosition >= maxScroll;
    }
    
    function updateMediaCounter() {
        const items = mediaGalleryGrid.querySelectorAll('.media-item');
        const totalCount = items.length;
        
        // Update counter in UI
        const counterEl = document.getElementById('media-count');
        if (counterEl) {
            counterEl.textContent = totalCount;
        }
        
        // Update individual media counters
        items.forEach((item, index) => {
            const counter = item.querySelector('.media-counter');
            if (counter) {
                counter.textContent = `${index + 1}/${totalCount}`;
            }
        });
    }
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            sliderPosition = 0;
            mediaGalleryGrid.style.transform = 'translateX(0)';
            updateSliderButtons();
        }, 250);
    });
    
    function showMessage(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="far ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        uploadMessages.appendChild(alertDiv);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    // Add fadeOut animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }
            to {
                opacity: 0;
                transform: scale(0.8);
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection

