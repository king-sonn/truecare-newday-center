<div class="assignment-submission">
    <div class="mhq-courses-details-area">
        <div class="mhq-courses-details-wrapper">
            <div class="mhq-left-content">
                <div class="assignment-submission-success">
                    <h4 class="assignment-submission-success-title">
                        <i class="fas fa-check-circle assignment-submission-success-icon"></i> Assignment Submitted
                        Successfully!
                    </h4>
                    <p class="assignment-submission-success-text">Your assignment has been submitted and is pending
                        instructor review.</p>
                </div>

                <div class="submission-details-box">
                    <h5 class="submission-details-title">Submission Details</h5>
                    <div class="submission-detail-item">
                        <strong class="submission-detail-label">Submitted:</strong>
                        <span
                            class="submission-detail-value">{{ $submission->submitted_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="submission-detail-item">
                        <strong class="submission-detail-label">Status:</strong>
                        <span class="badge"
                            style="background: #ffc107; color: #000; padding: 8px 16px; border-radius: 20px; text-transform: capitalize;">{{ $submission->status }}</span>
                    </div>

                    @if ($submission->submission_text)
                        <div class="submission-detail-divider">
                            <h5 style="color: var(--header); margin-bottom: 15px;">Your Submission</h5>
                            <div class="submission-text-box-content">
                                {!! $submission->submission_text !!}
                            </div>
                        </div>
                    @endif

                    @if ($submission->files->count() > 0)
                        <div class="submission-detail-divider">
                            <h5 style="color: var(--header); margin-bottom: 20px;">Submitted Files</h5>
                            <ul class="list-group" style="border: none;">
                                @foreach ($submission->files as $file)
                                    <li class="submission-file-item">
                                        <a href="{{ $file->file_url }}" download="{{ $file->original_filename }}"
                                            class="submission-file-link text-decoration-none">
                                            <i class="fas fa-file-download"></i> {{ $file->original_filename }}
                                        </a>
                                        <span
                                            class="submission-file-size">({{ $file->file_size ?? 'Unknown size' }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
