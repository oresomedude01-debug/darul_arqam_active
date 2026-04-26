<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\FeeItem;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class FeeStructureController extends Controller
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Display fee structures
     */
    public function index(Request $request): View
    {
        $sessions = AcademicSession::orderBy('session', 'desc')->get();

        $query = FeeStructure::query();

        // Filter by session
        if ($request->filled('session_id')) {
            $query->where('academic_session_id', $request->session_id);
        }

        $feeStructures = $query
            ->with('items.feeItem', 'academicSession')
            ->orderBy('academic_session_id', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('payments.fee-structures.index', compact('feeStructures', 'sessions'));
    }

    /**
     * Show form to create a new fee structure template
     */
    public function create(Request $request): View
    {
        $sessions = AcademicSession::where('is_active', true)
            ->orderBy('session', 'desc')
            ->get();
        $feeItems = FeeItem::where('status', 'active')->orderBy('name')->get();

        return view('payments.fee-structures.create', compact('sessions', 'feeItems'));
    }

    /**
     * Store a new fee structure template
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:fee_structures,name',
            'description' => 'nullable|string',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'fee_items' => 'required|array|min:1',
            'fee_items.*' => 'exists:fee_items,id',
            'amounts.*' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // Create structure template
            $structure = FeeStructure::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'academic_session_id' => $validated['academic_session_id'],
                'is_active' => true,
            ]);

            // Add fee items to structure
            $totalAmount = 0;
            foreach ($validated['fee_items'] as $index => $feeItemId) {
                $amount = (float) ($request->input('amounts')[$index] ?? 0);
                
                $structure->items()->create([
                    'fee_item_id' => $feeItemId,
                    'amount' => $amount,
                    'display_order' => $index,
                ]);
                
                $totalAmount += $amount;
            }

            // Update total
            $structure->update(['total_amount' => $totalAmount]);

            DB::commit();
            return redirect()->route('billing.fee-structures.index')
                ->with('success', 'Fee structure template created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating fee structure: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form to edit a single fee structure
     */
    public function edit(FeeStructure $feeStructure): View
    {
        $sessions = AcademicSession::orderBy('session', 'desc')->get();
        $feeItems = FeeItem::where('status', 'active')->orderBy('name')->get();

        return view('payments.fee-structures.edit', compact('feeStructure', 'sessions', 'feeItems'));
    }

    /**
     * Update a fee structure template
     */
    public function update(Request $request, FeeStructure $feeStructure): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:fee_structures,name,' . $feeStructure->id,
            'description' => 'nullable|string',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'is_active' => 'boolean',
        ]);

        $feeStructure->update($validated);

        return redirect()->route('billing.fee-structures.index')
            ->with('success', 'Fee structure updated successfully.');
    }

    /**
     * Delete a fee structure
     */
    public function destroy(FeeStructure $feeStructure): RedirectResponse
    {
        $feeStructure->delete();

        return redirect()->route('billing.fee-structures.index')
            ->with('success', 'Fee structure deleted successfully.');
    }

    /**
     * Get summary for a session/class
     */
    public function summary(Request $request): View
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:school_classes,id',
        ]);

        $summary = $this->billingService->getClassSummary(
            $validated['session_id'],
            $validated['class_id']
        );

        $session = AcademicSession::find($validated['session_id']);
        $class = SchoolClass::find($validated['class_id']);

        return view('payments.fee-structures.summary', compact('summary', 'session', 'class'));
    }
}