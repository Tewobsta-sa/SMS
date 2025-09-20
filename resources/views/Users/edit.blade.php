@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Student</h1>

    <form action="{{ route('students.update', $student) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- User Name & Email -->
            <div class="col-md-6 mb-3">
                <label>Student Name</label>
                <input type="text" name="user_name" class="form-control" value="{{ old('user_name', $student->user->name) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="user_email" class="form-control" value="{{ old('user_email', $student->user->email) }}" required>
            </div>

            <!-- Registration & Admission -->
            <div class="col-md-6 mb-3">
                <label>Registration No</label>
                <input type="text" name="registration_no" class="form-control" value="{{ old('registration_no', $student->registration_no) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Admission No</label>
                <input type="text" name="admission_no" class="form-control" value="{{ old('admission_no', $student->admission_no) }}">
            </div>

            <!-- Class & Section -->
            <div class="col-md-6 mb-3">
                <label>Class</label>
                <select name="class_id" class="form-control">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $student->class_id == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>Section</label>
                <select name="section_id" class="form-control">
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ $student->section_id == $section->id ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Parents -->
            <div class="col-md-12 mb-3">
                <label>Parents</label>
                <select name="parent_ids[]" class="form-control" multiple>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}"
                            {{ in_array($parent->id, $student->parents->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $parent->user->name }} ({{ $parent->user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Additional info -->
            <div class="col-md-12 mb-3">
                <label>Health Info</label>
                <textarea name="health_info" class="form-control">{{ old('health_info', $student->health_info) }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Family Info</label>
                <textarea name="family_info" class="form-control">{{ old('family_info', $student->family_info) }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Transfer History</label>
                <textarea name="transfer_history" class="form-control">{{ old('transfer_history', $student->transfer_history) }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Immunization</label>
                <textarea name="immunization" class="form-control">{{ old('immunization', $student->immunization) }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Immunization Record</label>
                <textarea name="immunization_record" class="form-control">{{ old('immunization_record', $student->immunization_record) }}</textarea>
            </div>

        </div>

        <button class="btn btn-primary">Update Student</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
