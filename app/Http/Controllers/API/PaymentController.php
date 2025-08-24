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
        
        try{
            $validator = Validator::make($request->all(), [
                'status'=>'required',
                'ref_no'=>'required',
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
                'ref_no'=>$request->ref_no,
                'message'=>$request->message,
                'trans_status'=>$request->trans_status,
                'accno'=>$request->accno,
                'amount'=>$request->amount,
                'remark'=>$request->remark
            ]);
            

             $pharmacyPres=PharmacyPrescription::where('id',$pres_id)->first();
             $otp=DeliveryAgent::where('customer_id',$user_id)->latest()->first();

            if ($request->trans_status === 'success') {
                $pharmacyPres->status = 2;
                $pharmacyPres->save(); // Make sure to save the updated model
            }
             
            //     if($request->status=='success'){
            //         $pharmacyPres->status=2;
            //         $otp->otp= rand(1000, 9999);
            //         $otp->delivery_status= 1;
            //     }else{
            //         $pharmacyPres->status=1;
            //         $otp->otp= null;
            //         $otp->delivery_status= null;
            //     }
            //  $pharmacyPres->update();
            //  $otp->update();

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

     public function paymentHistory($user_id)
     {
        try{
            $paymentHistory=Payment::leftJoin('pharmacy_prescriptions','pharmacy_prescriptions.id','=','payments.pres_id')
                                    ->leftJoin('pharmacies','pharmacies.id','=','pharmacy_prescriptions.pharmacy_id')
                                    ->where('pharmacy_prescriptions.user_id',$user_id)
                                    ->select('payments.status as payment_status','payments.ref_no as payment_ref_no','payments.amount as payment_amount','payments.created_at as payment_date','pharmacies.pharmacy_name as pharmacy_name')
                                    ->get();

            return response()->json(['data'=>$paymentHistory],200);
        }catch(Exception $e){
           return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500); 
        }
     }
    
}
