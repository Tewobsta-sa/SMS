<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import the Rule class

class FeeController extends Controller
{

    public function store(Request $request)
    {
        $user = $request->user();
        
        // 1. Authorize: Check if the user is an admin.
        $this->authorize('create', FeeStructure::class);

        $validated = $request->validate([
            // 2. Improved Validation: Scope checks to the admin's school.
            'grade_id' => ['required', Rule::exists('grades', 'id')->where('school_id', $user->school_id)],
            'section_id' => ['required', Rule::exists('sections', 'id')->where('school_id', $user->school_id)],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('school_id', $user->school_id)],
            'description' => 'required|string|max:255',
            'fee_type' => 'required|in:recurring,one-time',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        try {
            // 3. Secure and Simplified Creation
            $feeStructure = FeeStructure::create([
                'school_id' => $user->school_id, // Use the admin's school_id
                'grade_id' => $validated['grade_id'],
                'section_id' => $validated['section_id'],
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'is_recurring' => $validated['fee_type'] === 'recurring', // Simplified
                'due_date' => $validated['due_date'],
            ]);

            return response()->json([
                'message' => 'Fee structure created successfully',
                'data' => $feeStructure
            ], 201);
        } catch (\Exception $e) {
            // \Log::error($e); // Optional: for debugging
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function index(Request $request)
    {
        // Authorize that the user can view the list.
        $this->authorize('viewAny', FeeStructure::class);
        
        // Securely scope the query to the user's school.
        $fees = FeeStructure::where('school_id', $request->user()->school_id)
            ->with(['category', 'grade', 'section'])
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json($fees);
    }

    public function show(FeeStructure $feeStructure) // Use Route-Model Binding
    {
        // Authorize that the user can view this specific fee structure.
        $this->authorize('view', $feeStructure);

        // Load relationships if needed
        $feeStructure->load(['school', 'category', 'grade', 'section']);

        return response()->json(['status' => 'success', 'data' => $feeStructure]);
    }
}