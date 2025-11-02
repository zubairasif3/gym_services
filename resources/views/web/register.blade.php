@extends('web.layouts.app')
@section('title', 'Register - FitScout')

@push('styles')
<style>
    .register-page {
        min-height: 100vh;
        background-color: #2d2d2d;
        padding: 40px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .register-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .register-logo {
        text-align: center;
        margin-bottom: 40px;
    }

    .register-logo .header-logo {
        display: inline-block;
        text-decoration: none;
    }

    .register-logo .header-logo img {
        max-width: 120px;
        height: auto;
    }

    .register-tabs {
        display: flex;
        justify-content: center;
        gap: 0;
        margin-bottom: 30px;
        border-bottom: 2px solid rgba(0, 179, 241, 0.3);
        padding-bottom: 0;
    }

    .register-tab {
        padding: 5px 40px;
        background: transparent;
        border: 1px solid #00b3f1;
        border-bottom: 0px;
        border-radius: 12px 12px 0px 0px;
        color: white;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
        font-family: var(--body-font-family);
    }

    .register-tab.active {
        color: #00b3f1;
    }

    /* .register-tab.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #00b3f1;
    } */

    .register-form-container {
        background: transparent;
        padding: 0;
    }

    .register-form-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    .form-group-register {
        position: relative;
    }

    .form-label-register {
        display: block;
        color: white;
        font-size: 14px;
        font-weight: 400;
        margin-bottom: 8px;
        font-family: var(--body-font-family, 'DM Sans', sans-serif);
        padding-left: 10px;
    }

    .form-label-register .required {
        color: #00b3f1;
        margin-left: 3px;
    }

    .form-input-register {
        width: 100%;
        padding: 12px 15px;
        background: transparent;
        border: 1px solid #00b3f1;
        border-radius: 12px;
        color: white;
        font-size: 14px;
        font-family: var(--body-font-family);
        transition: border-color 0.3s 
    ease;
    }

    .form-input-register:focus {
        outline: none;
        border-color: #00b3f1;
        box-shadow: 0 0 0 2px rgba(0, 179, 241, 0.1);
    }

    .form-input-register::placeholder {
        color: #999;
    }

    .form-select-register {
        width: 100%;
        padding: 12px 35px 12px 15px;
        background: transparent;
        border: 1px solid #00b3f1;
        border-radius: 12px;
        color: white;
        font-size: 14px;
        font-family: var(--body-font-family);
        appearance: none;
        background-image: url(data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2300b3f1' d='M6 9L1 4h10z'/%3E%3C/svg%3E);
        background-repeat: no-repeat;
        background-position: right 15px center;
        cursor: pointer;
    }

    .form-select-register:focus {
        outline: none;
        border-color: #00b3f1;
        box-shadow: 0 0 0 2px rgba(0, 179, 241, 0.1);
        background: white;
        color: #222;
    }

    .password-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #00b3f1;
        cursor: pointer;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        opacity: 0.8;
    }

    .password-toggle i {
        font-size: 18px;
    }

    .required-fields-note {
        color: #00b3f1;
        font-size: 13px;
        margin-top: 15px;
        margin-bottom: 20px;
    }

    .privacy-checkbox-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 30px;
    }

    .privacy-checkbox {
        width: 18px;
        height: 18px;
        min-width: 18px;
        margin-top: 2px;
        cursor: pointer;
        accent-color: #00b3f1;
    }

    .privacy-text {
        color: white;
        font-size: 13px;
        line-height: 1.6;
        font-family: var(--body-font-family, 'DM Sans', sans-serif);
    }

    .privacy-text a {
        color: #00b3f1;
        text-decoration: none;
    }

    .privacy-text a:hover {
        text-decoration: underline;
    }

    .register-submit-btn {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        padding: 15px 30px;
        background: #00b3f1;
        border: 1px solid white;
        border-radius: 4px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: var(--body-font-family, 'DM Sans', sans-serif);
        display: block;
    }

    .register-submit-btn:hover {
        background: #0099cc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 179, 241, 0.3);
    }

    .register-submit-btn:active {
        transform: translateY(0);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    @media (max-width: 992px) {
        .register-form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .register-form-grid {
            grid-template-columns: 1fr;
        }

        .register-tab {
            padding: 12px 20px;
            font-size: 14px;
        }

        .register-logo-text {
            font-size: 24px;
        }
    }
</style>
@endpush

@section('content')
<style>
    /* Hide header and footer on register page */
    .header-nav,
    .footer-style1 {
        display: none !important;
    }
    
    .body_content {
        padding-top: 0 !important;
    }
    
    .register-page {
        margin-top: 0;
    }
</style>
<div class="register-page">
    <div class="register-container">
        <!-- Logo -->
        <div class="register-logo">
            <a class="header-logo logo1" href="{{ route('web.index') }}">
                <img src="{{ asset('web/images/logo.png') }}" alt="Header Logo" width="120px">
            </a>
        </div>

        <!-- Tabs -->
        <div class="register-tabs">
            <button type="button" class="register-tab active" data-tab="customer">Customer</button>
            <button type="button" class="register-tab" data-tab="professional">Professional</button>
        </div>

        <!-- Customer Form -->
        <form action="{{ route('web.register.process') }}" method="post" id="customerForm" class="tab-content active">
            @csrf
            <input type="hidden" name="user_type" value="2">
            <div class="register-form-container">
                <div class="register-form-grid">
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Name<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Surname<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="surname" placeholder="Surname" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Date of Birth<span class="required">*</span>
                        </label>
                        <input type="date" class="form-input-register" name="date_of_birth" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Username<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            E-mail<span class="required">*</span>
                        </label>
                        <input type="email" class="form-input-register" name="email" placeholder="email@example.com" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Country<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="country" placeholder="Country" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            City<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="city" placeholder="City" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Cap<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="cap" placeholder="CAP" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Password<span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input-register" name="password" id="customer_password" placeholder="Password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('customer_password', this)">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Confirm Password<span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input-register" name="password_confirmation" id="customer_password_confirmation" placeholder="Confirm Password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('customer_password_confirmation', this)">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <p class="required-fields-note">* Required fields</p>

                <div class="privacy-checkbox-wrapper">
                    <input type="checkbox" class="privacy-checkbox" id="customer_privacy" name="privacy_consent" required>
                    <label for="customer_privacy" class="privacy-text">
                        Dichiaro di aver letto e compreso l'<a href="{{ route('web.privacy_policy') }}" target="_blank">Informativa Privacy</a> e autorizzo il trattamento dei miei dati personali ai sensi del Regolamento UE 2016/679 (GDPR).
                    </label>
                </div>

                <button type="submit" class="register-submit-btn">Create Account</button>
            </div>
        </form>

        <!-- Professional Form -->
        <form action="{{ route('web.register.process') }}" method="post" id="professionalForm" class="tab-content">
            @csrf
            <input type="hidden" name="user_type" value="3">
            <div class="register-form-container">
                <div class="register-form-grid">
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Name<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Surname<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="surname" placeholder="Surname" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Business Name<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="business_name" placeholder="Business Name" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Username<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            E-mail<span class="required">*</span>
                        </label>
                        <input type="email" class="form-input-register" name="email" placeholder="email@example.com" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Country<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="country" placeholder="Country" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            City<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="city" placeholder="City" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Address
                        </label>
                        <input type="text" class="form-input-register" name="address" placeholder="Address">
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            CAP<span class="required">*</span>
                        </label>
                        <input type="text" class="form-input-register" name="cap" placeholder="CAP" required>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Choose Category<span class="required">*</span>
                        </label>
                        <select class="form-select-register" name="category_id" id="professional_category" required onchange="loadSubcategories(this.value, 'professional')">
                            <option value="">Choose Category</option>
                            @foreach($active_categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Subcategory 1<span class="required">*</span>
                        </label>
                        <select class="form-select-register" name="subcategory_1" id="professional_subcategory_1" required>
                            <option value="">Select Category First</option>
                        </select>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Subcategory 2
                        </label>
                        <select class="form-select-register" name="subcategory_2" id="professional_subcategory_2">
                            <option value="">Optional</option>
                        </select>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Subcategory 3
                        </label>
                        <select class="form-select-register" name="subcategory_3" id="professional_subcategory_3">
                            <option value="">Optional</option>
                        </select>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Password<span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input-register" name="password" id="professional_password" placeholder="Password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('professional_password', this)">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group-register">
                        <label class="form-label-register">
                            Confirm Password<span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input-register" name="password_confirmation" id="professional_password_confirmation" placeholder="Confirm Password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('professional_password_confirmation', this)">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Credit Card Section for Professionals -->
                <div class="credit-card-section" style="margin: 30px 0; padding: 25px; background: rgba(0, 179, 241, 0.05); border: 1px solid #00b3f1; border-radius: 12px;">
                    <h3 style="color: white; font-size: 18px; margin-bottom: 15px; font-family: var(--body-font-family, 'DM Sans', sans-serif);">
                        Payment Information <span class="required">*</span>
                    </h3>
                    <p style="color: #00b3f1; font-size: 13px; margin-bottom: 20px; font-family: var(--body-font-family, 'DM Sans', sans-serif);">
                        First year free, €99/year renewal
                    </p>
                    <div id="professional-card-element" style="padding: 12px; background: transparent; border: 1px solid #00b3f1; border-radius: 12px; margin-bottom: 10px;">
                        <!-- Stripe Elements will create form elements here -->
                    </div>
                    <div id="professional-card-errors" role="alert" style="color: #ff4444; font-size: 13px; margin-top: 10px; min-height: 20px;"></div>
                    <input type="hidden" name="payment_method_id" id="professional_payment_method_id">
                </div>

                <p class="required-fields-note">* Required fields</p>

                <div class="privacy-checkbox-wrapper">
                    <input type="checkbox" class="privacy-checkbox" id="professional_privacy" name="privacy_consent" required>
                    <label for="professional_privacy" class="privacy-text">
                        Dichiaro di aver letto e compreso l'<a href="{{ route('web.privacy_policy') }}" target="_blank">Informativa Privacy</a> e autorizzo il trattamento dei miei dati personali ai sensi del Regolamento UE 2016/679 (GDPR).
                    </label>
                </div>

                <button type="submit" class="register-submit-btn" id="professional-submit-btn">Create Account</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Initialize Stripe for Professional form
    let professionalStripe;
    let professionalCardElement;
    let professionalCardErrors;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize Stripe if we're on the professional tab
        const professionalForm = document.getElementById('professionalForm');
        if (professionalForm) {
            professionalStripe = Stripe("{{ config('services.stripe.key') }}");
            const professionalElements = professionalStripe.elements();
            
            professionalCardElement = professionalElements.create('card', {
                style: {
                    base: {
                        color: '#ffffff',
                        fontFamily: "'DM Sans', sans-serif",
                        fontSize: '14px',
                        '::placeholder': {
                            color: '#999999',
                        },
                    },
                    invalid: {
                        color: '#ff4444',
                    },
                },
            });
            
            professionalCardElement.mount('#professional-card-element');
            
            professionalCardErrors = document.getElementById('professional-card-errors');
            
            professionalCardElement.on('change', function(event) {
                if (event.error) {
                    professionalCardErrors.textContent = event.error.message;
                } else {
                    professionalCardErrors.textContent = '';
                }
            });
        }
    });

    // Handle professional form submission with Stripe
    const professionalForm = document.getElementById('professionalForm');
    if (professionalForm) {
        professionalForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const submitBtn = document.getElementById('professional-submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            
            try {
                // Create setup intent first (we'll handle this server-side after user creation)
                // For now, we'll just validate the card and continue with form submission
                const {setupIntent, error} = await professionalStripe.confirmCardSetup(
                    await getSetupIntentClientSecret(), {
                        payment_method: {
                            card: professionalCardElement,
                            billing_details: {
                                name: document.querySelector('#professionalForm input[name="name"]').value + ' ' + document.querySelector('#professionalForm input[name="surname"]').value,
                                email: document.querySelector('#professionalForm input[name="email"]').value,
                            },
                        },
                    }
                );
                
                if (error) {
                    professionalCardErrors.textContent = error.message;
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Account';
                    return;
                }
                
                // Set the payment method ID in hidden field
                document.getElementById('professional_payment_method_id').value = setupIntent.payment_method;
                
                // Now submit the form
                professionalForm.submit();
                
            } catch (err) {
                professionalCardErrors.textContent = 'An error occurred. Please try again.';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Account';
            }
        });
    }
    
    // Function to get setup intent client secret
    async function getSetupIntentClientSecret() {
        // We'll create the setup intent on the server side
        // For now, we'll use a temporary approach - create customer during registration
        const response = await fetch('/api/create-setup-intent-for-registration', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: document.querySelector('#professionalForm input[name="email"]').value,
                name: document.querySelector('#professionalForm input[name="name"]').value
            })
        });
        
        const data = await response.json();
        return data.clientSecret;
    }
