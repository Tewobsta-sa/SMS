@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Parent</h1>

    <form action="{{ route('parents.update', $parent->id) }}" method="POST">
        @csrf
        @method('PUT')

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
                <select name="user_id" class="form-control">
                    <option value="{{ $parent->user_id }}">Current: {{ $parent->user->name }} ({{ $parent->user->email }})</option>
                    @foreach($eligibleUsers as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $parent->user_id)==$user->id?'selected':'' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label>Occupation</label>
                <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $parent->occupation) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Relation</label>
                <select name="relation" class="form-control">
                    <option value="">Select Relation</option>
                    @foreach(['Father','Mother','Guardian'] as $rel)
                        <option value="{{ $rel }}" {{ old('relation', $parent->relation)==$rel?'selected':'' }}>{{ $rel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Parent</button>
        <a href="{{ route('parents.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection


