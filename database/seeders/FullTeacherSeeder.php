<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\Enrollment;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Attendance;

class FullTeacherSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create school
        $school = School::firstOrCreate(
            ['code' => 'SCH001'],
            ['name' => 'Edunova High School', 'address' => '123 Main Street', 'contact_email' => 'info@edunova.com', 'contact_phone' => '123456789']
        );

        // 2. Create teacher user
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            ['name' => 'Mr. John Doe', 'password' => Hash::make('password')]
        );

        // 3. Create teacher
        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id, 'school_id' => $school->id],
            ['employee_no' => 'EMP001', 'department' => 'Mathematics', 'specialization' => 'Algebra', 'experience_years' => 5]
        );

        // 4. Create class
        $class = SchoolClass::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Grade 10'],
            ['code' => 'G10', 'academic_year' => '2024-2025']
        );

        // 5. Create section
        $section = Section::firstOrCreate(
            ['school_id' => $school->id, 'class_id' => $class->id, 'name' => 'A']
        );

        // 6. Create subject
        $subject = Subject::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Mathematics']
        );

        // 7. Assign teacher to subject, class, section
        TeacherSubject::firstOrCreate([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'school_id' => $school->id,
        ]);

        // 8. Create students
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "student$i@example.com"],
                ['name' => "Student $i", 'password' => Hash::make('password')]
            );

            $student = Student::firstOrCreate(
                ['user_id' => $user->id, 'school_id' => $school->id],
                [
                    'registration_no' => 'REG' . rand(1000, 9999),
                    'admission_no' => 'ADM' . rand(1000, 9999),
                    'class_id' => $class->id,
                    'section_id' => $section->id,
                    'status' => 'active',
                    'registration_status' => 'approved',
                ]
            );

            Enrollment::firstOrCreate(
                ['student_id' => $student->id, 'class_id' => $class->id, 'section_id' => $section->id, 'school_id' => $school->id],
                ['active' => true]
            );
        }

        $studentIds = Student::pluck('id');

        // 9. Create assignment
        Assignment::firstOrCreate(
            ['teacher_id' => $teacher->id, 'class_id' => $class->id, 'section_id' => $section->id, 'title' => 'Math Homework 1'],
            ['description' => 'Solve Algebra problems', 'due_date' => now()->addWeek()]
        );

        // 10. Create exam
        $exam = Exam::firstOrCreate(
            ['school_id' => $school->id, 'class_id' => $class->id, 'section_id' => $section->id, 'subject_id' => $subject->id, 'title' => 'Math Midterm'],
            ['exam_date' => now()->addDays(10)]
        );

        // 11. Enter grades for each student
        foreach ($studentIds as $sid) {
            Grade::firstOrCreate(
                ['student_id' => $sid, 'exam_id' => $exam->id],
                [
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'class_id' => $class->id,
                    'section_id' => $section->id,
                    'school_id' => $school->id,
                    'marks_obtained' => rand(50, 100),
                    'total_marks' => 100,
                    'percentage' => rand(50, 100),
                    'grade_letter' => 'A',
                    'comments' => 'Good performance',
                    'graded_at' => now(),
                ]
            );
        }

        // 12. Attendance records
        foreach ($studentIds as $sid) {
            Attendance::firstOrCreate(
                ['student_id' => $sid, 'class_id' => $class->id, 'section_id' => $section->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'school_id' => $school->id, 'date' => now()->toDateString()],
                ['status' => 'present', 'marked_by' => $teacherUser->id]
            );
        }
    }
}
