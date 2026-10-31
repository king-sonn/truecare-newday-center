@extends('layouts.student')

@section('content')
    <!-- My Courses Section Start -->
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('student.partials.sidebar')

                <!-- Main Content Start -->
                <div class="dashboard-main">
                    <div class="section-title wow fadeInUp">
                        <h6>My Courses</h6>
                        <h2>Enrolled Courses</h2>
                        <p>Manage and access all your enrolled courses</p>
                    </div>

                    <div class="row g-4" style="margin-top: 30px;">
                        @forelse($enrollments as $enrollment)
                            <div class="col-xl-6 col-lg-6 col-md-6 wow fadeInUp"
                                data-wow-delay="{{ $enrollment['animation_delay'] }}s">
                                <div class="mhq-courses-box-items mt-0">
                                    <div class="mhq-courses-image">
                                        @if ($enrollment['featured_image'])
                                            <img src="{{ asset('storage/' . $enrollment['featured_image']) }}"
                                                alt="{{ $enrollment['course_title'] }}">
                                        @else
                                            <img src="{{ asset('assets/front/img/home-1/courses/courses-01.jpg') }}"
                                                alt="{{ $enrollment['course_title'] }}">
                                        @endif
                                        @if ($enrollment['course_category'])
                                            <span class="post-box">
                                                {{ $enrollment['course_category'] }}
                                            </span>
                                        @endif
                                        @if ($enrollment['is_pending'])
                                            <span class="badge bg-warning position-absolute"
                                                style="top: 10px; right: 10px;">
                                                Pending Approval
                                            </span>
                                        @elseif($enrollment['is_approved'])
                                            <span class="badge bg-success position-absolute"
                                                style="top: 10px; right: 10px;">
                                                Enrolled
                                            </span>
                                        @elseif($enrollment['is_completed'])
                                            <span class="badge bg-info position-absolute" style="top: 10px; right: 10px;">
                                                Completed
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mhq-courses-content">
                                        <div class="star">
                                            <i class="fa-solid fa-star-sharp"></i>
                                            <i class="fa-solid fa-star-sharp"></i>
                                            <i class="fa-solid fa-star-sharp"></i>
                                            <i class="fa-solid fa-star-sharp"></i>
                                            <i class="fa-solid fa-star-sharp"></i>
                                        </div>
                                        <h3><a
                                                href="{{ $enrollment['course_id'] ? route('student.courses.access', $enrollment['course_id']) : '#' }}">{{ $enrollment['course_title'] }}</a>
                                        </h3>
                                        <ul class="post-date">
                                            <li>
                                                @if ($enrollment['intro_video'])
                                                    <a href="{{ asset('storage/' . $enrollment['intro_video']) }}"
                                                        class="icon video-popup">
                                                        <i class="fa-regular fa-circle-play"></i>
                                                    </a>
                                                @endif
                                                {{ $enrollment['lesson_count'] }}+ Lessons
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <i class="fa-regular fa-user"></i>
                                                </div>
                                                @if ($enrollment['max_students'])
                                                    Max {{ $enrollment['max_students'] }} Students
                                                @else
                                                    Unlimited
                                                @endif
                                            </li>
                                        </ul>
                                        <div class="client-info-area">
                                            <div class="client-info">
                                                @if ($enrollment['instructor_avatar'])
                                                    <div class="img">
                                                        <img src="{{ asset('storage/' . $enrollment['instructor_avatar']) }}"
                                                            alt="{{ $enrollment['instructor_name'] }}">
                                                    </div>
                                                @else
                                                    <div class="img">
                                                        <img src="{{ asset('assets/front/img/home-1/courses/client-01.png') }}"
                                                            alt="{{ $enrollment['instructor_name'] }}">
                                                    </div>
                                                @endif
                                                {{ $enrollment['instructor_name'] }}
                                            </div>
                                            <h2>{{ $enrollment['price_label'] }}</h2>
                                        </div>
                                        @if ($enrollment['is_approved'] || $enrollment['is_completed'])
                                            <a href="{{ $enrollment['course_id'] ? route('student.courses.access', $enrollment['course_id']) : '#' }}"
                                                class="theme-btn py-2">
                                                Access Course
                                                <i class="fa-solid fa-arrow-up-right"></i>
                                            </a>
                                        @else
                                            <a href="{{ $enrollment['course_id'] ? route('courses.show', $enrollment['course_id']) : '#' }}"
                                                class="theme-btn py-2" style="background: #ffc107; color: #000;">
                                                <i class="fas fa-clock me-2"></i> Pending Approval
                                            </a>
                                        @endif
                                        @if ($enrollment['enrolled_at'])
                                            <p class="mt-2 mb-0 small text-muted">
                                                <i class="far fa-calendar me-1"></i>
                                                Enrolled on {{ $enrollment['enrolled_at']->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center wow fadeInUp" data-wow-delay=".3s"
                                    style="padding: 60px 40px;">
                                    <div class="mb-4">
                                        <i class="fas fa-book-open" style="font-size: 64px; color: #6c757d;"></i>
                                    </div>
                                    <h4>No Courses Enrolled Yet</h4>
                                    <p class="mb-4">You haven't enrolled in any courses yet. Start your learning journey
                                        by browsing our available courses.</p>
                                    <a href="{{ route('courses') }}" class="theme-btn">
                                        Browse Courses
                                        <i class="fa-solid fa-arrow-up-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    {{ $enrollments->links() }}
                </div>
                <!-- Main Content End -->
            </div>
        </div>
    </section>
@endsection
