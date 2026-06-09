@extends('layouts.app')
@section('title', 'Teacher Attendance')
@section('page-title', 'Teacher Attendance')

@section('content')
<div class="row g-3 mb-3">
    <!-- Mark Daily Attendance -->
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-clipboard-check-fill me-2"></i>Mark Attendance
            </div>
            <div class="card-body">
                <form action="{{ route('attendance.bulk') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="markDate" class="form-control" required value="{{ $date }}">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Teacher</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachers as $teacher)
                                <tr>
                                    <td class="fw-semibold small">{{ $teacher->name }}</td>
                                    <td>
                                        <select name="attendance[{{ $teacher->id }}]" class="form-select form-select-sm">
                                            @foreach($statuses as $s)
                                            <option value="{{ $s }}"
                                                {{ ($todayAttendance[$teacher->id] ?? 'present') == $s ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i class="bi bi-save me-1"></i>Save Attendance
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-list-check me-2"></i>Attendance Records</span>
                </div>
                <form method="GET" class="row g-2 mt-1">
                    <div class="col-4">
                        <select name="month" class="form-select form-select-sm">
                            @foreach($months as $m)
                                <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>{{ date('M', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <select name="year" class="form-select form-select-sm">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Teacher</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Approved</th>
                            @if(auth()->user()->isAdmin())
                            <th></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceRecords as $record)
                        <tr>
                            <td class="ps-3 fw-semibold small">{{ $record->teacher->name ?? 'N/A' }}</td>
                            <td class="small">{{ $record->date->format('d M Y') }}</td>
                            <td>
                                @php
                                    $statusColors = ['present' => 'bg-success', 'late' => 'bg-warning text-dark', 'half_day' => 'bg-info', 'absent' => 'bg-danger'];
                                @endphp
                                <span class="badge {{ $statusColors[$record->status] ?? 'bg-secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($record->approved)
                                    <span class="badge bg-success"><i class="bi bi-check2"></i></span>
                                @else
                                    <span class="badge bg-light text-muted">Pending</span>
                                @endif
                            </td>
                            @if(auth()->user()->isAdmin())
                            <td>
                                @if(!$record->approved)
                                <form action="{{ route('attendance.approve', $record) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-outline-success" style="font-size:0.7rem;padding:0.1rem 0.4rem;">
                                        Approve
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($attendanceRecords->hasPages())
            <div class="card-footer">{{ $attendanceRecords->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
