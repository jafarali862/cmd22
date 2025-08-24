<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use App\Models\PharmacyMedicine;
use App\Models\ClinicPrescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PharmacyPrescription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class CommonController extends Controller
{
    public function pharmacyLister()
    {
        try 
        {
            $userId = Auth::user()->id;           
            $pharmacies = Pharmacy::leftJoin('pharmacy_prescriptions as pp', 'pp.pharmacy_id', '=', 'pharmacies.id')
                         
                ->leftJoin('pharmacy_medicines as pm', function($join) 
                {
                    $join->on('pm.pharmacy_prescription_id', '=', 'pp.id')
                    ->whereNull('pm.deleted_at'); 
                })

                ->select('pharmacies.id as id','pharmacy_name','pharmacy_address','pharmacy_photo','pharmacies.city as city','pharmacies.email as email','pharmacies.phone_number as phone_number',            
                        'account_holder_name','account_no','ifsc','upi','qr_code',        
             
                // DB::raw("MAX(IF(pp.status = 1 AND pp.user_id = $userId,1,0)) as med_status")

                DB::raw("MAX(IF(pp.receipt IS NOT NULL AND pp.receipt != '',0,IF(pp.status = 1 AND pp.user_id = $userId, 1, 0))) as med_status"))

                ->groupBy('id', 'pharmacy_name', 'pharmacy_address', 'pharmacy_photo', 'pharmacies.city', 'pharmacies.email', 'pharmacies.phone_number','account_holder_name','account_no','ifsc','upi','qr_code')
                ->distinct()
                ->get();
             
            return response()->json(['pharmacys' => $pharmacies]);

        } 
        catch (Exception $e) 
        {
            Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clinicLister()
    {
        try {
            $clinics = Clinic::all();
            return response()->json(['clinics' => $clinics]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clinicTestLister($id)
    {

        try 
        {

            $clinics = Clinic::where('id', $id)->pluck('tests')->first();
            $tests = explode(',', json_decode($clinics));
            $tests = array_map('trim', $tests);
            return response()->json(['tests' => $tests]);
        } 
        catch (Exception $e) 
        {
            Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pPrescription(Request $request)
    {

        try 
        {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string',
                'pharmacy_id' => 'required|integer',
                'prescription.*' => 'required|image',
                'delivery_address' => 'required|string',
                'lat_long' => 'required|string',
                'payment_method' => 'required|string',
            ]);
    
            $prescriptionPaths = [];
            foreach ($request->file('prescription') as $file) {
                $prescriptionPaths[] = $file->store('prescriptions', 'public');
            }
    
            $prescription = PharmacyPrescription::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'pharmacy_id' => $request->pharmacy_id,
                'prescription' => json_encode($prescriptionPaths),
                'delivery_address' => $request->delivery_address,
                'payment_method' => $request->payment_method,
                'lat_long' => $request->lat_long,
            ]);
    
            $user = \App\Models\User::find($request->user_id);
    
            if ($user && $user->phone_number) 
            {

                $orderId = 'ORD' . str_pad($prescription->id, 5, '0', STR_PAD_LEFT); 
                $templateMessage = "Thanks for your order on CoMed. We've received your medicine order ID: $orderId and will notify you once it's processed. Areacode SCB";
    
                $response = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
                    
                    'username'    => 'ascbcomed',
                    'password'    => 'Comed@123',
                    'mobile'      => $user->phone_number,
                    'message'     => $templateMessage,
                    'sendername'  => 'ARDSCB',
                    'routetype'   => 1,
                    'tid'         => '1207175446449105383', 
                    'var1'        => $orderId,
                ]);
                \Log::info('Pharmacy SMS sent: ' . $response->body());


            }
    
            return response()->json(['success' => 'Prescription added successfully.'], 200);
        } 

        catch (Exception $e) 
        {
            \Log::error('Pharmacy prescription error: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }   
    }

    public function clinicData()
    {
        try 
        {
            $userId         =   Auth::user()->id;
            $deliverRequest =   ClinicPrescription::leftJoin('users', 'users.id', '=', 'clinic_prescriptions.user_id')
                                ->where('clinic_prescriptions.delivery_id', $userId)
                                // ->where('clinic_prescriptions.user_id', $userId)
                                ->whereIn('clinic_prescriptions.status', [0,1,2,3,4,5]) 
                                ->select('clinic_prescriptions.id','clinic_prescriptions.name as customer_name', 'users.phone_number', 'clinic_prescriptions.lat_long as deliv_coordinates', 
                                        'clinic_prescriptions.address as deliv_address','clinic_prescriptions.otp as otp', 'clinic_prescriptions.status as status',
                                         DB::raw("clinic_prescriptions.updated_at as updated_at_str"))
                            
                                ->get();
            return response()->json(['clincData' => $deliverRequest]);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function pharmacyData()
    {
        try 
        {
            $userId         =   Auth::user()->id;

            
            $deliverRequest =   PharmacyPrescription::leftJoin('users', 'users.id', '=', 'pharmacy_prescriptions.user_id')
                                ->leftJoin('pharmacies', 'pharmacies.id', '=', 'pharmacy_prescriptions.pharmacy_id') // 👈 Added JOIN here
                                // ->where('pharmacy_prescriptions.delivery_id', $userId)
                                ->where('pharmacy_prescriptions.delivery_id', $userId)
                                ->whereIn('pharmacy_prescriptions.status', [3, 4]) 
                                ->select('pharmacy_prescriptions.id','pharmacy_prescriptions.name','users.phone_number','pharmacy_prescriptions.lat_long as deliv_coordinates', 'pharmacy_prescriptions.status',
                                         'pharmacy_prescriptions.delivery_address','pharmacy_prescriptions.payment_method', 'pharmacy_prescriptions.expect_date','pharmacy_prescriptions.total_amount',
                                         'pharmacies.qr_code','pharmacies.upi',
                                          DB::raw("pharmacy_prescriptions.updated_at as updated_at_str"))

                                ->get();
                                return response()->json(['pharamcyData' => $deliverRequest]);
        } 

        catch (Exception $e) 
        {
        return response()->json([
        'error' => true,
        'message' => $e->getMessage(),
        ], 500);
        }
    }


    public function verify(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'ref_id'      => 'required|string',
            'customer_id' => 'required|string',
            'accno'       => 'required|string',
            'mobno'       => 'required|string',
            'bill_amt'    => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Fake logic: Let's say the account number must be longer than 5 digits to succeed
        if (strlen($request->accno) < 6) {
            return response()->json([
                'status' => 'failure',
                'message' => 'CBS account number not generated',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'Account verification successful.',
        ]);
    }


    
    public function sendOtpToPhone(Request $request)
    {
       

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:10,15',
            'id'           => 'required|exists:pharmacy_prescriptions,id',
        ]);
        
        if ($validator->fails()) {
            Log::warning('OTP request failed validation', ['errors' => $validator->errors()]);
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        $user = User::where('phone_number', $request->phone_number)->first();
        
        if (!$user) {
            Log::info('OTP request failed: phone number not registered', ['phone' => $request->phone_number]);
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered.'
            ], 404);
        }
        
        $otp   = rand(1000, 9999);
        $refId = rand(1000, 9999);
        
        // Save OTP details to user
        $user->otp            = $otp;
        $user->otp_ref_id     = $refId;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();
        
        // ✅ Fetch the exact prescription by ID
        $pharmacyPrescription = PharmacyPrescription::find($request->id);
        
        if (!$pharmacyPrescription || $pharmacyPrescription->user_id !== $user->id) {
            Log::info('OTP request failed: prescription not found or does not belong to user', [
                'user_id' => $user->id,
                'prescription_id' => $request->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Prescription not found or does not belong to this user.'
            ], 404);
        }
        
        // Prepare SMS
        $orderId    = 'ORD' . str_pad($pharmacyPrescription->id, 5, '0', STR_PAD_LEFT);
        $templateId = '1207175446609878410';
        $templateMessage = "Your OTP for confirming pharmacy order ID: $orderId delivery is $otp. Please share this with our delivery agent to complete the delivery. Areacode SCB";
        
        $smsPayload = [
            'username'   => 'ascbcomed',
            'password'   => 'Comed@123',
            'mobile'     => $user->phone_number,
            'message'    => $templateMessage,
            'sendername' => 'ARDSCB',
            'routetype'  => 1,
            'tid'        => $templateId,
            'var1'       => $orderId,
            'var2'       => $otp,
        ];
        
        $response = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', $smsPayload);
        
        // Log SMS attempt
        Log::info('Pharmacy OTP SMS attempted', [
            'mobile'     => $user->phone_number,
            'order_id'   => $orderId,
            'status'     => $response->status(),
            'response'   => $response->body(),
            'payload'    => $smsPayload,
        ]);
        
        // Check if SMS sent successfully
        if (str_contains($response->body(), 'Sent Successfully')) {
            // ✅ Update the prescription record
            $pharmacyPrescription->status = 4;
            $pharmacyPrescription->otp    = $otp; // assuming you have an `otp` column
            $pharmacyPrescription->save();
        
            return response()->json([
                'success'  => true,
                'message'  => 'OTP sent successfully. Please verify to continue.',
                'otp'      => $otp,
                'order_id' => $orderId,
            ]);
        }
        
        // If failed to send SMS
        return response()->json([
            'success'      => false,
            'message'      => 'Failed to send OTP.',
            'api_response' => $response->body(),
        ], 500);
        
        
    
    }

    public function sendOtpToPhoneclinic(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits_between:10,15',
            'id'           => 'required|exists:clinic_prescriptions,id', // validate prescription ID
        ]);
        
        if ($validator->fails()) {
            Log::warning('OTP request failed validation', ['errors' => $validator->errors()]);
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        $user = User::where('phone_number', $request->phone_number)->first();
        
        if (!$user) {
            Log::info('OTP request failed: phone number not registered', ['phone' => $request->phone_number]);
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered.'
            ], 404);
        }
        
        $otp   = rand(1000, 9999);
        $refId = rand(1000, 9999);
        
        // Save OTP to user
        $user->otp            = $otp;
        $user->otp_ref_id     = $refId;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();
        
        // ✅ Fetch Clinic Prescription by ID
        $clinicPrescription = ClinicPrescription::find($request->id);
        
        if (!$clinicPrescription || $clinicPrescription->user_id !== $user->id) {
            Log::info('OTP request failed: clinic prescription not found or does not belong to user', [
                'user_id' => $user->id,
                'prescription_id' => $request->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Clinic prescription not found or does not belong to this user.'
            ], 404);
        }
        
        $orderId = 'CP' . str_pad($clinicPrescription->id, 5, '0', STR_PAD_LEFT);
        $templateId = '1207175446753325392';
        $templateMessage = "Your OTP for confirming sample collection Test ID: $orderId is $otp. Please provide this to our agent to verify your pickup. Areacode SCB";
        
        $smsPayload = [
            'username'   => 'ascbcomed',
            'password'   => 'Comed@123',
            'mobile'     => $user->phone_number,
            'message'    => $templateMessage,
            'sendername' => 'ARDSCB',
            'routetype'  => 1,
            'tid'        => $templateId,
            'var1'       => $orderId,
            'var2'       => $otp,
        ];
        
        // Send SMS
        $response = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', $smsPayload);
        
        // Log SMS attempt
        Log::info('Clinic OTP SMS attempted', [
            'mobile'     => $user->phone_number,
            'order_id'   => $orderId,
            'status'     => $response->status(),
            'response'   => $response->body(),
            'payload'    => $smsPayload,
        ]);
        
        // If SMS sent successfully
        if (str_contains($response->body(), 'Sent Successfully')) {
            // ✅ Update clinic prescription with OTP and status
            $clinicPrescription->status = 4;
            $clinicPrescription->otp    = $otp; // Ensure you have an `otp` column
            $clinicPrescription->save();
        
            return response()->json([
                'success'  => true,
                'message'  => 'OTP sent successfully. Please verify to continue.',
                'otp'      => $otp,
                'order_id' => $orderId,
            ]);
        }
        
        // Failed to send
        return response()->json([
            'success'      => false,
            'message'      => 'Failed to send OTP.',
            'api_response' => $response->body(),
        ], 500);
        
    }
    
    public function clinicDataCompleted(Request $request, $id)
    {
        try {
       
            $validator = Validator::make($request->all(), [
                'delivery_coordinates' => 'required',
                'otp' => 'required',
                // 'status' => 'required',
            ]);
    
            if ($validator->fails()) 
            {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $prescriptions = ClinicPrescription::where('id', $id)->get();
    
            if ($prescriptions->isEmpty())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'No delivery records found.',
                ], 404);
            }
    
            // Check if any record matches the OTP
            // $matched = false;
            foreach ($prescriptions as $prescription) 
            {
                // if ($prescription->otp === $request->otp) {
                    $prescription->update([
                        'status' => 2,
                        'lat_long' => $request->delivery_coordinates,
                        'otp' => $request->otp,
                        'updated_at' => now()
                    ]);
                    // $matched = true;
                // }
            }
    
            // if (!$matched) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Invalid OTP.',
            //     ], 400);
            // }
    
            return response()->json([
                'status' => true,
                'message' => 'Delivery Completed Successfully.',
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    public function clinicDataCompletednew(Request $request, $id)
    {
        
        try {
            $validator = Validator::make($request->all(), [
                'delivery_coordinates'  => 'required',
                'otp'                   => 'required|digits:4',
                'phone_number'          => 'required|digits_between:10,15',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $user = User::where('phone_number', $request->phone_number)->first();
    
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mobile number not registered.'
                ], 404);
            }
    
        
            if ($user->otp !== $request->otp || now()->gt($user->otp_expires_at)) 
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired OTP.'
                ], 403);
            }
    
            // $prescriptions = ClinicPrescription::where('id', $id)->get();

            $prescriptions = ClinicPrescription::find($id);
    
            if ($prescriptions->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No delivery records found.',
                ], 404);
            }
    
            // foreach ($prescriptions as $prescription) {
            //     $prescription->update([
            //         'status' => 2,
            //         'delivery_coordinates' => $request->delivery_coordinates,
            //         'otp' => $request->otp,
            //     ]);
            // }


            $prescriptions->status               = 1;
            $prescriptions->delivery_coordinates = $request->delivery_coordinates;
            $prescriptions->otp                  = $request->otp;
            // $prescription->payment_status = $request->payment_status ?? $prescription->payment_status;
            $prescriptions->updated_at           = now();
            $prescriptions->save();

    
            $user->otp = null;
            $user->otp_ref_id = null;
            $user->otp_expires_at = null;
            $user->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Delivery Completed Successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    

        public function pMedicines(Request $request)
        {

        try 
        { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'pharmacy_id' => 'required|integer',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $prescription = PharmacyPrescription::where('user_id', $request->user_id)
            ->where('pharmacy_id', $request->pharmacy_id)
            ->latest()
            ->first();

        if (!$prescription) 
        {
            return response()->json([
                'status' => false,
                'message' => 'No prescription found for this user and pharmacy.'
            ], 404);
        }

        $prescriptionDetails = PharmacyPrescription::where('id', $prescription->id)
            ->select('id', 'expect_date', 'payment_method', 'total_amount')
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Prescription Details Retrieved Successfully.',
            'data' => $prescriptionDetails
        ], 200);

        } 

        catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'An error occurred.',
            'error' => $e->getMessage()
        ], 500);
        }
        }


    public function cPrescription(Request $request)
    {
 
        try 
        {
            $request->validate([
                'user_id' => 'required',
                'name' => 'required|string',
                'age' => 'required',
                'gender' => 'required',
                'clinic_id' => 'required',
                'prescription.*' => 'nullable|image',
                'test.*' => 'required',
                'address' => 'required|string',
                'lat_long' => 'nullable|string',
                'from_time' => 'nullable|string',
                'to_time' => 'nullable|string',
                'scheduled_at' => 'required',
            ]);
        
            $prescriptionPaths = [];
            if ($request->hasFile('prescription')) {
                foreach ($request->file('prescription') as $file) {
                    $prescriptionPaths[] = $file->store('clinic_prescriptions', 'public');
                }
            }
        
            $prescription = ClinicPrescription::create([
                'user_id'       => $request->user_id,
                'clinic_id'     => $request->clinic_id,
                'name'          => $request->name,
                'age'           => $request->age,
                'from_time'     => $request->from_time,
                'to_time'       => $request->to_time,
                'gender'        => $request->gender,
                'prescription'  => json_encode($prescriptionPaths),
                'test'          => json_encode($request->test),
                'address'       => $request->address,
                'scheduled_at'  => $request->scheduled_at,
                'lat_long'      => $request->lat_long,
            ]);
        
            $user = User::find($request->user_id);
            $clinic = Clinic::find($request->clinic_id);
            $clinicName = $clinic->clinic_name ?? 'Clinic';
        
            if ($user && $user->phone_number)      
            {
               
                $clinicPrescriptionId = 'CP' . str_pad($prescription->id, 5, '0', STR_PAD_LEFT);       
                $templateMessage = "Thank you for booking a sample collection on $clinicName for Test ID: $clinicPrescriptionId. We appreciate you choosing CoMed. Areacode SCB";
        
                $response = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
                    'username'    => 'ascbcomed',
                    'password'    => 'Comed@123',
                    'mobile'      => $user->phone_number,
                    'message'     => $templateMessage,
                    'sendername'  => 'ARDSCB',
                    'routetype'   => 1,
                    'tid'         => '1207175446057843677',
                    'var1'        => $clinicName,
                    'var2'        => $clinicPrescriptionId,
                    'var3'        => 'CoMed',
                ]);

                Log::info('Attempting to send SMS to user', [
                    'mobile' => $user->phone_number,
                    'clinic_id' => $request->clinic_id,
                    'clinicPrescriptionId' => $clinicPrescriptionId,
                    'message_sent' => $templateMessage,
                    'sms_api_status' => $response->status(),
                    'sms_api_response' => $response->body(),
                ]);

            } 


            else 
            {
                Log::warning('User or user phone number not found', [
                    'user_id' => $request->user_id,
                    'phone_number' => $user->phone_number ?? null,
                ]);
            }
        
            return response()->json(['success' => 'Prescriptions added successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Prescription error: ' . $e->getMessage());
        
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

    public function getPrescriptions(Request $request,$userId)
    {

    try 
    {
        if (!Auth::check()) 
        {
            return response()->json(['error' => 'User not authenticated. Please login.'], 401);
        }

        $authenticatedUserId = Auth::user()->id;

        if ((int)$userId !== $authenticatedUserId)
        {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

            $prescriptions = DB::table('clinic_prescriptions')
            ->join('clinics', 'clinic_prescriptions.clinic_id', '=', 'clinics.id')
            ->where('clinic_prescriptions.user_id', $userId)
            ->select('clinic_prescriptions.id','clinic_prescriptions.name','clinics.clinic_name','clinic_prescriptions.clinic_id','clinic_prescriptions.address','clinic_prescriptions.age',
                            'clinic_prescriptions.gender','clinic_prescriptions.test','clinic_prescriptions.from_time',
            'clinic_prescriptions.to_time','clinic_prescriptions.prescription','clinic_prescriptions.pres_upload',
            'clinic_prescriptions.status',DB::raw('DATE(clinic_prescriptions.scheduled_at) as date'))
            ->get();

        return response()->json(['data' => $prescriptions], 200);

    } 
    catch (Exception $e) 
    {
        Log::error($e);
        return response()->json([
            'message' => 'Something went wrong, please try again later.',
            'error' => $e->getMessage()
        ], 500);
    }

    }


  public function getpharmacyPrescriptions(Request $request,$userId)
  {

   try 
   {
      if (!Auth::check()) 
      {
       return response()->json(['error' => 'User not authenticated. Please login.'], 401);
      }

      $authenticatedUserId = Auth::user()->id;

      if ((int)$userId !== $authenticatedUserId) 
      {
          return response()->json(['error' => 'Unauthorized access.'], 403);
      }

        $prescriptions = DB::table('pharmacy_prescriptions')
                        ->leftJoin('payments', 'payments.pres_id','=','pharmacy_prescriptions.id') 
                        ->leftJoin('pharmacies', 'pharmacy_prescriptions.pharmacy_id', '=', 'pharmacies.id')
                        ->where('pharmacy_prescriptions.user_id', $userId)
                        ->select('pharmacy_prescriptions.id','pharmacy_prescriptions.name','payments.ref_no as payment_ref_no','pharmacies.pharmacy_name','pharmacy_prescriptions.pharmacy_id',
                        'pharmacy_prescriptions.delivery_address',
                        'pharmacy_prescriptions.prescription','pharmacy_prescriptions.lat_long','pharmacy_prescriptions.payment_method','pharmacy_prescriptions.total_amount',
                        'pharmacy_prescriptions.delivery_id','pharmacy_prescriptions.status',DB::raw('DATE(pharmacy_prescriptions.expect_date) as date'))
                        ->get();

      return response()->json(['data' => $prescriptions], 200);

        } 

        catch (Exception $e) 
        {
        Log::error($e);
        return response()->json([
        'message' => 'Something went wrong, please try again later.',
        'error' => $e->getMessage()
        ], 500);
        }
        }


        public function updatePrescription(Request $request, $userId, $prescriptionId)
        {
        try {

        if (!Auth::check()) {
        return response()->json(['error' => 'User not authenticated. Please login.'], 401);
        }

        $authenticatedUserId = Auth::id();

        if ((int)$userId !== $authenticatedUserId) {
        return response()->json(['error' => 'Unauthorized access.'], 403);
        }


        $validated = $request->validate([
        'from_time'     => 'nullable|string',
        'to_time'       => 'nullable|string',
        'prescription'  => 'nullable|string',
        'status'        => 'nullable|string',
        'scheduled_at'  => 'nullable|date'
        ]);

        $prescription = DB::table('clinic_prescriptions')
        ->where('id', $prescriptionId)
        ->where('user_id', $userId)
        ->first();

        if (!$prescription) {
        return response()->json(['error' => 'Prescription not found.'], 404);
        }

        $dataToUpdate = array_filter($validated, function ($value) 
        {
        return $value !== null;
        });

        if (empty($dataToUpdate)) {
        return response()->json(['message' => 'No valid fields provided for update.'], 400);
        }

        DB::table('clinic_prescriptions')
        ->where('id', $prescriptionId)
        ->update($dataToUpdate);

        return response()->json(['message' => 'Prescription updated successfully.'], 200);

        } catch (\Exception $e) {
        Log::error($e);
        return response()->json([
        'message' => 'Something went wrong. Please try again later.',
        'error' => $e->getMessage()
        ], 500);
        }

        }


        public function getPharmacyhistory(Request $request, $userId)
        {

        try 
        {
        if (!Auth::check()) {
        return response()->json(['error' => 'User not authenticated. Please login.'], 401);
        }

        $authenticatedUserId = Auth::user()->id;

        if ((int)$userId !== $authenticatedUserId) 
        {
        return response()->json(['error' => 'Unauthorized access.'], 403);
        }


        $history = DB::table('pharmacy_prescriptions')
        ->leftJoin('pharmacy_medicines', 'pharmacy_medicines.pharmacy_prescription_id', '=', 'pharmacy_prescriptions.id')
        ->leftJoin('pharmacies', 'pharmacy_prescriptions.pharmacy_id', '=', 'pharmacies.id')
        ->leftJoin('payments', 'payments.pres_id','=','pharmacy_prescriptions.id')  // Fixed the join condition
        ->where('pharmacy_prescriptions.user_id', $userId)
        ->select(
        'payments.trans_status as payment_status',
        'payments.ref_no as payment_ref_no',
        'pharmacy_medicines.total as payment_amount',
        'pharmacy_medicines.created_at as payment_date',
        'pharmacies.pharmacy_name',
        'pharmacy_prescriptions.status'
        )
        ->get();


        return response()->json(['data' => $history], 200);

        } 

        catch (\Exception $e) 
        {
        Log::error($e);
        return response()->json([
        'message' => 'Something went wrong, please try again later.',
        'error' => $e->getMessage()
        ], 500);
        }
        }


        public function getCashDelivery(Request $request, $userId)
        {

        try 
        {
        if (!Auth::check()) 
        {
        return response()->json(['error' => 'User not authenticated. Please login.'], 401);
        }

        $authenticatedUserId = Auth::user()->id;

        if ((int)$userId !== $authenticatedUserId) {
        return response()->json(['error' => 'Unauthorized access.'], 403);
        }


        $history = DB::table('pharmacy_prescriptions')
        ->leftJoin('payments', 'payments.pres_id','=','pharmacy_prescriptions.id')  // Fixed the join condition
        ->where('pharmacy_prescriptions.user_id', $userId)
        ->where('pharmacy_prescriptions.payment_method', 2)
        ->select('pharmacy_prescriptions.id','pharmacy_prescriptions.status','pharmacy_prescriptions.total_amount','pharmacy_prescriptions.payment_method'
        )
        ->get();


        return response()->json(['data' => $history], 200);

        } catch (\Exception $e) {
        Log::error($e);
        return response()->json([
            'message' => 'Something went wrong, please try again later.',
            'error' => $e->getMessage()
        ], 500);
        }
        }



    public function pCashDelivery(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'User not authenticated. Please login.'], 401);
            }
    
            $authenticatedUserId = Auth::id();
    
            $request->validate([
                'id'             => 'required|integer',
                'total_amount'   => 'required|numeric',
                'payment_method' => 'required|in:1,2',
            ]);
    
            $prescriptionId = $request->id;
    
            // Verify prescription ownership
            $prescription = DB::table('pharmacy_prescriptions')
                ->where('id', $prescriptionId)
                ->where('user_id', $authenticatedUserId)
                ->first();
    
            if (!$prescription) {
                return response()->json(['error' => 'Prescription not found or unauthorized.'], 403);
            }
    
            // ✅ Update the prescription status, payment method, and total_amount
            DB::table('pharmacy_prescriptions')
                ->where('id', $prescriptionId)
                ->update([
                    'status'         => 2,
                    'payment_method' => $request->payment_method,
                    'total_amount'   => $request->total_amount,
                    'updated_at'     => now()
                ]);
    
            // Optional: fetch updated record
            $updatedData = DB::table('pharmacy_prescriptions')
                ->leftJoin('payments', 'payments.pres_id', '=', 'pharmacy_prescriptions.id')
                ->where('pharmacy_prescriptions.id', $prescriptionId)
                ->select(
                    'pharmacy_prescriptions.id',
                    'pharmacy_prescriptions.total_amount',
                    'pharmacy_prescriptions.payment_method',
                    'pharmacy_prescriptions.status'
                )
                ->first();
    
            return response()->json(['message' => 'Updated successfully.', 'data' => $updatedData], 200);
    
        } catch (\Exception $e) {
            \Log::error('pCashDelivery error: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    


public function updatePaymentMethod(Request $request, $id)
{
    try {
        // 1. Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated. Please login.'], 401);
        }

        $authenticatedUserId = Auth::user()->id;

        // 2. Validate request
        $validated = $request->validate([
            'payment_method' => 'required|in:1,2', // 1: Online, 2: Cash on Delivery
        ]);

        // 3. Check that the prescription exists and belongs to the user
        $prescription = DB::table('pharmacy_prescriptions')
            ->where('id', $id)
            ->where('user_id', $authenticatedUserId)
            ->first();

        if (!$prescription) {
            return response()->json(['error' => 'Prescription not found or unauthorized access.'], 404);
        }

        // 4. Update the payment method
        DB::table('pharmacy_prescriptions')
            ->where('id', $id)
            ->update([
                'payment_method' => $validated['payment_method'],
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Payment method updated successfully.',
            'payment_method' => $validated['payment_method']
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while updating payment method.',
            'details' => $e->getMessage()
        ], 500);
    }
}


public function updateAccountData(Request $request, $id)
{
    try {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $authenticatedUserId = Auth::id();

        $validated = $request->validate([
            'contact_number' => 'required|string|max:20',
            'account_no'     => 'required|string|max:50',
        ]);

        // Ensure the user exists and the authenticated user is updating their own data
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user || $user->id !== $authenticatedUserId) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        DB::table('users')
            ->where('id', $id)
            ->update([
                'contact_number' => $validated['contact_number'],
                'account_no'     => $validated['account_no'],
                'updated_at'     => now()
            ]);

        return response()->json(['message' => 'Account updated successfully.'], 200);

    } 
    catch (\Exception $e) 
    {
        return response()->json([
            'error'   => 'An error occurred while updating account.',
            'details' => $e->getMessage()
        ], 500);
    }
}





