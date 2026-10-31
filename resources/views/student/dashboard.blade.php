@extends('layouts.student')

@section('content')
    <!-- Student Dashboard Section Start -->
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('student.partials.sidebar')

                <!-- Main Content Start -->
                <div class="dashboard-main">
                    <!-- Welcome Section -->
                    <div class="section-title wow fadeInUp">
                        <h6>Student Dashboard</h6>
                        <h2>Welcome back, {{ $firstName }}!</h2>
                        <p>Continue your learning journey and track your progress</p>
                    </div>

                    <!-- Stats Grid -->
                    <div class="dashboard-stats-minimal">
                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-1">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $stats['total_enrollments'] }}</div>
                                <div class="stat-label-minimal">Enrolled Courses</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-2">
                                <i class="fa-solid fa-check-circle"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $stats['completed_courses'] }}</div>
                                <div class="stat-label-minimal">Completed</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-3">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $stats['pending_enrollments'] }}</div>
                                <div class="stat-label-minimal">Pending</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-4">
                                <i class="fa-solid fa-play-circle"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $stats['total_lessons'] }}</div>
                                <div class="stat-label-minimal">Total Lessons</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Enrollments -->
                    <div class="section-title-area wow fadeInUp" style="margin-top: 50px; margin-bottom: 30px;">
                        <div class="section-title">
                            <h6>Recent Activity</h6>
                            <h2>My Recent Enrollments</h2>
                        </div>
                    </div>
                    <div class="dashboard-content-section">
                        @forelse($recentEnrollments as $enrollment)
                            <div class="dashboard-activity-item"
                                style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                <div class="dashboard-activity-icon">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div class="dashboard-activity-content">
                                    <h5><a href="{{ route('student.courses.access', $enrollment->course->id) }}"
                                            style="text-decoration: none; color: inherit;">{{ $enrollment->course->title }}</a>
                                    </h5>
                                    <p>{{ $enrollment->course->instructor->name ?? 'Instructor' }} •
                                        {{ $enrollment->lessons_count }} Lessons
                                    </p>
                                </div>
                                <div class="dashboard-activity-time">
                                    @if ($enrollment->enrolled_at)
                                        {{ $enrollment->enrolled_at->diffForHumans() }}
                                    @else
                                        Recently
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                <p class="mb-0">You haven't enrolled in any courses yet. <a href="{{ route('courses') }}">Browse
                                        courses</a> to get started!</p>
                            </div>
                        @endforelse

                        @if ($recentEnrollments->count() > 0)
                            <div class="text-center mt-4">
                                <a href="{{ route('student.my-courses') }}" class="theme-btn">
                                    View All Courses
                                    <i class="fa-solid fa-arrow-up-right"></i>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="section-title-area wow fadeInUp" style="margin-top: 50px; margin-bottom: 30px;">
                        <div class="section-title">
                            <h6>Quick Actions</h6>
                            <h2>Get Started</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-4 wow fadeInUp">
                            <div class="dashboard-content-section h-100">
                                <h3>
                                    <i class="fa-solid fa-compass"></i>
                                    Explore
                                </h3>
                                <div style="display: flex; flex-direction: column; gap: 15px;">
                                    <a href="{{ route('courses') }}" class="theme-btn"
                                        style="justify-content: flex-start; padding: 15px 20px;">
                                        <i class="fa-solid fa-search"></i>
                                        <span style="margin-left: 10px;">Browse All Courses</span>
                                    </a>
                                    <a href="{{ route('student.my-courses') }}" class="theme-btn"
                                        style="justify-content: flex-start; padding: 15px 20px; background: transparent; border: 2px solid var(--theme); color: var(--theme);">
                                        <i class="fa-solid fa-book-open"></i>
                                        <span style="margin-left: 10px;">My Courses</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4 wow fadeInUp" data-wow-delay=".2s">
                            <div class="dashboard-content-section h-100" style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                                <h3 style="font-size: 22px; font-weight: 800; color: #002b24; margin-bottom: 25px; display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-bullhorn" style="color: #004f44; font-size: 24px;"></i>
                                    Announcements
                                </h3>
                                
                                <div class="announcement-item" style="background: #f1f7f6; padding: 25px; border-radius: 16px; margin-bottom: 15px;">
                                    <h5 style="font-size: 18px; color: #004f44; margin-bottom: 12px; font-weight: 800;">Welcome!</h5>
                                    <p style="font-size: 15px; color: #444; margin: 0; line-height: 1.6;">
                                        Start your learning journey by exploring our wide range of courses!
                                    </p>
                                </div>
                                
                                <div class="announcement-item" style="background: #fff8f0; padding: 25px; border-radius: 16px;">
                                    <h5 style="font-size: 18px; color: #ff9d00; margin-bottom: 12px; font-weight: 800;">New Courses</h5>
                                    <p style="font-size: 15px; color: #444; margin: 0; line-height: 1.6;">
                                        Check out our latest courses and expand your knowledge!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Main Content End -->
            </div>
        </div>
    </section>
@endsection