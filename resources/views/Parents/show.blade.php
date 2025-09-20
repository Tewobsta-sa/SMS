@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Parent Details</h1>
        <div>
            <a href="{{ route('parents.edit', $parent) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('parents.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $parent->user->name }}</p>
                    <p><strong>Email:</strong> {{ $parent->user->email }}</p>
                    <p><strong>Phone:</strong> {{ $parent->user->phone }}</p>
                    <p><strong>Address:</strong> {{ $parent->user->address }}</p>
                    <p><strong>Occupation:</strong> {{ $parent->occupation }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Relation:</strong> {{ $parent->relation }}</p>
                    <p><strong>User ID:</strong> {{ $parent->user_id }}</p>
                    <p><strong>Parent ID:</strong> {{ $parent->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Students</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parent->students as $student)
                            <tr>
                                <td>{{ $student->id }}</td>
                                <td>{{ $student->user->name }}</td>
                                <td>{{ $student->user->email }}</td>
                                <td>{{ optional($student->class)->name }}</td>
                                <td>{{ optional($student->section)->name }}</td>
                                <td>
                                    @if(Route::has('students.show'))
                                        <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4">No students linked.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


