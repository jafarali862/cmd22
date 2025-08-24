<?php

namespace App\Http\Controllers\API;

use Log;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email',
                'password' => 'required|string|min:8',
                'date_of_birth' => 'required|date',
                'gender' => 'required|string|max:10',
                'phone_number' => 'required|string|max:15',
                'address' => 'required|string',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:15',
                'insurance_provider' => 'nullable|string|max:255',
                'insurance_policy_number' => 'nullable|string|max:255',
                'primary_physician' => 'nullable|string|max:255',
                'medical_history' => 'nullable|string',
                'medications' => 'nullable|string',
                'allergies' => 'nullable|string',
                'blood_type' => 'nullable|string|max:10',
                'status' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'insurance_provider' => $request->insurance_provider,
                'insurance_policy_number' => $request->insurance_policy_number,
                'primary_physician' => $request->primary_physician,
                'medical_history' => $request->medical_history,
                'medications' => $request->medications,
                'allergies' => $request->allergies,
                'blood_type' => $request->blood_type,
                'status' =>  1,
            ]);

            return response()->json([
                'message' => 'User successfully registered!',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {

                $user = User::find(Auth::user()->id);


                $token = $user->createToken('appToken')->accessToken;

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => $user,
                    'message' => 'User successfully Log in!',
                ], 200);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authenticate.',
                ], 401);
            }
        } catch (Exception $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {

            $userId = auth()->user()->id;

            if (!$userId) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email',
                'password' => 'nullable|string|min:8',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string|max:10',
                'phone_number' => 'nullable|string|max:15',
                'address' => 'nullable|string',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:15',
                'insurance_provider' => 'nullable|string|max:255',
                'insurance_policy_number' => 'nullable|string|max:255',
                'primary_physician' => 'nullable|string|max:255',
                'medical_history' => 'nullable|string',
                'medications' => 'nullable|string',
                'allergies' => 'nullable|string',
                'blood_type' => 'nullable|string|max:10',
                'role'=>'nullable|string|'

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = User::find($userId);
            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
                'date_of_birth' => $request->date_of_birth ?? $user->date_of_birth,
                'gender' => $request->gender ?? $user->gender,
                'phone_number' => $request->phone_number ?? $user->phone_number,
                'address' => $request->address ?? $user->address,
                'emergency_contact_name' => $request->emergency_contact_name ?? $user->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone ?? $user->emergency_contact_phone,
                'insurance_provider' => $request->insurance_provider ?? $user->insurance_provider,
                'insurance_policy_number' => $request->insurance_policy_number ?? $user->insurance_policy_number,
                'primary_physician' => $request->primary_physician ?? $user->primary_physician,
                'medical_history' => $request->medical_history ?? $user->medical_history,
                'medications' => $request->medications ?? $user->medications,
                'allergies' => $request->allergies ?? $user->allergies,
                'blood_type' => $request->blood_type ?? $user->blood_type,
                // 'role'=>$request->role
                
            ]);

            return response()->json([
                'message' => 'Profile updated successfully!',
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logOut()
    {
        try {
            $id = Auth::user()->id;
            $user=User::findOrFail($id);
            $user->tokens()->delete();

            return response()->json([
                'message' => 'User Logout Successfully!',
            ], 200);
        } catch (Exception $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
