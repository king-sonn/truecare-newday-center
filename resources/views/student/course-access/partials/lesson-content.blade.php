<div class="lesson-content">
    <div class="mhq-courses-details-area">
        <div class="mhq-courses-details-wrapper">
            <div class="mhq-left-content">
                <h3 class="mb-4">{{ $lesson->title }}</h3>

                @if ($videoData && $videoData['type'] === 'url' && $videoData['url'])
                    @if ($videoData['is_youtube'])
                        <div id="video-player-container">
                            <iframe width="100%" height="500" src="{{ $videoData['embed_url'] }}" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen class="video-iframe"></iframe>
                        </div>
                    @elseif($videoData['is_vimeo'])
                        <div id="video-player-container">
                            <iframe src="{{ $videoData['embed_url'] }}" width="100%" height="500" frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture" allowfullscreen
                                class="video-iframe"></iframe>
                        </div>
                    @else
                        <div id="video-player-container">
                            <video id="lesson-video-player" class="video-js vjs-default-skin" controls preload="auto"
                                data-setup='{}'>
                                <source src="{{ $videoData['url'] }}" type="video/mp4">
                            </video>
                        </div>
                    @endif
                @elseif($videoData && $videoData['type'] === 'upload' && $videoData['url'])
                    <div id="video-player-container">
                        <video id="lesson-video-player" class="video-js vjs-default-skin" controls preload="auto"
                            data-setup='{}'>
                            <source src="{{ $videoData['url'] }}" type="video/mp4">
                        </video>
                    </div>
                @endif

                @if ($lesson->description)
                    <div class="description-content mt-4">
                        <h3 class="mb-3">Description</h3>
                        <div class="lesson-description-content">{!! $lesson->description !!}</div>
                    </div>
                @endif

                <div class="lesson-actions mt-4">
                    <button id="mark-complete-btn" class="theme-btn style-2" data-lesson-id="{{ $lesson->id }}"
                        data-course-id="{{ $lesson->topic->course_id }}"
                        {{ $progress && $progress->is_completed ? 'disabled style="opacity: 0.6; cursor: not-allowed;"' : '' }}>
                        @if ($progress && $progress->is_completed)
                            Completed <i class="fas fa-check-circle"></i>
                        @else
                            Mark as Complete <i class="fas fa-check"></i>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
