@extends('layouts.instructor')

@section('content')
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('instructor.partials.sidebar')

                <div class="dashboard-main">
                    <!-- Page Header -->
                    <div class="dashboard-header wow fadeInUp">
                        <div class="section-title-area">
                            <div class="section-title">
                                <h6>Withdrawals</h6>
                                <h2>Manage Your Withdrawals</h2>
                            </div>
                            <div class="dashboard-header-actions">
                                <a href="{{ route('instructor.earnings') }}" class="back-btn">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    Back to Earnings
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Cards -->
                    <div class="dashboard-stats-minimal wow fadeInUp" data-wow-delay="0.1s">
                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon--theme">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ currency_format($availableBalance) }}</div>
                                <div class="stat-label-minimal">Available for Withdrawal</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon--warning">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ currency_format($pendingBalance) }}</div>
                                <div class="stat-label-minimal">Pending Withdrawals</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon--success">
                                <i class="fa-solid fa-check-circle"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $completedCount }}</div>
                                <div class="stat-label-minimal">Completed</div>
                            </div>
                        </div>

                        <div class="stat-card-minimal">
                            <div class="stat-icon-minimal stat-icon--violet">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <div class="stat-content-minimal">
                                <div class="stat-value-minimal">{{ $pendingCount }}</div>
                                <div class="stat-label-minimal">Pending Requests</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info wow fadeInUp" data-wow-delay="0.12s">
                        <i class="fa-solid fa-circle-info mr-2"></i>
                        Minimum withdrawal amount is <strong>{{ currency_format($minimumWithdrawal) }}</strong>.
                        @if ($availableBalance < $minimumWithdrawal)
                            You currently need
                            <strong>{{ currency_format($minimumWithdrawal - $availableBalance) }}</strong> more in your
                            balance to request a payout.
                        @else
                            You can request up to <strong>{{ currency_format($availableBalance) }}</strong> right now.
                        @endif
                    </div>

                    <!-- Withdrawal Request Form -->
                    @if ($availableBalance >= $minimumWithdrawal)
                        <div class="dashboard-courses-section wow fadeInUp dashboard-section-mt" data-wow-delay="0.2s">
                            <div class="section-title">
                                <h3>Request Withdrawal</h3>
                            </div>
                            <form class="withdrawal-form" id="withdrawalForm"
                                action="{{ route('instructor.withdrawals.store') }}" method="POST">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Withdrawal Method</label>
                                        <select name="method" class="form-control @error('method') is-invalid @enderror"
                                            required>
                                            <option value="">Select Method</option>
                                            <option value="bank_transfer"
                                                {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer
                                            </option>
                                            <option value="paypal" {{ old('method') === 'paypal' ? 'selected' : '' }}>
                                                PayPal</option>
                                            <option value="stripe" {{ old('method') === 'stripe' ? 'selected' : '' }}>
                                                Stripe</option>
                                        </select>
                                        @error('method')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <div class="amount-input-wrapper">
                                            <span class="currency-symbol">{{ currency_code() }}</span>
                                            <input type="number" name="amount"
                                                class="form-control @error('amount') is-invalid @enderror" step="0.01"
                                                min="{{ $minimumWithdrawal }}" max="{{ $availableBalance }}"
                                                placeholder="0.00" value="{{ old('amount') }}" required>
                                            <button type="button" class="btn-max"
                                                onclick="document.querySelector('input[name=amount]').value = '{{ $availableBalance }}'">
                                                Max
                                            </button>
                                        </div>
                                        <small class="form-hint">Minimum withdrawal:
                                            {{ currency_format($minimumWithdrawal) }} | Available:
                                            {{ currency_format($availableBalance) }}</small>
                                        @error('amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Payout Details</label>
                                    <input type="text" name="method_details"
                                        class="form-control @error('method_details') is-invalid @enderror"
                                        placeholder="Add account or payment details (e.g., bank account, PayPal email)"
                                        value="{{ old('method_details') }}">
                                    @error('method_details')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Notes (Optional)</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                        placeholder="Add any additional notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="cancel-btn"
                                        onclick="document.getElementById('withdrawalForm').reset()">
                                        Cancel
                                    </button>
                                    <button type="submit" class="theme-btn style-2 px-5">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        Submit Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Withdrawals History -->
                    <div class="dashboard-courses-section wow fadeInUp dashboard-section-mt" data-wow-delay="0.3s">
                        <div class="section-title-area section-title-area-compact">
                            <div class="section-title">
                                <h3>Withdrawal History</h3>
                            </div>
                            <div class="reviews-filters">
                                <button class="filter-btn active" data-filter="all">All</button>
                                <button class="filter-btn" data-filter="completed">Completed</button>
                                <button class="filter-btn" data-filter="pending">Pending</button>
                            </div>
                        </div>

                        <div class="transactions-table-wrapper">
                            <table class="students-table">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Completed Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($withdrawals as $withdrawal)
                                        <tr class="withdrawal-row" data-status="{{ $withdrawal->status }}">
                                            <td>
                                                <span class="reference-code">{{ $withdrawal->reference }}</span>
                                            </td>
                                            <td>{{ optional($withdrawal->requested_at)->format('M d, Y') ?? $withdrawal->created_at->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <div class="withdrawal-method">
                                                    @if ($withdrawal->method === 'bank_transfer')
                                                        <i class="fa-solid fa-building-columns"></i>
                                                    @elseif($withdrawal->method === 'paypal')
                                                        <i class="fa-brands fa-paypal"></i>
                                                    @else
                                                        <i class="fa-solid fa-credit-card"></i>
                                                    @endif
                                                    <span>{{ ucfirst(str_replace('_', ' ', $withdrawal->method)) }}</span>
                                                </div>
                                                <small
                                                    class="method-details">{{ $withdrawal->method_details ?? '—' }}</small>
                                            </td>
                                            <td>
                                                <span class="transaction-amount negative">
                                                    -{{ currency_format($withdrawal->amount) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge-table {{ $withdrawal->status }}">
                                                    {{ ucfirst($withdrawal->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ optional($withdrawal->processed_at)->format('M d, Y') ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center dashboard-empty-padding">
                                                <div class="dashboard-empty-state">
                                                    <i class="fa-solid fa-money-bill-transfer"></i>
                                                    <h3>No Withdrawals Yet</h3>
                                                    <p>Your withdrawal history will appear here once you make a withdrawal
                                                        request.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($pagination['lastPage'] > 1)
                            <div class="table-pagination">
                                <div class="pagination-info">
                                    Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of
                                    {{ $pagination['total'] }} withdrawals
                                </div>
                                <div class="pagination-links">
                                    @if ($pagination['currentPage'] > 1)
                                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['currentPage'] - 1]) }}"
                                            class="pagination-btn">
                                            <i class="fa-solid fa-chevron-left"></i> Previous
                                        </a>
                                    @else
                                        <span class="pagination-btn disabled">
                                            <i class="fa-solid fa-chevron-left"></i> Previous
                                        </span>
                                    @endif

                                    <div class="pagination-numbers">
                                        @for ($i = max(1, $pagination['currentPage'] - 2); $i <= min($pagination['lastPage'], $pagination['currentPage'] + 2); $i++)
                                            @if ($i === $pagination['currentPage'])
                                                <span class="pagination-number active">{{ $i }}</span>
                                            @else
                                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
                                                    class="pagination-number">{{ $i }}</a>
                                            @endif
                                        @endfor
                                    </div>

                                    @if ($pagination['currentPage'] < $pagination['lastPage'])
                                        <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['currentPage'] + 1]) }}"
                                            class="pagination-btn">
                                            Next <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span class="pagination-btn disabled">
                                            Next <i class="fa-solid fa-chevron-right"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Withdrawal Filter
                $('.reviews-filters .filter-btn').on('click', function() {
                    const filter = $(this).data('filter');

                    $('.reviews-filters .filter-btn').removeClass('active');
                    $(this).addClass('active');

                    if (filter === 'all') {
                        $('.withdrawal-row').fadeIn(300);
                    } else {
                        $('.withdrawal-row').each(function() {
                            if ($(this).data('status') === filter) {
                                $(this).fadeIn(300);
                            } else {
                                $(this).fadeOut(300);
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
