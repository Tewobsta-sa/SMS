<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Assignment;
use App\Models\Announcement;
use App\Models\Subject;
use App\Models\Teacher;
use Spatie\Permission\Models\Role;

class TestData1 extends Seeder
{
    public function run(): void
    {
        // 1. Create roles
        foreach (['parent','teacher','student'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Create school
        $school = School::firstOrCreate(
            ['code' => 'SCH001'],
            ['name' => 'Test School', 'address' => '123 School St']
        );

        // 3. Create subjects
        $subject = Subject::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Mathematics']
        );

        // 4. Create teacher
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'school_id' => $school->id,
                'name' => 'Mr. Smith',
                'password' => Hash::make('password'),
                'role' => 'teacher',
            ]
        );
        $teacherUser->assignRole('teacher');

        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id, 'school_id' => $school->id]
        );

        // 5. Create parent
        $parentUser = User::firstOrCreate(
            ['email' => 'parent@example.com'],
            [
                'school_id' => $school->id,
                'name' => 'John Parent',
                'password' => Hash::make('password'),
                'role' => 'parent',
            ]
        );
        $parentUser->assignRole('parent');

        $parent = ParentModel::firstOrCreate(
            ['user_id' => $parentUser->id, 'school_id' => $school->id],
            [
                'occupation' => 'Engineer',
                'relation' => 'Father',
            ]
        );

        // 6. Create students
        $studentUsers = [
            ['name' => 'Alice Doe', 'email' => 'alice@student.com', 'gender'=>'female', 'dob'=>'2010-05-15'],
            ['name' => 'Bob Doe', 'email' => 'bob@student.com', 'gender'=>'male', 'dob'=>'2011-07-20']
        ];

        $students = [];
        foreach ($studentUsers as $s) {
            $user = User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'school_id' => $school->id,
                    'name' => $s['name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                ]
            );
            $user->assignRole('student');

            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'school_id' => $school->id,
                    'registration_no' => strtoupper(substr($s['name'],0,3)).rand(100,999),
                    'date_of_birth' => $s['dob'],
                    'gender' => $s['gender'],
                ]
            );
            $students[] = $student;
        }

        // 7. Attach students to parent
        $parent->students()->syncWithoutDetaching(array_column($students, 'id'));

        // 8. Create class & section
        $class = SchoolClass::firstOrCreate(
            ['school_id'=>$school->id, 'name'=>'Grade 5', 'code'=>'G5', 'academic_year'=>'2025']
        );

        $section = Section::firstOrCreate(
            ['school_id'=>$school->id, 'class_id'=>$class->id, 'name'=>'Section A']
        );

        // 9. Enroll students
        foreach ($students as $student) {
            Enrollment::firstOrCreate([
                'student_id'=>$student->id,
                'class_id'=>$class->id,
                'section_id'=>$section->id,
                'school_id'=>$school->id
            ], ['active'=>true]);
        }

        // 10. Attendance
        foreach ($students as $student) {
            Attendance::firstOrCreate([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'school_id' => $school->id,
                'date' => now()->subDay()->toDateString(),
            ], [
                'status' => 'present',
                'remarks' => null,
                'marked_by' => $teacherUser->id
            ]);
        }

        // 11. Exam & Grades
        $exam = Exam::firstOrCreate([
            'school_id'=>$school->id,
            'class_id'=>$class->id,
            'section_id'=>$section->id,
            'subject_id'=>$subject->id,
            'title'=>'Math Test',
            'exam_date'=>now()->toDateString()
        ]);

        foreach ($students as $student) {
            Grade::firstOrCreate([
                'student_id'=>$student->id,
                'exam_id'=>$exam->id,
                'subject_id'=>$subject->id,
                'teacher_id'=>$teacher->id,
                'class_id'=>$class->id,
                'section_id'=>$section->id,
                'school_id'=>$school->id
            ], [
                'marks_obtained'=>rand(60,95),
                'total_marks'=>100,
                'percentage'=>rand(60,95),
                'grade_letter'=>'A',
                'comments'=>'Good work',
                'graded_at'=>now()
            ]);
        }

        // 12. Assignment
        Assignment::firstOrCreate([
            'teacher_id'=>$teacher->id,
            'class_id'=>$class->id,
            'section_id'=>$section->id,
            'title'=>'Math Homework'
        ], [
            'description'=>'Complete exercises 1-10',
            'due_date'=>now()->addWeek()->toDateString()
        ]);

        // 13. Announcement
        Announcement::firstOrCreate([
            'teacher_id'=>$teacher->id,
            'class_id'=>$class->id,
            'section_id'=>$section->id,
            'title'=>'Parent Meeting'
        ], [
            'message'=>'Parent-teacher meeting scheduled next week.'
        ]);
    }
}
