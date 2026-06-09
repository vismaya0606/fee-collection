@extends('layouts.app')
@section('title', 'Edit Student')
@section('page-title', 'Edit Student')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-pencil-fill me-2"></i>Edit Student: {{ $student->name }}
            </div>
            <div class="card-body">
                <form action="{{ route('students.update', $student) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                required value="{{ old('name', $student->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <input type="text" name="class" class="form-control @error('class') is-invalid @enderror"
                                required value="{{ old('class', $student->class) }}">
                            @error('class')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control" value="{{ old('section', $student->section) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Name <span class="text-danger">*</span></label>
                            <input type="text" name="parent_name" class="form-control @error('parent_name') is-invalid @enderror"
                                required value="{{ old('parent_name', $student->parent_name) }}">
                            @error('parent_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Phone <span class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" class="form-control @error('parent_phone') is-invalid @enderror"
                                required value="{{ old('parent_phone', $student->parent_phone) }}">
                            @error('parent_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monthly Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="monthly_fee" class="form-control @error('monthly_fee') is-invalid @enderror"
                                required min="0" step="0.01" value="{{ old('monthly_fee', $student->monthly_fee) }}">
                            @error('monthly_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" class="form-control @error('admission_date') is-invalid @enderror"
                                required value="{{ old('admission_date', $student->admission_date->format('Y-m-d')) }}">
                            @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $student->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Update Student
                        </button>
                        <a href="{{ route('students.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
