<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user','parents'])->paginate(20);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        // Get users with student role that are not assigned to students yet
        $users = User::role('student')
                    ->whereDoesntHave('student')
                    ->get();

        $parents = ParentModel::with('user')->get();
        $classes = \App\Models\SchoolClass::all();
        $sections = \App\Models\Section::all();

        return view('students.create', compact('users', 'parents', 'classes', 'sections'));
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'             => 'required|exists:users,id',
            'registration_no'     => 'nullable|string|unique:students,registration_no',
            'admission_no'        => 'nullable|string|unique:students,admission_no',
            'class_id'            => 'nullable|exists:school_classes,id',
            'section_id'          => 'nullable|exists:sections,id',
            'date_of_birth'       => 'nullable|date',
            'gender'              => 'nullable|in:male,female,other',
            'health_info'         => 'nullable|string',
            'family_info'         => 'nullable|string',
            'transfer_history'    => 'nullable|string',
            'immunization'        => 'nullable|string',
            'immunization_record' => 'nullable|string',
            'parent_ids'          => 'nullable|array',
            'parent_ids.*'        => 'exists:parents,id',
        ]);

        $user = User::findOrFail($data['user_id']);

        DB::transaction(function () use ($data, $user) {
            $student = Student::create([
                'school_id'          => $user->school_id,
                'user_id'            => $user->id,
                'registration_no'    => $data['registration_no'] ?? null,
                'admission_no'       => $data['admission_no'] ?? null,
                'class_id'           => $data['class_id'] ?? null,
                'section_id'         => $data['section_id'] ?? null,
                'date_of_birth'      => $data['date_of_birth'] ?? null,
                'gender'             => $data['gender'] ?? null,
                'health_info'        => $data['health_info'] ?? null,
                'family_info'        => $data['family_info'] ?? null,
                'transfer_history'   => $data['transfer_history'] ?? null,
                'immunization'       => $data['immunization'] ?? null,
                'immunization_record'=> $data['immunization_record'] ?? null,
                'enrollment_date'    => now(),
            ]);

            if (!empty($data['parent_ids'])) {
                $student->parents()->attach($data['parent_ids']);
            }
        });

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }



    public function show(Student $student)
    {
        $student->load('user','parents.user','class','section');
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $parents = ParentModel::with('user')->get();
        $classes = \App\Models\SchoolClass::all();
        $sections = \App\Models\Section::all();
        $eligibleUsers = User::whereDoesntHave('student')
            ->orWhere('id', $student->user_id) // allow current
            ->get();
        $student->load('parents');
        return view('students.edit', compact('student','parents','classes','sections','eligibleUsers'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'user_id'            => ['nullable','exists:users,id'],
            'registration_no'    => ['nullable','string', Rule::unique('students','registration_no')->ignore($student->id)],
            'admission_no'       => ['nullable','string', Rule::unique('students','admission_no')->ignore($student->id)],
            'class_id'           => 'nullable|exists:school_classes,id',
            'section_id'         => 'nullable|exists:sections,id',
            'date_of_birth'      => 'nullable|date',
            'gender'             => 'nullable|in:male,female,other',
            'health_info'        => 'nullable|string',
            'family_info'        => 'nullable|string',
            'transfer_history'   => 'nullable|string',
            'immunization'       => 'nullable|string',
            'immunization_record'=> 'nullable|string',
            'parent_ids'         => 'nullable|array',
            'parent_ids.*'       => 'exists:parents,id',
        ]);

        DB::transaction(function () use ($data, $student) {
            // update student
            $student->update([
                'user_id'            => $data['user_id'] ?? $student->user_id,
                'registration_no'    => $data['registration_no'] ?? $student->registration_no,
                'admission_no'       => $data['admission_no'] ?? $student->admission_no,
                'class_id'           => $data['class_id'] ?? $student->class_id,
                'section_id'         => $data['section_id'] ?? $student->section_id,
                'date_of_birth'      => $data['date_of_birth'] ?? $student->date_of_birth,
                'gender'             => $data['gender'] ?? $student->gender,
                'health_info'        => $data['health_info'] ?? $student->health_info,
                'family_info'        => $data['family_info'] ?? $student->family_info,
                'transfer_history'   => $data['transfer_history'] ?? $student->transfer_history,
                'immunization'       => $data['immunization'] ?? $student->immunization,
                'immunization_record'=> $data['immunization_record'] ?? $student->immunization_record,
            ]);

            $student->parents()->sync($data['parent_ids'] ?? []);
        });

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }



    public function destroy(Student $student)
    {
        // deleting student will cascade delete or detach - be careful
        $student->delete();
        return redirect()->route('students.index')->with('success','Student removed.');
    }
}
