@extends('layouts.app')
@section('breadcrumb')
    @include('partials.breadcrumb', [
        'page_title' => $pageTitle ?? 'Course Details',
        'base_url' => route('home'),
    ])
@endsection
@section('content')

    <!-- Courses Details Section Start -->
    <section class="mhq-courses-details-section section-padding">
        <div class="container">
            <div class="mhq-courses-details-area">
                <div class="mhq-image wow fadeInUp" data-wow-delay=".3s" style="position: relative; overflow: hidden;">
                    @if ($course->featured_image)
                        <img src="{{ asset('storage/' . $course->featured_image) }}" alt="{{ $course->title }}"
                            style="width: 100%; height: auto; display: block;">
                    @else
                        <img src="{{ asset('assets/front/img/inner/courses-details/courses-details.jpg') }}"
                            alt="{{ $course->title }}" style="width: 100%; height: auto; display: block;">
                    @endif
                    @if ($course->intro_video)
                        <div class="intro-video-overlay"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.3s ease; cursor: pointer; z-index: 10;">
                            <a href="javascript:void(0)" class="intro-video-play-btn"
                                data-intro-video="{{ asset('storage/' . $course->intro_video) }}"
                                style="color: white; font-size: 60px; text-decoration: none; z-index: 11;">
                                <i class="fa-regular fa-circle-play breathing-animation"></i>
                            </a>
                        </div>
                    @endif
                </div>
                <div class="mhq-courses-details-wrapper">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="mhq-left-content">
                                <ul class="list-date wow fadeInUp" data-wow-delay=".2s">
                                    @if ($course->category)
                                        <li>
                                            <h6>
                                                {{ ucfirst($course->category) }}
                                            </h6>
                                        </li>
                                    @endif
                                    @if ($totalReviews > 0)
                                        <li>
                                            <i class="fa-solid fa-star-sharp"></i>
                                            ({{ $averageRating }} - {{ $totalReviews }} Reviews)
                                        </li>
                                    @endif
                                    @if ($courseStartDate)
                                        <li>
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Starts in: </span>
                                            <span class="countdown-timer"
                                                data-date="{{ $courseStartDate->format('Y-m-d H:i:s') }}">
                                                <span class="countdown-days">0</span>d
                                                <span class="countdown-hours">0</span>h
                                                <span class="countdown-minutes">0</span>m
                                                <span class="countdown-seconds">0</span>s
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                                <h3 class="wow fadeInUp" data-wow-delay=".3s">{{ $course->title }}</h3>
                                <div class="courses-details-content">
                                    <ul class="nav">
                                        <li class="nav-item wow fadeInUp" data-wow-delay=".3s">
                                            <a href="#Course" data-bs-toggle="tab" class="nav-link active">
                                                Course Info
                                            </a>
                                        </li>
                                        <li class="nav-item wow fadeInUp" data-wow-delay=".5s">
                                            <a href="#Curriculum" data-bs-toggle="tab" class="nav-link">
                                                Curriculum
                                            </a>
                                        </li>
                                        <li class="nav-item wow fadeInUp" data-wow-delay=".5s">
                                            <a href="#Instructors" data-bs-toggle="tab" class="nav-link">
                                                Instructors
                                            </a>
                                        </li>
                                        @if ($totalReviews > 0 || $canReview)
                                            <li class="nav-item wow fadeInUp" data-wow-delay=".5s">
                                                <a href="#Reviews" data-bs-toggle="tab" class="nav-link bb-none">
                                                    Reviews
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <div class="tab-content">
                                        <div id="Course" class="tab-pane fade show active">
                                            <div class="description-content">
                                                <h3>Description</h3>
                                                <div class="mb-3">
                                                    {!! $course->description ?? '<p>No description available.</p>' !!}
                                                </div>

                                                @if ($course->objectives)
                                                    <h3 class="mt-5">What you'll learn in this course?</h3>
                                                    <div class="row g-4 mb-5">
                                                        @php
                                                            $objectives = array_filter(
                                                                array_map('trim', explode("\n", $course->objectives)),
                                                            );
                                                            $midPoint = ceil(count($objectives) / 2);
                                                            $firstColumn = array_slice($objectives, 0, $midPoint);
                                                            $secondColumn = array_slice($objectives, $midPoint);
                                                        @endphp
                                                        <div class="col-lg-6">
                                                            <ul class="list-item">
                                                                @foreach ($firstColumn as $objective)
                                                                    <li>
                                                                        <i class="fas fa-check-circle"></i>
                                                                        {{ $objective }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        @if (count($secondColumn) > 0)
                                                            <div class="col-lg-6">
                                                                <ul class="list-item">
                                                                    @foreach ($secondColumn as $objective)
                                                                        <li>
                                                                            <i class="fas fa-check-circle"></i>
                                                                            {{ $objective }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if ($course->requirements)
                                                    <h3>Requirements</h3>
                                                    <p>
                                                        {!! nl2br(e($course->requirements)) !!}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div id="Curriculum" class="tab-pane fade">
                                            <div class="course-curriculum-items">
                                                <h3>Course Curriculum</h3>
                                                <div class="courses-faq-items">
                                                    <div class="accordion" id="accordionExample">
                                                        @foreach ($course->topics as $topicIndex => $topic)
                                                            <div
                                                                class="accordion-item {{ $topicIndex === $course->topics->count() - 1 ? 'mb-0' : '' }}">
                                                                <h2 class="accordion-header"
                                                                    id="heading{{ $topic->id }}">
                                                                    <button
                                                                        class="accordion-button {{ $topicIndex === 0 ? '' : 'collapsed' }}"
                                                                        type="button" data-bs-toggle="collapse"
                                                                        data-bs-target="#collapse{{ $topic->id }}"
                                                                        aria-expanded="{{ $topicIndex === 0 ? 'true' : 'false' }}"
                                                                        aria-controls="collapse{{ $topic->id }}">
                                                                        {{ $topic->title }}
                                                                    </button>
                                                                </h2>
                                                                <div id="collapse{{ $topic->id }}"
                                                                    class="accordion-collapse collapse {{ $topicIndex === 0 ? 'show' : '' }}"
                                                                    aria-labelledby="heading{{ $topic->id }}"
                                                                    data-bs-parent="#accordionExample">
                                                                    <div class="accordion-body">
                                                                        <ul>
                                                                            @foreach ($topic->lessons as $lesson)
                                                                                <li>
                                                                                    <span>
                                                                                        <i class="fas fa-file-alt"></i>
                                                                                        {{ $lesson->title }}
                                                                                        @if ($lesson->is_preview)
                                                                                            <span
                                                                                                class="badge bg-info ms-2">Preview</span>
                                                                                        @endif
                                                                                    </span>
                                                                                    <span>
                                                                                        @if (
                                                                                            $lesson->is_preview &&
                                                                                                (($lesson->video_type === 'url' && $lesson->video_url) ||
                                                                                                    ($lesson->video_type === 'upload' && $lesson->video_path)))
                                                                                            <a href="javascript:void(0)"
                                                                                                class="lesson-preview-play"
                                                                                                data-video-type="{{ $lesson->video_type }}"
                                                                                                data-video-url="{{ $lesson->video_type === 'url' ? $lesson->video_url : asset('storage/' . $lesson->video_path) }}"
                                                                                                data-lesson-title="{{ $lesson->title }}"
                                                                                                style="cursor: pointer; text-decoration: none;">
                                                                                                <i
                                                                                                    class="fa-regular fa-circle-play text-primary me-2"></i>
                                                                                                @if ($lesson->duration)
                                                                                                    {{ gmdate('H:i', $lesson->duration * 60) }}
                                                                                                @endif
                                                                                            </a>
                                                                                        @else
                                                                                            <i class="far fa-lock me-2"></i>
                                                                                            @if ($lesson->duration)
                                                                                                {{ gmdate('H:i', $lesson->duration * 60) }}
                                                                                            @endif
                                                                                        @endif
                                                                                    </span>
                                                                                </li>
                                                                            @endforeach

                                                                            @foreach ($topic->quizzes as $quiz)
                                                                                <li>
                                                                                    <span>
                                                                                        <i
                                                                                            class="fas fa-question-circle"></i>
                                                                                        Quiz: {{ $quiz->title }}

                                                                                    </span>
                                                                                    <span>
                                                                                        <i class="far fa-lock"></i>
                                                                                        @if ($quiz->time_limit)
                                                                                            {{ $quiz->time_limit }} min
                                                                                        @endif
                                                                                    </span>
                                                                                </li>
                                                                            @endforeach

                                                                            @foreach ($topic->assignments as $assignment)
                                                                                <li>
                                                                                    <span>
                                                                                        <i class="fas fa-tasks"></i>
                                                                                        Assignment:
                                                                                        {{ $assignment->title }}
                                                                                        @if ($assignment->files->count() > 0)
                                                                                            <span
                                                                                                class="badge bg-secondary ms-2">{{ $assignment->files->count() }}
                                                                                                file(s)</span>
                                                                                        @endif
                                                                                    </span>
                                                                                    <span>
                                                                                        <i class="far fa-lock"></i>
                                                                                    </span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                        @if ($course->topics->count() === 0)
                                                            <div class="alert alert-info">
                                                                <p>No curriculum available for this course yet.</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="Instructors" class="tab-pane fade">
                                            <div class="instructors-items">
                                                <h3>Instructors</h3>
                                                @if ($course->instructor)
                                                    <div class="instructors-box-items">
                                                        <div class="thumb">
                                                            @if ($course->instructor->profile_photo)
                                                                <img src="{{ asset('storage/' . $course->instructor->profile_photo) }}"
                                                                    alt="{{ $course->instructor->name }}">
                                                            @else
                                                                <img src="{{ asset('assets/front/img/inner/courses-details/courses-01.png') }}"
                                                                    alt="{{ $course->instructor->name }}">
                                                            @endif
                                                        </div>
                                                        <div class="content">
                                                            <h4>{{ $course->instructor->name }}</h4>
                                                            @if ($course->instructor->specialization)
                                                                <span>{{ $course->instructor->specialization }}</span>
                                                            @endif
                                                            @if ($course->instructor->bio)
                                                                <p>{{ $course->instructor->bio }}</p>
                                                            @else
                                                                <p>Experienced instructor passionate about teaching and
                                                                    sharing knowledge.</p>
                                                            @endif
                                                            <div class="social-icon">
                                                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                                                <a href="#"><i class="fab fa-instagram"></i></a>
                                                                <a href="#"><i class="fab fa-dribbble"></i></a>
                                                                <a href="#"><i class="fab fa-behance"></i></a>
                                                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-info">
                                                        <p>Instructor information not available.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($totalReviews > 0 || $canReview)
                                            <div id="Reviews" class="tab-pane fade">
                                                <div class="courses-reviews-items">
                                                    <h3>Course Reviews</h3>

                                                    @if ($totalReviews > 0)
                                                        <div class="courses-reviews-box-items">
                                                            <div class="courses-reviews-box">
                                                                <div class="reviews-box">
                                                                    <h2><span class="count">{{ $averageRating }}</span>
                                                                    </h2>
                                                                    <div class="star">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            <i
                                                                                class="fas fa-star{{ $i <= round($averageRating) ? '' : ' color-2' }}"></i>
                                                                        @endfor
                                                                    </div>
                                                                    <p>{{ $totalReviews }}+ Reviews</p>
                                                                </div>
                                                                <div class="reviews-ratting-right">
                                                                    @for ($rating = 5; $rating >= 1; $rating--)
                                                                        @php
                                                                            $count = $ratingDistribution[$rating] ?? 0;
                                                                            $percentage =
                                                                                $totalReviews > 0
                                                                                    ? ($count / $totalReviews) * 100
                                                                                    : 0;
                                                                        @endphp
                                                                        <div class="reviews-ratting-item">
                                                                            <div class="star">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    <i
                                                                                        class="fas fa-star{{ $i <= $rating ? '' : ' color-2' }}"></i>
                                                                                @endfor
                                                                            </div>
                                                                            <div class="progress">
                                                                                <div class="progress-value"
                                                                                    style="width: {{ $percentage }}%">
                                                                                </div>
                                                                            </div>
                                                                            <span>({{ $count }})</span>
                                                                        </div>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-info">
                                                            <p>No reviews yet. Be the first to review this course!</p>
                                                        </div>
                                                    @endif

                                                    <!-- Review Form for Enrolled Students -->
                                                    @if ($canReview && !$userReview)
                                                        <div class="review-form-section"
                                                            style="margin-top: 30px; padding: 30px; background: #f8f9fa; border-radius: 10px;">
                                                            <h4 style="margin-bottom: 20px;">Write a Review</h4>
                                                            <form
                                                                action="{{ route('courses.reviews.store', $course->id) }}"
                                                                method="POST" id="reviewForm">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label class="form-label"
                                                                        style="font-weight: 500;">Rating
                                                                        <span class="text-danger">*</span></label>
                                                                    <div class="rating-input"
                                                                        style="display: flex; gap: 5px; font-size: 30px; cursor: pointer;">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            <i class="fas fa-star rating-star"
                                                                                data-rating="{{ $i }}"
                                                                                style="color: #ddd; transition: color 0.2s;"></i>
                                                                        @endfor
                                                                    </div>
                                                                    <input type="hidden" name="rating" id="ratingInput"
                                                                        value="" required>
                                                                    <small class="text-danger d-none"
                                                                        id="ratingError">Please
                                                                        select a rating before submitting.</small>
                                                                    @error('rating')
                                                                        <div class="text-danger mt-2">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="comment" class="form-label"
                                                                        style="font-weight: 500;">Your Review</label>
                                                                    <textarea name="comment" id="comment" class="form-control" rows="5"
                                                                        placeholder="Share your experience with this course..."></textarea>
                                                                    @error('comment')
                                                                        <div class="text-danger mt-2">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <button type="submit" class="theme-btn py-2 px-4"
                                                                    id="submitReviewBtn">
                                                                    <i class="fa-solid fa-paper-plane me-2"></i>
                                                                    Submit Review
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @elseif($canReview && $userReview)
                                                        <div class="review-form-section"
                                                            style="margin-top: 30px; padding: 30px; background: #f8f9fa; border-radius: 10px;">
                                                            <h4 style="margin-bottom: 20px;">Your Review</h4>
                                                            <form
                                                                action="{{ route('courses.reviews.update', [$course->id, $userReview->id]) }}"
                                                                method="POST" id="reviewForm">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="mb-3">
                                                                    <label class="form-label"
                                                                        style="font-weight: 500;">Rating
                                                                        <span class="text-danger">*</span></label>
                                                                    <div class="rating-input"
                                                                        style="display: flex; gap: 5px; font-size: 30px; cursor: pointer;">
                                                                        @for ($i = 1; $i <= 5; $i++)
                                                                            <i class="fas fa-star rating-star"
                                                                                data-rating="{{ $i }}"
                                                                                style="color: {{ $i <= $userReview->rating ? '#FFAE5D' : '#ddd' }}; transition: color 0.2s;"></i>
                                                                        @endfor
                                                                    </div>
                                                                    <input type="hidden" name="rating" id="ratingInput"
                                                                        value="{{ $userReview->rating }}" required>
                                                                    @error('rating')
                                                                        <div class="text-danger mt-2">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="comment" class="form-label"
                                                                        style="font-weight: 500;">Your Review</label>
                                                                    <textarea name="comment" id="comment" class="form-control" rows="5"
                                                                        placeholder="Share your experience with this course...">{{ $userReview->comment }}</textarea>
                                                                    @error('comment')
                                                                        <div class="text-danger mt-2">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <button type="submit" class="theme-btn">
                                                                    <i class="fa-solid fa-save me-2"></i>
                                                                    Update Review
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @elseif(!auth()->check())
                                                        <div class="alert alert-info" style="margin-top: 30px;">
                                                            <p><a href="{{ route('login') }}">Login</a> and enroll in this
                                                                course to write a review.</p>
                                                        </div>
                                                    @endif

                                                    <!-- Display Reviews -->
                                                    @if ($totalReviews > 0)
                                                        <div class="reviews-list" style="margin-top: 40px;">
                                                            <h4 style="margin-bottom: 20px;">All Reviews
                                                                ({{ $totalReviews }})
                                                            </h4>
                                                            @foreach ($reviews as $review)
                                                                <div class="instructors-box-items"
                                                                    style="margin-bottom: 30px; padding: 20px; border: 1px solid #eee; border-radius: 10px;">
                                                                    <div class="thumb">
                                                                        @if ($review->user->profile_photo)
                                                                            <img src="{{ asset('storage/' . $review->user->profile_photo) }}"
                                                                                alt="{{ $review->user->name }}">
                                                                        @else
                                                                            <img src="{{ asset('assets/front/img/home-1/courses/client-01.png') }}"
                                                                                alt="{{ $review->user->name }}">
                                                                        @endif
                                                                    </div>
                                                                    <div class="content">
                                                                        <h4>{{ $review->user->name }}</h4>
                                                                        <span>{{ $review->created_at->format('M d, Y') }}</span>
                                                                        <div class="star" style="margin-top: 10px;">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                <i class="fas fa-star"
                                                                                    style="color: {{ $i <= $review->rating ? '#FFAE5D' : '#ddd' }};"></i>
                                                                            @endfor
                                                                        </div>
                                                                        @if ($review->comment)
                                                                            <p style="margin-top: 15px;">
                                                                                {{ $review->comment }}</p>
                                                                        @endif

                                                                        <!-- Instructor Replies -->
                                                                        @if ($review->replies->where('is_instructor', true)->count() > 0)
                                                                            <div
                                                                                style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                                                                                @foreach ($review->replies->where('is_instructor', true) as $reply)
                                                                                    <div
                                                                                        style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 10px;">
                                                                                        <div
                                                                                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                                                                            <i class="fa-solid fa-chalkboard-user"
                                                                                                style="color: #28a745;"></i>
                                                                                            <strong
                                                                                                style="color: #28a745;">Instructor
                                                                                                Response</strong>
                                                                                            <span
                                                                                                style="color: #666; font-size: 0.9em;">{{ $reply->created_at->format('M d, Y') }}</span>
                                                                                        </div>
                                                                                        <p style="margin: 0; color: #333;">
                                                                                            {{ $reply->reply }}</p>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="details-list-area">
                                <ul class="details-list">
                                    @if ($totalDuration > 0)
                                        <li>
                                            <span>
                                                <i class="far fa-clock me-2"></i>
                                                Total Duration
                                            </span>
                                            @php
                                                $hours = floor($totalDuration / 60);
                                                $minutes = $totalDuration % 60;
                                            @endphp
                                            @if ($hours > 0)
                                                {{ $hours }}h {{ $minutes }}m
                                            @else
                                                {{ $minutes }}m
                                            @endif
                                        </li>
                                    @endif
                                    <li>
                                        <span>
                                            <i class="fas fa-users-class me-2"></i>
                                            Students
                                        </span>
                                        @if ($course->max_students)
                                            Max {{ $course->max_students }}
                                        @else
                                            Unlimited
                                        @endif
                                    </li>
                                    <li>
                                        <span>
                                            <i class="fal fa-book-spells me-2"></i>
                                            Lessons
                                        </span>
                                        {{ $lessonCount }}
                                    </li>
                                    @if ($course->difficulty)
                                        <li>
                                            <span>
                                                <i class="far fa-layer-group me-2"></i>
                                                Skill level
                                            </span>
                                            {{ ucfirst($course->difficulty) }}
                                        </li>
                                    @endif
                                    @if ($course->language)
                                        <li>
                                            <span>
                                                <i class="fas fa-globe me-2"></i>
                                                Language
                                            </span>
                                            {{ ucfirst($course->language) }}
                                        </li>
                                    @endif
                                    @if ($course->certifications || $course->certifications == '')
                                        <li>
                                            <span>
                                                <i class="fas fa-certificate me-2"></i>
                                                Certifications
                                            </span>
                                            Yes
                                        </li>
                                    @endif
                                    @if ($course->schedule_date)
                                        <li>
                                            <span>
                                                <i class="fas fa-chart-line-down me-2"></i>
                                                Start Date
                                            </span>
                                            {{ $course->schedule_date->format('d M, Y') }}
                                        </li>
                                    @endif
                                    @if ($course->instructor)
                                        <li>
                                            <span>
                                                <i class="far fa-user me-2"></i>
                                                Instructor
                                            </span>
                                            {{ $course->instructor->name }}
                                        </li>
                                    @endif
                                </ul>
                                @php
                                    $rawPrice = $course->sale_price ?? $course->regular_price;
                                    $price =
                                        $course->pricing_model === 'free' ? 'Free' : currency_format($rawPrice ?? 0);
                                @endphp
                                <div class="theme-btn w-100 bg-color-4 mb-3 text-center" style="padding: 15px;">
                                    Price: {{ $price }}
                                </div>
                                @if ($isEnrolled)
                                    <a href={{ route('courses.show', $course->id) }}}}" class="theme-btn w-100 style-2"
                                        style="background: #28a745;">
                                        <i class="fas fa-check-circle me-2"></i> Already Enrolled
                                    </a>
                                @elseif(auth()->check())
                                    @if ($course->pricing_model === 'free')
                                        <a href="{{ route('courses.checkout', $course->id) }}"
                                            class="theme-btn w-100 style-2">
                                            Enroll Now
                                        </a>
                                    @else
                                        <a href="{{ route('courses.checkout', $course->id) }}"
                                            class="theme-btn w-100 style-2">
                                            Buy Now
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="theme-btn w-100 style-2">
                                        Login to Enroll
                                    </a>
                                @endif
                                <div class="social-icon d-flex align-items-center">
                                    <span>Share: </span>
                                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#"><i class="fab fa-twitter"></i></a>
                                    <a href="#"><i class="fab fa-vimeo-v"></i></a>
                                    <a href="#"><i class="fab fa-pinterest-p"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Video Modal -->
    <div id="lessonVideoModal" class="modal fade" tabindex="-1" aria-labelledby="lessonVideoModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lessonVideoModalTitle">Lesson Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="lessonVideoContainer">
                        <video id="lessonVideoPlayer" controls style="width: 100%; max-height: 500px;" class="d-none">
                            Your browser does not support the video tag.
                        </video>
                        <div id="lessonVideoIframe" class="d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Intro Video Modal -->
    <div id="introVideoModal" class="modal fade" tabindex="-1" aria-labelledby="introVideoModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="introVideoModalTitle">Course Introduction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="introVideoContainer">
                        <video id="introVideoPlayer" controls style="width: 100%; max-height: 500px;">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')
        <script>
            $(document).ready(function() {
                const videoModal = new bootstrap.Modal(document.getElementById('lessonVideoModal'));
                const videoPlayer = document.getElementById('lessonVideoPlayer');
                const videoIframe = document.getElementById('lessonVideoIframe');
                const modalTitle = document.getElementById('lessonVideoModalTitle');

                $('.lesson-preview-play').on('click', function(e) {
                    e.preventDefault();
                    const videoType = $(this).data('video-type');
                    const videoUrl = $(this).data('video-url');
                    const lessonTitle = $(this).data('lesson-title');

                    modalTitle.textContent = lessonTitle || 'Lesson Preview';

                    if (videoType === 'url') {
                        let embedUrl = videoUrl;
                        if (videoUrl.includes('youtube.com/watch')) {
                            embedUrl = videoUrl.replace('watch?v=', 'embed/').split('&')[0];
                        } else if (videoUrl.includes('youtu.be/')) {
                            embedUrl = videoUrl.replace('youtu.be/', 'youtube.com/embed/');
                        } else if (videoUrl.includes('vimeo.com/')) {
                            const vimeoId = videoUrl.split('vimeo.com/')[1].split('?')[0];
                            embedUrl = 'https://player.vimeo.com/video/' + vimeoId;
                        }

                        videoPlayer.classList.add('d-none');
                        videoIframe.classList.remove('d-none');
                        videoIframe.innerHTML = '<iframe src="' + embedUrl +
                            '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="width: 100%; height: 500px;"></iframe>';
                    } else {
                        videoIframe.classList.add('d-none');
                        videoPlayer.classList.remove('d-none');
                        videoPlayer.src = videoUrl;
                    }

                    videoModal.show();
                });

                $('#lessonVideoModal').on('hidden.bs.modal', function() {
                    videoPlayer.pause();
                    videoPlayer.src = '';
                    videoIframe.innerHTML = '';
                });

                const introVideoModal = new bootstrap.Modal(document.getElementById('introVideoModal'));
                const introVideoPlayer = document.getElementById('introVideoPlayer');

                $('.intro-video-play-btn').on('click', function(e) {
                    e.preventDefault();
                    const introVideoUrl = $(this).data('intro-video');
                    introVideoPlayer.src = introVideoUrl;
                    introVideoModal.show();
                });

                $('#introVideoModal').on('hidden.bs.modal', function() {
                    introVideoPlayer.pause();
                    introVideoPlayer.src = '';
                });

                // Star rating functionality
                $('.rating-star').on('click', function() {
                    const rating = $(this).data('rating');
                    const container = $(this).closest('.rating-input');
                    const input = container.siblings('#ratingInput');

                    input.val(rating);

                    container.find('.rating-star').each(function() {
                        const starRating = $(this).data('rating');
                        if (starRating <= rating) {
                            $(this).css('color', '#FFAE5D');
                        } else {
                            $(this).css('color', '#ddd');
                        }
                    });
                });

                $('.rating-star').on('mouseenter', function() {
                    const rating = $(this).data('rating');
                    const container = $(this).closest('.rating-input');

                    container.find('.rating-star').each(function() {
                        const starRating = $(this).data('rating');
                        if (starRating <= rating) {
                            $(this).css('color', '#FFAE5D');
                        } else {
                            $(this).css('color', '#ddd');
                        }
                    });
                });

                $('.rating-input').on('mouseleave', function() {
                    const input = $(this).siblings('#ratingInput');
                    const currentRating = parseInt(input.val()) || 0;

                    $(this).find('.rating-star').each(function() {
                        const starRating = $(this).data('rating');
                        if (starRating <= currentRating) {
                            $(this).css('color', '#FFAE5D');
                        } else {
                            $(this).css('color', '#ddd');
                        }
                    });
                });

                // Validate rating before form submission
                $('#reviewForm').on('submit', function(e) {
                    const rating = $('#ratingInput').val();
                    if (!rating || rating === '' || rating === '0') {
                        e.preventDefault();
                        $('#ratingError').removeClass('d-none');
                        ToastMagic?.error('Please select a rating before submitting.');
                        return false;
                    }
                    $('#ratingError').addClass('d-none');
                });

                // Countdown timer
                const countdownTimer = $('.countdown-timer');
                if (countdownTimer.length > 0) {
                    const targetDate = new Date(countdownTimer.data('date')).getTime();

                    const updateCountdown = function() {
                        const now = new Date().getTime();
                        const distance = targetDate - now;

                        if (distance < 0) {
                            countdownTimer.closest('li').html(
                                '<i class="fas fa-calendar-alt"></i> Course has started');
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        countdownTimer.find('.countdown-days').text(days);
                        countdownTimer.find('.countdown-hours').text(hours);
                        countdownTimer.find('.countdown-minutes').text(minutes);
                        countdownTimer.find('.countdown-seconds').text(seconds);
                    };

                    updateCountdown();
                    setInterval(updateCountdown, 1000);
                }
            });
        </script>
    @endpush

@endsection
