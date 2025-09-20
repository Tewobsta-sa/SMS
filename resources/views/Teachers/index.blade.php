@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Teachers</h1>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary">Add Teacher</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Employee No</th>
                            <th>Department</th>
                            <th>Hire Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td>{{ $teacher->id }}</td>
                                <td>{{ optional($teacher->user)->name }}</td>
                                <td>{{ optional($teacher->user)->email }}</td>
                                <td>{{ $teacher->employee_no }}</td>
                                <td>{{ $teacher->department }}</td>
                                <td>{{ $teacher->hire_date }}</td>
                                <td class="text-end">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this teacher?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4">No teachers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $teachers->links() }}
    </div>
</div>
@endsection


