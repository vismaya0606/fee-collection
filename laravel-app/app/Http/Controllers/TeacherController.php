<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $teachers = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'subject'   => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
            'email'     => 'nullable|email|max:255',
            'salary'    => 'required|numeric|min:0',
            'join_date' => 'required|date',
            'status'    => 'required|in:active,inactive',
        ]);

        Teacher::create($validated);

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully.');
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'subject'   => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
            'email'     => 'nullable|email|max:255',
            'salary'    => 'required|numeric|min:0',
            'join_date' => 'required|date',
            'status'    => 'required|in:active,inactive',
        ]);

        $teacher->update($validated);

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}
