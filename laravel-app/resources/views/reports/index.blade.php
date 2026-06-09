@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Report Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="fees" {{ $reportType == 'fees' ? 'selected' : '' }}>Fee Collection</option>
                    <option value="expenses" {{ $reportType == 'expenses' ? 'selected' : '' }}>Expenses</option>
                    <option value="teachers" {{ $reportType == 'teachers' ? 'selected' : '' }}>Teachers</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach($months as $m)
                        <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-bar-chart-line me-1"></i>Generate
                </button>
            </div>
            <div class="col-md-2">
                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
        </form>
    </div>
</div>

@if($reportType == 'fees')
<!-- Fee Report -->
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card text-center" style="background:linear-gradient(135deg,#11998e,#38ef7d);color:white;">
            <div class="card-body py-2">
                <div class="small">Total Collected</div>
                <div class="h4 fw-bold mb-0">₹{{ number_format($data['total']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;">
            <div class="card-body py-2">
                <div class="small">Paid Count</div>
                <div class="h4 fw-bold mb-0">{{ $data['paid_count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center" style="background:linear-gradient(135deg,#f093fb,#f5576c);color:white;">
            <div class="card-body py-2">
                <div class="small">Pending Count</div>
                <div class="h4 fw-bold mb-0">{{ $data['pending_count'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body py-2">
                <div class="text-muted small">By Payment Mode</div>
                @foreach($data['by_mode'] as $mode => $amount)
                <div class="small"><span class="fw-bold">{{ ucfirst($mode) }}:</span> ₹{{ number_format($amount) }}</div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-cash-coin me-2"></i>
        Fee Collection Report — {{ date('F', mktime(0,0,0,$filterMonth,1)) }} {{ $filterYear }}
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Receipt No</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Date</th>
                    <th>Collected By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['payments'] as $payment)
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-dark">{{ $payment->receipt_no }}</span></td>
                    <td class="fw-semibold">{{ $payment->student->name ?? 'N/A' }}</td>
                    <td>{{ $payment->student->class ?? '-' }}</td>
                    <td class="text-success fw-bold">₹{{ number_format($payment->amount) }}</td>
                    <td><span class="badge bg-primary">{{ ucfirst($payment->payment_mode) }}</span></td>
                    <td class="text-muted small">{{ $payment->payment_date->format('d M Y') }}</td>
                    <td class="text-muted small">{{ $payment->collector->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@elseif($reportType == 'expenses')
<!-- Expense Report -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card text-center" style="background:linear-gradient(135deg,#f093fb,#f5576c);color:white;">
            <div class="card-body py-2">
                <div class="small">Total Expenses</div>
                <div class="h4 fw-bold mb-0">₹{{ number_format($data['total']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body py-2">
                <div class="text-muted small mb-1">By Category</div>
                <div class="row">
                    @foreach($data['by_category'] as $cat => $amount)
                    <div class="col-md-4 small">
                        <span class="fw-bold">{{ $cat }}:</span> ₹{{ number_format($amount) }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-receipt-cutoff me-2"></i>
        Expense Report — {{ date('F', mktime(0,0,0,$filterMonth,1)) }} {{ $filterYear }}
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['expenses'] as $expense)
                <tr>
                    <td class="ps-3"><span class="badge bg-primary">{{ $expense->category }}</span></td>
                    <td>{{ $expense->description }}</td>
                    <td class="text-danger fw-bold">₹{{ number_format($expense->amount) }}</td>
                    <td class="text-muted small">{{ $expense->date->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@elseif($reportType == 'teachers')
<!-- Teacher Report -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card text-center" style="background:linear-gradient(135deg,#f7971e,#ffd200);color:white;">
            <div class="card-body py-2">
                <div class="small">Total Salaries Paid</div>
                <div class="h4 fw-bold mb-0">₹{{ number_format($data['total_salary']) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-person-badge-fill me-2"></i>
        Teacher Report — {{ date('F', mktime(0,0,0,$filterMonth,1)) }} {{ $filterYear }}
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Teacher</th>
                    <th>Subject</th>
                    <th>Present Days</th>
                    <th>Salary Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['teachers'] as $teacher)
                @php
                    $salaryRecord = $data['salaries']->where('teacher_id', $teacher->id)->first();
                @endphp
                <tr>
                    <td class="ps-3 fw-semibold">{{ $teacher->name }}</td>
                    <td>{{ $teacher->subject }}</td>
                    <td>{{ $teacher->present_days }} days</td>
                    <td>
                        @if($salaryRecord)
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="badge bg-danger">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($salaryRecord)
                            <span class="text-success fw-bold">₹{{ number_format($salaryRecord->amount) }}</span>
                        @else
                            <span class="text-muted">₹{{ number_format($teacher->salary) }} (due)</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No teachers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
