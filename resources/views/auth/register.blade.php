@extends('layouts.app')

@section('content')
    <!-- Register Section Start -->
    <section class="mhq-hero-section fix mhq-hero-1 bg-cover pt-6"
        style="background-image: url('{{ asset('assets/front/img/home-1/hero/hero-bg.jpg') }}');">
        <div class="hero-shape-1 float-bob-x">
            <img src="{{ asset('assets/front/img/home-1/hero/shape-01.png') }}" alt="img">
        </div>
        <div class="hero-shape-2 float-bob-y">
            <img src="{{ asset('assets/front/img/home-1/hero/shape-02.png') }}" alt="img">
        </div>
        <div class="hero-shape-3 float-bob-y">
            <img src="{{ asset('assets/front/img/home-1/hero/shape-03.png') }}" alt="img">
        </div>
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-7 col-md-9">
                    <div class="mhq-register-wrapper bg-white wow fadeInUp" data-wow-delay=".3s">
                        <div class="mhq-register-header text-center mb-4">
                            <?php $siteName = site_name(); ?>
                            <a href="{{ route('home') }}" class="register-logo mb-3">
                                <img src="{{ branding_asset(['dark_logo_path', 'logos.secondary', 'primary_logo_path', 'logos.primary'], 'assets/front/img/logo/black-logo.svg') }}" alt="{{ $siteName }} logo">
                            </a>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s">Create Your Account</h2>
                            <p class="wow fadeInUp" data-wow-delay=".3s">Join thousands of learners and start your journey
                                today</p>
                        </div>

                        <form action="{{ route('register') }}" method="POST" class="mhq-register-form">
                            @csrf


                            <div class="row">
                                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                                    <div class="form-clt">
                                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                                            placeholder="First Name" class="@error('first_name') is-invalid @enderror"
                                            required autocomplete="given-name" autofocus>
                                    </div>
                                    @error('first_name')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                                    <div class="form-clt">
                                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                                            placeholder="Last Name" class="@error('last_name') is-invalid @enderror"
                                            required autocomplete="family-name">
                                    </div>
                                    @error('last_name')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-12 wow fadeInUp" data-wow-delay=".5s">
                                    <div class="form-clt">
                                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                                            placeholder="Your Email" class="@error('email') is-invalid @enderror" required
                                            autocomplete="email">
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-12 wow fadeInUp" data-wow-delay=".6s">
                                    <div class="form-clt position-relative">
                                        <input type="password" name="password" id="password" placeholder="Your Password"
                                            class="@error('password') is-invalid @enderror" required
                                            autocomplete="new-password">
                                        <span class="toggle-password position-absolute">
                                            <i class="fa-regular fa-eye" id="togglePassword"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">Password must be at least 8 characters</small>
                                </div>

                                <div class="col-lg-12 wow fadeInUp" data-wow-delay=".7s">
                                    <div class="form-clt position-relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            placeholder="Confirm Password" required autocomplete="new-password">
                                        <span class="toggle-password-confirm position-absolute">
                                            <i class="fa-regular fa-eye" id="togglePasswordConfirm"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mb-4 wow fadeInUp" data-wow-delay=".8s">
                                <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#"
                                        class="text-primary">Privacy Policy</a>
                                </label>
                            </div>

                            <x-recaptcha-widget class="mb-4 wow fadeInUp" data-wow-delay=".85s" action="student_register" />

                            <button type="submit" class="theme-btn w-100 mb-3 wow fadeInUp" data-wow-delay=".9s"
                                id="registerSubmitBtn">
                                Create Account
                                <i class="fa-solid fa-arrow-up-right"></i>
                            </button>

                            <div class="text-center wow fadeInUp" data-wow-delay="1s">
                                <p class="mb-0">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="text-primary fw-bold">Sign In</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const togglePassword = document.getElementById('togglePassword');
                const password = document.getElementById('password');

                const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
                const passwordConfirm = document.getElementById('password_confirmation');

                if (togglePassword) {
                    togglePassword.addEventListener('click', function () {
                        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                        password.setAttribute('type', type);
                        this.classList.toggle('fa-eye');
                        this.classList.toggle('fa-eye-slash');
                    });
                }

                if (togglePasswordConfirm) {
                    togglePasswordConfirm.addEventListener('click', function () {
                        const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordConfirm.setAttribute('type', type);
                        this.classList.toggle('fa-eye');
                        this.classList.toggle('fa-eye-slash');
                    });
                }

                const registerForm = document.querySelector('.mhq-register-form');
                const submitBtn = document.getElementById('registerSubmitBtn');
                if (registerForm && submitBtn) {
                    registerForm.addEventListener('submit', function () {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Creating... <i class="fa fa-spinner fa-spin"></i>';
                    });
                }
            });
        </script>
    @endpush
@endsection