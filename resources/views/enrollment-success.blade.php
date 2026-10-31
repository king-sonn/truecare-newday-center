@extends('layouts.app')

@section('breadcrumb')
    @php
        $page_title = 'Enrollment Successful';
        $base_url = route('home');
    @endphp
    @include('partials.breadcrumb')
@endsection

@section('content')
    <!-- Success Section Start -->
    <section class="mhq-hero-section fix mhq-hero-1 bg-cover py-2 hero-bg-default">
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
                    <div class="success-wrapper bg-white wow fadeInUp" data-wow-delay=".3s">
                        <div class="success-icon mb-4">
                            <div class="success-checkmark">
                                <div class="check-icon">
                                    <span class="icon-line line-tip"></span>
                                    <span class="icon-line line-long"></span>
                                    <div class="icon-circle"></div>
                                    <div class="icon-fix"></div>
                                </div>
                            </div>
                        </div>

                        <h2 class="mb-3 wow fadeInUp" data-wow-delay=".4s">
                            @if ($enrollment->isApproved())
                                Enrollment Successful!
                            @else
                                Enrollment Request Submitted
                            @endif
                        </h2>

                        <p class="mb-4 wow fadeInUp" data-wow-delay=".5s">
                            @if ($enrollment->isApproved())
                                Congratulations! You have successfully enrolled in
                                <strong>{{ $enrollment->course->title }}</strong>.
                            @else
                                Your enrollment request for <strong>{{ $enrollment->course->title }}</strong> has been
                                submitted successfully.
                                @if ($enrollment->payment && $enrollment->payment->isOffline())
                                    Your payment is pending admin verification. We will notify you once it's approved.
                                @endif
                            @endif
                        </p>

                        <div class="course-details mb-4 wow fadeInUp course-details-card" data-wow-delay=".6s">
                            <div class="d-flex align-items-start mb-3">
                                @if ($enrollment->course->featured_image)
                                    <img src="{{ asset('storage/' . $enrollment->course->featured_image) }}"
                                        alt="{{ $enrollment->course->title }}" class="me-3 course-thumb-sm">
                                @else
                                    <img src="{{ asset('assets/front/img/inner/courses-details/courses-details.jpg') }}"
                                        alt="{{ $enrollment->course->title }}" class="me-3 course-thumb-sm">
                                @endif
                                <div>
                                    <h5 class="mb-1">{{ $enrollment->course->title }}</h5>
                                    @if ($enrollment->course->instructor)
                                        <p class="mb-1 small text-muted">Instructor:
                                            {{ $enrollment->course->instructor->name }}</p>
                                    @endif
                                    @if ($enrollment->payment)
                                        <p class="mb-0 small"><strong>Amount Paid:
                                                {{ currency_format($enrollment->payment->amount) }}</strong></p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons wow fadeInUp" data-wow-delay=".7s">
                            @if ($enrollment->isApproved())
                                <a href="{{ route('student.courses.access', $enrollment->course->id) }}"
                                    class="theme-btn me-3 mb-2">
                                    Access Course
                                    <i class="fa-solid fa-arrow-up-right"></i>
                                </a>
                            @endif
                            <a href="{{ route('courses') }}" class="theme-btn style-2 mb-2">
                                Browse More Courses
                                <i class="fa-solid fa-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
