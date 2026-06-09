<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $filterClass   = $request->input('class');
        $filterSection = $request->input('section');

        $query = TimetableEntry::with('teacher');

        if ($filterClass) {
            $query->where('class', $filterClass);
        }
        if ($filterSection) {
            $query->where('section', $filterSection);
        }

        $entries  = $query->orderBy('day')->orderBy('period')->get();
        $classes  = TimetableEntry::distinct()->orderBy('class')->pluck('class');
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();
        $days     = TimetableEntry::getDays();

        // Group entries by day
        $grouped = [];
        foreach ($days as $day) {
            $grouped[$day] = $entries->where('day', $day)->sortBy('period')->values();
        }

        return view('timetable.index', compact('grouped', 'classes', 'teachers', 'days', 'filterClass', 'filterSection'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class'      => 'required|string|max:50',
            'section'    => 'nullable|string|max:10',
            'day'        => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'period'     => 'required|integer|min:1|max:10',
            'subject'    => 'required|string|max:100',
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        TimetableEntry::create($validated);

        return redirect()->route('timetable.index')->with('success', 'Timetable entry added successfully.');
    }

    public function update(Request $request, TimetableEntry $timetable)
    {
        $validated = $request->validate([
            'class'      => 'required|string|max:50',
            'section'    => 'nullable|string|max:10',
            'day'        => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'period'     => 'required|integer|min:1|max:10',
            'subject'    => 'required|string|max:100',
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        $timetable->update($validated);

        return redirect()->route('timetable.index')->with('success', 'Timetable entry updated successfully.');
    }

    public function destroy(TimetableEntry $timetable)
    {
        $timetable->delete();
        return redirect()->route('timetable.index')->with('success', 'Timetable entry deleted.');
    }
}
