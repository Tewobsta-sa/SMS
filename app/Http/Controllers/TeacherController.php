<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->paginate(20);
        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        $users = User::role('teacher')
            ->whereDoesntHave('teacher')
            ->get();
        return view('teachers.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'employee_no'      => 'nullable|string|unique:teachers,employee_no',
            'hire_date'        => 'nullable|date',
            'department'       => 'nullable|string',
            'specialization'   => 'nullable|string',
            'qualifications'   => 'nullable|string',
            'workload'         => 'nullable|string',
            'workload_hours'   => 'nullable|numeric',
            'experience_years' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['user_id']);
            Teacher::create([
                'school_id'        => $user->school_id,
                'user_id'          => $user->id,
                'employee_no'      => $data['employee_no'] ?? null,
                'hire_date'        => $data['hire_date'] ?? null,
                'department'       => $data['department'] ?? null,
                'specialization'   => $data['specialization'] ?? null,
                'qualifications'   => $data['qualifications'] ?? null,
                'workload'         => $data['workload'] ?? null,
                'workload_hours'   => $data['workload_hours'] ?? null,
                'experience_years' => $data['experience_years'] ?? null,
            ]);
        });

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load('user');
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        $eligibleUsers = User::where(function($q){
                $q->role('teacher')->whereDoesntHave('teacher');
            })
            ->orWhere('id', $teacher->user_id)
            ->get();
        return view('teachers.edit', compact('teacher','eligibleUsers'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'user_id'          => 'nullable|exists:users,id',
            'employee_no'      => 'nullable|string|unique:teachers,employee_no,' . $teacher->id,
            'hire_date'        => 'nullable|date',
            'department'       => 'nullable|string',
            'specialization'   => 'nullable|string',
            'qualifications'   => 'nullable|string',
            'workload'         => 'nullable|string',
            'workload_hours'   => 'nullable|numeric',
            'experience_years' => 'nullable|numeric',
        ]);

        $teacher->update([
            'user_id'          => $data['user_id'] ?? $teacher->user_id,
            'employee_no'      => $data['employee_no'] ?? $teacher->employee_no,
            'hire_date'        => $data['hire_date'] ?? $teacher->hire_date,
            'department'       => $data['department'] ?? $teacher->department,
            'specialization'   => $data['specialization'] ?? $teacher->specialization,
            'qualifications'   => $data['qualifications'] ?? $teacher->qualifications,
            'workload'         => $data['workload'] ?? $teacher->workload,
            'workload_hours'   => $data['workload_hours'] ?? $teacher->workload_hours,
            'experience_years' => $data['experience_years'] ?? $teacher->experience_years,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }
}
