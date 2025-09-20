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
use App\Models\Announcement;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Attendance;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a school
        $school = School::create([
            'name' => 'Edunova High School',
            'code' => 'EDU001',
            'address' => '123 Main Street',
            'contact_email' => 'info@edunova.com',
            'contact_phone' => '123456789',
        ]);

        // 2. Create teacher user
        $teacherUser = User::create([
            'name' => 'Mr. John Doe',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
        ]);

        // 3. Create teacher
        $teacher = Teacher::create([
            'school_id' => $school->id,
            'user_id' => $teacherUser->id,
            'employee_no' => 'EMP001',
            'department' => 'Mathematics',
            'specialization' => 'Algebra',
            'experience_years' => 5,
        ]);

        // 4. Create class
        $class = SchoolClass::create([
            'school_id' => $school->id,
            'name' => 'Grade 10',
            'code' => 'G10',
            'academic_year' => '2024-2025',
        ]);

        // 5. Create section
        $section = Section::create([
            'class_id' => $class->id,
            'school_id' => $school->id,
            'name' => 'A',
        ]);

        // 6. Create subject
        $subject = Subject::create([
            'school_id' => $school->id,
            'name' => 'Mathematics',
        ]);

        // 7. Assign teacher to subject, class, section
        TeacherSubject::create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'school_id' => $school->id,
        ]);

        // 8. Create student users + students
        for ($i = 1; $i <= 5; $i++) {
            $studentUser = User::create([
                'name' => "Student $i",
                'email' => "student$i@example.com",
                'password' => Hash::make('password'),
            ]);

            $student = Student::create([
                'school_id' => $school->id,
                'user_id' => $studentUser->id,
                'registration_no' => "REG00$i",
                'admission_no' => "ADM00$i",
                'class_id' => $class->id,
                'section_id' => $section->id,
                'status' => 'active',
                'registration_status' => 'approved',
            ]);

            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'school_id' => $school->id,
                'active' => true,
            ]);
        }

        // 9. Create an assignment
        Assignment::create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'title' => 'Math Homework 1',
            'description' => 'Solve Algebra problems',
            'due_date' => now()->addWeek(),
        ]);

        // 10. Create an announcement
        Announcement::create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'title' => 'Exam Reminder',
            'message' => 'Mid-term exams will start next week.',
        ]);

        // 11. Create exam + grades
        $exam = Exam::create([
            'school_id' => $school->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'title' => 'Math Midterm',
            'exam_date' => now()->addDays(10),
        ]);

        $studentIds = Student::pluck('id');
        foreach ($studentIds as $sid) {
            Grade::create([
                'student_id' => $sid,
                'exam_id' => $exam->id,
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
            ]);
        }

        // 12. Attendance records
        foreach ($studentIds as $sid) {
            Attendance::create([
                'student_id' => $sid,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'school_id' => $school->id,
                'date' => now(),
                'status' => 'present',
                'marked_by' => $teacherUser->id,
            ]);
        }
    }
}
