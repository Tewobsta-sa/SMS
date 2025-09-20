<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    public function index()
    {
        $parents = ParentModel::with('user','students')->paginate(20);
        return view('parents.index', compact('parents'));
    }

    public function create()
    {
        $users = User::role('parent')
            ->whereDoesntHave('parentProfile')
            ->get();
        return view('parents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'occupation' => 'nullable|string',
            'relation'   => ['nullable','in:Father,Mother,Guardian'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['user_id']);
            ParentModel::create([
                'school_id'  => $user->school_id,
                'user_id'    => $user->id,
                'occupation' => $data['occupation'] ?? null,
                'relation'   => $data['relation'] ?? 'Guardian',
            ]);
        });

        return redirect()->route('parents.index')->with('success','Parent created.');
    }

    public function show(ParentModel $parent)
    {
        $parent->load('user','students.user','students.class','students.section');
        return view('parents.show', compact('parent'));
    }

    public function edit(ParentModel $parent)
    {
        $parent->load('user');
        $eligibleUsers = User::where(function($q){
                $q->role('parent')->whereDoesntHave('parentProfile');
            })
            ->orWhere('id', $parent->user_id)
            ->get();
        return view('parents.edit', compact('parent','eligibleUsers'));
    }

    public function update(Request $request, ParentModel $parent)
    {
        $data = $request->validate([
            'user_id'    => 'nullable|exists:users,id',
            'occupation' => 'nullable|string',
            'relation'   => ['nullable','in:Father,Mother,Guardian'],
        ]);

        DB::transaction(function () use ($data, $parent) {
            $parent->update([
                'user_id'    => $data['user_id'] ?? $parent->user_id,
                'occupation' => $data['occupation'] ?? $parent->occupation,
                'relation'   => $data['relation'] ?? $parent->relation,
            ]);
        });

        return redirect()->route('parents.show', $parent)->with('success','Parent updated.');
    }

    public function destroy(ParentModel $parent)
    {
        $parent->delete();
        return redirect()->route('parents.index')->with('success','Parent removed.');
    }
}