public function receiptPrescription(Request $request, $user_id, $pres_id)
{
    try {
        // Validate file and amount only — user_id is coming from route
        $request->validate([
            'receipt'      => 'required|image',
            'total_amount' => 'required|string',
        ]);

        if (!$request->hasFile('receipt')) {
            return response()->json([
                'success' => false,
                'message' => 'No receipt file was uploaded.',
            ], 400);
        }

        $receiptFile = $request->file('receipt');

        if (!$receiptFile->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Uploaded file is not valid.',
            ], 400);
        }

        $receiptPath = $receiptFile->store('prescriptions', 'public');

        // Find existing prescription record
        $pharmacyPres = PharmacyPrescription::where('id', $pres_id)
                            ->where('user_id', $user_id)
                            ->first();

        if (!$pharmacyPres) {
            return response()->json([
                'success' => false,
                'message' => 'Prescription record not found for this user.',
            ], 404);
        }

        // Update receipt and total_amount
        $pharmacyPres->receipt = $receiptPath;
        $pharmacyPres->total_amount = $request->total_amount;
        $pharmacyPres->save();

        return response()->json([
            'success' => true,
            'message' => 'Receipt data uploaded successfully.',
            'data'    => [
                'receipt'      => $pharmacyPres->receipt,
                'total_amount' => $pharmacyPres->total_amount,
                'created_at'   => $pharmacyPres->created_at->format('Y-m-d H:i:s'),
                'updated_at'   => $pharmacyPres->updated_at->format('Y-m-d H:i:s'),
            ],
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
            'error'   => $e->getMessage()
        ], 500);
    }
}


        public function getAccountData($id)
        {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $authenticatedUserId = Auth::id();

            $user = DB::table('users')
                ->select('id', 'name', 'email', 'contact_number', 'account_no')
                ->where('id', $id)
                ->first();

            if (!$user || $user->id !== $authenticatedUserId) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }

            return response()->json([
                'message' => 'User account data fetched successfully.',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch account data.',
                'details' => $e->getMessage()
            ], 500);
        }
        }

        public function cancelPharMedicine($id)
        {
        try {
        DB::beginTransaction();

        $prescription = PharmacyPrescription::findOrFail($id);
        $prescription->forceDelete();

        DB::commit();

        return response()->json(['success' => 'Prescription cancelled successfully.'], 200);
        } 
        catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Something went wrong, please try again later.',
            'error' => $e->getMessage()
        ], 500);
        }
        }


}
