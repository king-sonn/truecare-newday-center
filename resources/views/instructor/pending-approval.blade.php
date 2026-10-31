@extends('layouts.instructor')

@section('content')
    <!-- Instructor Pending Approval Section Start -->
    <section class="mhq-hero-section fix mhq-hero-1 bg-cover"
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
            <div class="row justify-content-center align-items-center min-vh-100 py-5">
                <div class="col-lg-8 col-md-10">
                    <div class="instructor-wrapper bg-white wow fadeInUp" data-wow-delay=".3s">
                        <div class="instructor-header text-center mb-4">
                            @if ($rejection_reason != null)
                                <h2 class="wow fadeInUp" data-wow-delay=".2s">Application Rejected</h2>
                            @else
                                <h2 class="wow fadeInUp" data-wow-delay=".2s">Application Under Review</h2>
                            @endif

                        </div>

                        <div class="instructor-info-box text-center">
                            <div class="mb-4">
                                <i class="fa-solid fa-clock fa-4x text-primary"></i>
                            </div>
                            <h5 class="mb-3">Thank you for your interest!</h5>
                            @if ($rejection_reason != null)
                                <p class="mb-0">We're sorry to inform you that your instructor application has been
                                    rejected. Please review the rejection reason below and make the necessary adjustments to
                                    your application.</p>
                                <p class="mb-0 bg-warning fw-bold p-2 rounded-2">{{ $rejection_reason }}</p>
                            @else
                                <p class="mb-0">Your instructor application has been received and is currently being
                                    reviewed by our team. We'll notify you via email once a decision has been made.</p>
                                <p class="mb-0 bg-success fw-bold p-2 rounded-2">{{ $approval_reason }}</p>
                            @endif
                        </div>

                        <div class="text-center mt-4">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="theme-btn">
                                    Logout <i class="fa-solid fa-sign-out me-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