</script>
<script>
    // Tab switching
    document.querySelectorAll('.register-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Update tab styles
            document.querySelectorAll('.register-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide forms
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            if (tabName === 'customer') {
                document.getElementById('customerForm').classList.add('active');
            } else {
                document.getElementById('professionalForm').classList.add('active');
                // Re-initialize Stripe card element when switching to professional tab
                if (professionalStripe && !professionalCardElement) {
                    const professionalElements = professionalStripe.elements();
                    professionalCardElement = professionalElements.create('card', {
                        style: {
                            base: {
                                color: '#ffffff',
                                fontFamily: "'DM Sans', sans-serif",
                                fontSize: '14px',
                                '::placeholder': {
                                    color: '#999999',
                                },
                            },
                            invalid: {
                                color: '#ff4444',
                            },
                        },
                    });
                    professionalCardElement.mount('#professional-card-element');
                    professionalCardElement.on('change', function(event) {
                        if (event.error) {
                            professionalCardErrors.textContent = event.error.message;
                        } else {
                            professionalCardErrors.textContent = '';
                        }
                    });
                }
            }
        });
    });

    // Password toggle
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Load subcategories when category is selected
    function loadSubcategories(categoryId, prefix) {
        if (!categoryId) {
            // Reset subcategories
            ['1', '2', '3'].forEach(num => {
                const select = document.getElementById(`${prefix}_subcategory_${num}`);
                if (select) {
                    select.innerHTML = num === '1' ? '<option value="">Select Category First</option>' : '<option value="">Optional</option>';
                }
            });
            return;
        }

        fetch(`/api/subcategories/${categoryId}`)
            .then(response => response.json())
            .then(data => {
                ['1', '2', '3'].forEach(num => {
                    const select = document.getElementById(`${prefix}_subcategory_${num}`);
                    if (select) {
                        const isRequired = num === '1';
                        select.innerHTML = isRequired 
                            ? '<option value="">Select Subcategory</option>'
                            : '<option value="">Optional</option>';
                        
                        data.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.textContent = subcategory.name;
                            select.appendChild(option);
                        });
                    }
                });
            })
            .catch(error => {
                console.error('Error loading subcategories:', error);
            });
    }
</script>
@endpush
