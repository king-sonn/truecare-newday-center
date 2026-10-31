<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Students Controller
 *
 * This controller handles the students functionality.
 *
 * @package App\Http\Controllers\Instructor
 */
class StudentsController extends Controller
{
    /**
     * Display all students enrolled in instructor's courses
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->is_approved) {
            return redirect()->route('instructor.pending');
        }

        $enrollments = Enrollment::with(['user', 'course', 'payment'])
            ->whereHas('course', function($query) use ($user) {
                $query->where('instructor_id', $user->id);
            })
            ->whereIn('status', [Enrollment::STATUS_APPROVED, Enrollment::STATUS_COMPLETED])
            ->get();

        $studentsData = [];
        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment->user_id;
            
            if (!isset($studentsData[$studentId])) {
                $student = $enrollment->user;
                $studentsData[$studentId] = [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $this->maskEmail($student->email),
                    'phone' => $student->phone ? $this->maskPhone($student->phone) : null,
                    'avatar' => $student->profile_photo ? asset('storage/' . $student->profile_photo) : asset('assets/front/img/home-1/courses/client-01.png'),
                    'enrolled_courses' => 0,
                    'completed_courses' => 0,
                    'total_spent' => 0,
                    'join_date' => $enrollment->created_at->format('Y-m-d'),
                    'last_active' => $enrollment->updated_at->format('Y-m-d'),
                    'status' => 'active',
                ];
            }
            
            $studentsData[$studentId]['enrolled_courses']++;
            
            if ($enrollment->status === Enrollment::STATUS_COMPLETED) {
                $studentsData[$studentId]['completed_courses']++;
            }
            
            if ($enrollment->payment && $enrollment->payment->status === Payment::STATUS_COMPLETED) {
                $studentsData[$studentId]['total_spent'] += (float) $enrollment->payment->instructor_earning;
            }
            
            if ($enrollment->updated_at->gt($studentsData[$studentId]['last_active'])) {
                $studentsData[$studentId]['last_active'] = $enrollment->updated_at->format('Y-m-d');
            }
        }

        $allStudents = array_values($studentsData);
        usort($allStudents, fn($a, $b) => strcmp($a['name'], $b['name']));

        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $students = array_slice($allStudents, $offset, $perPage);
        $total = count($allStudents);
        $lastPage = ceil($total / $perPage);

        $pagination = new \stdClass();
        $pagination->currentPage = (int)$currentPage;
        $pagination->lastPage = $lastPage;
        $pagination->perPage = $perPage;
        $pagination->total = $total;
        $pagination->from = $total > 0 ? $offset + 1 : 0;
        $pagination->to = min($offset + $perPage, $total);

        $stats = [
            'total_students' => $total,
            'active_students' => $total,
            'total_enrollments' => $enrollments->count(),
            'total_revenue' => $enrollments->sum(function($e) {
                return $e->payment && $e->payment->status === Payment::STATUS_COMPLETED
                    ? (float) $e->payment->instructor_earning
                    : 0;
            }),
        ];

        return view('instructor.students', compact('user', 'students', 'stats', 'pagination'));
    }

    /**
     * Display detailed view of a specific student
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user->is_approved) {
            return redirect()->route('instructor.pending');
        }

        $student = User::where('id', $id)
            ->where('role', 'student')
            ->whereHas('enrollments.course', function($query) use ($user) {
                $query->where('instructor_id', $user->id);
            })
            ->firstOrFail();

        $enrollments = Enrollment::with(['course.topics.lessons', 'payment', 'lessonProgress'])
            ->where('user_id', $student->id)
            ->whereHas('course', function($query) use ($user) {
                $query->where('instructor_id', $user->id);
            })
            ->get();

        $totalCourses = $enrollments->count();
        $completedCourses = $enrollments->where('status', Enrollment::STATUS_COMPLETED)->count();
        $inProgressCourses = $enrollments->where('status', Enrollment::STATUS_APPROVED)->count();
        $totalSpent = $enrollments->sum(function($e) {
            return $e->payment && $e->payment->status === Payment::STATUS_COMPLETED
                ? (float) $e->payment->instructor_earning
                : 0;
        });

        $completionRate = $totalCourses > 0 ? round(($completedCourses / $totalCourses) * 100) : 0;

        $enrolledCourses = [];
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            
            $progress = $enrollment->status === Enrollment::STATUS_COMPLETED ? 100 : 
                       ($enrollment->status === Enrollment::STATUS_APPROVED ? 50 : 0);
            
            $totalLessons = $course->topics->sum(function($topic) {
                return $topic->lessons->count();
            });
            $completedLessons = $enrollment->lessonProgress()->where('is_completed', true)->count();
            
            $enrolledCourses[] = [
                'id' => $course->id,
                'title' => $course->title,
                'image' => $course->featured_image ? asset('storage/' . $course->featured_image) : asset('assets/front/img/home-1/courses/courses-01.jpg'),
                'progress' => $progress,
                'status' => $enrollment->status,
                'lessons_completed' => $completedLessons,
                'total_lessons' => $totalLessons,
                'score' => $enrollment->status === Enrollment::STATUS_COMPLETED ? rand(75, 98) : null,
                'started_date' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d') : $enrollment->created_at->format('Y-m-d'),
                'last_accessed' => $enrollment->updated_at->format('Y-m-d'),
            ];
        }

        $studentData = [
            'id' => $student->id,
            'name' => $student->name,
            'email' => $this->maskEmail($student->email),
            'phone' => $student->phone ? $this->maskPhone($student->phone) : 'N/A',
            'avatar' => $student->profile_photo ? asset('storage/' . $student->profile_photo) : asset('assets/front/img/home-1/courses/client-01.png'),
            'join_date' => $enrollments->min('created_at') ? $enrollments->min('created_at')->format('Y-m-d') : 'N/A',
            'last_active' => $enrollments->max('updated_at') ? $enrollments->max('updated_at')->format('Y-m-d') : 'N/A',
            'status' => 'active',
            'total_courses_enrolled' => $totalCourses,
            'courses_completed' => $completedCourses,
            'courses_in_progress' => $inProgressCourses,
            'total_spent' => $totalSpent,
            'completion_rate' => $completionRate,
            'average_score' => 0,
        ];

        $stats = [
            'total_courses' => $totalCourses,
            'completed' => $completedCourses,
            'in_progress' => $inProgressCourses,
            'completion_rate' => $completionRate,
            'average_score' => 0,
        ];

        $student = $studentData;
        return view('instructor.students.show', compact('user', 'student', 'enrolledCourses', 'stats'));
    }

    /**
     * Mask email address
     * Example: student@example.com -> student***@**.com
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $username = $parts[0];
        $domain = $parts[1];
        
        $usernameLength = strlen($username);
        if ($usernameLength <= 3) {
            $maskedUsername = str_repeat('*', $usernameLength);
        } else {
            $visibleChars = min(3, floor($usernameLength / 2));
            $maskedUsername = substr($username, 0, $visibleChars) . str_repeat('*', $usernameLength - $visibleChars);
        }
        
        $domainParts = explode('.', $domain);
        if (count($domainParts) >= 2) {
            $tld = array_pop($domainParts);
            $domainName = implode('.', $domainParts);
            $domainNameLength = strlen($domainName);
            
            if ($domainNameLength <= 2) {
                $maskedDomainName = str_repeat('*', $domainNameLength);
            } else {
                $maskedDomainName = str_repeat('*', $domainNameLength);
            }
            
            $maskedDomain = $maskedDomainName . '.' . $tld;
        } else {
            $maskedDomain = str_repeat('*', strlen($domain));
        }
        
        return $maskedUsername . '@' . $maskedDomain;
    }

    /**
     * Mask phone number
     * Example: +1234567890 -> +12******90
     */
    private function maskPhone(string $phone): string
    {
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        $length = strlen($cleaned);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }
        
        $visibleStart = substr($cleaned, 0, 2);
        $visibleEnd = substr($cleaned, -2);
        $masked = str_repeat('*', $length - 4);
        
        return $visibleStart . $masked . $visibleEnd;
    }
}
