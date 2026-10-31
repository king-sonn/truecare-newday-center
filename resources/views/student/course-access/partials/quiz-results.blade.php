<div class="quiz-results">
    <div class="mhq-courses-details-area">
        <div class="mhq-courses-details-wrapper">
            <div class="mhq-left-content">
                <h3 class="mb-4">Quiz Results: {{ $quiz->title }}</h3>

                <div class="results-summary mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="results-summary-card {{ $attempt->passed ? 'passed' : 'failed' }}">
                                <h3 class="results-score-value {{ $attempt->passed ? 'passed' : 'failed' }}">
                                    {{ $attempt->score }}%
                                </h3>
                                <p style="margin-bottom: 5px;">
                                    @if ($attempt->passed)
                                        <span class="badge badge-passed">Passed</span>
                                    @else
                                        <span class="badge badge-failed">Failed</span>
                                    @endif
                                </p>
                                <small class="results-passing-score">Passing Score: {{ $quiz->passing_score }}%</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="results-summary-card theme">
                                <h3 class="results-score-value theme">
                                    {{ $correctAnswersCount ?? $attempt->answers->where('is_correct', true)->count() }}/{{ $quiz->questions->count() }}
                                </h3>
                                <p class="results-label">Correct Answers</p>
                            </div>
                        </div>
                    </div>
                    <div class="results-actions mt-4">
                        <button class="theme-btn retake-quiz-btn" data-quiz-id="{{ $quiz->id }}"
                            data-course-id="{{ $quiz->topic->course_id }}">
                            <i class="fas fa-redo"></i> Retake Quiz
                        </button>
                    </div>
                </div>

                <div class="questions-review">
                    <h3 class="mb-4 question-review-title">Question Review</h3>
                    @foreach ($questionsData ?? [] as $questionData)
                        <div
                            class="question-review-item mb-4 {{ $questionData['is_correct'] ? 'correct' : 'incorrect' }}">
                            <div class="question-review-header">
                                <h5 class="question-review-number">Question {{ $questionData['index'] + 1 }}</h5>
                                @if ($questionData['is_correct'])
                                    <span class="badge question-review-badge correct">
                                        <i class="fas fa-check"></i> Correct
                                    </span>
                                @else
                                    <span class="badge question-review-badge incorrect">
                                        <i class="fas fa-times"></i> Incorrect
                                    </span>
                                @endif
                            </div>
                            <p class="mb-3 question-review-text">{{ $questionData['question']->question }}</p>

                            <div class="options">
                                @foreach ($questionData['options'] as $option)
                                    <div
                                        class="review-option-item {{ $option['is_correct'] ? 'correct-answer' : ($option['is_wrong'] ? 'wrong-answer' : 'default') }}">
                                        <div class="review-option-content">
                                            @if ($option['is_selected'])
                                                <i class="fas fa-check-circle"
                                                    style="color: {{ $questionData['is_correct'] ? '#28a745' : '#dc3545' }};"></i>
                                            @endif
                                            @if ($option['is_correct'])
                                                <i class="fas fa-star" style="color: #28a745;"></i>
                                            @endif
                                            <span
                                                class="review-option-text {{ $option['is_correct'] ? 'correct' : ($option['is_wrong'] ? 'wrong' : 'default') }}">
                                                {{ $option['text'] }}
                                            </span>
                                            @if ($option['is_correct'])
                                                <small class="review-option-label correct">(Correct Answer)</small>
                                            @endif
                                            @if ($option['is_wrong'])
                                                <small class="review-option-label wrong">(Your Answer)</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
