<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\School;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('school')->get(); // eager load school
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $schools = School::all();
        return view('users.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'role'     => 'required|in:admin,teacher,parent,student',
            'password' => 'required|string|min:6|confirmed',
            'profile_picture_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $profilePath = null;
        if ($request->hasFile('profile_picture_url')) {
            $profilePath = $request->file('profile_picture_url')->store('profiles', 'public');
        }

        $user = User::create([
            'school_id' => $request->school_id,
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'role'      => $request->role, // save in DB
            'profile_picture_url' => $profilePath,
            'password'  => bcrypt($request->password),
            'is_active' => $request->has('is_active'),
        ]);

        // ✅ Assign role in Spatie
        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }


    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
       $schools = School::all();
       return view('users.edit', compact('user', 'schools'));
    }

    public function update(Request $request, User $user)
    {

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:admin,teacher,parent,student',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_picture_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address', 'school_id', 'role']);
        $data['is_active'] = $request->has('is_active');
        $data['role'] = $request->role;


        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('profile_picture_url')) {
            if ($user->profile_picture_url) {
                Storage::disk('public')->delete($user->profile_picture_url);
            }
            $data['profile_picture_url'] = $request->file('profile_picture_url')->store('profiles', 'public');
        }

        $user->update($data);

        // ✅ Update role in Spatie
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
}



    public function destroy(User $user)
    {
        if ($user->profile_picture_url) {
            Storage::disk('public')->delete($user->profile_picture_url);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
