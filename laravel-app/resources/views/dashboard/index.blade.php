@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Total Students</span>
                    <i class="bi bi-people-fill fs-4 opacity-75"></i>
                </div>
                <div class="stat-value">{{ number_format($totalStudents) }}</div>
                <small class="opacity-75">Active enrolled</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="background:linear-gradient(135deg,#11998e,#38ef7d);color:white;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Fee Collected</span>
                    <i class="bi bi-cash-coin fs-4 opacity-75"></i>
                </div>
                <div class="stat-value">₹{{ number_format($feeCollectedThisMonth) }}</div>
                <small class="opacity-75">This month</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="background:linear-gradient(135deg,#f093fb,#f5576c);color:white;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Pending Fees</span>
                    <i class="bi bi-exclamation-circle-fill fs-4 opacity-75"></i>
                </div>
                <div class="stat-value">{{ number_format($pendingFeeCount) }}</div>
                <small class="opacity-75">Students pending</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="background:linear-gradient(135deg,#4facfe,#00f2fe);color:white;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label">Teachers</span>
                    <i class="bi bi-person-badge-fill fs-4 opacity-75"></i>
                </div>
                <div class="stat-value">{{ number_format($totalTeachers) }}</div>
                <small class="opacity-75">Active teachers</small>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body p-3">
                <div class="text-muted small mb-1">This Month Expenses</div>
                <div class="h4 fw-bold text-danger mb-0">₹{{ number_format($expensesThisMonth) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body p-3">
                <div class="text-muted small mb-1">This Month Salaries</div>
                <div class="h4 fw-bold text-warning mb-0">₹{{ number_format($salariesThisMonth) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body p-3">
                <div class="text-muted small mb-1">Net Profit/Loss</div>
                <div class="h4 fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                    {{ $netProfit >= 0 ? '+' : '' }}₹{{ number_format($netProfit) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <span><i class="bi bi-cash-stack me-2 text-primary"></i>Recent Fee Payments</span>
                <a href="{{ route('fees.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Student</th>
                                <th>Class</th>
                                <th>Amount</th>
                                <th>Receipt</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                            <tr>
                                <td class="ps-3">{{ $payment->student->name ?? 'N/A' }}</td>
                                <td>{{ $payment->student->class ?? '' }}</td>
                                <td class="text-success fw-bold">₹{{ number_format($payment->amount) }}</td>
                                <td><span class="badge bg-light text-dark">{{ $payment->receipt_no }}</span></td>
                                <td class="text-muted small">{{ $payment->payment_date->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No payments yet this month</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-bell-fill me-2 text-warning"></i>Notifications
            </div>
            <div class="card-body">
                @if($newInquiries > 0)
                <div class="d-flex align-items-center gap-3 p-2 bg-light rounded mb-2">
                    <div class="rounded-circle bg-info d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;min-width:36px;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">New Inquiries</div>
                        <div class="text-muted" style="font-size:0.78rem;">{{ $newInquiries }} new inquiry{{ $newInquiries > 1 ? 's' : '' }} waiting</div>
                    </div>
                </div>
                @endif
                @if($followUpInquiries > 0)
                <div class="d-flex align-items-center gap-3 p-2 bg-light rounded mb-2">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;min-width:36px;">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">Follow-up Due</div>
                        <div class="text-muted" style="font-size:0.78rem;">{{ $followUpInquiries }} follow-up{{ $followUpInquiries > 1 ? 's' : '' }} overdue</div>
                    </div>
                </div>
                @endif
                @if($pendingFeeCount > 0)
                <div class="d-flex align-items-center gap-3 p-2 bg-light rounded mb-2">
                    <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;min-width:36px;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">Pending Fees</div>
                        <div class="text-muted" style="font-size:0.78rem;">{{ $pendingFeeCount }} student{{ $pendingFeeCount > 1 ? 's' : '' }} haven't paid</div>
                    </div>
                </div>
                @endif
                @if(!$newInquiries && !$followUpInquiries && !$pendingFeeCount)
                <div class="text-center text-muted py-3">
                    <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                    All caught up!
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header py-3">
                <i class="bi bi-graph-up me-2 text-primary"></i>Monthly Fee Trend
            </div>
            <div class="card-body">
                @foreach($monthlyFeeChart as $item)
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $item['label'] }}</span>
                        <span class="fw-bold">₹{{ number_format($item['amount']) }}</span>
                    </div>
                    @php
                        $maxAmount = max(array_column($monthlyFeeChart, 'amount'));
                        $percent = $maxAmount > 0 ? ($item['amount'] / $maxAmount * 100) : 0;
                    @endphp
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar" style="width:{{ $percent }}%;background:linear-gradient(90deg,#667eea,#764ba2);"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
