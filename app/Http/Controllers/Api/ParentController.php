<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Assignment;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParentController extends Controller
{
    /**
     * GET /me/children
     * List all students linked to the logged-in parent
     */
    public function getMyChildren(): JsonResponse
    {
        try {
            $parent = $this->getCurrentParent();

            $children = $parent->students()->get()->map(fn($student) => [
                'id' => $student->id,
                'registration_no' => $student->registration_no,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'email' => $student->email,
                'phone' => $student->phone,
                'date_of_birth' => $student->date_of_birth,
                'gender' => $student->gender,
            ]);

            return response()->json(['success' => true, 'data' => $children]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch children: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch children'], 500);
        }
    }

    /**
     * GET /students/{id}/attendance
     */
    public function getStudentAttendance(int $id): JsonResponse
    {
        try {
            $this->authorizeParentFor($id);

            $attendance = Attendance::where('student_id', $id)
                ->orderBy('date', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $attendance]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch attendance: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch attendance'], 500);
        }
    }

/**
 * GET /students/{id}/grades
 */
    public function getStudentGrades(int $id): JsonResponse
    {
        try {
            $this->authorizeParentFor($id);

            $grades = Grade::where('student_id', $id)
                ->with(['exam', 'subject', 'teacher']) // optional: include related data
                ->get();

            return response()->json(['success' => true, 'data' => $grades]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch grades: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch grades'], 500);
        }
    }

/**
 * GET /students/{id}/assignments
 */
    public function getStudentAssignments(int $id): JsonResponse
    {
        try {
            $this->authorizeParentFor($id);

            // Get assignments via enrollments
            $assignments = Assignment::whereIn('class_id', function ($query) use ($id) {
                    $query->select('class_id')
                        ->from('enrollments')
                        ->where('student_id', $id)
                        ->where('active', true);
                })
                ->orderBy('due_date', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $assignments]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch assignments: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch assignments'], 500);
        }
    }




    /**
     * GET /classes/{classId}/announcements
     */
    public function getClassAnnouncements(int $classId): JsonResponse
    {
        try {
            $announcements = Announcement::where('class_id', $classId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $announcements]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch announcements: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch announcements'], 500);
        }
    }

    /**
     * Utility: get current logged-in parent
     */
    private function getCurrentParent(): ParentModel
    {
        $user = Auth::user();
        $parent = ParentModel::where('user_id', $user->id)->first();

        if (!$parent) {
            throw new \Exception('Parent not found for logged-in user.');
        }

        return $parent;
    }

    /**
     * Utility: authorize parent for a given student
     */
    private function authorizeParentFor(int $studentId): void
    {
        $parent = $this->getCurrentParent();

        if (!$parent->students()->where('students.id', $studentId)->exists()) {
            abort(403, 'You are not authorized to view this student.');
        }
    }
}
