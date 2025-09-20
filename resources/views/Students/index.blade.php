@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Students</h1>

    <a href="{{ route('students.create') }}" class="btn btn-primary mb-3">Add New Student</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Class</th>
                <th>Section</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td>{{ $student->user->name }}</td>
                <td>{{ $student->user->email }}</td>
                <td>{{ $student->class->name ?? '-' }}</td>
                <td>{{ $student->section->name ?? '-' }}</td>
                <td>{{ ucfirst($student->status) }}</td>
                <td>
                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('students.destroy', $student) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No students found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $students->links() }}
</div>
@endsection
