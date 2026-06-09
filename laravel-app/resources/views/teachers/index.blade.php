@extends('layouts.app')
@section('title', 'Teachers')
@section('page-title', 'Teachers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
        <i class="bi bi-person-plus-fill me-2"></i>Add Teacher
    </button>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, subject, phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('teachers.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-person-badge-fill me-2"></i>Teachers ({{ $teachers->total() }})
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Name</th>
                    <th>Subject</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Salary</th>
                    <th>Join Date</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $teacher->name }}</td>
                    <td>{{ $teacher->subject }}</td>
                    <td>{{ $teacher->phone }}</td>
                    <td class="text-muted small">{{ $teacher->email ?? '-' }}</td>
                    <td>₹{{ number_format($teacher->salary) }}</td>
                    <td class="text-muted small">{{ $teacher->join_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $teacher->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($teacher->status) }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Delete teacher {{ addslashes($teacher->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No teachers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $teachers->firstItem() ?? 0 }}–{{ $teachers->lastItem() ?? 0 }} of {{ $teachers->total() }}</small>
        {{ $teachers->links() }}
    </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New Teacher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('teachers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salary (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="salary" class="form-control" required min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Join Date <span class="text-danger">*</span></label>
                            <input type="date" name="join_date" class="form-control" required value="{{ date('Y-m-d') }}">
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
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
