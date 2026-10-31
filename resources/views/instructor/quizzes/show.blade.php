@extends('layouts.instructor')

@section('content')
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('instructor.partials.sidebar')

                <div class="dashboard-main">
                    <div class="section-title-area wow fadeInUp" style="margin-bottom: 25px;">
                        <div class="section-title">
                            <h6>Quizzes</h6>
                            <h2>{{ $quiz->title }}</h2>
                            <p>Monitor attempts, identify knowledge gaps, and follow learner progress.</p>
                        </div>
                        <div class="section-actions">
                            <a href="{{ route('instructor.quizzes.index') }}" class="breadcrumb-back">
                                <i class="fa-solid fa-arrow-left-long"></i>
                                Back to quizzes
                            </a>
                        </div>
                    </div>

                    <div class="assignment-overview-card wow fadeInUp" data-wow-delay=".05s">
                        <div class="overview-header">
                            <div class="overview-row">
                                <div>
                                    <span class="overview-label">Course</span>
                                    <h4>{{ optional($quiz->topic->course)->title ?? 'Unknown Course' }}</h4>
                                </div>
                                <div>
                                    <span class="overview-label">Passing Score</span>
                                    <h4>{{ $quiz->passing_score }}%</h4>
                                </div>
                                <div>
                                    <span class="overview-label">Attempts</span>
                                    <h4>{{ $stats['total_attempts'] }}</h4>
                                </div>
                            </div>
                            <div class="overview-row">
                                <div class="overview-stat graded">
                                    <span class="stat-label">Passed</span>
                                    <span class="stat-value">{{ $stats['passed_attempts'] }}</span>
                                </div>
                                <div class="overview-stat returned">
                                    <span class="stat-label">Failed</span>
                                    <span class="stat-value">{{ $stats['failed_attempts'] }}</span>
                                </div>
                                <div class="overview-stat pending">
                                    <span class="stat-label">Avg Score</span>
                                    <span
                                        class="stat-value">{{ $stats['average_score'] !== null ? $stats['average_score'] . '%' : '—' }}</span>
                                </div>
                                <div class="overview-stat">
                                    <span class="stat-label">Best Score</span>
                                    <span
                                        class="stat-value">{{ $stats['best_score'] !== null ? $stats['best_score'] . '%' : '—' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="overview-body">
                            <div class="overview-section">
                                <h5>Average Completion Time</h5>
                                <p class="assignment-description" style="margin: 0;">
                                    {{ $stats['average_time'] ?? 'Not enough data yet.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="submissions-filter-card wow fadeInUp" data-wow-delay=".1s">
                        <form method="GET" action="{{ route('instructor.quizzes.show', $quiz) }}"
                            class="submissions-filter-form">
                            <div class="filter-row d-flex justify-content-between gap-3">
                                <div class="filter-group">
                                    <label for="attempt-status-filter">Result</label>
                                    <div class="filter-select">
                                        <select id="attempt-status-filter" name="status" class="nice-select">
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
                                    <label for="attempt-search">Search</label>
                                    <div class="filter-search">
                                        <input type="search" id="attempt-search" name="search" class="search-input"
                                            value="{{ $searchTerm }}" placeholder="Search by student name or email">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button type="submit" class="filter-submit-btn">
                                    <i class="fa-solid fa-search"></i>
                                    Apply Filters
                                </button>
                                @if ($statusFilter || $searchTerm)
                                    <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="filter-reset-btn">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="submissions-table-wrapper wow fadeInUp" data-wow-delay=".15s">
                        <table class="students-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Score</th>
                                    <th>Result</th>
                                    <th>Time Taken</th>
                                    <th>Started</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attempts as $attempt)
                                    <tr>
                                        <td>
                                            <div class="student-table-info">
                                                <div class="student-details">
                                                    <div class="student-name">
                                                        {{ $attempt->user?->name ?? 'Unknown Student' }}</div>
                                                    <div class="student-email-small">{{ $attempt->user?->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="table-value">{{ $attempt->score }}%</span>
                                        </td>
                                        <td>
                                            <span
                                                class="status-badge-table {{ $attempt->status_class }}">{{ $attempt->status_label }}</span>
                                        </td>
                                        <td>
                                            <span class="table-value">{{ $attempt->time_taken_formatted ?? '—' }}</span>
                                        </td>
                                        <td>
                                            <span class="table-value">{{ $attempt->started_at_formatted ?? '—' }}</span>
                                        </td>
                                        <td>
                                            <span class="table-value">{{ $attempt->completed_at_formatted ?? '—' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="dashboard-empty-state">
                                                <i class="fa-solid fa-chart-line"></i>
                                                <h3>No Attempts Yet</h3>
                                                <p>Once learners take this quiz, their attempts will appear here.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($attempts->hasPages())
                        <div class="table-pagination">
                            <div class="pagination-info">
                                Showing {{ $attempts->firstItem() }} to {{ $attempts->lastItem() }} of
                                {{ $attempts->total() }} attempts
                            </div>
                            <div class="pagination-links">
                                @if ($attempts->onFirstPage())
                                    <span class="pagination-btn disabled">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Previous
                                    </span>
                                @else
                                    <a href="{{ $attempts->previousPageUrl() }}" class="pagination-btn">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Previous
                                    </a>
                                @endif

                                <div class="pagination-numbers">
                                    @foreach ($attempts->getUrlRange(max(1, $attempts->currentPage() - 2), min($attempts->lastPage(), $attempts->currentPage() + 2)) as $page => $url)
                                        @if ($page === $attempts->currentPage())
                                            <span class="pagination-number active">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}"
                                                class="pagination-number">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                </div>

                                @if ($attempts->hasMorePages())
                                    <a href="{{ $attempts->nextPageUrl() }}" class="pagination-btn">
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
@endsection
