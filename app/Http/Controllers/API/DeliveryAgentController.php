<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\PharmacyPrescription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryAgentController extends Controller
{
    public function deliveryRequests()
    {
        try 
        {
            $userId = Auth::user()->id;
            $deliverRequest = DeliveryAgent::leftJoin('users', 'users.id', '=', 'delivery_agents.customer_id')
                ->leftJoin('pharmacy_prescriptions','pharmacy_prescriptions.id','=','delivery_agents.pres_id')
                ->where('delivery_status', 0)
                ->where('delivery_agent_id', $userId)
                // ->select('users.name as customer_name', 'delivery_agents.address as deliv_address', 'delivery_agents.coordinates as deliv_coordinates', 'delivery_agents.customer_mob', 'delivery_agents.id', 'delivery_agents.otp as otp')
                
                ->select(
                'users.name as customer_name',
                'delivery_agents.address as deliv_address',
                'delivery_agents.coordinates as deliv_coordinates',
                'delivery_agents.customer_mob',
                'delivery_agents.id as delivery_agent_id',
                'delivery_agents.otp as otp',
                'pharmacy_prescriptions.total_amount',  // All fields from pharmacy_prescriptions
                DB::raw("
                CASE pharmacy_prescriptions.payment_method
                WHEN 1 THEN 'Online Payment'
                WHEN 2 THEN 'Cash on Delivery'
                ELSE 'Unknown'
                END as payment_method
                ")
                )

                ->get();
            return response()->json(['deliveryData' => $deliverRequest]);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }





    public function deliveryCompleted(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'delivered_coordinates' => 'required',
                'otp' => 'required',
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            $deliveryAgent = DeliveryAgent::where('id', $id)->where('otp', $request->otp)->first();

            if ($deliveryAgent) {
                $deliveryAgent->update([
                    'delivery_status'=>1,
                    'delivered_coordinates' => $request->delivered_coordinates,
                ]);

                PharmacyPrescription::where('id',$deliveryAgent->pres_id)->update([
                    'status'=>3
                ]);

                return response()->json([
                    'message' => 'Delivery Completed Successfully.',
                ]);
            } else {
                return response()->json([
                    'message' => 'Invalid OTP.',
                ], 400);
            }
        } 
        catch (Exception $e) 
        {
           return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }

    }


    
    public function pharmacydeliveryCompleted(Request $request, $id)
    {

        // try {
      
        //     $validator = Validator::make($request->all(), [
        //         'delivered_coordinates' => 'required',
        //         'otp'                   => 'required|digits:4',
        //         'phone_number'          => 'required|digits_between:10,15',
        //         'confirm_payment'        => 'nullable|image',
        //         'payment_type'          => 'nullable|string',

        //     ]);
    
        //     if ($validator->fails()) {
        //         return response()->json([
        //             'status' => false,
        //             'errors' => $validator->errors()
        //         ], 422);
        //     }
    
      
        //     $user = User::where('phone_number', $request->phone_number)->first();
    
        //     if (!$user) 
        //     {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'Mobile number not registered.',
        //         ], 404);
        //     }
    
    
        //     if ($user->otp !== $request->otp || !$user->otp_expires_at || now()->gt($user->otp_expires_at)) 
        //     {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'Invalid or expired OTP.',
        //         ], 400);
        //     }
    
 
        //     $prescription = PharmacyPrescription::find($id);
    
        //     if (!$prescription) 
        //     {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'No delivery records found.',
        //         ], 404);
        //     }
    

        //     $prescription->status               = 4;
        //     $prescription->delivery_coordinates = $request->delivered_coordinates;
        //     $prescription->otp                  = $request->otp;
        //     $prescription->confirm_payment      = $request->confirm_payment;
        //     $prescription->payment_type        = $request->payment_type;
        //     $prescription->updated_at           = now();
        //     $prescription->save();

        //     $user->otp = null;
        //     $user->otp_ref_id = null;
        //     $user->otp_expires_at = null;
        //     $user->save();
    
        //     return response()->json([
        //         'status' => true,
        //         'message' => 'Delivery Completed Successfully.',
        //     ]);
    
        // } 

        // catch (\Exception $e) 
        // {
        //     return response()->json([
        //         'error' => true,
        //         'message' => $e->getMessage(),
        //     ], 500);
        // }


        try {
            $validator = Validator::make($request->all(), [
                'delivered_coordinates' => 'required',
                'otp'                   => 'required|digits:4',
                'phone_number'          => 'required|digits_between:10,15',
                'confirm_payment'       => 'nullable|image', // image upload
                'payment_type'          => 'nullable|string',
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
                    'message' => 'Mobile number not registered.',
                ], 404);
            }
        
            if ($user->otp !== $request->otp || !$user->otp_expires_at || now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired OTP.',
                ], 400);
            }
        
            $prescription = PharmacyPrescription::find($id);
        
            if (!$prescription) {
                return response()->json([
                    'status' => false,
                    'message' => 'No delivery records found.',
                ], 404);
            }
        
            if ($request->hasFile('confirm_payment')) 
            {
                $file = $request->file('confirm_payment');
                $path = $file->store('prescriptions', 'public'); // Stored in: storage/app/public/prescriptions
                $prescription->confirm_payment = 'storage/' . $path; // Save accessible path in DB
            }
        
            $prescription->status = 4;
            $prescription->delivery_coordinates = $request->delivered_coordinates;
            $prescription->otp = $request->otp;
            $prescription->payment_type = $request->payment_type;
            $prescription->updated_at = now();
            $prescription->save();
        
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


    }
