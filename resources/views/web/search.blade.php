@extends('web.layouts.app')
@section('title', 'Search Professionals')

@section('content')
<div style="padding-top: 78px;"></div>

<section class="breadcumb-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-lg-10">
                <div class="breadcumb-style1 mb10-xs">
                    <div class="breadcumb-list">
                        <a href="{{ url('') }}">Home</a>
                        <a href="#">Search</a>
                    </div>
                    <h2 class="mt-2">Search Results for "{{ $query }}"</h2>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pt-0 pb100">
    <div class="container">
        <div class="row">
            @forelse($professionals as $professional)
                <div class="col-sm-6 col-xl-3 mb-4">
                    <div class="listing-style1 bdrs16">
                        <!-- Clickable Banner Image -->
                        <a href="{{ route('professional.profile', $professional->username) }}" class="list-thumb d-block">
                            @if($professional->profile && $professional->profile->wallpaper_image)
                                <img class="w-100" 
                                     src="{{ asset('storage/' . $professional->profile->wallpaper_image) }}" 
                                     alt="{{ $professional->name }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <img class="w-100" 
                                     src="{{ asset('web/images/listings/g-1.jpg') }}" 
                                     alt="{{ $professional->name }}"
                                     style="height: 200px; object-fit: cover;">
                            @endif
                        </a>
                        
                        <div class="list-content">
                            <!-- Avatar and Professional Name -->
                            <div class="d-flex align-items-center mb-2">
                                <a href="{{ route('professional.profile', $professional->username) }}" class="d-flex align-items-center text-decoration-none w-100">
                                    <span class="position-relative me-2">
                                        <img class="rounded-circle" 
                                             src="{{ asset($professional->avatar_url ? 'storage/' . $professional->avatar_url : 'web/images/team/fl-s-2.png') }}" 
                                             alt="{{ $professional->name }}" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="online-badges"></span>
                                    </span>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fz14 fw500 dark-color notranslate" translate="no">
                                            {{ $professional->name }} {{ $professional->surname }}
                                        </h6>
                                    </div>
                                </a>
                            </div>
                            
                            <!-- First Service Subcategory -->
                            @if($professional->first_service && $professional->first_service->subcategory)
                                <p class="list-text body-color fz14 mb-2">
                                    {{ $professional->first_service->subcategory->name }}
                                </p>
                            @endif
                            
                            <hr class="my-2">
                            
                            <!-- Price Range -->
                            <div class="list-meta mt15">
                                <div class="budget">
                                    <p class="mb-0 body-color">
                                        Price range
                                        <span class="fz17 fw500 dark-color ms-1">
                                            @if($professional->min_price == $professional->max_price)
                                                €{{ number_format($professional->min_price, 2) }}
                                            @else
                                                €{{ number_format($professional->min_price, 2) }} - €{{ number_format($professional->max_price, 2) }}
                                            @endif
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="far fa-user-slash text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">No professionals found matching your query.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
