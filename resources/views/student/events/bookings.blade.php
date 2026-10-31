@extends('layouts.student')

@section('content')
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('student.partials.sidebar')

                <div class="dashboard-main">
                    <div class="section-title-area wow fadeInUp" style="margin-bottom: 30px;">
                        <div class="section-title">
                            <h6>Events</h6>
                            <h2>My Event Bookings</h2>
                            <p>Track the events you've signed up for and stay prepared.</p>
                        </div>
                    </div>

                    <div class="dashboard-content-section wow fadeInUp">
                        @if ($bookings->isEmpty())
                            <div
                                class="dashboard-empty-state text-center d-flex flex-column align-items-center justify-content-center">
                                <i class="fa-solid fa-calendar-xmark"></i>
                                <h3 class="text-center">No Event Bookings Yet</h3>
                                <p>Browse available events and reserve your seat to see them listed here.</p>
                                <a href="{{ route('events.index') }}" class="theme-btn style-2 py-2 px-4">
                                    Explore Events
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>When</th>
                                            <th>Seats</th>
                                            <th>Status</th>
                                            <th>Reference</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bookings as $booking)
                                            <tr>
                                                <td>
                                                    <span class="mb-1">{{ $booking['event_title'] }}</span>
                                                    <div class="text-muted small">Booked
                                                        {{ $booking['booked_at_formatted'] }}</div>
                                                </td>
                                                <td class="text-muted small">
                                                    @if ($booking['has_event'])
                                                        <div>{{ $booking['start_date_formatted'] ?? 'TBD' }}</div>
                                                        <div>{{ $booking['start_time_formatted'] ?? 'TBD' }}</div>
                                                    @else
                                                        <div>—</div>
                                                    @endif
                                                </td>
                                                <td>{{ $booking['seats'] }}</td>
                                                <td>
                                                    <span class="{{ $booking['status_badge_class'] }}">
                                                        {{ $booking['status_label'] }}
                                                    </span>
                                                </td>
                                                <td><code>{{ $booking['reference'] }}</code></td>
                                                <td class="text-end">
                                                    <a href="{{ $booking['ticket_url'] }}" class="back-btn" target="_blank">
                                                        View Ticket
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $bookings->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
