@extends('layouts.instructor')

@section('content')
    <!-- Instructor Assignment Detail Section Start -->
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('instructor.partials.sidebar')

                <!-- Main Content Start -->
                <div class="dashboard-main">
                    <div class="section-title-area wow fadeInUp" style="margin-bottom: 25px;">
                        <div class="section-title">
                            <h6>Assignments</h6>
                            <h2>{{ $assignment->title }}</h2>
                            <p>Review and grade submissions for this assignment.</p>
                        </div>
                        <div class="section-actions">
                            <a href="{{ route('instructor.assignments.index') }}" class="breadcrumb-back">
                                <i class="fa-solid fa-arrow-left-long"></i>
                                Back to assignments
                            </a>
                        </div>
                    </div>

                    <!-- Assignment Overview -->
                    <div class="assignment-overview-card wow fadeInUp" data-wow-delay=".05s">
                        <div class="overview-header">
                            <div class="overview-row">
                                <div>
                                    <span class="overview-label">Course</span>
                                    <h4>{{ optional($assignment->topic->course)->title ?? 'Unknown Course' }}</h4>
                                </div>
                                <div>
                                    <span class="overview-label">Topic</span>
                                    <h4>{{ optional($assignment->topic)->title ?? 'No topic' }}</h4>
                                </div>
                                <div>
                                    <span class="overview-label">Total Submissions</span>
                                    <h4>{{ $assignment->submissions_count }}</h4>
                                </div>
                            </div>
                            <div class="overview-row">
                                <div class="overview-stat pending">
                                    <span class="stat-label">Pending</span>
                                    <span class="stat-value">{{ $assignment->pending_submissions_count }}</span>
                                </div>
                                <div class="overview-stat graded">
                                    <span class="stat-label">Graded</span>
                                    <span class="stat-value">{{ $assignment->graded_submissions_count }}</span>
                                </div>
                                <div class="overview-stat returned">
                                    <span class="stat-label">Returned</span>
                                    <span class="stat-value">{{ $assignment->returned_submissions_count }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="overview-body">
                            @if ($assignment->instructions)
                                <div class="overview-section">
                                    <h5>Instructions</h5>
                                    <div class="assignment-content">
                                        {!! $assignment->instructions !!}
                                    </div>
                                </div>
                            @elseif($assignment->description)
                                <div class="overview-section">
                                    <h5>Description</h5>
                                    <div class="assignment-content">
                                        {!! $assignment->description !!}
                                    </div>
                                </div>
                            @else
                                <div class="overview-section">
                                    <p>No instructions or description have been added for this assignment yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="submissions-filter-card wow fadeInUp" data-wow-delay=".1s">
                        <form method="GET" action="{{ route('instructor.assignments.show', $assignment->id) }}"
                            class="submissions-filter-form">
                            <div class="filter-row d-flex flex-wrap gap-3">
                                <div class="filter-group">
                                    <label for="status-filter">Status</label>
                                    <div class="filter-select">
                                        <select id="status-filter" name="status" class="nice-select">
                                            <option value="">Select</option>
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ $statusFilter === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-group">
                                    <label for="submission-search">Search</label>
                                    <div class="filter-search">
                                        <input type="search" id="submission-search" name="search" class="search-input"
                                            value="{{ $searchTerm }}" placeholder="Search by student name or email">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button type="submit" class="filter-submit-btn">
                                    <i class="fa-solid fa-filter"></i>
                                    Apply Filters
                                </button>
                                @if ($statusFilter || $searchTerm)
                                    <a href="{{ route('instructor.assignments.show', $assignment->id) }}"
                                        class="filter-reset-btn">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Submissions Table -->
                    <div class="submissions-table-wrapper wow fadeInUp" data-wow-delay=".15s">
                        <table class="students-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submissions as $submission)
                                    <tr>
                                        <td>
                                            <div class="student-table-info">
                                                <div class="student-avatar-small">
                                                    <img src="{{ $submission->user->profile_photo ? asset('storage/' . $submission->user->profile_photo) : asset('assets/front/img/home-1/courses/client-01.png') }}"
                                                        alt="{{ $submission->user->name }}">
                                                </div>
                                                <div class="student-details">
                                                    <div class="student-name">{{ $submission->user->name }}</div>

                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="table-value">
                                                {{ optional($submission->submitted_at)->format('M d, Y h:i A') ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge-table {{ $submission->status_class }}">
                                                {{ $statusOptions[$submission->status] ?? ucfirst($submission->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="table-value">
                                                {{ $submission->grade !== null ? $submission->grade . '%' : 'Not graded' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('instructor.assignments.submissions.show', $submission->id) }}"
                                                    class="table-action-btn" title="Review submission">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="dashboard-empty-state">
                                                <i class="fa-solid fa-inbox"></i>
                                                <h3>No submissions yet</h3>
                                                <p>Students have not submitted this assignment yet. You'll see them here
                                                    once they do.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($submissions->hasPages())
                        <div class="table-pagination">
                            <div class="pagination-info">
                                Showing {{ $submissions->firstItem() }} to {{ $submissions->lastItem() }} of
                                {{ $submissions->total() }} submissions
                            </div>
                            <div class="pagination-links">
                                @if ($submissions->onFirstPage())
                                    <span class="pagination-btn disabled">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Previous
                                    </span>
                                @else
                                    <a href="{{ $submissions->previousPageUrl() }}" class="pagination-btn">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Previous
                                    </a>
                                @endif

                                <div class="pagination-numbers">
                                    @foreach ($submissions->getUrlRange(max(1, $submissions->currentPage() - 2), min($submissions->lastPage(), $submissions->currentPage() + 2)) as $page => $url)
                                        @if ($page == $submissions->currentPage())
                                            <span class="pagination-number active">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}"
                                                class="pagination-number">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                </div>

                                @if ($submissions->hasMorePages())
                                    <a href="{{ $submissions->nextPageUrl() }}" class="pagination-btn">
                                        Next
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="pagination-btn disabled">
                                        Next
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- Instructor Assignment Detail Section End -->
@endsection
