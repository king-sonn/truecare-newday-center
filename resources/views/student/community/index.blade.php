@extends('layouts.student')

@section('content')
    <!-- Student Community Section Start -->
    <section class="section-padding section-bg fix">
        <div class="container">
            <div class="dashboard-layout">
                @include('student.partials.sidebar')

                <!-- Main Content Start -->
                <div class="dashboard-main">
                    <div class="d-flex justify-content-between align-items-center mb-5 wow fadeInUp">
                        <div class="section-title mb-0">
                            <h6>Student Community</h6>
                            <h2>Discussion Q&A</h2>
                            <p>Ask questions, share knowledge, and grow with other students.</p>
                        </div>
                        <button class="theme-btn" data-bs-toggle="modal" data-bs-target="#askQuestionModal">
                            <i class="fa-solid fa-plus me-2"></i>
                            Ask Question
                        </button>
                    </div>

                    <!-- Search & Filter Bar -->
                    <div class="dashboard-content-section mb-5 wow fadeInUp"
                        style="border-radius: 20px; padding: 20px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                        <div class="row align-items-center">
                            <div class="col-lg-7 mb-3 mb-lg-0">
                                <ul class="nav nav-pills custom-pills">
                                    <li class="nav-item">
                                        <a class="nav-link {{ $filter == 'all' ? 'active' : '' }}"
                                            href="{{ route('student.community.index', ['filter' => 'all']) }}">All
                                            Questions</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $filter == 'unanswered' ? 'active' : '' }}"
                                            href="{{ route('student.community.index', ['filter' => 'unanswered']) }}">Unanswered</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $filter == 'my_questions' ? 'active' : '' }}"
                                            href="{{ route('student.community.index', ['filter' => 'my_questions']) }}">My
                                            Questions</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-5">
                                <form action="{{ route('student.community.index') }}" method="GET" class="search-form">
                                    <input type="hidden" name="filter" value="{{ $filter }}">
                                    <div class="input-group"
                                        style="border-radius: 12px; overflow: hidden; border: 1px solid #eee;">
                                        <input type="text" name="search" class="form-control border-0"
                                            placeholder="Search discussions..." value="{{ request('search') }}"
                                            style="padding: 12px 20px;">
                                        <button class="btn btn-theme border-0" type="submit"
                                            style="background: var(--theme); color: white; padding: 0 20px;">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Questions List -->
                    <div class="questions-container">
                        @forelse($questions as $question)
                            @include('student.community.partials.question-card', ['question' => $question])
                        @empty
                            <div class="dashboard-content-section text-center py-5 wow fadeInUp"
                                style="border-radius: 20px; background: white;">
                                <div class="mb-4">
                                    <i class="fa-solid fa-comments-question" style="font-size: 60px; color: #ddd;"></i>
                                </div>
                                <h4>No questions found</h4>
                                <p class="text-muted">Be the first one to start a discussion in this category!</p>
                                <button class="theme-btn mt-3" data-bs-toggle="modal" data-bs-target="#askQuestionModal">
                                    <i class="fa-solid fa-plus me-2"></i>
                                    Ask Question
                                </button>
                            </div>
                        @endforelse

                        <div class="mt-5 d-flex justify-content-center">
                            {{ $questions->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                <!-- Main Content End -->
            </div>
        </div>
    </section>

    @include('student.community.partials.ask-question-modal')

    <style>
        .custom-pills .nav-link {
            color: #666;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 12px;
            transition: all 0.3s;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
        }

        .custom-pills .nav-link.active {
            background: #f1f7f6;
            color: var(--theme);
        }

        .custom-pills .nav-link:hover:not(.active) {
            background: #f9f9f9;
            color: #002b24;
        }

        .pagination {
            gap: 10px;
        }

        .page-item .page-link {
            border-radius: 10px !important;
            border: 1px solid #eee;
            color: #666;
            padding: 10px 18px;
            font-weight: 700;
        }

        .page-item.active .page-link {
            background: var(--theme);
            border-color: var(--theme);
            color: white;
        }
    </style>
@endsection