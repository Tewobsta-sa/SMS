<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentApplicationRequest;
use App\Models\StudentApplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentApplicationController extends Controller
{
    /**
     * Parent Submits Application (POST)
     */
    public function store(StoreStudentApplicationRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $application = StudentApplication::create(array_merge(
                $request->validated(),
                [
                    'parent_id' => auth()->user()->id,
                    'status' => 'pending',
                    'submitted_at' => now(),
                ]
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'application_id' => $application->id,
                'status' => $application->status,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Student application submission failed: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to submit application.'], 500);
        }
    }

    /**
     * Parent Views All Their Applications (GET)
     */
    public function index(Request $request): JsonResponse
    {
        $applications = StudentApplication::where('parent_id', $request->user()->id)
            ->select('id', 'first_name', 'last_name', 'status', 'submitted_at')
            ->get();

        return response()->json($applications);
    }

    /**
     * Parent Views Single Application (GET)
     */
    public function show(string $id): JsonResponse
    {
        $application = StudentApplication::where('parent_id', auth()->user()->id)
            ->findOrFail($id);

        return response()->json($application);
    }
}
