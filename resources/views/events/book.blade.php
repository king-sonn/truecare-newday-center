@extends('layouts.app')

@section('breadcrumb')
    @include('partials.breadcrumb', [
        'page_title' => $pageTitle ?? 'Book Your Seat',
        'base_url' => route('events.index'),
    ])
@endsection

@section('content')
    <section class="mhq-hero-section fix mhq-hero-1 bg-cover py-4 hero-bg-default">
        <div class="hero-shape-1 float-bob-x">
            <img src="{{ asset('assets/front/img/home-1/hero/shape-01.png') }}" alt="shape">
        </div>
        <div class="hero-shape-2 float-bob-y">
            <img src="{{ asset('assets/front/img/home-1/hero/shape-02.png') }}" alt="shape">
        </div>
        <div class="hero-shape-3 float-bob-y">
            <img src="{{ asset('assets/front/img/home-1/hero/shape-03.png') }}" alt="shape">
        </div>
        <div class="container">
            <div class="row justify-content-center align-items-center py-5">
                <div class="col-lg-10">
                    <div class="booking-wrapper bg-white wow fadeInUp" data-wow-delay=".3s">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold mb-2">Reserve Your Seat</h2>
                            <p class="text-muted mb-0">
                                You're booking a seat for <strong>{{ $event->title }}</strong> on
                                <strong>{{ optional($event->start_date)->format('M d, Y') ?? 'TBD' }}</strong> at
                                <strong>{{ $event->start_time ?? 'TBD' }}</strong>.
                            </p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('events.book.store', $event->slug) }}" method="POST" class="row g-4">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" name="first_name" class="form-control form-control-lg"
                                    value="{{ old('first_name', auth()->user()->name ? explode(' ', auth()->user()->name)[0] : null) }}"
                                    placeholder="John" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" name="last_name" class="form-control form-control-lg"
                                    value="{{ old('last_name') }}" placeholder="Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg"
                                    value="{{ old('email', auth()->user()->email) }}" placeholder="john@example.com"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control form-control-lg"
                                    value="{{ old('phone', auth()->user()->phone) }}" placeholder="+1234567890">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Number of Seats</label>
                                <input type="number" name="seats" min="1"
                                    max="{{ $event->remainingSeats() ?? 50 }}" value="{{ old('seats', 1) }}"
                                    class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Preferred Contact Method</label>
                                <select name="contact_method" class="form-select form-select-lg">
                                    <option value="email" @selected(old('contact_method') === 'email')>Email</option>
                                    <option value="phone" @selected(old('contact_method') === 'phone')>Phone</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Additional Notes (Optional)</label>
                                <textarea name="notes" rows="4" class="form-control" placeholder="Anything we should know?">{{ old('notes') }}</textarea>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center pt-3">
                                <a href="{{ route('events.show', $event->slug) }}" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Event
                                </a>
                                <button type="submit" class="theme-btn style-2 btn-lg">
                                    Confirm Booking <i class="fas fa-ticket-alt ms-2"></i>
                                </button>
                            </div>
                        </form>

                        <div class="row mt-5 g-4">
                            <div class="col-md-4">
                                <div class="p-4 rounded info-card-soft">
                                    <h5 class="fw-bold">Schedule</h5>
                                    <p class="mb-1"><i
                                            class="far fa-calendar-alt me-2 text-theme"></i>{{ optional($event->start_date)->format('M d, Y') ?? 'TBD' }}
                                        - {{ optional($event->end_date)->format('M d, Y') ?? 'Same Day' }}</p>
                                    <p class="mb-0"><i
                                            class="far fa-clock me-2 text-theme"></i>{{ $event->start_time ?? 'TBD' }}
                                        @if ($event->end_time)
                                            - {{ $event->end_time }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 rounded info-card-soft">
                                    <h5 class="fw-bold">Location</h5>
                                    <p class="mb-2"><i
                                            class="far fa-map-marker-alt me-2 text-theme"></i>{{ $event->location ?? 'To be announced' }}
                                    </p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 rounded info-card-soft">
                                    <h5 class="fw-bold">Ticket Price</h5>
                                    <p class="display-6 fw-bold text-theme">
                                        {{ ($event->price ?? 0) > 0 ? currency_format($event->price ?? 0) : 'Free' }}</p>
                                    <p class="mb-1 text-muted">
                                        {{ $event->total_seats ? ($event->remainingSeats() ?? 0) . ' seats remaining' : 'Unlimited seats' }}
                                    </p>
                                    <small class="text-muted">{{ $event->bookings_count ?? 0 }} bookings ·
                                        {{ $event->confirmed_bookings_count ?? 0 }} confirmed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
