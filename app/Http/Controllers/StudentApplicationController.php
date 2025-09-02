<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentApplicationRequest;
use App\Models\StudentApplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StudentApplicationController extends Controller
{
    /**
     * Parent Submits Application (POST)
     */
    public function store(StoreStudentApplicationRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $application = new StudentApplication();
            $application->school_id = $request->input('school_id');
            $application->first_name = $request->input('first_name');
            $application->last_name = $request->input('last_name');
            $application->date_of_birth = $request->input('date_of_birth');
            $application->gender = $request->input('gender');
            $application->address = $request->input('address');
            $application->parent_name = $request->input('parent_name');
            $application->parent_relation = $request->input('parent_relation');
            $application->parent_phone = $request->input('parent_phone');
            $application->parent_email = $request->input('parent_email');
            $application->occupation = $request->input('occupation');
            $application->document_id = $request->input('document_id');
            $application->parent_id = auth()->user()->id;
            $application->status = 'pending';
            $application->submitted_at = now();

            $application->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'application_id' => $application->id,
                'status' => $application->status,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to submit application.', 'error' => $e->getMessage()], 500);
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
