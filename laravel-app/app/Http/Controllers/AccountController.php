<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\TeacherSalary;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->input('year', now()->year);

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $feeIncome   = FeePayment::where('month', $m)->where('year', $selectedYear)->sum('amount');
            $expenses    = Expense::whereMonth('date', $m)->whereYear('date', $selectedYear)->sum('amount');
            $salaries    = TeacherSalary::where('month', $m)->where('year', $selectedYear)->sum('amount');
            $netProfit   = $feeIncome - $expenses - $salaries;

            $monthlyData[] = [
                'month'      => $m,
                'month_name' => date('F', mktime(0, 0, 0, $m, 1)),
                'fee_income' => $feeIncome,
                'expenses'   => $expenses,
                'salaries'   => $salaries,
                'net_profit' => $netProfit,
            ];
        }

        $yearlyTotals = [
            'fee_income' => array_sum(array_column($monthlyData, 'fee_income')),
            'expenses'   => array_sum(array_column($monthlyData, 'expenses')),
            'salaries'   => array_sum(array_column($monthlyData, 'salaries')),
            'net_profit' => array_sum(array_column($monthlyData, 'net_profit')),
        ];

        // Current month summary
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        $currentSummary = [
            'fee_income' => FeePayment::where('month', $currentMonth)->where('year', $currentYear)->sum('amount'),
            'expenses'   => Expense::whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->sum('amount'),
            'salaries'   => TeacherSalary::where('month', $currentMonth)->where('year', $currentYear)->sum('amount'),
        ];
        $currentSummary['net_profit'] = $currentSummary['fee_income'] - $currentSummary['expenses'] - $currentSummary['salaries'];

        $years = range(now()->year - 3, now()->year + 1);

        return view('accounts.index', compact('monthlyData', 'yearlyTotals', 'currentSummary', 'selectedYear', 'years'));
    }
}
