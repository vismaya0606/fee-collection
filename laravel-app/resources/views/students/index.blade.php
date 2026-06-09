@extends('layouts.app')
@section('title', 'Students')
@section('page-title', 'Students')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        <i class="bi bi-person-plus-fill me-2"></i>Add Student
    </button>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, parent, phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="class" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls }}" {{ request('class') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Search
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <span><i class="bi bi-people-fill me-2"></i>Students ({{ $students->total() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">
                        <a href="{{ request()->fullUrlWithQuery(['sort'=>'name','dir'=> request('sort')=='name' && request('dir')=='asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                            Name <i class="bi bi-arrow-down-up"></i>
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort'=>'class','dir'=> request('sort')=='class' && request('dir')=='asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                            Class <i class="bi bi-arrow-down-up"></i>
                        </a>
                    </th>
                    <th>Parent</th>
                    <th>Phone</th>
                    <th>Monthly Fee</th>
                    <th>Admission</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $student->name }}</td>
                    <td>{{ $student->class }}{{ $student->section ? ' - '.$student->section : '' }}</td>
                    <td>{{ $student->parent_name }}</td>
                    <td>{{ $student->parent_phone }}</td>
                    <td>₹{{ number_format($student->monthly_fee) }}</td>
                    <td class="text-muted small">{{ $student->admission_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $student->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Delete student {{ addslashes($student->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }} of {{ $students->total() }}</small>
        {{ $students->links() }}
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('students.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <input type="text" name="class" class="form-control" required value="{{ old('class') }}" placeholder="e.g. Grade 5">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control" value="{{ old('section') }}" placeholder="A, B, C...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Name <span class="text-danger">*</span></label>
                            <input type="text" name="parent_name" class="form-control" required value="{{ old('parent_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Phone <span class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" class="form-control" required value="{{ old('parent_phone') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monthly Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="monthly_fee" class="form-control" required min="0" step="0.01" value="{{ old('monthly_fee', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" class="form-control" required value="{{ old('admission_date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
