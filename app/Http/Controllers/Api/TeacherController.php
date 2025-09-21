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
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;

class TeacherController extends Controller
{
    /**
     * GET /me/classes
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
                ]);

            return response()->json(['success' => true, 'data' => $classes]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch teacher classes: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /classes/{classId}/roster
     */
    public function getClassRoster(int $classId, Request $request): JsonResponse
    {
        $sectionId = $request->query('section_id'); // optional filter
        try {
            $teacher = $this->getCurrentTeacher();
            $this->authorizeTeacherFor($classId, $sectionId);

            $enrollments = Enrollment::where('class_id', $classId)
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->where('active', true)
                ->with('student')
                ->get();

            $students = $enrollments->map(fn($enroll) => [
                'id' => $enroll->student->id,
                'registration_no' => $enroll->student->registration_no,
                'first_name' => $enroll->student->first_name,
                'last_name' => $enroll->student->last_name,
                'email' => $enroll->student->email,
                'phone' => $enroll->student->phone,
                'date_of_birth' => $enroll->student->date_of_birth,
                'gender' => $enroll->student->gender,
            ]);

            return response()->json(['success' => true, 'data' => $students]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch class roster: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /classes/{classId}/attendance
     */
   public function markAttendance(int $classId, Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
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
        $this->authorizeTeacherFor($classId, $request->section_id);

        DB::transaction(function () use ($request, $classId, $teacher) {
            foreach ($request->records as $record) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $record['student_id'],
                        'class_id' => $classId,
                        'section_id' => $request->section_id,
                        'date' => $request->date, // use top-level date
                    ],
                    [
                        'status' => $record['status'],
                        'teacher_id' => $teacher->id,
                        'school_id' => $teacher->school_id,
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
     * GET /classes/{classId}/assignments
     */
    public function getAssignmentsByClass(int $classId): JsonResponse
    {
        try {
            $teacher = $this->getCurrentTeacher();
            $this->authorizeTeacherFor($classId, null);

            $assignments = Assignment::where('class_id', $classId)
                ->where('teacher_id', $teacher->id)
                ->get();

            return response()->json(['success' => true, 'data' => $assignments]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch assignments: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /classes/{classId}/assignments
     */
    public function createAssignment(int $classId, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $teacher = $this->getCurrentTeacher();
            $this->authorizeTeacherFor($classId, $request->section_id, $request->subject_id);

            $assignmentData = [
                'teacher_id' => $teacher->id,
                'school_id' => $teacher->school_id,
                'class_id' => $classId,
                'section_id' => $request->section_id,
                'subject_id' => $request->subject_id,
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
            ];

            // Handle file upload
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('assignments', 'public');
                $assignmentData['file_path'] = $path;
            }

            $assignment = Assignment::create($assignmentData);

            return response()->json(['success' => true, 'data' => $assignment]);
        } catch (\Exception $e) {
            Log::error('Failed to create assignment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /classes/{classId}/grades
     */
    public function enterGrades(int $classId, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'records' => 'required|array',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.assessment_id' => 'required|exists:exams,id',
            'records.*.score' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $teacher = $this->getCurrentTeacher();

            DB::transaction(function () use ($request, $teacher, $classId) {
                foreach ($request->records as $record) {
                    $exam = Exam::findOrFail($record['assessment_id']);
                    $this->authorizeTeacherFor($classId, $exam->section_id, $exam->subject_id);

                    Grade::updateOrCreate(
                        [
                            'exam_id' => $exam->id,
                            'student_id' => $record['student_id'],
                        ],
                        [
                            'score' => $record['score'],
                            'teacher_id' => $teacher->id,
                            'school_id' => $teacher->school_id,
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
     * POST /classes/{classId}/announcements
     */
    public function createAnnouncement(int $classId, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $teacher = $this->getCurrentTeacher();
            $this->authorizeTeacherFor($classId, $request->section_id);

            $announcement = Announcement::create([
                'teacher_id' => $teacher->id,
                'school_id' => $teacher->school_id,
                'class_id' => $classId,
                'section_id' => $request->section_id,
                'title' => $request->title,
                'message' => $request->body,
            ]);

            return response()->json(['success' => true, 'data' => $announcement]);
        } catch (\Exception $e) {
            Log::error('Failed to create announcement: ' . $e->getMessage());
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
    private function authorizeTeacherFor(int $classId, ?int $sectionId = null, ?int $subjectId = null): void
    {
        $query = $this->getCurrentTeacher()->teacherSubjects()
            ->where('class_id', $classId);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if (!$query->exists()) {
            throw new AuthorizationException('You are not authorized for this class/section/subject.');
        }
    }
}
