@forelse($courses as $index => $course)
    @php
        $lessonCount = $course->topics->sum(function ($topic) {
            return $topic->lessons->count();
        });
        $price =
            $course->pricing_model === 'free'
                ? 'Free'
                : currency_format($course->sale_price ?? $course->regular_price ?? 0);
        $delay = ($index % 6) * 0.2;
    @endphp
    <div class="col-xl-6 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="{{ $delay }}s">
        <div class="mhq-courses-box-items mt-0">
            <div class="mhq-courses-image">
                @if ($course->featured_image)
                    <img src="{{ asset('storage/' . $course->featured_image) }}" alt="{{ $course->title }}">
                @else
                    <img src="{{ asset('assets/front/img/home-1/courses/courses-01.jpg') }}" alt="{{ $course->title }}">
                @endif
                @if ($course->category)
                    <span class="post-box">
                        {{ ucfirst($course->category) }}
                    </span>
                @endif
            </div>
            <div class="mhq-courses-content">
                <div class="star">
                    @php $averageRating = round($course->average_rating ?? 0); @endphp
                    @for ($i = 1; $i <= 5; $i++)
                        <i
                            class="fa-solid fa-star-sharp {{ $i <= $averageRating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                </div>
                <h3><a href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a></h3>
                <ul class="post-date">
                    <li>
                        @if ($course->intro_video)
                            <a href="{{ asset('storage/' . $course->intro_video) }}" class="icon video-popup">
                                <i class="fa-regular fa-circle-play"></i>
                            </a>
                        @endif
                        {{ $lessonCount }}+ Lessons
                    </li>
                    <li>
                        <div class="icon">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        @if ($course->max_students)
                            Max {{ $course->max_students }} Students
                        @else
                            Unlimited
                        @endif
                    </li>
                </ul>
                <div class="client-info-area">
                    <div class="client-info">
                        @if ($course->instructor && $course->instructor->profile_photo)
                            <div class="img">
                                <img src="{{ asset('storage/' . $course->instructor->profile_photo) }}"
                                    alt="{{ $course->instructor->name }}">
                            </div>
                        @else
                            <div class="img">
                                <img src="{{ asset('assets/front/img/home-1/courses/client-01.png') }}"
                                    alt="{{ $course->instructor->name ?? 'Instructor' }}">
                            </div>
                        @endif
                        {{ $course->instructor->name ?? 'Instructor' }}
                    </div>
                    <h2>{{ $price }}</h2>
                </div>
                <a href="{{ route('courses.show', $course->id) }}" class="theme-btn">
                    Enroll Now
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            <p>No courses found. Please try different search criteria.</p>
        </div>
    </div>
@endforelse
