<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Devrabiul\ToastMagic\Facades\ToastMagic;

/**
 * Course Controller
 *
 * This controller handles the management of courses.
 *
 * @package App\Http\Controllers\Admin
 */
class CourseController extends Controller
{
    /**
     * Display all courses
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $query = Course::with(['instructor', 'enrollments']);

        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if (request()->has('status') && request()->status) {
            if (request()->status === 'archived') {
                $query->where('status', 'archived');
            } else {
                $query->where('status', request()->status);
            }
        } else {
            $query->where('status', '!=', 'archived');
        }

        if (request()->has('visibility') && request()->visibility) {
            $query->where('visibility', request()->visibility);
        }

        if (request()->has('pricing_model') && request()->pricing_model) {
            $query->where('pricing_model', request()->pricing_model);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $pendingCount = Course::where('status', 'pending')->count();

        return view('admin.courses.index', compact('courses', 'pendingCount'));
    }

    /**
     * Display pending courses for approval
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function pending()
    {
        $courses = Course::where('status', 'pending')
            ->with('instructor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.courses.pending', compact('courses'));
    }

    /**
     * Approve a course
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $course = Course::findOrFail($id);

        $request = request();
        $reason = $request->input('reason', '');

        $course->status = 'published';
        $course->approval_reason = $reason;
        $course->rejection_reason = null;
        $course->save();

        $message = "Course '{$course->title}' has been approved and published successfully.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        ToastMagic::success($message);
        return back();
    }

    /**
     * Reject a course
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $course = Course::findOrFail($id);

        $request = request();
        $reason = $request->input('reason', '');

        $courseTitle = $course->title;
        $course->status = 'draft';
        $course->rejection_reason = $reason;
        $course->approval_reason = null;
        $course->save();

        $message = "Course '{$courseTitle}' has been rejected and set back to draft.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        ToastMagic::info($message);
        return back();
    }

    /**
     * Display course details
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $course = Course::with([
            'instructor',
            'topics.lessons',
            'topics.quizzes',
            'topics.assignments',
            'enrollments',
            'reviews.user',
            'reviews.replies.user'
        ])->findOrFail($id);

        $lessonCount = $course->topics->sum(function ($topic) {
            return $topic->lessons->count();
        });

        $quizCount = $course->topics->sum(function ($topic) {
            return $topic->quizzes->count();
        });

        $assignmentCount = $course->topics->sum(function ($topic) {
            return $topic->assignments->count();
        });

        $totalDuration = $course->topics->sum(function ($topic) {
            return $topic->lessons->sum(function ($lesson) {
                return $lesson->duration ?? 0;
            });
        });

        $reviews = $course->reviews;
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

        return view('admin.courses.show', compact(
            'course',
            'lessonCount',
            'quizCount',
            'assignmentCount',
            'totalDuration',
            'totalReviews',
            'averageRating'
        ));
    }

    /**
     * Update course status
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($id)
    {
        $course = Course::findOrFail($id);
        $request = request();

        $newStatus = $request->input('status');
        $oldStatus = $course->status;

        $validStatuses = ['draft', 'pending', 'published', 'archived'];
        if (!in_array($newStatus, $validStatuses)) {
            ToastMagic::error('Invalid status selected.');
            return back();
        }

        $course->status = $newStatus;

        if ($newStatus === 'archived') {
            $course->deleted_date = now();
        } else {
            $course->deleted_date = null;
        }

        if ($newStatus !== 'published' && $oldStatus === 'pending') {
            $course->approval_reason = null;
        }
        if ($newStatus !== 'draft' && $oldStatus === 'pending') {
            $course->rejection_reason = null;
        }

        $course->save();

        $statusLabels = [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'published' => 'Published',
            'archived' => 'Archived'
        ];

        ToastMagic::success("Course '{$course->title}' status has been changed to {$statusLabels[$newStatus]}.");
        return back();
    }

    /**
     * Archive a course (soft delete)
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $courseTitle = $course->title;

        $course->status = 'archived';
        $course->deleted_date = now();
        $course->save();

        ToastMagic::success("Course '{$courseTitle}' has been archived successfully.");
        return back();
    }
}
