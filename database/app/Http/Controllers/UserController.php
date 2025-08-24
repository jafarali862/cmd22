<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('backend.user.users', compact('users'));
    }

    public function create()
    {
        return view('backend.user.user_create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'phone_number' => 'required|string|max:10',
            'address' => 'required|string',
            'user_type'=>'required',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_phone' => 'required|string',
            'insurance_provider' => 'nullable|string',
            'insurance_policy_number' => 'nullable|string',
            'primary_physician' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_type' => 'nullable|string|max:10',
            'status' => 'required|boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'user_type'=>$request->user_type,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'insurance_provider' => $request->insurance_provider,
            'insurance_policy_number' => $request->insurance_policy_number,
            'primary_physician' => $request->primary_physician,
            'medical_history' => $request->medical_history,
            'medications' => $request->medications,
            'allergies' => $request->allergies,
            'blood_type' => $request->blood_type,
            'status' => $request->status,
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'New User Created',
            'message' =>  'New User: ' . $request->name . 'created by ' . Auth::user()->name,
        ]);

        return redirect()->route('users.index')->with('success', 'User added successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('backend.user.user_edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6',
                'date_of_birth' => 'required|date',
                'gender' => 'required|string',
                'phone_number' => 'required|string|max:10',
                'address' => 'required|string',
                'user_type'=>'required',
                'emergency_contact_name' => 'nullable|string',
                'emergency_contact_phone' => 'nullable|string',
                'insurance_provider' => 'nullable|string',
                'insurance_policy_number' => 'nullable|string',
                'primary_physician' => 'nullable|string',
                'medical_history' => 'nullable|string',
                'medications' => 'nullable|string',
                'allergies' => 'nullable|string',
                'blood_type' => 'nullable|string|max:10',
                'status' => 'nullable|boolean',
                'role' => 'nullable'
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'user_type'=>$request->user_type,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'insurance_provider' => $request->insurance_provider,
                'insurance_policy_number' => $request->insurance_policy_number,
                'primary_physician' => $request->primary_physician,
                'medical_history' => $request->medical_history,
                'medications' => $request->medications,
                'allergies' => $request->allergies,
                'blood_type' => $request->blood_type,
                'status' => $request->status,
            ]);

            $user = User::findOrFail($id);
            if ($user->status == 0) {
                $user->tokens()->delete();
            }

            Log::create([
                'user_id' => auth()->id(),
                'log_type' => 'User updated',
                'message' =>  'User: ' . $request->name . ' , user Id: ' . $id . ' , User updated by ' . Auth::user()->name,
            ]);

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->tokens()->delete();
        $user->delete();

        Log::create([
                'user_id' => auth()->id(),
                'log_type' => 'User Deleted',
                'message' =>  'User: ' . $user->name . ' , user Id: ' . $id . ' , User deleted by ' . Auth::user()->name,
            ]);

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
