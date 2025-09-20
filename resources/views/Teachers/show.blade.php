@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Teacher Details</h1>
        <div>
            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ optional($teacher->user)->name }}</p>
                    <p><strong>Email:</strong> {{ optional($teacher->user)->email }}</p>
                    <p><strong>Phone:</strong> {{ optional($teacher->user)->phone }}</p>
                    <p><strong>Address:</strong> {{ optional($teacher->user)->address }}</p>
                    <p><strong>Employee No:</strong> {{ $teacher->employee_no }}</p>
                    <p><strong>Department:</strong> {{ $teacher->department }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Hire Date:</strong> {{ $teacher->hire_date }}</p>
                    <p><strong>Specialization:</strong> {{ $teacher->specialization }}</p>
                    <p><strong>Qualifications:</strong> {{ $teacher->qualifications }}</p>
                    <p><strong>Experience Years:</strong> {{ $teacher->experience_years }}</p>
                    <p><strong>Workload:</strong> {{ $teacher->workload }}</p>
                    <p><strong>Workload Hours:</strong> {{ $teacher->workload_hours }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


