<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Inquiry;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherSalary;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        $totalStudents    = Student::where('status', 'active')->count();
        $totalTeachers    = Teacher::where('status', 'active')->count();
        $feeCollectedThisMonth = FeePayment::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount');
        $pendingFeeCount = Student::where('status', 'active')
            ->whereDoesntHave('feePayments', function ($q) use ($currentMonth, $currentYear) {
                $q->where('month', $currentMonth)->where('year', $currentYear);
            })->count();
        $expensesThisMonth = Expense::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
        $salariesThisMonth = TeacherSalary::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount');
        $netProfit = $feeCollectedThisMonth - $expensesThisMonth - $salariesThisMonth;
        $newInquiries = Inquiry::where('status', 'new')->count();
        $followUpInquiries = Inquiry::where('status', 'follow-up')
            ->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<=', now()->toDateString())
            ->count();

        $recentPayments = FeePayment::with('student')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $monthlyFeeChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $monthlyFeeChart[] = [
                'label'  => $m->format('M Y'),
                'amount' => FeePayment::where('month', $m->month)->where('year', $m->year)->sum('amount'),
            ];
        }

        return view('dashboard.index', compact(
            'totalStudents',
            'totalTeachers',
            'feeCollectedThisMonth',
            'pendingFeeCount',
            'expensesThisMonth',
            'salariesThisMonth',
            'netProfit',
            'newInquiries',
            'followUpInquiries',
            'recentPayments',
            'monthlyFeeChart',
            'currentMonth',
            'currentYear'
        ));
    }
}
