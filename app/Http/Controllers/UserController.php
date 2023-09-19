<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        //return view('users.index', ['users' => $model->paginate(15)]);
        $users = User::latest()
        // ->whereNull('users.deleted_at')
            ->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::latest()->get();
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(User $user)
    // {
    //     $user->create(array_merge($user->validated(), [
    //         'password' => 'test' 
    //     ]));

    //     return redirect()->route('users.index')
    //         ->withSuccess(__('User created successfully.'));
    // }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            
            // Add more validation rules as per your requirements
        ]);

        $user = User::create(array_merge($validatedData, [
            'password' => Hash::make('Changeme@123')
        ]));

        $user->syncRoles($request->get('role'));

        return redirect()->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) 
    {
        return view('users.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user) 
    {
        return view('users.edit', [
            'user' => $user,
            // 'userRole' => optional($user->roles)->pluck('name')->toArray() ?? [],
            'roles' => Role::latest()->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(User $user, ProfileRequest $request) 
    {
        $user->update($request->validated());

        $user->syncRoles($request->get('role'));

        return redirect()->route('users.index')
            ->withSuccess(__('User updated successfully.'));
    }

    public function resetPassword(Request $request, $userId) 
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.required' => 'The new password field is required.',
            'password.confirmed' => 'The new password confirmation does not match.',
            // Add any other custom error messages for the 'password' field if needed.
        ]);

        $user = User::find($userId);
        $user->password = Hash::make($request->input('password'));
        $user->save();      

        return redirect()->route('users.index')
            ->withSuccess(__('User password updated successfully.'));    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) 
    {
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }
}
