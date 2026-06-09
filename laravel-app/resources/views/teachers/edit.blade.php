@extends('layouts.app')
@section('title', 'Edit Teacher')
@section('page-title', 'Edit Teacher')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-pencil-fill me-2"></i>Edit Teacher: {{ $teacher->name }}
            </div>
            <div class="card-body">
                <form action="{{ route('teachers.update', $teacher) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                required value="{{ old('name', $teacher->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                                required value="{{ old('subject', $teacher->subject) }}">
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                required value="{{ old('phone', $teacher->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->email) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salary (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror"
                                required min="0" step="0.01" value="{{ old('salary', $teacher->salary) }}">
                            @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Join Date <span class="text-danger">*</span></label>
                            <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror"
                                required value="{{ old('join_date', $teacher->join_date->format('Y-m-d')) }}">
                            @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $teacher->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $teacher->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Update Teacher
                        </button>
                        <a href="{{ route('teachers.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
