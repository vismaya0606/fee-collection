<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TeacherSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);

        $paidTeacherIds = TeacherSalary::where('month', $month)
            ->where('year', $year)
            ->pluck('teacher_id');

        $pendingTeachers = Teacher::where('status', 'active')
            ->whereNotIn('id', $paidTeacherIds)
            ->orderBy('name')
            ->get();

        $paidSalaries = TeacherSalary::with('teacher', 'paidByUser')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        $months = range(1, 12);
        $years  = range(now()->year - 2, now()->year + 1);

        return view('salaries.index', compact('pendingTeachers', 'paidSalaries', 'month', 'year', 'months', 'years'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id'   => 'required|exists:teachers,id',
            'month'        => 'required|integer|between:1,12',
            'year'         => 'required|integer|min:2000',
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        $alreadyPaid = TeacherSalary::where('teacher_id', $validated['teacher_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($alreadyPaid) {
            return back()->with('error', 'Salary already recorded for this teacher for the selected month/year.');
        }

        $validated['paid_by'] = Auth::id();
        TeacherSalary::create($validated);

        return redirect()->route('salaries.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'Salary payment recorded successfully.');
    }

    public function destroy(TeacherSalary $salary)
    {
        $month = $salary->month;
        $year  = $salary->year;
        $salary->delete();
        return redirect()->route('salaries.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Salary record deleted.');
    }
}
