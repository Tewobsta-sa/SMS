<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // 1. Authorize that the user is an admin using the policy
        // This will automatically throw a 403 Forbidden error if the check fails.
        $this->authorize('create', Category::class);

        // 2. Validate the incoming data
        $validated = $request->validate([
            // We no longer need the school_id from the request, 
            // as we will use the admin's own school_id.
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        // 3. Combine validated data with the admin's school_id
        $dataToCreate = array_merge($validated, [
            'school_id' => $user->school_id,
        ]);

        // 4. Create the category using the safe, authorized data
        try {
            $category = Category::createAndGenerateCode($dataToCreate);
            return response()->json(['status' => 'success', 'data' => $category], 201);
        } catch (\Exception $e) {
            // \Log::error($e); // Good for debugging
            return response()->json([
                'message' => 'An unexpected server error occurred.',
            ], 500);
        }
    }
}