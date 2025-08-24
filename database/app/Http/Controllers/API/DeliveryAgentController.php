<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryAgentController extends Controller
{
    public function deliveryRequests()
    {
        try{
            $userId=Auth::user()->id;
            $deliverRequest=DeliveryAgent::leftJoin('users','users.id','=','delivery_agents.customer_id')
                                           ->where('delivery_status',0)
                                           ->where('delivery_agent_id',$userId)
                                           ->select('users.name as customer_name','delivery_agents.address as deliv_address','delivery_agents.coordinates as deliv_coordinates','delivery_agents.customer_mob','delivery_agents.id','delivery_agents.otp as otp')
                                           ->get();
            return response()->json(['deliveryData'=>$deliverRequest]);  
        }catch(Exception $e){
            return $e->getMessage();
        }
                                    
    }

    public function deliveryCompleted(Request $request,$id)
    {
        try{
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
            $deliveryCompleted=DeliveryAgent::where('id',$id)->update([
               'delivered_coordinates'=>$request->delivered_coordinates,
               'otp'=>$request->otp
            ]);

            if( $deliveryCompleted){
                return response()->json(['success',200]);
            }

        }catch(Exception $e){
            $e->getMessage();
        }
    }
}
