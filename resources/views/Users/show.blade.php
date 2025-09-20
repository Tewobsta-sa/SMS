@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Student Details</h1>

    <a href="{{ route('students.edit', $student) }}" class="btn btn-warning mb-3">Edit Student</a>
    <a href="{{ route('students.index') }}" class="btn btn-secondary mb-3">Back to List</a>

    <table class="table table-bordered">
        <tr>
            <th>Name</th>
            <td>{{ $student->user->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $student->user->email }}</td>
        </tr>
        <tr>
            <th>Registration No</th>
            <td>{{ $student->registration_no }}</td>
        </tr>
        <tr>
            <th>Admission No</th>
            <td>{{ $student->admission_no }}</td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td>{{ $student->date_of_birth }}</td>
        </tr>
        <tr>
            <th>Gender</th>
            <td>{{ ucfirst($student->gender) }}</td>
        </tr>
        <tr>
            <th>Class</th>
            <td>{{ $student->class->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Section</th>
            <td>{{ $student->section->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Parents</th>
            <td>
                @foreach($student->parents as $parent)
                    {{ $parent->user->name }} ({{ $parent->user->email }})<br>
                @endforeach
            </td>
        </tr>
        <tr>
            <th>Health Info</th>
            <td>{{ $student->health_info }}</td>
        </tr>
        <tr>
            <th>Family Info</th>
            <td>{{ $student->family_info }}</td>
        </tr>
        <tr>
            <th>Transfer History</th>
            <td>{{ $student->transfer_history }}</td>
        </tr>
        <tr>
            <th>Immunization</th>
            <td>{{ $student->immunization }}</td>
        </tr>
        <tr>
            <th>Immunization Record</th>
            <td>{{ $student->immunization_record }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($student->status) }}</td>
        </tr>
        <tr>
            <th>Registration Status</th>
            <td>{{ ucfirst($student->registration_status) }}</td>
        </tr>
    </table>
</div>
@endsection
