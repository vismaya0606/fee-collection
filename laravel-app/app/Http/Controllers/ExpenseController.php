<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('addedBy');

        $filterMonth    = $request->input('month');
        $filterYear     = $request->input('year', now()->year);
        $filterCategory = $request->input('category');

        if ($filterMonth) {
            $query->whereMonth('date', $filterMonth)->whereYear('date', $filterYear);
        } elseif ($filterYear) {
            $query->whereYear('date', $filterYear);
        }

        if ($filterCategory) {
            $query->where('category', $filterCategory);
        }

        $expenses   = $query->orderByDesc('date')->paginate(20)->withQueryString();
        $totalAmount = $query->sum('amount');
        $categories  = Expense::getCategories();
        $months      = range(1, 12);
        $years       = range(now()->year - 2, now()->year + 1);

        return view('expenses.index', compact('expenses', 'totalAmount', 'categories', 'months', 'years', 'filterMonth', 'filterYear', 'filterCategory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'    => 'required|string|max:100',
            'description' => 'required|string',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
        ]);

        $validated['added_by'] = Auth::id();
        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $categories = Expense::getCategories();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category'    => 'required|string|max:100',
            'description' => 'required|string',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
