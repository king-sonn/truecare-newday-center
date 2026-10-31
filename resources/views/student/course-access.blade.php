@extends('layouts.student')

@push('styles')
    <link href="{{ asset('assets/front/css/video-js.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <!-- Course Access Full Screen -->
    <div class="course-access-fullscreen">
        <!-- Course Header Bar -->
        <div class="course-header-bar">
            <div class="course-header-left">
                <button class="sidebar-toggle-btn" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="course-header-item-info">
                    @if ($currentItemType === 'lesson')
                        <i class="fas fa-play-circle course-header-play-icon"></i>
                    @elseif($currentItemType === 'quiz')
                        <i class="fas fa-question-circle course-header-play-icon"></i>
                    @else
                        <i class="fas fa-file-alt course-header-play-icon"></i>
                    @endif
                    <span id="current-item-title"
                        class="course-header-title">{{ $currentItem->title ?? 'Loading...' }}</span>
                    @if ($currentItemDurationFormatted)
                        <span class="course-header-duration">({{ $currentItemDurationFormatted }} min)</span>
                    @endif
                </div>
            </div>
            <div class="course-header-right">
                <a href="{{ route('student.my-courses') }}" class="course-header-link">
                    <i class="fas fa-arrow-left"></i> Go to Course Home
                </a>
            </div>
        </div>

        <!-- Main Container -->
        <div class="course-access-main-container">
            <!-- Sidebar -->
            <div class="course-sidebar-wrapper" id="course-sidebar-wrapper">
                <div class="course-sidebar" id="course-sidebar">
                    @include('student.course-access.partials.sidebar')
                </div>
            </div>

            <!-- Main Content -->
            <div class="course-main-content-wrapper" id="main-content-wrapper">
                <div class="course-main-content" id="main-content">
                    @if (isset($isCompleted) && $isCompleted)
                        <div style="margin-bottom: 30px;">
                            @include('student.course-access.partials.completion-notice', [
                                'course' => $course,
                            ])
                        </div>
                    @endif

                    @if ($currentItemType === 'lesson')
                        @include('student.course-access.partials.lesson-content', [
                            'lesson' => $currentItem,
                            'progress' => $userProgress[$currentItem->id] ?? null,
                            'videoData' => $initialLessonVideoData ?? null,
                        ])
                    @elseif($currentItemType === 'quiz')
                        @if ($initialQuizPassed ?? false)
                            @include(
                                'student.course-access.partials.quiz-results',
                                $initialQuizResultsData ?? []
                            )
                        @else
                            @include('student.course-access.partials.quiz-content', [
                                'quiz' => $currentItem,
                                'previousAttempt' => $userQuizAttempts[$currentItem->id] ?? null,
                            ])
                        @endif
                    @else
                        @include('student.course-access.partials.assignment-content', [
                            'assignment' => $currentItem,
                            'submission' => $userAssignmentSubmissions[$currentItem->id] ?? null,
                        ])
                    @endif
                </div>
            </div>
        </div>

        <!-- Floating Navigation Buttons -->
        <div class="floating-navigation-buttons">
            <button id="prev-btn" class="floating-nav-btn" title="Previous">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button id="next-btn" class="floating-nav-btn" title="Next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/front/js/video.min.js') }}"></script>
        <script src="{{ asset('assets/front/js/videojs-http-streaming.min.js') }}"></script>
        <script>
            const courseId = {{ $course->id }};
            let currentItemId = {{ $currentItem->id ?? 0 }};
            let currentItemType = '{{ $currentItemType }}';
            let videoPlayer = null;
        </script>
        <script src="{{ asset('assets/front/js/student/course-access.js') }}"></script>
    @endpush
@endsection
