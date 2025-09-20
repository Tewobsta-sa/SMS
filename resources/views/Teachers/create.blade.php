@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Teacher</h1>

    <form action="{{ route('teachers.store') }}" method="POST">
        @csrf

        <div class="row">
            @if ($errors->any())
            <div class="col-12">
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="col-md-12 mb-3">
                <label>User</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id')==$user->id?'selected':'' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label>Employee No</label>
                <input type="text" name="employee_no" class="form-control" value="{{ old('employee_no') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Hire Date</label>
                <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Department</label>
                <input type="text" name="department" class="form-control" value="{{ old('department') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Specialization</label>
                <input type="text" name="specialization" class="form-control" value="{{ old('specialization') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Qualifications</label>
                <input type="text" name="qualifications" class="form-control" value="{{ old('qualifications') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Workload</label>
                <input type="text" name="workload" class="form-control" value="{{ old('workload') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Workload Hours</label>
                <input type="number" step="0.01" name="workload_hours" class="form-control" value="{{ old('workload_hours') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Experience Years</label>
                <input type="number" step="0.01" name="experience_years" class="form-control" value="{{ old('experience_years') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create Teacher</button>
        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection


