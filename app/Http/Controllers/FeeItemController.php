<?php

namespace App\Http\Controllers;

use App\Models\FeeItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FeeItemController extends Controller
{
    /**
     * Display a listing of fee items
     */
    public function index(): View
    {
        $feeItems = FeeItem::orderBy('name')->paginate(20);
        return view('billing.fee_items.index', compact('feeItems'));
    }

    /**
     * Show the form for creating a new fee item
     */
    public function create(): View
    {
        return view('billing.fee_items.create');
    }

    /**
     * Store a newly created fee item
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fee_items,name',
            'description' => 'nullable|string|max:1000',
            'is_optional' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0|max:9999999.99',
            'status' => 'required|in:active,inactive',
        ]);

        FeeItem::create($validated);

        return redirect()->route('billing.fee-items.index')
            ->with('success', 'Fee item created successfully.');
    }

    /**
     * Show the form for editing a fee item
     */
    public function edit(FeeItem $feeItem): View
    {
        return view('billing.fee_items.edit', compact('feeItem'));
    }

    /**
     * Update the specified fee item
     */
    public function update(Request $request, FeeItem $feeItem): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fee_items,name,' . $feeItem->id,
            'description' => 'nullable|string|max:1000',
            'is_optional' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0|max:9999999.99',
            'status' => 'required|in:active,inactive',
        ]);

        $feeItem->update($validated);

        return redirect()->route('billing.fee-items.index')
            ->with('success', 'Fee item updated successfully.');
    }

    /**
     * Deactivate a fee item
     */
    public function deactivate(FeeItem $feeItem): RedirectResponse
    {
        $feeItem->update(['status' => 'inactive']);

        return redirect()->route('billing.fee-items.index')
            ->with('success', 'Fee item deactivated successfully.');
    }

    /**
     * Activate a fee item
     */
    public function activate(FeeItem $feeItem): RedirectResponse
    {
        $feeItem->update(['status' => 'active']);

        return redirect()->route('billing.fee-items.index')
            ->with('success', 'Fee item activated successfully.');
    }

    /**
     * Delete a fee item
     */
    public function destroy(FeeItem $feeItem): RedirectResponse
    {
        // Don't delete if there are related fee structures
        if ($feeItem->feeStructures()->count() > 0) {
            return redirect()->route('billing.fee-items.index')
                ->with('error', 'Cannot delete this fee item. It is used in fee structures.');
        }

        $feeItem->delete();

        return redirect()->route('billing.fee-items.index')
            ->with('success', 'Fee item deleted successfully.');
    }
}