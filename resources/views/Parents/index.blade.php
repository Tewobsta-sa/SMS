@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Parents</h1>
        <a href="{{ route('parents.create') }}" class="btn btn-primary">Add Parent</a>
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
                            <th>Occupation</th>
                            <th>Relation</th>
                            <th>Students</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parents as $parent)
                            <tr>
                                <td>{{ $parent->id }}</td>
                                <td>{{ $parent->user->name }}</td>
                                <td>{{ $parent->user->email }}</td>
                                <td>{{ $parent->occupation }}</td>
                                <td>{{ $parent->relation }}</td>
                                <td>
                                    @if($parent->students && $parent->students->count())
                                        {{ $parent->students->pluck('user.name')->implode(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('parents.show', $parent) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('parents.edit', $parent) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('parents.destroy', $parent) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this parent?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4">No parents found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $parents->links() }}
    </div>
</div>
@endsection


