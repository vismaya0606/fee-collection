@extends('layouts.app')
@section('title', 'Teacher Salaries')
@section('page-title', 'Teacher Salaries')

@section('content')
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach($months as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-filter me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-hourglass-split me-2 text-warning"></i>
                Pending Salary — {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Teacher</th>
                            <th>Subject</th>
                            <th>Salary</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingTeachers as $teacher)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $teacher->name }}</td>
                            <td class="text-muted small">{{ $teacher->subject }}</td>
                            <td>₹{{ number_format($teacher->salary) }}</td>
                            <td>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                    data-bs-target="#paySalaryModal"
                                    data-teacher-id="{{ $teacher->id }}"
                                    data-teacher-name="{{ $teacher->name }}"
                                    data-amount="{{ $teacher->salary }}">
                                    Pay
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-success py-3">
                                <i class="bi bi-check-circle-fill d-block fs-4 mb-1"></i>
                                All salaries paid!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-check-circle-fill me-2 text-success"></i>
                Paid Salaries — {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
                <span class="float-end text-success fw-bold">
                    Total: ₹{{ number_format($paidSalaries->sum('amount')) }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Teacher</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Paid By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paidSalaries as $salary)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $salary->teacher->name ?? 'N/A' }}</td>
                            <td class="text-success fw-bold">₹{{ number_format($salary->amount) }}</td>
                            <td class="text-muted small">{{ $salary->payment_date->format('d M Y') }}</td>
                            <td class="text-muted small">{{ $salary->paidByUser->name ?? '-' }}</td>
                            <td>
                                <form action="{{ route('salaries.destroy', $salary) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete salary record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No salary payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pay Salary Modal -->
<div class="modal fade" id="paySalaryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-wallet2 me-2"></i>Pay Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('salaries.store') }}" method="POST">
                @csrf
                <input type="hidden" name="teacher_id" id="payTeacherId">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3">
                        Paying salary for: <strong id="payTeacherName"></strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="payTeacherAmount" class="form-control" required min="1" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Pay Salary
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('paySalaryModal').addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget;
    document.getElementById('payTeacherId').value = btn.dataset.teacherId;
    document.getElementById('payTeacherName').textContent = btn.dataset.teacherName;
    document.getElementById('payTeacherAmount').value = btn.dataset.amount;
});
</script>
@endpush
@endsection
