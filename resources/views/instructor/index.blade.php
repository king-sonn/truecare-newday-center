@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('breadcrumb')
    @include('partials.breadcrumb', [
        'page_title' => $pageTitle ?? 'Instructors',
        'base_url' => route('home'),
    ])
@endsection

@section('content')
    <section class="mhq-team-section2 section-padding fix">
        <div class="container">
            <div class="section-title text-center">
                <h6 class="wow fadeInUp">Instructors</h6>
                <h2 class="wow fadeInUp" data-wow-delay=".2s">Meet Our Inspiring Educators</h2>
                <p class="wow fadeInUp" data-wow-delay=".35s">
                    Discover passionate mentors who bring real-world expertise to every session.
                </p>
            </div>
            <div class="row g-4">
                @forelse($instructors as $instructor)
                    @php
                        $delay = number_format(($loop->index % 4) * 0.1 + 0.1, 1);
                        $stats = $instructor->stats;
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ $delay }}s">
                        <div class="mhq-team-box-items mt-0 instructor-card">
                            <div class="mhq-image">
                                <img src="{{ $instructor->display_avatar }}" alt="{{ $instructor->name }}">
                                <div class="mhq-content">
                                    <h4><a href="{{ route('instructors.show', $instructor) }}">{{ $instructor->name }}</a>
                                    </h4>
                                    <p>{{ $instructor->headline }}</p>
                                </div>
                            </div>
                            <div
                                class="mhq-team-meta instructor-stats small text-muted mt-3 d-flex justify-content-between">
                                <span class="stat-chip"><i class="fa-solid fa-book-open mr-1"></i>{{ $stats['courses'] }}
                                    Courses</span>
                                <span class="stat-chip"><i
                                        class="fa-solid fa-user mr-1"></i>{{ number_format($stats['students']) }}</span>
                                <span class="stat-chip"><i
                                        class="fa-solid fa-star mr-1 text-warning"></i>{{ $stats['rating'] ? number_format($stats['rating'], 1) : '—' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info mb-0 text-center">No instructors available yet. Please check back soon.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $instructors->links('vendor.pagination.stisla-default') }}
            </div>
        </div>
    </section>
@endsection
