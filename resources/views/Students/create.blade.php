@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Student</h1>

    <form action="{{ route('students.store') }}" method="POST">
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
            <!-- Select Existing User -->
            <div class="col-md-12 mb-3">
                <label>Student User</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Select Student User</option>
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

            <!-- Registration -->
            <div class="col-md-6 mb-3">
                <label>Registration No</label>
                <input type="text" name="registration_no" class="form-control" value="{{ old('registration_no') }}">
            </div>

            <!-- Admission -->
            <div class="col-md-6 mb-3">
                <label>Admission No</label>
                <input type="text" name="admission_no" class="form-control" value="{{ old('admission_no') }}">
            </div>

            <!-- Date of Birth -->
            <div class="col-md-6 mb-3">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
            </div>

            <!-- Gender -->
            <div class="col-md-6 mb-3">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                    <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                    <option value="other" {{ old('gender')=='other'?'selected':'' }}>Other</option>
                </select>
            </div>

            <!-- Class -->
            <div class="col-md-6 mb-3">
                <label>Class</label>
                <select name="class_id" class="form-control">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id')==$class->id?'selected':'' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Section -->
            <div class="col-md-6 mb-3">
                <label>Section</label>
                <select name="section_id" class="form-control">
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id')==$section->id?'selected':'' }}>
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
                        <option value="{{ $parent->id }}" {{ in_array($parent->id, old('parent_ids', [])) ? 'selected' : '' }}>
                            {{ $parent->user->name }} ({{ $parent->user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Additional info -->
            <div class="col-md-12 mb-3">
                <label>Health Info</label>
                <textarea name="health_info" class="form-control">{{ old('health_info') }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Family Info</label>
                <textarea name="family_info" class="form-control">{{ old('family_info') }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Transfer History</label>
                <textarea name="transfer_history" class="form-control">{{ old('transfer_history') }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Immunization</label>
                <textarea name="immunization" class="form-control">{{ old('immunization') }}</textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>Immunization Record</label>
                <textarea name="immunization_record" class="form-control">{{ old('immunization_record') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create Student</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
