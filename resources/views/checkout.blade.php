@extends('layouts.app')

@section('breadcrumb')
    @php
        $page_title = 'Checkout';
        $base_url = route('home');
    @endphp
    @include('partials.breadcrumb')
@endsection

@section('content')
    <!-- Checkout Section Start -->
    <section class="mhq-hero-section fix mhq-hero-1 bg-cover py-4 hero-bg-default">
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
            <div class="row justify-content-center align-items-center py-5">
                <div class="col-lg-10">
                    <div class="checkout-wrapper bg-white wow fadeInUp" data-wow-delay=".3s">
                        <div class="checkout-header text-center mb-4">
                            <h2 class="wow fadeInUp" data-wow-delay=".2s">Complete Your Enrollment</h2>
                            <p class="wow fadeInUp" data-wow-delay=".3s">Review your course details and proceed to payment
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <form id="checkoutForm" action="{{ route('courses.checkout.process', $course->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="course-summary mb-4 wow fadeInUp" data-wow-delay=".4s">
                                        <h4 class="mb-3">Course Summary</h4>
                                        <div class="d-flex align-items-start">
                                            @if ($course->featured_image)
                                                <img src="{{ asset('storage/' . $course->featured_image) }}"
                                                    alt="{{ $course->title }}" class="me-3 course-thumb-lg">
                                            @else
                                                <img src="{{ asset('assets/front/img/inner/courses-details/courses-details.jpg') }}"
                                                    alt="{{ $course->title }}" class="me-3 course-thumb-lg">
                                            @endif
                                            <div>
                                                <h5>{{ $course->title }}</h5>
                                                @if ($course->instructor)
                                                    <p class="mb-1"><small>Instructor:
                                                            {{ $course->instructor->name }}</small></p>
                                                @endif
                                                <p class="mb-0"><strong>Price:
                                                        {{ $price > 0 ? currency_format($price) : 'Free' }}</strong></p>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($price > 0)
                                        <div class="payment-method mb-4 wow fadeInUp" data-wow-delay=".5s">
                                            <h4 class="mb-3">Select Payment Method</h4>
                                            @php($first = true)
                                            @foreach ($onlineGateways as $gateway)
                                                @php($identifier = $gateway->identifier)
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="radio" name="payment_method"
                                                        id="{{ $identifier }}" value="{{ $identifier }}"
                                                        {{ $first ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $identifier }}">
                                                        <strong>{{ $gateway->name }} (Online Payment)</strong>
                                                        <p class="mb-0 small text-muted">
                                                            {{ $gatewayDescriptions[$identifier] ?? 'Pay securely with this gateway.' }}
                                                        </p>
                                                    </label>
                                                </div>
                                                @php($first = false)
                                            @endforeach
                                            @if ($offlineGateway && $offlineGateway->is_enabled)
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="radio" name="payment_method"
                                                        id="offline" value="offline" {{ $first ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="offline">
                                                        <strong>Offline Payment</strong>
                                                        <p class="mb-0 small text-muted">Submit receipt and transaction ID
                                                            for manual verification</p>
                                                    </label>
                                                </div>
                                            @endif
                                            @error('payment_method')
                                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        @if ($offlineGateway && $offlineGateway->is_enabled)
                                            <div id="offlinePaymentSection"
                                                class="offline-payment-section mb-4 wow fadeInUp {{ !$onlineGateways->count() ? 'is-visible' : '' }}"
                                                data-wow-delay=".6s">
                                                <h5 class="mb-3">Payment Details</h5>
                                                @if (!empty($offlineInstructions))
                                                    <div class="alert alert-info alert-preline" role="alert">
                                                        {{ $offlineInstructions }}
                                                    </div>
                                                @endif
                                                <div class="mb-3">
                                                    <label for="transaction_id" class="form-label">Transaction ID <span
                                                            class="text-danger">*</span></label>
                                                    <div class="form-clt">
                                                        <input type="text" name="transaction_id" id="transaction_id"
                                                            value="{{ old('transaction_id') }}"
                                                            placeholder="Enter your transaction ID"
                                                            class="@error('transaction_id') is-invalid @enderror">
                                                    </div>
                                                    @error('transaction_id')
                                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="receipt_file" class="form-label">Upload Receipt <span
                                                            class="text-danger">*</span></label>
                                                    <div class="form-clt">
                                                        <input type="file" name="receipt_file" id="receipt_file"
                                                            accept=".pdf,.jpg,.jpeg,.png"
                                                            class="@error('receipt_file') is-invalid @enderror">
                                                    </div>
                                                    <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max
                                                        5MB)</small>
                                                    @error('receipt_file')
                                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="form-actions wow fadeInUp" data-wow-delay=".7s">
                                        <button type="submit" class="theme-btn w-100" id="submitBtn">
                                            @if ($price > 0)
                                                Proceed to Payment
                                            @else
                                                Complete Enrollment
                                            @endif
                                            <i class="fa-solid fa-arrow-up-right"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="col-lg-4">
                                <div class="checkout-summary bg-light p-4 rounded wow fadeInUp" data-wow-delay=".5s">
                                    <h5 class="mb-3">Order Summary</h5>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Course:</span>
                                        <span>{{ $course->title }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Price:</span>
                                        <span><strong>{{ $price > 0 ? currency_format($price) : 'Free' }}</strong></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span><strong>Total:</strong></span>
                                        <span><strong>{{ $price > 0 ? currency_format($price) : 'Free' }}</strong></span>
                                    </div>
                                    <a href="{{ route('courses.show', $course->id) }}"
                                        class="theme-btn style-2 w-100 text-center">
                                        <i class="fa-regular fa-arrow-left me-2"></i> Back to Course
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            $(document).ready(function() {

                $('input[name="payment_method"]').on('change', function() {
                    if ($(this).val() === 'offline') {
                        $('#offlinePaymentSection').slideDown().addClass('is-visible');
                        $('#transaction_id').prop('required', true);
                        $('#receipt_file').prop('required', true);
                    } else {
                        $('#offlinePaymentSection').slideUp().removeClass('is-visible');
                        $('#transaction_id').prop('required', false);
                        $('#receipt_file').prop('required', false);
                    }
                });

                $('input[name="payment_method"]:checked').trigger('change');

                $('#checkoutForm').on('submit', function(e) {
                    const paymentMethod = $('input[name="payment_method"]:checked').val();
                    const price = {{ $price }};

                    if (price > 0 && paymentMethod === 'razorpay') {
                        e.preventDefault();
                        handleRazorpayPayment();
                    } else if (price > 0 && paymentMethod === 'stripe') {
                        e.preventDefault();
                        handleStripePayment();
                    } else if (price > 0 && paymentMethod === 'paystack') {
                        e.preventDefault();
                        handlePaystackPayment();
                    } else if (price > 0 && paymentMethod === 'flutterwave') {
                        e.preventDefault();
                        handleFlutterwavePayment();
                    } else if (price > 0 && paymentMethod === 'paypal') {
                        e.preventDefault();
                        handlePaypalPayment();
                    } else if (price > 0 && paymentMethod === 'sslcommerz') {
                        e.preventDefault();
                        handleSslcommerzPayment();
                    } else if (price > 0 && paymentMethod === 'mollie') {
                        e.preventDefault();
                        handleMolliePayment();
                    }
                });

                function handleRazorpayPayment() {
                    $('#submitBtn').prop('disabled', true).html('Processing... <i class="fa fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: $('#checkoutForm').attr('action'),
                        method: 'POST',
                        data: new FormData($('#checkoutForm')[0]),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                const options = {
                                    key: response.key,
                                    amount: response.amount,
                                    currency: 'INR',
                                    name: response.name,
                                    description: response.description,
                                    order_id: response.order_id,
                                    prefill: response.prefill,
                                    handler: function(razorpayResponse) {
                                        const form = $('<form>', {
                                            method: 'POST',
                                            action: response.callback_url
                                        });

                                        form.append($('<input>', {
                                            type: 'hidden',
                                            name: 'razorpay_order_id',
                                            value: razorpayResponse.razorpay_order_id
                                        }));

                                        form.append($('<input>', {
                                            type: 'hidden',
                                            name: 'razorpay_payment_id',
                                            value: razorpayResponse.razorpay_payment_id
                                        }));

                                        form.append($('<input>', {
                                            type: 'hidden',
                                            name: 'razorpay_signature',
                                            value: razorpayResponse.razorpay_signature
                                        }));

                                        form.append($('<input>', {
                                            type: 'hidden',
                                            name: 'enrollment_id',
                                            value: response.enrollment_id
                                        }));

                                        form.append($('<input>', {
                                            type: 'hidden',
                                            name: '_token',
                                            value: '{{ csrf_token() }}'
                                        }));

                                        $('body').append(form);
                                        form.submit();
                                    },
                                    modal: {
                                        ondismiss: function() {
                                            $('#submitBtn').prop('disabled', false).html(
                                                'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>'
                                                );
                                        }
                                    }
                                };

                                const razorpay = new Razorpay(options);
                                razorpay.open();
                            }
                        },
                        error: function(xhr) {
                            $('#submitBtn').prop('disabled', false).html(
                                'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        }
                    });
                }

                function handleStripePayment() {
                    $('#submitBtn').prop('disabled', true).html('Processing... <i class="fa fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: $('#checkoutForm').attr('action'),
                        method: 'POST',
                        data: new FormData($('#checkoutForm')[0]),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success && response.checkout_url) {
                                window.location.href = response.checkout_url;
                            } else {
                                $('#submitBtn').prop('disabled', false).html(
                                    'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                                alert('An error occurred. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            $('#submitBtn').prop('disabled', false).html(
                                'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        }
                    });
                }

                function handlePaystackPayment() {
                    $('#submitBtn').prop('disabled', true).html('Processing... <i class="fa fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: $('#checkoutForm').attr('action'),
                        method: 'POST',
                        data: new FormData($('#checkoutForm')[0]),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success && response.authorization_url) {
                                window.location.href = response.authorization_url;
                            } else {
                                $('#submitBtn').prop('disabled', false).html(
                                    'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                                alert('An error occurred. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            $('#submitBtn').prop('disabled', false).html(
                                'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        }
                    });
                }

                function handleFlutterwavePayment() {
                    $('#submitBtn').prop('disabled', true).html('Processing... <i class="fa fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: $('#checkoutForm').attr('action'),
                        method: 'POST',
                        data: new FormData($('#checkoutForm')[0]),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success && response.checkout_url) {
                                window.location.href = response.checkout_url;
                            } else {
                                $('#submitBtn').prop('disabled', false).html(
                                    'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                                alert('An error occurred. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            $('#submitBtn').prop('disabled', false).html(
                                'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        }
                    });
                }

                function handlePaypalPayment() {
                    $('#submitBtn').prop('disabled', true).html('Processing... <i class="fa fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: $('#checkoutForm').attr('action'),
                        method: 'POST',
                        data: new FormData($('#checkoutForm')[0]),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success && response.checkout_url) {
                                window.location.href = response.checkout_url;
                            } else {
                                $('#submitBtn').prop('disabled', false).html(
                                    'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                                alert('An error occurred. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            $('#submitBtn').prop('disabled', false).html(
                                'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                alert(xhr.responseJSON.message);
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        }
                    });
                }

                function handleSslcommerzPayment() {
                    $('#submitBtn').prop('disabled', true).html('Processing... <i class="fa fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: $('#checkoutForm').attr('action'),
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: new FormData($('#checkoutForm')[0]),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success && response.checkout_url) {
                                window.location.href = response.checkout_url;
                            } else {
                                $('#submitBtn').prop('disabled', false).html(
                                    'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                                alert(response.message || 'An error occurred. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            $('#submitBtn').prop('disabled', false).html(
                                'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                            let errorMessage = 'An error occurred. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    const errorData = JSON.parse(xhr.responseText);
                                    if (errorData.message) {
                                        errorMessage = errorData.message;
                                    }
                                } catch (e) {
                                        
                                }
                            }
                            alert(errorMessage);
                        }
                    });
                }

                function handleMolliePayment() {
                    $('#submitBtn').prop('disabled', true).html('Processing... <i class="fa fa-spinner fa-spin"></i>');

                    $.ajax({
                        url: $('#checkoutForm').attr('action'),
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: new FormData($('#checkoutForm')[0]),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success && response.checkout_url) {
                                window.location.href = response.checkout_url;
                            } else {
                                $('#submitBtn').prop('disabled', false).html(
                                    'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                                alert(response.message || 'An error occurred. Please try again.');
                            }
                        },
                        error: function(xhr) {
                            $('#submitBtn').prop('disabled', false).html(
                                'Proceed to Payment <i class="fa-solid fa-arrow-up-right"></i>');
                            let errorMessage = 'An error occurred. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    const errorData = JSON.parse(xhr.responseText);
                                    if (errorData.message) {
                                        errorMessage = errorData.message;
                                    }
                                } catch (e) {
                                    
                                }
                            }
                            alert(errorMessage);
                        }
                    });
                }
            });
        </script>
    @endpush

@endsection
