        <!-- Header Section Start -->
        <header id="header-sticky" class="header-1">
            <div class="logo-shape">
                <img src="{{ asset('assets/front/img/inner/logo-shape.png') }}" alt="img">
            </div>
            <div class="container-fluid">
                <div class="mega-menu-wrapper">
                    <div class="header-main">
                        <div class="logo">
                            <?php $siteName = site_name(); ?>
                            <a href="{{ route('home') }}" class="header-logo">
                                <img src="{{ branding_asset(['dark_logo_path', 'logos.secondary', 'primary_logo_path', 'logos.primary'], 'assets/front/img/logo/black-logo.svg') }}"
                                    alt="{{ $siteName }} logo">
                            </a>
                            <a href="{{ route('home') }}" class="header-logo2">
                                <img src="{{ branding_asset(['primary_logo_path', 'logos.primary'], 'assets/front/img/logo/white-logo.svg') }}"
                                    alt="{{ $siteName }} logo">
                            </a>
                        </div>
                        <div class="header-right-items">
                            <div class="mean__menu-wrapper">
                                <div class="main-menu">
                                    <nav id="mobile-menu">
                                        <ul>
                                            <?php $mainMenu = $frontendMenus['main-navigation'] ?? []; ?>
                                            @if (!empty($mainMenu))
                                                @include('partials.menu-items', [
                                                    'items' => $mainMenu,
                                                    'isRoot' => true,
                                                ])
                                            @else
                                                <li><a href="{{ route('home') }}">Home</a></li>
                                                <li><a href="{{ route('about') }}">About</a></li>
                                                <li><a href="#our-work">Our Work</a></li>
                                                <li class="has-dropdown">
                                                    <a href="#programs">Programs <i class="fa-solid fa-chevron-down"></i></a>
                                                    <ul class="submenu">
                                                        <li><a href="#programs-education">Education Re-entry</a></li>
                                                        <li><a href="#programs-childcare">Childcare</a></li>
                                                        <li><a href="#programs-counseling">Counseling</a></li>
                                                        <li><a href="#programs-life-skills">Life Skills</a></li>
                                                        <li><a href="#programs-vocational">Vocational</a></li>
                                                        <li><a href="#programs-health">Health Wellness</a></li>
                                                    </ul>
                                                </li>
                                                <li><a href="#impact-stories">Impact Stories</a></li>
                                                <li><a href="{{ route('contact') }}">Contact</a></li>
                                                <li><a href="{{ route('donate') }}">Donate</a></li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="header-right d-flex justify-content-end align-items-center">
                                <a href="#" class="main-header__search search-toggler">
                                    <i class="fa-regular fa-magnifying-glass"></i>
                                </a>

                                @if(Auth::check())
                                    <?php $currentUser = Auth::user(); ?>
                                    @include('partials.notification-dropdown', ['currentUser' => $currentUser])

                                    @php
                                        $userRole = 'guest';
                                        if($currentUser->isAdmin()) $userRole = 'admin';
                                        elseif($currentUser->isInstructor()) $userRole = 'instructor';
                                        elseif($currentUser->isStudent()) $userRole = 'student';
                                        
                                        $dashboardRoute = route('home');
                                        $dashboardText = 'Dashboard';
                                        
                                        if($userRole === 'admin') { 
                                            $dashboardRoute = route('admin.dashboard'); 
                                            $dashboardText = 'Admin Dashboard'; 
                                        } elseif($userRole === 'instructor') { 
                                            $dashboardRoute = route('instructor.dashboard'); 
                                            $dashboardText = 'Instructor Dashboard'; 
                                        } elseif($userRole === 'student') { 
                                            $dashboardRoute = route('student.dashboard'); 
                                            $dashboardText = 'My Dashboard'; 
                                        }
                                    @endphp

                                    <a href="{{ $dashboardRoute }}" class="theme-btn">
                                        {{ $dashboardText }}
                                        <i class="fa-solid fa-arrow-up-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="theme-btn" style="border:2px solid #0F766E; color:#0F766E;">
                                        Mom Portal
                                        <i class="fa-solid fa-arrow-up-right"></i>
                                    </a>
                                @endif

                                <div class="header__hamburger d-xl-none my-auto">
                                    <div class="sidebar__toggle">
                                        <i class="fas fa-bars"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @include('partials.search')
