@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Student Details</h1>

    <ul>
        <li><strong>Name:</strong> {{ $student->user->name }}</li>
        <li><strong>Email:</strong> {{ $student->user->email }}</li>
        <li><strong>Phone:</strong> {{ $student->user->phone ?? '-' }}</li>
        <li><strong>Address:</strong> {{ $student->user->address ?? '-' }}</li>
        <li><strong>Registration No:</strong> {{ $student->registration_no ?? '-' }}</li>
        <li><strong>Admission No:</strong> {{ $student->admission_no ?? '-' }}</li>
        <li><strong>Date of Birth:</strong> {{ $student->date_of_birth ?? '-' }}</li>
        <li><strong>Gender:</strong> {{ ucfirst($student->gender ?? '-') }}</li>
        <li><strong>Class / Section:</strong> {{ $student->class?->name ?? '-' }} / {{ $student->section?->name ?? '-' }}</li>
        <li><strong>School:</strong> {{ $student->school?->name ?? '-' }}</li>
        <li>
            <strong>Parents:</strong>
            <ul>
                @foreach($student->parents as $p)
                    <li>{{ $p->user->name }} ({{ $p->relation ?? '-' }})</li>
                @endforeach
            </ul>
        </li>
        <li><strong>Health Info:</strong> {{ $student->health_info ?? '-' }}</li>
        <li><strong>Family Info:</strong> {{ $student->family_info ?? '-' }}</li>
        <li><strong>Transfer History:</strong> {{ $student->transfer_history ?? '-' }}</li>
        <li><strong>Immunization:</strong> {{ $student->immunization ?? '-' }}</li>
        <li><strong>Immunization Record:</strong> {{ $student->immunization_record ?? '-' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($student->status) }}</li>
        <li><strong>Registration Status:</strong> {{ ucfirst($student->registration_status) }}</li>
        <li><strong>Enrollment Date:</strong> {{ $student->enrollment_date ?? '-' }}</li>
        <li><strong>Created At:</strong> {{ $student->created_at }}</li>
        <li><strong>Updated At:</strong> {{ $student->updated_at }}</li>
    </ul>

    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('students.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
