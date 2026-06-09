@extends('layouts.app')
@section('title', 'Inquiries')
@section('page-title', 'Student Inquiries')

@section('content')
@if($followUps->count() > 0)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-calendar-event-fill fs-5"></i>
    <div>
        <strong>Follow-up Due!</strong> {{ $followUps->count() }} inquiry follow-up{{ $followUps->count() > 1 ? 's are' : ' is' }} overdue:
        @foreach($followUps->take(3) as $fu)
            <span class="badge bg-warning text-dark ms-1">{{ $fu->name }}</span>
        @endforeach
    </div>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInquiryModal">
        <i class="bi bi-person-plus-fill me-2"></i>Add Inquiry
    </button>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('inquiries.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-person-plus-fill me-2"></i>Inquiries ({{ $inquiries->total() }})
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Name</th>
                    <th>Class</th>
                    <th>Phone</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Follow-up</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inquiry)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $inquiry->name }}</td>
                    <td>{{ $inquiry->class }}</td>
                    <td>{{ $inquiry->parent_phone }}</td>
                    <td class="text-muted small">{{ $inquiry->source ?? '-' }}</td>
                    <td>
                        @php
                            $statusColors = [
                                'new' => 'bg-info',
                                'follow-up' => 'bg-warning text-dark',
                                'interested' => 'bg-success',
                                'not-interested' => 'bg-secondary',
                                'converted' => 'bg-primary',
                            ];
                        @endphp
                        <span class="badge {{ $statusColors[$inquiry->status] ?? 'bg-secondary' }}">
                            {{ ucfirst(str_replace('-', ' ', $inquiry->status)) }}
                        </span>
                    </td>
                    <td class="text-muted small">
                        @if($inquiry->follow_up_date)
                            <span class="{{ $inquiry->follow_up_date->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ $inquiry->follow_up_date->format('d M Y') }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        @if($inquiry->status !== 'converted')
                        <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal"
                            data-bs-target="#editInquiryModal{{ $inquiry->id }}">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal"
                            data-bs-target="#convertModal{{ $inquiry->id }}">
                            <i class="bi bi-person-check-fill"></i>
                        </button>
                        @else
                        <span class="badge bg-light text-success me-1">Converted</span>
                        @endif
                        <form action="{{ route('inquiries.destroy', $inquiry) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Delete this inquiry?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No inquiries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $inquiries->firstItem() ?? 0 }}–{{ $inquiries->lastItem() ?? 0 }} of {{ $inquiries->total() }}</small>
        {{ $inquiries->links() }}
    </div>
</div>

<!-- Add Inquiry Modal -->
<div class="modal fade" id="addInquiryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inquiries.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class Interested <span class="text-danger">*</span></label>
                            <input type="text" name="class" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Student Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Phone <span class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Source</label>
                            <select name="source" class="form-select">
                                <option value="">Select source...</option>
                                @foreach($sources as $src)
                                    <option value="{{ $src }}">{{ $src }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach($statuses as $s)
                                    @if($s !== 'converted')
                                    <option value="{{ $s }}">{{ ucfirst(str_replace('-', ' ', $s)) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Follow-up Date</label>
                            <input type="date" name="follow_up_date" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit & Convert Modals for each inquiry -->
@foreach($inquiries as $inquiry)
<!-- Edit Modal -->
<div class="modal fade" id="editInquiryModal{{ $inquiry->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inquiries.update', $inquiry) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ $inquiry->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <input type="text" name="class" class="form-control" required value="{{ $inquiry->class }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $inquiry->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Phone <span class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" class="form-control" required value="{{ $inquiry->parent_phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Source</label>
                            <select name="source" class="form-select">
                                <option value="">Select...</option>
                                @foreach($sources as $src)
                                    <option value="{{ $src }}" {{ $inquiry->source == $src ? 'selected' : '' }}>{{ $src }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}" {{ $inquiry->status == $s ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('-', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Follow-up Date</label>
                            <input type="date" name="follow_up_date" class="form-control" value="{{ $inquiry->follow_up_date?->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ $inquiry->notes }}</textarea>
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

<!-- Convert Modal -->
<div class="modal fade" id="convertModal{{ $inquiry->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-check-fill me-2"></i>Convert to Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inquiries.convert', $inquiry) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        Converting <strong>{{ $inquiry->name }}</strong> to a student
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Name <span class="text-danger">*</span></label>
                        <input type="text" name="parent_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Monthly Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="monthly_fee" class="form-control" required min="0" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Convert to Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
