@extends('layouts.admin')

@section('title', 'Revenue Distribution')

@section('breadcrumb')
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></div>
        <div class="breadcrumb-item">Revenue Distribution</div>
    </div>
@endsection

@section('main-content')
    <div class="section-body">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="section-title mb-1">Revenue Distribution</h2>
                    <p class="section-lead mb-0 mt-2">
                        Configure how course revenue is split between the platform and instructors.
                    </p>
                </div>
                <div class="mb-3 mb-md-0">
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Settings
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Commission Structure</h4>
                    </div>
                    <form action="{{ route('admin.settings.revenue.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="mode">Commission Type</label>
                                <select name="mode" id="mode"
                                    class="form-control @error('mode') is-invalid @enderror">
                                    <option value="percentage"
                                        {{ old('mode', $settings['mode'] ?? 'percentage') === 'percentage' ? 'selected' : '' }}>
                                        Percentage of sale amount
                                    </option>
                                    <option value="fixed"
                                        {{ old('mode', $settings['mode'] ?? 'percentage') === 'fixed' ? 'selected' : '' }}>
                                        Fixed amount per enrollment
                                    </option>
                                </select>
                                @error('mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="value">
                                    Commission Value
                                    <span class="text-muted small d-block mt-1" id="value-help">
                                        {{ old('mode', $settings['mode'] ?? 'percentage') === 'fixed' ? 'Amount taken by platform for each paid enrollment.' : 'Percentage retained by the platform from each paid enrollment.' }}
                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="value" id="value" min="0" step="0.01"
                                        value="{{ old('value', $settings['value'] ?? 0) }}"
                                        class="form-control @error('value') is-invalid @enderror">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="value-suffix">
                                            {{ old('mode', $settings['mode'] ?? 'percentage') === 'fixed' ? currency_code() : '%' }}
                                        </span>
                                    </div>
                                    @error('value')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <ul class="mb-0 pl-3">
                                    <li>Commission applies to all paid course enrollments.</li>
                                    <li>For offline payments, commission is applied once the payment is approved.</li>
                                    <li>Instructor earnings are credited to their wallet balance automatically.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modeInput = document.getElementById('mode');
            const valueSuffix = document.getElementById('value-suffix');
            const valueHelp = document.getElementById('value-help');

            if (modeInput) {
                modeInput.addEventListener('change', function() {
                    if (modeInput.value === 'fixed') {
                        valueSuffix.textContent = '{{ currency_code() }}';
                        valueHelp.textContent = 'Amount taken by platform for each paid enrollment.';
                    } else {
                        valueSuffix.textContent = '%';
                        valueHelp.textContent =
                            'Percentage retained by the platform from each paid enrollment.';
                    }
                });
            }
        });
    </script>
@endpush
