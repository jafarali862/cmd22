<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Payment;
use App\Models\PharmacyPrescription;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function paymentConformed(Request $request,$user_id,$pres_id)
    {
        //dd($request);
        try{
            $validator = Validator::make($request->all(), [
                'status'=>'required',
                'message'=>'required',
                'trans_status'=>'nullable',
                'accno'=>'nullable',
                'amount'=>'nullable',
                'remark'=>'nullable'
            ]);

            if ($validator->fails()) {
                
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $payment=Payment::create([
                'pres_id'=>$pres_id,
                'status'=>$request->status,
                'message'=>$request->message,
                'trans_status'=>$request->trans_status,
                'accno'=>$request->accno,
                'amount'=>$request->amount,
                'remark'=>$request->remark
            ]);
            

             $pharmacyPres=PharmacyPrescription::where('id',$pres_id)->first();
             $otp=DeliveryAgent::where('customer_id',$user_id)->latest()->first();
             
                if($request->status=='success'){
                    $pharmacyPres->status=2;
                    $otp->otp= rand(1000, 9999);
                }else{
                    $pharmacyPres->status=1;
                    $otp->otp= null;
                }
             $pharmacyPres->update();
             $otp->update();

            if($payment){
                return response()->json(['data'=>'success'],201);
            }


        }catch(Exception $e)
        {
         return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
