@extends('layouts.instructor')

@section('content')
    <!-- Instructor Students Section Start -->
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('instructor.partials.sidebar')

                <!-- Main Content Start -->
                <div class="dashboard-main">
                    <!-- Page Header -->
                    <div class="section-title-area wow fadeInUp section-title-area-spacing">
                        <div class="section-title">
                            <h6>Students</h6>
                            <h2>My Students</h2>
                            <p>Manage and view details of all students enrolled in your courses</p>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="dashboard-stats-minimal dashboard-gap-lg">
                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-1">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $stats['total_students'] }}</div>
                                <div class="stat-label-minimal">Total Students</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-2">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $stats['active_students'] }}</div>
                                <div class="stat-label-minimal">Active Students</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-3">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $stats['total_enrollments'] }}</div>
                                <div class="stat-label-minimal">Total Enrollments</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-4">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ currency_format($stats['total_revenue']) }}</div>
                                <div class="stat-label-minimal">Total Revenue</div>
                            </div>
                        </div>
                    </div>

                    <!-- Students Section with Gradient Background -->
                    <div class="dashboard-courses-section">
                        <div class="students-header">
                            <h3 class="wow fadeInUp">All Students ({{ $pagination->total }})</h3>
                            <div class="students-header-actions wow fadeInUp" data-wow-delay=".2s">
                                <div class="students-filter">
                                    <button class="filter-btn active" data-filter="all">All</button>
                                    <button class="filter-btn" data-filter="active">Active</button>
                                    <button class="filter-btn" data-filter="inactive">Inactive</button>
                                </div>
                                <div class="table-search">
                                    <input type="text" id="studentSearch" placeholder="Search students..."
                                        class="search-input">
                                    <i class="fa-solid fa-search"></i>
                                </div>
                            </div>
                        </div>

                        <div class="students-table-wrapper">
                            <table class="students-table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Status</th>
                                        <th>Enrolled Courses</th>
                                        <th>Completed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                        <tr data-status="{{ $student['status'] }}">
                                            <td>
                                                <div class="student-table-info">
                                                    <div class="student-avatar-small">
                                                        <img src="{{ $student['avatar'] }}" alt="{{ $student['name'] }}">
                                                    </div>
                                                    <div class="student-details">
                                                        <div class="student-name">{{ $student['name'] }}</div>
                                                        <div class="student-email-small">{{ $student['email'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge-table {{ $student['status'] }}">
                                                    {{ ucfirst($student['status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="table-value">{{ $student['enrolled_courses'] }}</span>
                                            </td>
                                            <td>
                                                <span class="table-value">{{ $student['completed_courses'] }}</span>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="{{ route('instructor.students.show', $student['id']) }}"
                                                        class="table-action-btn" title="View Profile">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">
                                                <div class="dashboard-empty-state">
                                                    <i class="fa-solid fa-users"></i>
                                                    <h3>No Students Yet</h3>
                                                    <p>You don't have any students enrolled in your courses yet. Start
                                                        promoting your courses to attract students.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($pagination->lastPage > 1)
                            <div class="table-pagination">
                                <div class="pagination-info">
                                    Showing {{ $pagination->from }} to {{ $pagination->to }} of {{ $pagination->total }}
                                    students
                                </div>
                                <div class="pagination-links">
                                    @if ($pagination->currentPage > 1)
                                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination->currentPage - 1]) }}"
                                            class="pagination-btn">
                                            <i class="fa-solid fa-chevron-left"></i>
                                            Previous
                                        </a>
                                    @else
                                        <span class="pagination-btn disabled">
                                            <i class="fa-solid fa-chevron-left"></i>
                                            Previous
                                        </span>
                                    @endif

                                    <div class="pagination-numbers">
                                        @for ($i = max(1, $pagination->currentPage - 2); $i <= min($pagination->lastPage, $pagination->currentPage + 2); $i++)
                                            @if ($i == $pagination->currentPage)
                                                <span class="pagination-number active">{{ $i }}</span>
                                            @else
                                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                                    class="pagination-number">{{ $i }}</a>
                                            @endif
                                        @endfor
                                    </div>

                                    @if ($pagination->currentPage < $pagination->lastPage)
                                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination->currentPage + 1]) }}"
                                            class="pagination-btn">
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
                    <!-- Students Section End -->
                </div>
                <!-- Main Content End -->
            </div>
        </div>
    </section>
@endsection
