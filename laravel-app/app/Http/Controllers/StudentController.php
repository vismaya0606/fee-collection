<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
            });
        }

        if ($class = $request->input('class')) {
            $query->where('class', $class);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $sortField = $request->input('sort', 'name');
        $sortDir   = $request->input('dir', 'asc');
        $allowedSorts = ['name', 'class', 'admission_date', 'monthly_fee', 'status'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'name';
        }
        $query->orderBy($sortField, $sortDir === 'desc' ? 'desc' : 'asc');

        $students = $query->paginate(20)->withQueryString();
        $classes = Student::distinct()->orderBy('class')->pluck('class');

        return view('students.index', compact('students', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'class'          => 'required|string|max:50',
            'section'        => 'nullable|string|max:10',
            'parent_name'    => 'required|string|max:255',
            'parent_phone'   => 'required|string|max:20',
            'address'        => 'nullable|string',
            'status'         => 'required|in:active,inactive',
            'admission_date' => 'required|date',
            'monthly_fee'    => 'required|numeric|min:0',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'class'          => 'required|string|max:50',
            'section'        => 'nullable|string|max:10',
            'parent_name'    => 'required|string|max:255',
            'parent_phone'   => 'required|string|max:20',
            'address'        => 'nullable|string',
            'status'         => 'required|in:active,inactive',
            'admission_date' => 'required|date',
            'monthly_fee'    => 'required|numeric|min:0',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
