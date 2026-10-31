<div class="assignment-content">
    <div class="mhq-courses-details-area">
        <div class="mhq-courses-details-wrapper">
            <div class="mhq-left-content">
                <h3 class="mb-4">{{ $assignment->title }}</h3>

                @if ($assignment->description)
                    <div class="assignment-description mb-4">
                        {!! $assignment->description !!}
                    </div>
                @endif

                @if ($assignment->instructions)
                    <div class="assignment-instructions mb-4">
                        <h5 class="assignment-instructions-title">
                            <i class="fas fa-list-check assignment-instructions-icon"></i> Instructions
                        </h5>
                        <div class="assignment-instructions-text">
                            {!! $assignment->instructions !!}
                        </div>
                    </div>
                @endif

                @if ($assignment->files->count() > 0)
                    <div class="assignment-files mb-4">
                        <h5 class="assignment-files-title">
                            <i class="fas fa-paperclip assignment-files-icon"></i> Attachments
                        </h5>
                        <ul class="list-group" style="border: none;">
                            @foreach ($assignment->files as $file)
                                <li class="assignment-file-item">
                                    <a href="{{ asset('storage/' . $file->file_path) }}"
                                        download="{{ $file->original_filename }}"
                                        class="assignment-file-link text-decoration-none">
                                        <i class="fas fa-file-download"></i> {{ $file->original_filename }}
                                    </a>
                                    <span class="assignment-file-size">({{ $file->file_size ?? 'Unknown size' }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($submission)
                    <div class="submission-status mb-4">
                        <div
                            class="submission-status-box submission-status-{{ $submissionData['status_class'] ?? 'pending' }}">
                            <h5 class="submission-status-title">
                                Submission Status: <span
                                    class="submission-status-text">{{ $submissionData['status_label'] ?? 'Pending' }}</span>
                            </h5>
                            @if (!empty($submissionData['is_graded']))
                                <p style="margin-bottom: 10px;">
                                    <strong>Grade:</strong>
                                    <span class="submission-grade-value">{{ $submission->grade ?? 'N/A' }}%</span>
                                </p>
                            @endif
                            @if ($submission->instructor_notes)
                                <p style="margin-bottom: 10px;">
                                    <strong>Instructor Notes:</strong>
                                    <span style="color: var(--text);">{{ $submission->instructor_notes }}</span>
                                </p>
                            @endif
                            <p style="margin-bottom: 0;">
                                <strong>Submitted:</strong>
                                <span style="color: var(--text);">{{ $submissionData['submitted_at'] ?? '—' }}</span>
                            </p>
                        </div>

                        @if ($submission->submission_text)
                            <div class="submission-text-box mb-4">
                                <h5 style="color: var(--header); margin-bottom: 15px;">Your Submission</h5>
                                <div class="submission-text-content">
                                    {!! $submission->submission_text !!}
                                </div>
                            </div>
                        @endif

                        @if ($submission->files->count() > 0)
                            <div class="submission-files-box">
                                <h5 style="color: var(--header); margin-bottom: 20px;">Submitted Files</h5>
                                <ul class="list-group" style="border: none;">
                                    @foreach ($submission->files as $file)
                                        <li class="submission-file-item">
                                            <a href="{{ $file->file_url }}" download="{{ $file->original_filename }}"
                                                class="submission-file-link text-decoration-none">
                                                <i class="fas fa-file-download"></i> {{ $file->original_filename }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    @if (!empty($submissionData['can_resubmit']))
                        <div class="assignment-form-resubmit mb-4">
                            <div class="assignment-form mb-3"
                                style="background: rgba(0, 79, 68, 0.05); padding: 20px; border-radius: 12px;">
                                <h5 style="margin-bottom: 10px; color: var(--header);">Ready to try again?</h5>
                                <p style="margin: 0; color: var(--text);">Your instructor has returned this submission.
                                    Update your work below and resubmit when you're ready.</p>
                            </div>

                            <form id="assignment-form" enctype="multipart/form-data"
                                data-assignment-id="{{ $assignment->id }}"
                                data-course-id="{{ $assignment->topic->course_id }}" class="assignment-form">
                                <div class="mb-4">
                                    <label for="submission_text" class="form-label assignment-form-label">Submission
                                        Text (Optional)</label>
                                    <textarea class="form-control assignment-form-textarea" id="submission_text" name="submission_text" rows="10"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="files" class="form-label assignment-form-label">Upload Files
                                        (Optional)</label>
                                    <input type="file" class="form-control assignment-form-file-input" id="files"
                                        name="files[]" multiple
                                        accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.txt,.jpg,.jpeg,.png,.gif">
                                    <small class="assignment-form-help">You can upload up to 10 files. Maximum file
                                        size: 10MB each.</small>
                                    <div id="file-preview" class="mt-3"></div>
                                </div>

                                <button type="submit" class="theme-btn py-2 px-4">
                                    <i class="fas fa-sync"></i> Resubmit Assignment
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <form id="assignment-form" enctype="multipart/form-data" data-assignment-id="{{ $assignment->id }}"
                        data-course-id="{{ $assignment->topic->course_id }}" class="assignment-form">
                        <div class="mb-4">
                            <label for="submission_text" class="form-label assignment-form-label">Submission Text
                                (Optional)</label>
                            <textarea class="form-control assignment-form-textarea" id="submission_text" name="submission_text" rows="10"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="files" class="form-label assignment-form-label">Upload Files
                                (Optional)</label>
                            <input type="file" class="form-control assignment-form-file-input" id="files"
                                name="files[]" multiple
                                accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.txt,.jpg,.jpeg,.png,.gif">
                            <small class="assignment-form-help">You can upload up to 10 files. Maximum file size: 10MB
                                each.</small>
                            <div id="file-preview" class="mt-3"></div>
                        </div>

                        <button type="submit" class="theme-btn py-2 px-4">
                            <i class="fas fa-paper-plane"></i> Submit Assignment
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
