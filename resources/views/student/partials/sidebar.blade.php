<!-- Sidebar Start -->
<aside class="dashboard-sidebar">
    <div class="dashboard-profile">
        <div class="dashboard-profile-image">
            @if ($user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}">
            @else
                <img src="{{ asset('assets/front/img/home-1/courses/client-01.png') }}" alt="{{ $user->name }}">
            @endif
        </div>
        <h4>{{ $user->name }}</h4>
        <p>Student</p>
        <span class="status-badge">✓ Active</span>
    </div>

    <button class="dashboard-menu-toggle" type="button" aria-label="Toggle menu">
        <i class="fa-solid fa-bars"></i>
        <span>Menu</span>
    </button>

    <nav class="dashboard-nav-wrapper">
        <ul class="dashboard-nav">
            <li>
                <a href="{{ route('student.dashboard') }}"
                    class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.community.index') }}"
                    class="{{ request()->routeIs('student.community.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Community</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.notifications.index') }}"
                    class="{{ request()->routeIs('student.notifications.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.ai-chat.index') }}"
                    class="{{ request()->routeIs('student.ai-chat.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-robot"></i>
                    <span>AI Assistant</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.my-courses') }}"
                    class="{{ request()->routeIs('student.my-courses') ? 'active' : '' }}">
                    <i class="fa-solid fa-book-open"></i>
                    <span>My Courses</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.assignments') }}"
                    class="{{ request()->routeIs('student.assignments') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>My Assignments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.quiz-attempts') }}"
                    class="{{ request()->routeIs('student.quiz-attempts') ? 'active' : '' }}">
                    <i class="fa-solid fa-circle-question"></i>
                    <span>My Quiz Attempts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.events.bookings') }}"
                    class="{{ request()->routeIs('student.events.bookings') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>My Event Bookings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.certificates') }}"
                    class="{{ request()->routeIs('student.certificates') ? 'active' : '' }}">
                    <i class="fa-solid fa-certificate"></i>
                    <span>Certificates</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.payments') }}"
                    class="{{ request()->routeIs('student.payments') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Payment History</span>
                </a>
            </li>
            <li>
                <a href="{{ route('student.settings') }}"
                    class="{{ request()->routeIs('student.settings') ? 'active' : '' }}">
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