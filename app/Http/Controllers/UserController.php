<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use App\Http\Requests\ProfileRequest;
use App\Models\BasicInformation;
use Hamcrest\Type\IsNumeric;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index(Request $request, User $model)
    {
        $users = User::latest();

        if ($request->has('name')) {

            if(is_numeric($request->input('name'))){
                
                $studentNumber = $request->input('name');
                $ifUserExists = User::where('name', $studentNumber)->first();
                if($ifUserExists){
                    try{
                        $getPrivateEmail = BasicInformation::find($studentNumber);
                        $privateEmail = trim($getPrivateEmail->PrivateEmail);
                        if (!filter_var($privateEmail, FILTER_VALIDATE_EMAIL)) {
                            $privateEmail = $studentNumber . '@lmmu.ac.zm';
                        }
                        $student = User::where('name', $studentNumber)->first();

                        $student->update([
                            'email' => $privateEmail                            
                        ]);
                    }catch(\Exception $e){
                        // Ignore the exception and continue
                    }
                }            
            }

            $name = $request->input('name');
            if (!empty($name)) {
                $users->where('name', 'like', '%' . $name . '%');
            }
        }

        $users = $users->paginate(15);

        return view('users.index', compact('users'));
    }

    public function searchForUser(Request $request)
    {
        $searchTerm = $request->get('term');

        $users = User::where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                    ->get();

        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::latest()->get();
        return view('users.create',compact('roles'));
    }

    public function resetUserPassword($userId)
    {
        $user = User::find($userId);
        if ($user && $user->hasRole('Student')) {
            $username = $user->name;
            try {
                
                $privateEmail = trim(BasicInformation::find($username)->PrivateEmail);
                if (!filter_var($privateEmail, FILTER_VALIDATE_EMAIL)) {
                    $privateEmail = $username . '@lmmu.ac.zm';
                }
                $user->update([
                    'email' => $privateEmail,
                    'password' => Hash::make('12345678')
                ]);
                $this->sendTestEmail($username);
            } catch (\Exception $e) {
                // Log the exception or handle it as needed
            }
        }
        return redirect()->back()->with('success', 'Password reset successfully.');
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
            'password' => Hash::make('12345678')
        ]));

        $user->syncRoles($request->get('role'));

        return redirect()->route('users.index')
        ->with('success', 'Created Successfully.');
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
    // public function update(User $user, ProfileRequest $request) 
    // {
    //     $user->update($request->validated());

    //     $user->syncRoles($request->get('role'));

    //     return redirect()->route('users.index')
    //         ->withSuccess(__('User updated successfully.'));
    // }

    public function update(Request $request,$userId)
    {
        // Define your custom validation rules for the email and name fields
        $request->validate([
            'name' => ['required', 'min:3'],
            // 'email' => [
            //     'required',
            //     'email',
            //     Rule::unique((new User)->getTable())->ignore($userId),
            // ],
        ]);       

        $user = User::find($userId);
        $user->name = $request->input('name');
        // $user->email = $request->input('email');
        $user->save();
        
        $user->syncRoles($request->get('role'));

        return redirect()->back()->with('success', 'Updated Succesfully.');
    }

    public function resetPassword(Request $request, $userId)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ], [
            'password.required' => 'The new password field is required.',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.regex' => 'The password must contain at least one letter, one number, and one special character (!@#$%^&*).',
            // Add any other custom error messages for the 'password' field if needed.
        ]);

        // return $request->input('password');

        $user = User::find($userId);
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->back()->with('success', 'Updated Succesfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) 
    {
        $user->delete();

        return redirect()->route('users.index')
        ->with('success', 'Updated Successfully.');
    }
}
