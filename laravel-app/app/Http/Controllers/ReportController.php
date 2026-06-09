<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherSalary;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType  = $request->input('type', 'fees');
        $filterMonth = (int) $request->input('month', now()->month);
        $filterYear  = (int) $request->input('year', now()->year);

        $data = [];

        if ($reportType === 'fees') {
            $data['payments'] = FeePayment::with('student', 'collector')
                ->where('month', $filterMonth)
                ->where('year', $filterYear)
                ->orderBy('payment_date')
                ->get();
            $data['total']          = $data['payments']->sum('amount');
            $data['paid_count']     = $data['payments']->count();
            $data['pending_count']  = Student::where('status', 'active')
                ->whereDoesntHave('feePayments', function ($q) use ($filterMonth, $filterYear) {
                    $q->where('month', $filterMonth)->where('year', $filterYear);
                })->count();
            $data['by_mode'] = $data['payments']->groupBy('payment_mode')->map->sum('amount');
        } elseif ($reportType === 'expenses') {
            $data['expenses']  = Expense::whereMonth('date', $filterMonth)
                ->whereYear('date', $filterYear)
                ->orderBy('date')
                ->get();
            $data['total']     = $data['expenses']->sum('amount');
            $data['by_category'] = $data['expenses']->groupBy('category')->map->sum('amount');
        } elseif ($reportType === 'teachers') {
            $data['teachers'] = Teacher::withCount(['attendances as present_days' => function ($q) use ($filterMonth, $filterYear) {
                $q->whereMonth('date', $filterMonth)->whereYear('date', $filterYear)->where('status', 'present');
            }])->orderBy('name')->get();

            $data['salaries'] = TeacherSalary::with('teacher')
                ->where('month', $filterMonth)
                ->where('year', $filterYear)
                ->get();
            $data['total_salary'] = $data['salaries']->sum('amount');
        }

        $months = range(1, 12);
        $years  = range(now()->year - 2, now()->year + 1);

        return view('reports.index', compact('data', 'reportType', 'filterMonth', 'filterYear', 'months', 'years'));
    }
}
