@extends('layouts.instructor')

@section('content')
    <section class="section-bg fix section-padding">
        <div class="container">
            <div class="dashboard-layout">
                @include('instructor.partials.sidebar')

                <div class="dashboard-main">
                    <!-- Page Header -->
                    <div class="dashboard-header wow fadeInUp">
                        <div class="section-title-area">
                            <div class="section-title">
                                <h6>Settings</h6>
                                <h2>Account Settings</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Settings Section -->
                    <div class="dashboard-courses-section wow fadeInUp" data-wow-delay="0.1s">
                        <div class="section-title">
                            <h3>Profile Information</h3>
                            <p class="section-subtitle">Update your profile information and personal details</p>
                        </div>

                        <form class="settings-form" id="profileForm"
                            action="{{ route('instructor.settings.profile.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Avatar Upload -->
                            <div class="form-group avatar-upload-group">
                                <label>Profile Picture</label>
                                <div class="avatar-upload-wrapper">
                                    <div class="avatar-preview">
                                        <img src="{{ $profile['avatar'] }}" alt="{{ $profile['name'] }}" id="avatarPreview">
                                        <div class="avatar-overlay">
                                            <i class="fa-solid fa-camera"></i>
                                            <span>Change Photo</span>
                                        </div>
                                    </div>
                                    <input type="file" name="avatar" id="avatarInput" accept="image/*"
                                        style="display: none;">
                                    <small class="form-hint">Recommended: 300x300px, Max size: 2MB</small>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Full Name <span class="required">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $profile['name'] }}"
                                        required placeholder="Enter your full name">
                                </div>
                                <div class="form-group">
                                    <label>Email Address <span class="required">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ $profile['email'] }}" required placeholder="Enter your email">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" class="form-control"
                                        value="{{ $profile['phone'] }}" placeholder="Enter your phone number">
                                </div>
                            </div>

                            <!-- Bio -->
                            <div class="form-group">
                                <label>Biography</label>
                                <textarea name="bio" class="form-control" rows="5"
                                    placeholder="Tell us about yourself, your experience, and what you teach...">{{ $profile['bio'] }}</textarea>
                                <small class="form-hint">Write a brief description about yourself and your expertise</small>
                            </div>


                            <!-- Social Media Links -->
                            <div class="form-section-divider">
                                <h4>Social Media Links</h4>
                                <p class="section-subtitle">Connect your social media profiles</p>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>
                                        <i class="fa-brands fa-linkedin"></i> LinkedIn
                                    </label>
                                    <input type="url" name="linkedin" class="form-control"
                                        value="{{ $profile['linkedin'] }}" placeholder="https://linkedin.com/in/username">
                                </div>
                                <div class="form-group">
                                    <label>
                                        <i class="fa-brands fa-twitter"></i> Twitter
                                    </label>
                                    <input type="url" name="twitter" class="form-control"
                                        value="{{ $profile['twitter'] }}" placeholder="https://twitter.com/username">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>
                                        <i class="fa-brands fa-facebook"></i> Facebook
                                    </label>
                                    <input type="url" name="facebook" class="form-control"
                                        value="{{ $profile['facebook'] }}" placeholder="https://facebook.com/username">
                                </div>
                                <div class="form-group">
                                    <label>
                                        <i class="fa-brands fa-youtube"></i> YouTube
                                    </label>
                                    <input type="url" name="youtube" class="form-control"
                                        value="{{ $profile['youtube'] }}" placeholder="https://youtube.com/@username">
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <button type="button" class="cancel-btn"
                                    onclick="document.getElementById('profileForm').reset()">
                                    Cancel
                                </button>
                                <button type="submit" class="theme-btn style-2 px-5">
                                    <i class="fa-solid fa-save"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Password Change Section -->
                    <div class="dashboard-courses-section wow fadeInUp" data-wow-delay="0.2s" style="margin-top: 30px;">
                        <div class="section-title">
                            <h3>Change Password</h3>
                            <p class="section-subtitle">Update your password to keep your account secure</p>
                        </div>

                        <form class="settings-form" id="passwordForm"
                            action="{{ route('instructor.settings.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Current Password <span class="required">*</span></label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="current_password" class="form-control" required
                                        placeholder="Enter current password" id="currentPassword">
                                    <button type="button" class="password-toggle"
                                        onclick="togglePassword('currentPassword', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>New Password <span class="required">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" name="new_password" class="form-control" required
                                            placeholder="Enter new password" id="newPassword">
                                        <button type="button" class="password-toggle"
                                            onclick="togglePassword('newPassword', this)">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="form-hint">Minimum 8 characters</small>
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password <span class="required">*</span></label>
                                    <div class="password-input-wrapper">
                                        <input type="password" name="new_password_confirmation" class="form-control"
                                            required placeholder="Confirm new password" id="confirmPassword">
                                        <button type="button" class="password-toggle"
                                            onclick="togglePassword('confirmPassword', this)">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="cancel-btn"
                                    onclick="document.getElementById('passwordForm').reset()">
                                    Cancel
                                </button>
                                <button type="submit" class="theme-btn style-2 px-5">
                                    <i class="fa-solid fa-key"></i>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Account Settings Section -->
                    <div class="dashboard-courses-section wow fadeInUp" data-wow-delay="0.3s" style="margin-top: 30px;">
                        <div class="section-title">
                            <h3>Account Settings</h3>
                            <p class="section-subtitle">Manage your account preferences</p>
                        </div>

                        <div class="account-settings-list">
                            <form action="{{ route('instructor.settings.notifications.update') }}" method="POST"
                                id="instructorNotificationForm">
                                @csrf
                                @method('PUT')

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Email Notifications</h4>
                                        <p>Receive email notifications about courses, students, and earnings</p>
                                    </div>
                                    <div class="setting-control">
                                        <label class="toggle-switch">
                                            <input type="hidden" name="email_notifications" value="0">
                                            <input type="checkbox" class="notification-toggle" name="email_notifications"
                                                value="1"
                                                {{ data_get($notificationSettings, 'email_notifications', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Course Updates</h4>
                                        <p>Get notified when students enroll in your courses</p>
                                    </div>
                                    <div class="setting-control">
                                        <label class="toggle-switch">
                                            <input type="hidden" name="course_updates" value="0">
                                            <input type="checkbox" class="notification-toggle" name="course_updates"
                                                value="1"
                                                {{ data_get($notificationSettings, 'course_updates', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Review Notifications</h4>
                                        <p>Receive alerts when students leave reviews</p>
                                    </div>
                                    <div class="setting-control">
                                        <label class="toggle-switch">
                                            <input type="hidden" name="review_notifications" value="0">
                                            <input type="checkbox" class="notification-toggle"
                                                name="review_notifications" value="1"
                                                {{ data_get($notificationSettings, 'review_notifications', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h4>Marketing Emails</h4>
                                        <p>Receive tips, updates, and promotional content</p>
                                    </div>
                                    <div class="setting-control">
                                        <label class="toggle-switch">
                                            <input type="hidden" name="marketing_emails" value="0">
                                            <input type="checkbox" class="notification-toggle" name="marketing_emails"
                                                value="1"
                                                {{ data_get($notificationSettings, 'marketing_emails', false) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="dashboard-courses-section danger-zone wow fadeInUp" data-wow-delay="0.4s"
                        style="margin-top: 30px;">
                        <div class="section-title">
                            <h3 style="color: #ef4444;">Danger Zone</h3>
                            <p class="section-subtitle">Irreversible and destructive actions</p>
                        </div>

                        <div class="danger-actions">
                            @isset($latestDeletionRequest)
                                @php($deletionStatus = $latestDeletionRequest->status)
                                <div class="alert {{ $deletionStatus === 'pending' ? 'alert-warning' : ($deletionStatus === 'rejected' ? 'alert-danger' : 'alert-success') }} mb-4"
                                    role="alert">
                                    @if ($deletionStatus === 'pending')
                                        <strong>Awaiting approval:</strong> Your account deletion request was submitted on
                                        {{ $latestDeletionRequest->created_at->format('M d, Y') }}. We will notify you once an
                                        admin processes it.
                                    @elseif($deletionStatus === 'rejected')
                                        <strong>Request rejected:</strong>
                                        {{ $latestDeletionRequest->note ?? 'Please contact support for more details.' }}
                                    @elseif($deletionStatus === 'approved')
                                        <strong>Approved:</strong> This request has already been processed.
                                    @endif
                                </div>
                            @endisset
                            <div class="danger-item">
                                <div class="danger-info">
                                    <h4>Delete Account</h4>
                                    <p>Permanently delete your account and all associated data. This action cannot be
                                        undone.</p>
                                </div>
                                @if (isset($deletionStatus) && $deletionStatus === 'pending')
                                    <button type="button" class="danger-btn" disabled>
                                        <i class="fa-solid fa-clock"></i>
                                        Awaiting Approval
                                    </button>
                                @else
                                    <button type="button" class="danger-btn" onclick="confirmDeleteAccount()">
                                        <i class="fa-solid fa-trash"></i>
                                        Delete Account
                                    </button>
                                @endif
                            </div>
                        </div>
                        <form id="instructor-delete-account-form"
                            action="{{ route('instructor.settings.account.destroy') }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Avatar Preview
                $('#avatarInput').on('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#avatarPreview').attr('src', e.target.result);
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $('.avatar-preview').on('click', function() {
                    $('#avatarInput').click();
                });

            });

            function togglePassword(inputId, button) {
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            function confirmDeleteAccount() {
                const form = document.getElementById('instructor-delete-account-form');

                if (!form) {
                    return;
                }

                Swal.fire({
                    title: 'Delete Account?',
                    text: 'This will permanently remove your account and all associated data. This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#1d4ed8'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Submitting request…',
                            text: 'Please wait while we submit your account deletion request for admin approval.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                                form.submit();
                            }
                        });
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const notificationForm = document.getElementById('instructorNotificationForm');

                if (notificationForm) {
                    notificationForm.addEventListener('change', function(event) {
                        if (event.target.classList.contains('notification-toggle')) {
                            notificationForm.submit();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
