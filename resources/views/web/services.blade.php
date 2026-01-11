@extends('web.layouts.app')
@section('title', 'Services')

@section('content')
    {{-- <div style="padding-top: 78px;"></div> --}}
    <!-- Breadcumb Sections -->
    <section class="breadcumb-section">
        <div class="container">
          <div class="row">
            <div class="col-sm-8 col-lg-8">
              <div class="breadcumb-style1 mb10-xs">
                <div class="breadcumb-list">
                  <a href="#">Home</a>
                  <a href="#">Services</a>
                </div>
              </div>
            </div>
            <div class="col-sm-12 col-lg-12">
                <div class="filter-panel bg-white rounded-3 shadow-sm p-3 border mb-4">
                    <form id="filters-form" method="GET" action="">
                        <!-- Preserve category and subcategory parameters -->
                        @if($categoryId)
                            <input type="hidden" name="categoryId" value="{{ $categoryId }}">
                        @endif
                        @if($subcategoryId)
                            <input type="hidden" name="subcategoryId" value="{{ $subcategoryId }}">
                        @endif
                        
                        <div class="row g-3">
                            <!-- Filter Header -->
                            <div class="col-auto">
                                <div class="d-flex align-items-center h-100">
                                    <i class="fas fa-filter text-primary me-2"></i>
                                    <h6 class="mb-0 fw-bold text-dark">Filters:</h6>
                                </div>
                            </div>
                            
                            <!-- Country Filter -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark mb-1 small">
                                    <i class="fas fa-globe-americas text-primary me-1"></i>Location
                                </label>
                                <select id="country-select" 
                                        name="country" 
                                        class="form-select rounded-pill border-2 shadow-sm" 
                                        style="height: 38px;"
                                        onchange="this.form.submit()">
                                    <option value="">🌍 All Countries</option>
                                    @foreach ($finalCountries as $country)
                                        <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Price Range Filter -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark mb-1 small">
                                    <i class="fas fa-dollar-sign text-success me-1"></i>Price Range
                                </label>
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-2">€</span>
                                        <input type="number" 
                                               name="min_price" 
                                               class="form-control border-2" 
                                               style="height: 38px;"
                                               placeholder="{{ $priceRange->min_price ?? 0 }}"
                                               value="{{ $minPrice }}"
                                               min="0">
                                    </div>
                                    <span class="text-muted">-</span>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-2">€</span>
                                        <input type="number" 
                                               name="max_price" 
                                               class="form-control border-2" 
                                               style="height: 38px;"
                                               placeholder="{{ $priceRange->max_price ?? 1000 }}"
                                               value="{{ $maxPrice }}"
                                               min="0">
                                    </div>
                                </div>
                                <small class="text-muted">Range: €{{ $priceRange->min_price ?? 0 }} - €{{ $priceRange->max_price ?? 1000 }}</small>
                            </div>
                            
                            <!-- Apply Filter Button -->
                            <div class="col-md-2 ">
                                <div style="margin-top: 34px;">
                                    <button type="submit" class="btn btn-primary rounded-pill shadow-sm fw-semibold w-100" style="height: 38px;">
                                        <i class="fas fa-search me-1"></i>Apply
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Clear Filters -->
                            @if(request('country') || request('min_price') || request('max_price'))
                                <div class="col-md-2">
                                    <div style="margin-top: 34px;">
                                        <a href="{{ route('web.services', [$categoryId, $subcategoryId]) }}" 
                                           class="btn btn-outline-danger rounded-pill px-3 w-100" 
                                           style="height: 38px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-times me-1"></i>Clear
                                        </a>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Active Filters Display -->
                            @if(request('country') || request('min_price') || request('max_price'))
                                <div class="col-12">
                                    <div class="active-filters pt-2 border-top">
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <small class="text-muted fw-semibold me-2">Active:</small>
                                            @if(request('country'))
                                                <span class="badge bg-primary rounded-pill">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ request('country') }}
                                                </span>
                                            @endif
                                            @if(request('min_price'))
                                                <span class="badge bg-success rounded-pill">
                                                    Min: €{{ request('min_price') }}
                                                </span>
                                            @endif
                                            @if(request('max_price'))
                                                <span class="badge bg-success rounded-pill">
                                                    Max: €{{ request('max_price') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
          </div>
        </div>
    </section>
    <!-- Popular Services -->
    <section class="pt-0 pb100">
        <div class="container">
          <div class="row align-items-center wow fadeInUp">
            <div class="col-xl-5">
              <div class="main-title mb30-lg">
                <h2 class="title">Featured Professionals</h2>
                <p class="paragraph">Top-rated professionals in your selected category</p>
              </div>
            </div>
            <div class="col-xl-7">
              <div class="navpill-style2 at-home9 mb50-lg">
                <ul class="nav nav-pills mb20 justify-content-xl-end" id="pills-tab" role="tablist">
                    @foreach($categories as $index => $category)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw500 dark-color {{ $categoryId == $category->id ? 'active' : ($loop->first && !$categoryId ? 'active' : '') }}"
                                id="pills-{{ $category->id }}-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-{{ $category->id }}"
                                type="button"
                                role="tab"
                                aria-controls="pills-{{ $category->id }}"
                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                {{ $category->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="navpill-style2">
                <div class="tab-content ha" id="pills-tabContent">
                    @foreach($categories as $index => $category)
                        <div class="tab-pane fade {{ $categoryId == $category->id ? 'show active' : ($loop->first && !$categoryId ? 'show active' : '') }}" id="pills-{{ $category->id }}" role="tabpanel" aria-labelledby="pills-{{ $category->id }}-tab">
                            @if($category->subcategories->count())
                                <div class="mb3 d-flex flex-wrap align-items-center justify-content-start gap-2 mb-4">
                                <button class="btn btn-sm btn-outline-dark subcategory-filter {{ ($subcategoryId ==  null) ? 'active' : '' }}" data-category="{{ $category->id }}" data-subcategory="all">All</button>
                                @foreach($category->subcategories as $subcategory)
                                    <button class="btn btn-sm btn-outline-dark subcategory-filter {{ ($subcategoryId == $subcategory->id) ? 'active' : '' }}" data-category="{{ $category->id }}" data-subcategory="{{ $subcategory->id }}">
                                    {{ $subcategory->name }}
                                    </button>
                                @endforeach
                                </div>
                            @endif

                            <div class="row professionals-container" data-category="{{ $category->id }}">
                                @forelse($category->professionals as $professional)
                                <div class="col-sm-6 col-xl-3 professional-card" data-subcategories="{{ $professional->services->pluck('sub_category_id')->unique()->implode(',') }}">
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
                                            <p class="text-muted mt-3 mb-0">No professionals available in this category</p>
                                        </div>
                                    </div>
                                @endforelse

                                <div class="col-lg-12">
                                    <div class="text-center mt30">
                                        {{-- <a class="ud-btn btn-light-thm bdrs60" href="#">
                                            View All Professionals<i class="fal fa-arrow-right-long"></i>
                                        </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
.filter-panel {
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid #e3e6f0 !important;
}

.filter-panel:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.filter-group {
    position: relative;
}

.filter-group label {
    color: #2c3e50;
    font-size: 0.9rem;
}

.form-select {
    transition: all 0.3s ease;
    border-color: #dee2e6;
}

.form-select:focus {
    border-color: #00b3f1;
    box-shadow: 0 0 0 0.2rem rgba(0, 179, 241, 0.25);
    transform: scale(1.02);
}

.input-group-text {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-color: #dee2e6;
    color: #495057;
    font-weight: 600;
}

.form-control {
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    transform: scale(1.02);
}

.btn-primary {
    background: linear-gradient(135deg, #00b3f1 0%, #0099d4 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0099d4 0%, #0080b7 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 179, 241, 0.4);
}

.btn-outline-danger {
    transition: all 0.3s ease;
}

.btn-outline-danger:hover {
    transform: scale(1.05);
}

.badge {
    transition: all 0.3s ease;
    font-size: 0.75rem;
}

.badge:hover {
    transform: scale(1.1);
}

.active-filters {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.price-range-info {
    background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
    padding: 8px;
    border-radius: 8px;
    margin-top: 8px;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .filter-panel {
        margin-bottom: 20px;
    }
    
    .col-6 {
        margin-bottom: 10px;
    }
}

/* Loading animation for form submission */
.btn-primary.loading {
    position: relative;
    color: transparent;
}

.btn-primary.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border-radius: 50%;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    animation: spin 1s ease infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.subcategory-filter');
    const filterForm = document.getElementById('filters-form');
    const submitBtn = filterForm.querySelector('button[type="submit"]');

    // Subcategory filter functionality
    buttons.forEach(button => {
      button.addEventListener('click', function () {
        const categoryId = this.getAttribute('data-category');
        const subcategoryId = this.getAttribute('data-subcategory');

        // Remove 'active' from all buttons in the group
        document.querySelectorAll(`.subcategory-filter[data-category="${categoryId}"]`).forEach(btn => {
          btn.classList.remove('active');
        });
        this.classList.add('active');

        // Show/hide professionals with animation
        document.querySelectorAll(`.professionals-container[data-category="${categoryId}"] .professional-card`).forEach(card => {
          const subcategories = card.getAttribute('data-subcategories').split(',');
          if (subcategoryId === 'all' || subcategories.includes(subcategoryId)) {
            card.style.display = 'block';
            card.style.animation = 'fadeInUp 0.5s ease';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });

    // Add loading state to filter button
    filterForm.addEventListener('submit', function() {
      submitBtn.classList.add('loading');
      submitBtn.disabled = true;
    });

    // Price input validation and formatting
    const priceInputs = document.querySelectorAll('input[name="min_price"], input[name="max_price"]');
    priceInputs.forEach(input => {
      input.addEventListener('input', function() {
        // Remove any non-numeric characters except decimal point
        this.value = this.value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        const parts = this.value.split('.');
        if (parts.length > 2) {
          this.value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limit to 2 decimal places
        if (parts[1] && parts[1].length > 2) {
          this.value = parseFloat(this.value).toFixed(2);
        }
      });

      // Add visual feedback for valid input
      input.addEventListener('blur', function() {
        if (this.value && !isNaN(this.value)) {
          this.classList.add('is-valid');
          this.classList.remove('is-invalid');
        } else if (this.value) {
          this.classList.add('is-invalid');
          this.classList.remove('is-valid');
        } else {
          this.classList.remove('is-valid', 'is-invalid');
        }
      });
    });

    // Auto-submit country filter with loading state
    const countrySelect = document.getElementById('country-select');
    countrySelect.addEventListener('change', function() {
      submitBtn.classList.add('loading');
      submitBtn.disabled = true;
    });
  });
</script>
@endpush

