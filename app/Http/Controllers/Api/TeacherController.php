<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Assignment;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Exam;
use App\Models\Enrollment;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;

class TeacherController extends Controller
{
    /**
     * GET /me/classes - Get teacher's assigned classes
     */
    public function getMyClasses(): JsonResponse
    {
        try {
            $teacher = $this->getCurrentTeacher();

            $classes = $teacher->teacherSubjects()
                ->with(['schoolClass', 'section', 'subject'])
                ->get()
                ->map(fn($ts) => [
                    'id' => $ts->schoolClass->id,
                    'name' => $ts->schoolClass->name,
                    'section' => [
                        'id' => $ts->section->id,
                        'name' => $ts->section->name,
                        'code' => $ts->section->code,
                    ],
                    'subject' => [
                        'id' => $ts->subject->id,
                        'name' => $ts->subject->name,
                        'code' => $ts->subject->code,
                    ],
                    'academic_year' => $ts->academic_year,
                    'semester' => $ts->semester,
                ]) ;

            return response()->json(['success' => true, 'data' => $classes]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch teacher classes: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function markAttendance(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'class_id' => 'required|exists:school_classes,id',
        'section_id' => 'required|exists:sections,id',
        'date' => 'required|date',
        'records' => 'required|array',
        'records.*.student_id' => 'required|exists:students,id',
        'records.*.status' => 'required|in:present,absent,late,excused',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    try {
        $teacher = $this->getCurrentTeacher();
        $this->authorizeTeacherFor($request->class_id, $request->section_id);

        DB::transaction(function () use ($request, $teacher) {
            foreach ($request->records as $record) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $record['student_id'],
                        'class_id' => $request->class_id,
                        'section_id' => $request->section_id,
                        'date' => $request->date,
                    ],
                    [
                        'status' => $record['status'],
                        'teacher_id' => $teacher->id,          // auto-fill teacher_id
                        'school_id' => $teacher->school_id ?? null, // optional school_id
                    ]
                );
            }
        });

        return response()->json(['success' => true, 'message' => 'Attendance marked successfully']);
    } catch (\Exception $e) {
        Log::error('Failed to mark attendance: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


    /**
     * Get students enrolled in a specific class and section
     */
    public function getStudentsByClass(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|integer|exists:school_classes,id',
            'section_id' => 'required|integer|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $this->authorizeTeacherFor($request->class_id, $request->section_id);

            $students = Enrollment::where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->active()
                ->with('student')
                ->get()
                ->map(fn($enrollment) => [
                    'id' => $enrollment->student->id,
                    'student_id' => $enrollment->student->student_id,
                    'first_name' => $enrollment->student->first_name,
                    'last_name' => $enrollment->student->last_name,
                    'email' => $enrollment->student->email,
                    'phone' => $enrollment->student->phone,
                    'date_of_birth' => $enrollment->student->date_of_birth,
                    'gender' => $enrollment->student->gender,
                ]);

            return response()->json(['success' => true, 'data' => $students]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch students by class: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create assignment (teacher_id auto-filled)
     */
    public function createAssignment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $teacher = $this->getCurrentTeacher();
            $this->authorizeTeacherFor($request->class_id, $request->section_id, $request->subject_id);

            $assignment = Assignment::create([
                'teacher_id' => $teacher->id,             // auto-filled from logged-in teacher
                'school_id'  => $teacher->school_id ?? null, // optional multi-tenancy
                'class_id'   => $request->class_id,
                'section_id' => $request->section_id,
                'subject_id' => $request->subject_id,
                'title'      => $request->title,
                'description'=> $request->description,
                'due_date'   => $request->due_date,
            ]);

            return response()->json(['success' => true, 'data' => $assignment]);
        } catch (\Exception $e) {
            Log::error('Failed to create assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create announcement (optional: include teacher_id)
     */
    public function createAnnouncement(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $teacher = $this->getCurrentTeacher();
            $this->authorizeTeacherFor($request->class_id, $request->section_id);

            $announcement = Announcement::create([
                'teacher_id' => $teacher->id,      // auto-fill teacher_id
                'school_id'  => $teacher->school_id ?? null,
                'class_id'   => $request->class_id,
                'section_id' => $request->section_id,
                'title'      => $request->title,
                'message'    => $request->message,
            ]);

            return response()->json(['success' => true, 'data' => $announcement]);
        } catch (\Exception $e) {
            Log::error('Failed to create announcement: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function enterGrades(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'exam_id' => 'required|exists:exams,id',
        'records' => 'required|array',
        'records.*.student_id' => 'required|exists:students,id',
        'records.*.score' => 'required|numeric|min:0|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    try {
        $teacher = $this->getCurrentTeacher();
        $exam = Exam::findOrFail($request->exam_id);
        $this->authorizeTeacherFor($exam->class_id, $exam->section_id, $exam->subject_id);

        DB::transaction(function () use ($request, $exam, $teacher) {
            foreach ($request->records as $record) {
                Grade::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $record['student_id'],
                    ],
                    [
                        'score' => $record['score'],
                        'teacher_id' => $teacher->id,          // auto-fill teacher_id
                        'school_id' => $teacher->school_id ?? null, // optional school_id
                    ]
                );
            }
        });

        return response()->json(['success' => true, 'message' => 'Grades recorded successfully']);
    } catch (\Exception $e) {
        Log::error('Failed to enter grades: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


    /**
     * Utility: get current logged-in teacher
     */
    private function getCurrentTeacher(): Teacher
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            throw new \Exception('Teacher not found for logged-in user.');
        }

        return $teacher;
    }

    /**
     * Utility: authorize teacher for class/section/subject
     */
    private function authorizeTeacherFor(int $classId, int $sectionId, ?int $subjectId = null): void
    {
        $query = $this->getCurrentTeacher()->teacherSubjects()
            ->where('class_id', $classId)
            ->where('section_id', $sectionId);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if (!$query->exists()) {
            throw new AuthorizationException('You are not authorized for this class/section/subject.');
        }
    }
}
