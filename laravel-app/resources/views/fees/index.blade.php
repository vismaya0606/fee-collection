@extends('layouts.app')
@section('title', 'Fee Collection')
@section('page-title', 'Fee Collection')

@section('content')
<!-- Month/Year Filter -->
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
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search student..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-filter me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-start border-4 border-danger">
            <div class="card-body py-2">
                <div class="text-muted small">Pending</div>
                <div class="h4 fw-bold text-danger mb-0">{{ $pendingStudents->total() }} students</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-4 border-success">
            <div class="card-body py-2">
                <div class="text-muted small">Paid</div>
                <div class="h4 fw-bold text-success mb-0">{{ $paidStudents->total() }} students</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-4 border-primary">
            <div class="card-body py-2">
                <div class="text-muted small">Period</div>
                <div class="h5 fw-bold text-primary mb-0">{{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-3" id="feeTabs">
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'pending' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}">
            <i class="bi bi-hourglass-split me-1 text-danger"></i>
            Pending <span class="badge bg-danger ms-1">{{ $pendingStudents->total() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'paid' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'paid']) }}">
            <i class="bi bi-check-circle-fill me-1 text-success"></i>
            Paid <span class="badge bg-success ms-1">{{ $paidStudents->total() }}</span>
        </a>
    </li>
</ul>

@if($tab == 'pending')
<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-clock-history me-2 text-danger"></i>Pending Fee Students
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Student</th>
                    <th>Class</th>
                    <th>Parent Phone</th>
                    <th>Monthly Fee</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingStudents as $student)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $student->name }}</td>
                    <td>{{ $student->class }}{{ $student->section ? '-'.$student->section : '' }}</td>
                    <td>{{ $student->parent_phone }}</td>
                    <td>₹{{ number_format($student->monthly_fee) }}</td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                            data-bs-target="#payFeeModal"
                            data-student-id="{{ $student->id }}"
                            data-student-name="{{ $student->name }}"
                            data-amount="{{ $student->monthly_fee }}">
                            <i class="bi bi-cash me-1"></i>Collect Fee
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-success py-4">
                        <i class="bi bi-check-circle-fill fs-3 d-block mb-2"></i>
                        All students have paid for {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pendingStudents->hasPages())
    <div class="card-footer">{{ $pendingStudents->links() }}</div>
    @endif
</div>
@else
<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-check-circle-fill me-2 text-success"></i>Paid Students
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Student</th>
                    <th>Class</th>
                    <th>Amount</th>
                    <th>Receipt No</th>
                    <th>Payment Mode</th>
                    <th>Date</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paidStudents as $student)
                @php $payment = $student->feePayments->first(); @endphp
                <tr>
                    <td class="ps-3 fw-semibold">{{ $student->name }}</td>
                    <td>{{ $student->class }}{{ $student->section ? '-'.$student->section : '' }}</td>
                    <td class="text-success fw-bold">₹{{ $payment ? number_format($payment->amount) : '-' }}</td>
                    <td>
                        @if($payment)
                        <span class="badge bg-light text-dark">{{ $payment->receipt_no }}</span>
                        @endif
                    </td>
                    <td>
                        @if($payment)
                        <span class="badge {{ $payment->payment_mode == 'cash' ? 'bg-success' : ($payment->payment_mode == 'online' ? 'bg-primary' : 'bg-info') }}">
                            {{ ucfirst($payment->payment_mode) }}
                        </span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $payment ? $payment->payment_date->format('d M Y') : '-' }}</td>
                    <td class="text-end pe-3">
                        @if($payment)
                        <a href="{{ route('fees.receipt', $payment) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-printer"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('fees.destroy', $payment) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Delete this payment record?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No paid students yet for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($paidStudents->hasPages())
    <div class="card-footer">{{ $paidStudents->links() }}</div>
    @endif
</div>
@endif

<!-- Pay Fee Modal -->
<div class="modal fade" id="payFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Collect Fee Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('fees.store') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" id="payStudentId">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3">
                        Collecting fee for: <strong id="payStudentName"></strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="payAmount" class="form-control" required min="1" step="0.01">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('payFeeModal').addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget;
    document.getElementById('payStudentId').value = btn.dataset.studentId;
    document.getElementById('payStudentName').textContent = btn.dataset.studentName;
    document.getElementById('payAmount').value = btn.dataset.amount;
});
</script>
@endpush
@endsection
