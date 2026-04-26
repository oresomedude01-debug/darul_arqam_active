<?php

namespace App\Http\Controllers;

use App\Models\RegistrationToken;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TokenController extends Controller
{
    /**
     * Display a listing of tokens with filters
     */
    public function index(Request $request)
    {
        $query = RegistrationToken::with(['user.profile', 'schoolClass', 'creator']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('session')) {
            $query->where('session_year', $request->session);
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        // Get tokens with pagination
        $tokens = $query->latest()->paginate(20);
        
        // DEBUG: Log to see what's being queried
        \Log::info('Tokens Query SQL: ' . $query->toSql());
        \Log::info('Tokens Count: ' . RegistrationToken::count());
        \Log::info('All Tokens: ' . json_encode(RegistrationToken::all()->pluck('code', 'id')));

        // Get statistics
        $stats = [
            'total' => RegistrationToken::count(),
            'active' => RegistrationToken::active()->count(),
            'consumed' => RegistrationToken::consumed()->count(),
            'expired' => RegistrationToken::expired()->count(),
            'disabled' => RegistrationToken::where('status', 'disabled')->count(),
        ];

        return view('tokens.index', compact('tokens', 'stats'));
    }

    /**
     * Show the form for creating new tokens
     */
    public function create()
    {
        $academicSessions = \App\Models\AcademicSession::orderBy('session', 'desc')->get();
        $classes = \App\Models\SchoolClass::orderBy('name')->get();
        return view('tokens.create', compact('academicSessions', 'classes'));
    }

    /**
     * Store newly created tokens
     */
    public function store(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'session_year' => 'required|string',
            'class_level' => 'nullable|string',
            'expires_at' => 'nullable|date|after:today',
            'note' => 'nullable|string|max:500',
        ]);

        $quantity = $request->quantity;
        $createdTokens = [];

        for ($i = 0; $i < $quantity; $i++) {
            $token = RegistrationToken::create([
                'code' => RegistrationToken::generateTokenCode(),
                'status' => 'active',
                'session_year' => $request->session_year,
                'class_level' => $request->class_level,
                'expires_at' => $request->expires_at ? Carbon::parse($request->expires_at) : null,
                'note' => $request->note,
                'created_by' => auth()->id() ?? 1, // TODO: Replace with actual auth
            ]);

            $createdTokens[] = $token;
        }

        return redirect()
            ->route('tokens.index')
            ->with('success', "Successfully generated {$quantity} token(s)!")
            ->with('created_tokens', $createdTokens);
    }

    /**
     * Display the specified token
     */
    public function show(string $id)
    {
        $token = RegistrationToken::with(['user.profile', 'schoolClass', 'creator'])->findOrFail($id);
        return view('tokens.show', compact('token'));
    }

    /**
     * Update token status (disable/enable)
     */
    public function update(Request $request, string $id)
    {
        $token = RegistrationToken::findOrFail($id);

        $request->validate([
            'status' => 'required|in:active,disabled',
        ]);

        $token->update(['status' => $request->status]);

        $message = $request->status === 'disabled'
            ? 'Token disabled successfully!'
            : 'Token enabled successfully!';

        return redirect()
            ->route('tokens.index')
            ->with('success', $message);
    }

    /**
     * Bulk disable tokens
     */
    public function bulkDisable(Request $request)
    {
        $request->validate([
            'token_ids' => 'required|array',
            'token_ids.*' => 'exists:registration_tokens,id',
        ]);

        $count = RegistrationToken::whereIn('id', $request->token_ids)
            ->where('status', 'active')
            ->update(['status' => 'disabled']);

        return redirect()
            ->route('tokens.index')
            ->with('success', "Successfully disabled {$count} token(s)!");
    }

    /**
     * Bulk enable tokens
     */
    public function bulkEnable(Request $request)
    {
        $request->validate([
            'token_ids' => 'required|array',
            'token_ids.*' => 'exists:registration_tokens,id',
        ]);

        $count = RegistrationToken::whereIn('id', $request->token_ids)
            ->where('status', 'disabled')
            ->update(['status' => 'active']);

        return redirect()
            ->route('tokens.index')
            ->with('success', "Successfully enabled {$count} token(s)!");
    }

    /**
     * Validate token for enrollment
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $token = RegistrationToken::where('code', $request->code)->first();

        if (!$token) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid token code. Please check and try again.',
            ], 404);
        }

        if (!$token->isValid()) {
            $message = match($token->status) {
                'consumed' => 'This token has already been used.',
                'expired' => 'This token has expired.',
                'disabled' => 'This token has been disabled.',
                default => 'This token is not valid.',
            };

            return response()->json([
                'valid' => false,
                'message' => $message,
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Token is valid! Proceeding to enrollment form...',
            'token' => [
                'code' => $token->code,
                'session_year' => $token->session_year,
                'class_level' => $token->class_level,
            ],
        ]);
    }
}
