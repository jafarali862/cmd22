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
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function register(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'nullable|string',
                'date_of_birth' => 'required|date',
                'gender' => 'required|string|max:10',
                'phone_number' => 'required|string|max:10|unique:users,phone_number',
                'address' => 'required|string',
                'user_type' => 'required|string',
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

            ], 
            [
                
                'email.unique' => 'This email is already registered',
                'phone_number.unique' => 'This phone number is already in use',
            ]);
        
            // ]);

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
                'user_type' => $request->user_type,
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

        } 
        
        catch (Exception $e) 
        {
            \Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delivery_login(Request $request)
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

    public function login(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required|digits_between:10,15',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $user = User::where('phone_number', $request->phone_number)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile number not registered.'
        ], 404);
    }

    $otp = rand(100000, 999999);
    $refId = rand(1000, 9999);

    $user->otp = $otp;
    $user->otp_ref_id = $refId;
    $user->otp_expires_at = now()->addMinutes(5);
    $user->save();

    $templateMessage = "Your login OTP for CoMed is $otp. Please enter this code to continue. Do not share this OTP with anyone. Areacode SCB";

    $response = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
        'username'   => 'ascbcomed',
        'password'   => 'Comed@123',
        'mobile'     => $user->phone_number,
        'message'    => $templateMessage,
        'sendername' => 'ARDSCB',
        'routetype'  => 1,
        'tid'        => '1207175437936872721',
        'var1'       => 'CoMed',
        'var2'       => $otp,
    ]);

    if (str_contains($response->body(), 'Sent Successfully')) {
        return response()->json([
            'success'       => true,
            'message'       => 'OTP sent successfully. Please verify to continue.',
            'otp_ref_id'    => $refId,
            'phone_number' => $user->phone_number,
            'otp'           => $otp, 
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Failed to send OTP.',
        'api_response' => $response->body(),
    ], 500);
}


public function verifyOtp(Request $request)
    {
    $validator = Validator::make($request->all(), [
    'phone_number' => 'required|digits_between:10,15',
    'otp'   => 'required|digits:6',
    ]);

    if ($validator->fails()) {
    return response()->json(['error' => $validator->errors()], 422);
    }

    $user = User::where('phone_number', $request->phone_number)->first();

    if (!$user) {
    return response()->json(['success' => false, 'message' => 'User not found.'], 404);
    }

    if ($user->otp != $request->otp) {
    return response()->json(['success' => false, 'message' => 'Invalid OTP.'], 401);
    }

    if (now()->gt($user->otp_expires_at)) {
    return response()->json(['success' => false, 'message' => 'OTP expired.'], 403);
    }

    // OTP is valid — clear it
    $user->otp = null;
    $user->otp_ref_id = null;
    $user->otp_expires_at = null;
    $user->save();

    // Generate token
    $token = $user->createToken('appToken')->accessToken;

    return response()->json([
    'success' => true,
    'message' => 'Login successful.',
    'token' => $token,
    'user' => $user
    ]);
    }


    public function auth(Request $request)
    {
    

    // return response()->json([
    //     'status'  => 'success',
    //     'data'    => [
    //         'token' => '20|yqe8LsUekqZJ543HoddYDBRUfefh9PZbkyyP2635',
    //     ],
    //     'message' => 'successful',
    // ], 200);


    // $result =  Http::post('https://neopay.dineshsoftware.in:546/areacode/payment/public', $request);

    // return $result->json();

   
    $response = Http::withoutVerifying()->post(
        'https://neopay.dineshsoftware.in:546/areacode/payment/public/api/auth',
        [
            'client_id'     => $request->input('client_id'),
            'username'      => $request->input('username'),
            'password'      => $request->input('password'),
        ]
    );
    
    return response()->json($response->json(), $response->status());
    }

    public function accountverification(Request $request)
    {
        
        // $response = Http::withoutVerifying()->post(
        // 'https://neopay.dineshsoftware.in:546/areacode/payment/public/api/account-verification',
        // [
        // 'ref_id'            => $request->input('ref_id'),
        // 'customer_id'       => $request->input('customer_id'),
        // 'accno'             => $request->input('accno'),
        // 'mobno'             => $request->input('mobno'),
        // 'bill_amt'          => $request->input('bill_amt'),
        // ]
        // );

        // return response()->json($response->json(), $response->status());


        $token = $request->bearerToken();
        \Log::info('Incoming Bearer token:', [$token]);
        
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])
            ->post('https://neopay.dineshsoftware.in:546/areacode/payment/public/api/account-verification', [
                'ref_id'      => $request->input('ref_id'),
                'customer_id' => $request->input('customer_id'),
                'accno'       => $request->input('accno'),
                'mobno'       => $request->input('mobno'),
                'bill_amt'    => $request->input('bill_amt'),
            ]);
        
        return response()->json($response->json(), $response->status());
    }

    public function fundtransfer(Request $request)
    {   
        
        $token = $request->bearerToken();
        \Log::info('Incoming Bearer token:', [$token]);
        
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])
            ->post('https://neopay.dineshsoftware.in:546/areacode/payment/public/api/fund-transfer',[
                'reference_id'              => $request->input('reference_id'),
                'accno'                     => $request->input('accno'),
                'mobileno'                  => $request->input('mobileno'),
                'txnamount'                 => $request->input('txnamount'),
                'otp'                       => $request->input('otp'),
                'remarks'                   => $request->input('remarks'),
                ]
                );

    return response()->json($response->json(), $response->status());   
    }

    public function fundtransferstatus(Request $request)
    {
        
        $token = $request->bearerToken();
        \Log::info('Incoming Bearer token:', [$token]);
        
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post('https://neopay.dineshsoftware.in:546/areacode/payment/public/api/fund-transfer-status',[

                  'ref_id'              => $request->input('ref_id')
                    ]
                    );
    
    return response()->json($response->json(), $response->status());
    
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
