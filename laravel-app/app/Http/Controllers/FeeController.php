<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);
        $search = $request->input('search', '');
        $tab   = $request->input('tab', 'pending');

        $paidStudentIds = FeePayment::where('month', $month)
            ->where('year', $year)
            ->pluck('student_id');

        $pendingQuery = Student::where('status', 'active')
            ->whereNotIn('id', $paidStudentIds);
        $paidQuery = Student::whereIn('id', $paidStudentIds);

        if ($search) {
            $pendingQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
            $paidQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $pendingStudents = $pendingQuery->orderBy('class')->orderBy('name')->paginate(20, ['*'], 'pending_page')->withQueryString();
        $paidStudents    = $paidQuery->with(['feePayments' => function ($q) use ($month, $year) {
            $q->where('month', $month)->where('year', $year);
        }])->orderBy('class')->orderBy('name')->paginate(20, ['*'], 'paid_page')->withQueryString();

        $months = range(1, 12);
        $years  = range(now()->year - 2, now()->year + 1);

        return view('fees.index', compact('pendingStudents', 'paidStudents', 'month', 'year', 'months', 'years', 'search', 'tab'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'amount'       => 'required|numeric|min:1',
            'month'        => 'required|integer|between:1,12',
            'year'         => 'required|integer|min:2000',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|in:cash,online,cheque',
            'notes'        => 'nullable|string',
        ]);

        $alreadyPaid = FeePayment::where('student_id', $validated['student_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($alreadyPaid) {
            return back()->with('error', 'Fee already recorded for this student for the selected month/year.');
        }

        $validated['receipt_no']   = FeePayment::generateReceiptNo($validated['month'], $validated['year']);
        $validated['collected_by'] = Auth::id();

        FeePayment::create($validated);

        return redirect()->route('fees.index', ['month' => $validated['month'], 'year' => $validated['year'], 'tab' => 'paid'])
            ->with('success', 'Fee payment recorded. Receipt: ' . $validated['receipt_no']);
    }

    public function receipt(FeePayment $fee)
    {
        $fee->load('student', 'collector');
        return view('fees.receipt', compact('fee'));
    }

    public function destroy(FeePayment $fee)
    {
        $month = $fee->month;
        $year  = $fee->year;
        $fee->delete();
        return redirect()->route('fees.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Payment record deleted.');
    }
}
