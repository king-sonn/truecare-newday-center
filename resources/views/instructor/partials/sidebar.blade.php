<!-- Sidebar Start -->
<aside class="dashboard-sidebar">
    <div class="dashboard-profile">
        <div class="dashboard-profile-image">
            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}">
        </div>
        <h4>{{ $user->name }}</h4>
        <p>Senior Instructor</p>
        <span class="status-badge">✓ Verified</span>
    </div>

    <button class="dashboard-menu-toggle" type="button" aria-label="Toggle menu">
        <i class="fa-solid fa-bars"></i>
        <span>Menu</span>
    </button>

    <nav class="dashboard-nav-wrapper">
        <ul class="dashboard-nav">
            <li>
                <a href="{{ route('instructor.dashboard') }}"
                    class="{{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.courses') }}"
                    class="{{ request()->routeIs('instructor.courses') ? 'active' : '' }}">
                    <i class="fa-solid fa-book-open"></i>
                    <span>My Courses</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.assignments.index') }}"
                    class="{{ request()->routeIs('instructor.assignments*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>Assignments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.quizzes.index') }}"
                    class="{{ request()->routeIs('instructor.quizzes*') ? 'active' : '' }}">
                    <i class="fa-solid fa-circle-question"></i>
                    <span>Quiz Attempts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.events.index') }}"
                    class="{{ request()->routeIs('instructor.events*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Events</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.students') }}"
                    class="{{ request()->routeIs('instructor.students*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Students</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.analytics') }}"
                    class="{{ request()->routeIs('instructor.analytics') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Analytics</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.reviews') }}"
                    class="{{ request()->routeIs('instructor.reviews') ? 'active' : '' }}">
                    <i class="fa-solid fa-comments"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.earnings') }}"
                    class="{{ request()->routeIs('instructor.earnings') || request()->routeIs('instructor.withdrawals') ? 'active' : '' }}">
                    <i class="fa-solid fa-wallet"></i>
                    <span>Earnings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.settings') }}"
                    class="{{ request()->routeIs('instructor.settings') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="">

                    <i class="fa-solid fa-arrow-up-right"></i>Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>
<!-- Sidebar End -->
