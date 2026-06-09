<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Student;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::with('convertedStudent');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $inquiries  = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $statuses   = Inquiry::getStatuses();
        $sources    = Inquiry::getSources();
        $followUps  = Inquiry::where('status', 'follow-up')
            ->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<=', now()->toDateString())
            ->orderBy('follow_up_date')
            ->get();

        return view('inquiries.index', compact('inquiries', 'statuses', 'sources', 'followUps'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'class'          => 'required|string|max:50',
            'phone'          => 'nullable|string|max:20',
            'parent_phone'   => 'required|string|max:20',
            'source'         => 'nullable|string|max:100',
            'status'         => 'required|in:new,follow-up,interested,not-interested,converted',
            'notes'          => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ]);

        Inquiry::create($validated);

        return redirect()->route('inquiries.index')->with('success', 'Inquiry added successfully.');
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'class'          => 'required|string|max:50',
            'phone'          => 'nullable|string|max:20',
            'parent_phone'   => 'required|string|max:20',
            'source'         => 'nullable|string|max:100',
            'status'         => 'required|in:new,follow-up,interested,not-interested,converted',
            'notes'          => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ]);

        $inquiry->update($validated);

        return redirect()->route('inquiries.index')->with('success', 'Inquiry updated successfully.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return redirect()->route('inquiries.index')->with('success', 'Inquiry deleted.');
    }

    public function convert(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'parent_name'    => 'required|string|max:255',
            'address'        => 'nullable|string',
            'admission_date' => 'required|date',
            'monthly_fee'    => 'required|numeric|min:0',
        ]);

        $student = Student::create([
            'name'           => $inquiry->name,
            'class'          => $inquiry->class,
            'parent_name'    => $validated['parent_name'],
            'parent_phone'   => $inquiry->parent_phone,
            'address'        => $validated['address'] ?? null,
            'status'         => 'active',
            'admission_date' => $validated['admission_date'],
            'monthly_fee'    => $validated['monthly_fee'],
        ]);

        $inquiry->update([
            'status'               => 'converted',
            'converted_student_id' => $student->id,
        ]);

        return redirect()->route('inquiries.index')->with('success', 'Inquiry converted to student successfully.');
    }
}
