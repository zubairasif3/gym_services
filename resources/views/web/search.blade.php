@extends('web.layouts.app')
@section('title', 'Search Services')

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
            @forelse($services as $gig)
                <div class="col-sm-6 col-xl-3 mb-4">
                    <div class="listing-style1 bdrs16">
                        <div class="list-thumb">
                            <img class="w-100" src="{{ asset($gig->thumbnail ?? 'storage/' . $gig->images->first()->image_path ?? 'web/images/listings/g-1.jpg') }}" alt="{{ $gig->title }}">
                        </div>
                        <div class="list-content">
                            <p class="list-text body-color fz14 mb-1">{{ $gig->subcategory->name ?? 'Subcategory' }}</p>
                            <h5 class="list-title">
                                <a href="{{ route('gigs.show', $gig->slug) }}">{{ Str::limit($gig->title, 50) }}</a>
                            </h5>
                            <hr class="my-2">
                            <div class="list-meta d-flex justify-content-between align-items-center mt15">
                                <a class="d-flex" href="{{ route('professional.profile', $gig->user->username) }}">
                                    <span class="position-relative mr10">
                                        <img class="rounded-circle wa" src="{{ asset($gig->user->avatar_url ? 'storage/' . $gig->user->avatar_url : 'web/images/team/fl-s-2.png') }}" alt="Freelancer Photo" style="width: 32px; height: 32px;">
                                        <span class="online-badges"></span>
                                    </span>
                                    <div>
                                        <div>
                                            <span class="fz14 notranslate" translate="no">{{ $gig->user->name }}</span>
                                        </div>
                                        <div class="budget">
                                            <p class="mb-0 body-color">Starting at<span class="fz17 fw500 dark-color ms-1">€{{ $gig->starting_price }}</span></p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No services found matching your query.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
