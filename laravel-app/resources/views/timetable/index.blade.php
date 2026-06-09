@extends('layouts.app')
@section('title', 'Timetable')
@section('page-title', 'Class Timetable')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEntryModal">
        <i class="bi bi-plus-lg me-2"></i>Add Entry
    </button>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="class" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls }}" {{ $filterClass == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="section" class="form-control form-control-sm" placeholder="Section..." value="{{ $filterSection }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-filter me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('timetable.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

@foreach($days as $day)
@if($grouped[$day]->count() > 0)
<div class="card mb-3">
    <div class="card-header py-2" style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;">
        <strong>{{ $day }}</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Period</th>
                    <th>Time</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped[$day] as $entry)
                <tr>
                    <td class="ps-3 fw-bold">P{{ $entry->period }}</td>
                    <td class="text-muted small">{{ substr($entry->start_time, 0, 5) }} – {{ substr($entry->end_time, 0, 5) }}</td>
                    <td>{{ $entry->class }}{{ $entry->section ? '-'.$entry->section : '' }}</td>
                    <td class="fw-semibold">{{ $entry->subject }}</td>
                    <td class="text-muted">{{ $entry->teacher->name ?? '-' }}</td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                            data-bs-target="#editEntryModal{{ $entry->id }}">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <form action="{{ route('timetable.destroy', $entry) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Delete this entry?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach

@if(collect($grouped)->flatten()->count() == 0)
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-calendar3 fs-1 d-block mb-3"></i>
        No timetable entries yet. Click "Add Entry" to start.
    </div>
</div>
@endif

<!-- Add Entry Modal -->
<div class="modal fade" id="addEntryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Timetable Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('timetable.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <input type="text" name="class" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Day <span class="text-danger">*</span></label>
                            <select name="day" class="form-select" required>
                                @foreach($days as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Period <span class="text-danger">*</span></label>
                            <input type="number" name="period" class="form-control" required min="1" max="10">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">No teacher assigned</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->subject }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Entry Modals -->
@foreach(collect($grouped)->flatten() as $entry)
<div class="modal fade" id="editEntryModal{{ $entry->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Timetable Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('timetable.update', $entry) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <input type="text" name="class" class="form-control" required value="{{ $entry->class }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control" value="{{ $entry->section }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Day <span class="text-danger">*</span></label>
                            <select name="day" class="form-select" required>
                                @foreach($days as $d)
                                    <option value="{{ $d }}" {{ $entry->day == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Period <span class="text-danger">*</span></label>
                            <input type="number" name="period" class="form-control" required min="1" max="10" value="{{ $entry->period }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" required value="{{ $entry->subject }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">No teacher assigned</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ $entry->teacher_id == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" required value="{{ substr($entry->start_time, 0, 5) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" required value="{{ substr($entry->end_time, 0, 5) }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
