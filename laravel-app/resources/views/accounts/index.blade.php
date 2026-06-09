@extends('layouts.app')
@section('title', 'Accounts')
@section('page-title', 'Accounts Overview')

@section('content')
<!-- Current Month Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card h-100" style="background:linear-gradient(135deg,#11998e,#38ef7d);color:white;">
            <div class="card-body">
                <div class="small opacity-85">Fee Income (This Month)</div>
                <div class="h3 fw-bold mb-0">₹{{ number_format($currentSummary['fee_income']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100" style="background:linear-gradient(135deg,#f093fb,#f5576c);color:white;">
            <div class="card-body">
                <div class="small opacity-85">Expenses (This Month)</div>
                <div class="h3 fw-bold mb-0">₹{{ number_format($currentSummary['expenses']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100" style="background:linear-gradient(135deg,#f7971e,#ffd200);color:white;">
            <div class="card-body">
                <div class="small opacity-85">Salaries (This Month)</div>
                <div class="h3 fw-bold mb-0">₹{{ number_format($currentSummary['salaries']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100 {{ $currentSummary['net_profit'] >= 0 ? '' : 'border-danger' }}"
            style="background:linear-gradient(135deg,{{ $currentSummary['net_profit'] >= 0 ? '#667eea,#764ba2' : '#e53e3e,#c53030' }});color:white;">
            <div class="card-body">
                <div class="small opacity-85">Net Profit/Loss</div>
                <div class="h3 fw-bold mb-0">
                    {{ $currentSummary['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($currentSummary['net_profit']) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Year Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-filter me-1"></i>View Year
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Annual Summary Table -->
<div class="card">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <span><i class="bi bi-bar-chart-line-fill me-2"></i>Monthly Summary — {{ $selectedYear }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Month</th>
                    <th class="text-end text-success">Fee Income</th>
                    <th class="text-end text-danger">Expenses</th>
                    <th class="text-end text-warning">Salaries</th>
                    <th class="text-end">Net Profit/Loss</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyData as $row)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $row['month_name'] }}</td>
                    <td class="text-end text-success">₹{{ number_format($row['fee_income']) }}</td>
                    <td class="text-end text-danger">₹{{ number_format($row['expenses']) }}</td>
                    <td class="text-end text-warning">₹{{ number_format($row['salaries']) }}</td>
                    <td class="text-end fw-bold {{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $row['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($row['net_profit']) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-dark">
                <tr>
                    <th class="ps-3">TOTAL {{ $selectedYear }}</th>
                    <th class="text-end">₹{{ number_format($yearlyTotals['fee_income']) }}</th>
                    <th class="text-end">₹{{ number_format($yearlyTotals['expenses']) }}</th>
                    <th class="text-end">₹{{ number_format($yearlyTotals['salaries']) }}</th>
                    <th class="text-end {{ $yearlyTotals['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $yearlyTotals['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($yearlyTotals['net_profit']) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
