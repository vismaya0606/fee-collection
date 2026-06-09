<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $filterMonth = $request->input('month', now()->month);
        $filterYear  = $request->input('year', now()->year);
        $teacherId   = $request->input('teacher_id');

        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        $attendanceQuery = TeacherAttendance::with('teacher', 'approvedByUser')
            ->whereYear('date', $filterYear)
            ->whereMonth('date', $filterMonth);

        if ($teacherId) {
            $attendanceQuery->where('teacher_id', $teacherId);
        }

        $attendanceRecords = $attendanceQuery->orderByDesc('date')->orderBy('teacher_id')->paginate(30)->withQueryString();

        // Today's attendance map
        $todayAttendance = TeacherAttendance::where('date', $date)
            ->pluck('status', 'teacher_id');

        $statuses = TeacherAttendance::getStatuses();
        $months   = range(1, 12);
        $years    = range(now()->year - 1, now()->year + 1);

        return view('attendance.index', compact(
            'teachers', 'attendanceRecords', 'todayAttendance',
            'date', 'filterMonth', 'filterYear', 'statuses', 'months', 'years', 'teacherId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'date'       => 'required|date',
            'status'     => 'required|in:present,late,half_day,absent',
            'notes'      => 'nullable|string',
        ]);

        TeacherAttendance::updateOrCreate(
            ['teacher_id' => $validated['teacher_id'], 'date' => $validated['date']],
            ['status' => $validated['status'], 'notes' => $validated['notes'] ?? null, 'approved' => false, 'approved_by' => null]
        );

        return redirect()->route('attendance.index', ['date' => $validated['date']])
            ->with('success', 'Attendance recorded.');
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'date'           => 'required|date',
            'attendance'     => 'required|array',
            'attendance.*'   => 'required|in:present,late,half_day,absent',
        ]);

        foreach ($validated['attendance'] as $teacherId => $status) {
            TeacherAttendance::updateOrCreate(
                ['teacher_id' => $teacherId, 'date' => $validated['date']],
                ['status' => $status, 'approved' => false, 'approved_by' => null]
            );
        }

        return redirect()->route('attendance.index', ['date' => $validated['date']])
            ->with('success', 'Bulk attendance saved.');
    }

    public function approve(TeacherAttendance $attendance)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can approve attendance.');
        }
        $attendance->update(['approved' => true, 'approved_by' => Auth::id()]);
        return back()->with('success', 'Attendance approved.');
    }

    public function destroy(TeacherAttendance $attendance)
    {
        $date = $attendance->date->toDateString();
        $attendance->delete();
        return redirect()->route('attendance.index', ['date' => $date])->with('success', 'Attendance record deleted.');
    }
}
