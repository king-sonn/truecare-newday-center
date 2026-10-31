@extends('layouts.student')

@section('content')
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('student.partials.sidebar')

                <div class="dashboard-main">
                    <div class="section-title-area wow fadeInUp section-title-area-spacing">
                        <div class="section-title">
                            <h6>Payments</h6>
                            <h2>Payment History</h2>
                            <p>Review every payment for your courses, including offline receipts and pending verifications.
                            </p>
                        </div>
                    </div>

                    <div class="dashboard-stats-minimal dashboard-gap-md">
                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-1">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $summary['total_payments'] }}</div>
                                <div class="stat-label-minimal">Total Payments</div>
                            </div>
                        </div>
                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-2">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $summary['completed_payments'] }}</div>
                                <div class="stat-label-minimal">Completed</div>
                            </div>
                        </div>
                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-3">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $summary['pending_payments'] }}</div>
                                <div class="stat-label-minimal">Pending</div>
                            </div>
                        </div>
                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon-4">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ currency_format($summary['total_amount']) }}</div>
                                <div class="stat-label-minimal">Total Paid</div>
                            </div>
                        </div>
                    </div>

                    <div class="submissions-filter-card wow fadeInUp" data-wow-delay=".1s">
                        <form method="GET" action="{{ route('student.payments') }}" class="submissions-filter-form">

                            <div class="filter-group">
                                <label for="student-payment-method">Method</label>
                                <div class="filter-select">
                                    <select id="student-payment-method" name="method" class="nice-select">
                                        @foreach ($methodOptions as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ ($methodFilter ?? '') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="filter-group">
                                <label for="student-payment-status">Status</label>
                                <div class="filter-select">
                                    <select id="student-payment-status" name="status" class="nice-select">
                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ ($statusFilter ?? '') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="filter-group">
                                <label for="student-payment-search">Search</label>
                                <div class="filter-search">
                                    <input type="search" id="student-payment-search" name="search" class="search-input"
                                        value="{{ $searchTerm }}" placeholder="Course or transaction ID">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>
                            </div>

                            <div class="filter-actions">
                                <button type="submit" class="filter-submit-btn">
                                    <i class="fa-solid fa-search"></i>
                                    Apply Filters
                                </button>
                                @if ($methodFilter || $statusFilter || $searchTerm)
                                    <a href="{{ route('student.payments') }}" class="filter-reset-btn">
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
                                    <th>Course</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Transaction</th>
                                    <th>Created</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>
                                            <span class="table-value">{{ $payment->course_title }}</span>
                                        </td>
                                        <td>
                                            <span class="table-value">{{ $payment->method_label }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="status-badge-table {{ $payment->status_class }}">{{ $payment->status_label }}</span>
                                        </td>
                                        <td>
                                            <span class="table-value">{{ currency_format($payment->amount) }}</span>
                                        </td>
                                        <td>
                                            <span class="table-value">{{ $payment->transaction_id ?? '—' }}</span>
                                        </td>
                                        <td>
                                            <span class="table-value">{{ $payment->created_at_formatted ?? '—' }}</span>
                                        </td>
                                        <td>
                                            @if ($payment->receipt_url)
                                                <a href="{{ $payment->receipt_url }}" target="_blank" rel="noopener"
                                                    class="assignment-action-btn action-btn-compact">
                                                    <i class="fa-solid fa-download"></i>
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="dashboard-empty-state">
                                                <i class="fa-solid fa-receipt"></i>
                                                <h3>No Payments Yet</h3>
                                                <p>Once you enroll in a course, your payments will appear here.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($payments->hasPages())
                        <div class="table-pagination">
                            <div class="pagination-info">
                                Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of
                                {{ $payments->total() }} payments
                            </div>
                            <div class="pagination-links">
                                @if ($payments->onFirstPage())
                                    <span class="pagination-btn disabled">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Previous
                                    </span>
                                @else
                                    <a href="{{ $payments->previousPageUrl() }}" class="pagination-btn">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Previous
                                    </a>
                                @endif

                                <div class="pagination-numbers">
                                    @foreach ($payments->getUrlRange(max(1, $payments->currentPage() - 2), min($payments->lastPage(), $payments->currentPage() + 2)) as $page => $url)
                                        @if ($page === $payments->currentPage())
                                            <span class="pagination-number active">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}"
                                                class="pagination-number">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                </div>

                                @if ($payments->hasMorePages())
                                    <a href="{{ $payments->nextPageUrl() }}" class="pagination-btn">
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
