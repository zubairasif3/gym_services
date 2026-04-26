@extends('web.layouts.app')

@section('title', __('web.customer_profile.title'))

@section('content')
<style>
    .customer-profile-page {
        background: linear-gradient(180deg, #f5f8fc 0%, #eef3f9 100%);
        min-height: calc(100vh - 120px);
    }

    .profile-page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #182230;
        margin-bottom: 0.25rem;
    }

    .profile-page-subtitle {
        color: #5f6b7a;
        margin-bottom: 0;
    }

    .profile-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08);
        border: 1px solid #e8eef6;
        overflow: hidden;
    }

    .profile-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #fbfdff;
    }

    .profile-card-header i {
        color: #00b3f1;
    }

    .profile-card-header h5,
    .profile-card-header h4 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #1f2a37;
    }

    .profile-card-body {
        padding: 1.25rem;
    }

    .avatar-wrapper {
        width: 126px;
        height: 126px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #00b3f1 0%, #0092d1 100%);
        padding: 4px;
        box-shadow: 0 10px 25px rgba(0, 179, 241, 0.25);
    }

    .avatar-wrapper img,
    .avatar-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        background: #fff;
    }

    .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        color: #00a9e8;
    }

    .field-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #738397;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.4rem;
    }

    .readonly-input {
        background-color: #f4f8fc !important;
        color: #243243 !important;
        border: 1px solid #dbe5f1 !important;
        font-weight: 500;
    }

    .readonly-note {
        font-size: 0.8rem;
        color: #7a8796;
    }

    .btn-profile-primary {
        background: linear-gradient(135deg, #00b3f1 0%, #0096d9 100%);
        border: none;
        color: #fff;
        font-weight: 700;
        border-radius: 10px;
        padding: 0.72rem 1rem;
    }

    .btn-profile-primary:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(0, 179, 241, 0.3);
    }

    @media (max-width: 991px) {
        .profile-page-title {
            font-size: 1.6rem;
        }
    }
</style>
<section class="our-dashbord dashbord pt30 pb50 customer-profile-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 mb20">
                <h2 class="profile-page-title">{{ __('web.customer_profile.title') }}</h2>
                <p class="profile-page-subtitle">{{ __('web.customer_profile.subtitle') }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-4 mb30">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-user-circle"></i>
                        <h5>{{ __('web.customer_profile.avatar') }}</h5>
                    </div>
                    <div class="profile-card-body">
                        <div class="avatar-wrapper">
                            @if($user->avatar_url)
                                <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="{{ __('web.customer_profile.avatar') }}">
                            @else
                                <div class="avatar-placeholder">{{ $user->initials }}</div>
                            @endif
                        </div>

                        <form action="{{ route('customer.profile.avatar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="field-label">{{ __('web.customer_profile.change_avatar') }}</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*" required>
                                <small class="readonly-note">{{ __('web.customer_profile.avatar_help') }}</small>
                            </div>
                            <button type="submit" class="btn btn-profile-primary w-100">{{ __('web.customer_profile.update_avatar') }}</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="profile-card mb30">
                    <div class="profile-card-header">
                        <i class="fas fa-id-card"></i>
                        <h4>{{ __('web.customer_profile.personal_data') }}</h4>
                    </div>
                    <div class="profile-card-body custom-form-style1">
                        <p class="readonly-note mb-3">{{ __('web.customer_profile.personal_help') }}</p>
                        <form action="{{ route('customer.profile.details') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb3">
                                    <label class="field-label">{{ __('web.customer_profile.first_name') }}</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6 mb3">
                                    <label class="field-label">{{ __('web.customer_profile.last_name') }}</label>
                                    <input type="text" class="form-control" name="surname" value="{{ old('surname', $user->surname) }}" required>
                                </div>
                                <div class="col-md-6 mb3">
                                    <label class="field-label">{{ __('web.customer_profile.username') }}</label>
                                    <input type="text" class="form-control readonly-input" value="{{ $user->username }}" readonly disabled>
                                </div>
                                <div class="col-md-6 mb3">
                                    <label class="field-label">{{ __('web.customer_profile.email') }}</label>
                                    <input type="text" class="form-control readonly-input" value="{{ $user->email }}" readonly disabled>
                                </div>
                                <div class="col-md-4 mb3">
                                    <label class="field-label">{{ __('web.customer_profile.city') }}</label>
                                    <input type="text" class="form-control" name="city" value="{{ old('city', $user->profile->city ?? '') }}">
                                </div>
                                <div class="col-md-4 mb3">
                                    <label class="field-label">{{ __('web.customer_profile.country') }}</label>
                                    <input type="text" class="form-control" name="country" value="{{ old('country', $user->profile->country ?? '') }}">
                                </div>
                                <div class="col-md-4 mb3">
                                    <label class="field-label">{{ __('web.customer_profile.zip') }}</label>
                                    <input type="text" class="form-control" name="cap" value="{{ old('cap', $user->profile->cap ?? '') }}">
                                </div>
                                <div class="col-md-6 mb3">
                                    <label class="field-label">{{ __('web.customer_profile.dob') }}</label>
                                    <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', $user->profile && $user->profile->date_of_birth ? $user->profile->date_of_birth->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-profile-primary">{{ __('web.customer_profile.save_details') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-lock"></i>
                        <h4>{{ __('web.customer_profile.change_password') }}</h4>
                    </div>
                    <div class="profile-card-body custom-form-style1">
                        <form action="{{ route('customer.profile.password') }}" method="POST">
                            @csrf
                            <div class="mb3">
                                <label class="field-label">{{ __('web.customer_profile.current_password') }}</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb3">
                                <label class="field-label">{{ __('web.customer_profile.new_password') }}</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb3">
                                <label class="field-label">{{ __('web.customer_profile.confirm_new_password') }}</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="mt-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-profile-primary">{{ __('web.customer_profile.update_password') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

