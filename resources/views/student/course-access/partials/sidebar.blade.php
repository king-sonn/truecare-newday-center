<!-- Search Bar -->
<div class="sidebar-search">
    <div class="search-input-wrapper">
        <input type="text" id="search-input" class="search-input" placeholder="Search Course Content">
        <button type="button" class="search-icon-btn">
            <i class="fas fa-search"></i>
        </button>
    </div>
    <div id="search-results" class="search-results" style="display: none;"></div>
</div>

<!-- Course Curriculum -->
<div class="course-curriculum">
    @foreach ($topicsData as $topicData)
        <div class="chapter-section">
            <div class="chapter-header" data-chapter="{{ $topicData['index'] + 1 }}">
                <div class="chapter-header-left">
                    <i class="fas fa-chevron-down chapter-toggle-icon"></i>
                    <span class="chapter-number">Topic {{ $topicData['index'] + 1 }}</span>
                    <span class="chapter-title">{{ $topicData['topic']->title }}</span>
                </div>
                <span
                    class="chapter-progress">{{ $topicData['progress']['completed_items'] }}/{{ $topicData['progress']['total_items'] }}</span>
            </div>
            <div class="chapter-content">
                @foreach ($topicData['items'] as $item)
                    <div class="curriculum-item {{ $item['type'] }}-item {{ $item['is_active'] ? 'active' : '' }} {{ !$item['is_accessible'] ? 'locked' : '' }}"
                        data-item-id="{{ $item['id'] }}" data-item-type="{{ $item['type'] }}"
                        data-course-id="{{ $course->id }}"
                        data-accessible="{{ $item['is_accessible'] ? '1' : '0' }}">
                        <div class="curriculum-item-icon">
                            @if (!$item['is_accessible'])
                                <i class="fas fa-lock"></i>
                            @elseif($item['type'] === 'lesson')
                                <i class="fas fa-play"></i>
                            @elseif($item['type'] === 'quiz')
                                <i class="fas fa-comments"></i>
                            @else
                                <i class="fas fa-file-alt"></i>
                            @endif
                        </div>
                        <div class="curriculum-item-content">
                            <span class="curriculum-item-title">{{ $item['title'] }}</span>
                            @if ($item['duration_formatted'])
                                <span class="curriculum-item-duration">{{ $item['duration_formatted'] }}</span>
                            @endif
                        </div>
                        <div class="curriculum-item-status">
                            @if ($item['is_completed'])
                                <i class="fas fa-check-circle status-completed"></i>
                            @elseif(!$item['is_accessible'])
                                <i class="fas fa-lock status-locked"></i>
                            @else
                                <div class="status-circle"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
