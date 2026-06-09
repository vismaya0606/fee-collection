<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $fee->receipt_no }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
        .receipt-wrapper { max-width: 600px; margin: 2rem auto; }
        .receipt-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .receipt-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .receipt-header h4 { margin: 0; font-weight: 700; }
        .receipt-header p { margin: 0.25rem 0 0; opacity: 0.85; font-size: 0.875rem; }
        .receipt-body { padding: 1.5rem; }
        .receipt-no {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .info-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px dashed #e9ecef; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6c757d; font-size: 0.875rem; }
        .info-value { font-weight: 600; }
        .amount-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin: 1rem 0;
        }
        .amount-box .label { font-size: 0.8rem; opacity: 0.85; }
        .amount-box .amount { font-size: 2rem; font-weight: 700; }
        .receipt-footer { text-align: center; padding: 1rem 1.5rem; background: #f8f9fa; font-size: 0.8rem; color: #6c757d; }
        @media print {
            body { background: white; }
            .receipt-wrapper { margin: 0; max-width: 100%; }
            .no-print { display: none !important; }
            .receipt-card { box-shadow: none; }
        }
    </style>
</head>
<body>
<div class="receipt-wrapper">
    <div class="no-print d-flex justify-content-between mb-3">
        <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer me-1"></i>Print Receipt
        </button>
    </div>

    <div class="receipt-card">
        <div class="receipt-header">
            <h4><i class="bi bi-mortarboard-fill me-2"></i>Demo School Fee Management</h4>
            <p>Fee Payment Receipt</p>
        </div>
        <div class="receipt-body">
            <div class="text-center">
                <span class="receipt-no">{{ $fee->receipt_no }}</span>
            </div>

            <div class="amount-box">
                <div class="label">Amount Paid</div>
                <div class="amount">₹{{ number_format($fee->amount, 2) }}</div>
                <div class="label mt-1">For {{ $fee->month_name }} {{ $fee->year }}</div>
            </div>

            <div class="info-row">
                <span class="info-label">Student Name</span>
                <span class="info-value">{{ $fee->student->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Class</span>
                <span class="info-value">{{ $fee->student->class }}{{ $fee->student->section ? ' - '.$fee->student->section : '' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Parent Name</span>
                <span class="info-value">{{ $fee->student->parent_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Parent Phone</span>
                <span class="info-value">{{ $fee->student->parent_phone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Date</span>
                <span class="info-value">{{ $fee->payment_date->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Mode</span>
                <span class="info-value">{{ ucfirst($fee->payment_mode) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Collected By</span>
                <span class="info-value">{{ $fee->collector->name ?? 'N/A' }}</span>
            </div>
            @if($fee->notes)
            <div class="info-row">
                <span class="info-label">Notes</span>
                <span class="info-value">{{ $fee->notes }}</span>
            </div>
            @endif
        </div>
        <div class="receipt-footer">
            <div>Generated on {{ now()->format('d M Y, h:i A') }}</div>
            <div class="mt-1 fw-semibold">Thank you for your payment!</div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
