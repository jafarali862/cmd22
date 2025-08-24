<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAgent;
use App\Models\Log;
use Illuminate\Http\Request;
use App\Models\PharmacyMedicine;
use Illuminate\Support\Facades\Auth;
use App\Models\PharmacyPrescription as pprescription;

class PharmacyMedicineController extends Controller
{
    public function addMedicine(Request $request)
    {
       

        $request->validate([
            'pharmacy_prescription_id' => 'required',
            'medicines.*' => 'nullable|string|max:255',
            'quantities.*' => 'nullable|integer',
            'amounts.*' => 'nullable|numeric',
            'total.*' => 'nullable|numeric',
            'start_time_1' => 'nullable|date_format:H:i|after_or_equal:' .now()->format('H:i'),
            'end_time_1' => 'nullable|date_format:H:i|after:start_time_1',
            'start_time_2' => 'nullable|date_format:H:i|after_or_equal:' .now()->format('H:i'),
            'end_time_2' => 'nullable|date_format:H:i|after:start_time_2',
            'start_time_3' => 'nullable|date_format:H:i|after_or_equal:' .now()->format('H:i'),
            'end_time_3' => 'nullable|date_format:H:i|after:start_time_3',
            'req_unit.*' => 'nullable',
            'avail_unit.*' => 'nullable',
            'assigned_user'=>'nullable',
            'medicine'=>'nullable',
            'total_number'=>'nullable',
            'total_amount'=>'nullable',
            'refference'=>'nullable'
        ]);

        if ($request->medicines) {
            foreach ($request->medicines as $index => $medicineName) {
                if (isset($request->quantities[$index]) && isset($request->amounts[$index]) && isset($request->total[$index])) {
                    PharmacyMedicine::create([
                        'pharmacy_prescription_id' => $request->pharmacy_prescription_id,
                        'medicine_name' => $medicineName,
                        'quantity' => $request->quantities[$index],
                        'amount' => $request->amounts[$index],
                        'total' => $request->total[$index],
                        'start_time_1' => $request->start_time_1,
                        'end_time_1' => $request->end_time_1,
                        'start_time_2' => $request->start_time_2,
                        'end_time_2' => $request->end_time_2,
                        'start_time_3' => $request->start_time_3,
                        'end_time_3' => $request->end_time_3,
                        'req_unit' => $request->req_unit[$index],
                        'avail_unit' => $request->avail_unit[$index],
                        'status'=> 1
                    ]);
                    Log::create([
                        'user_id' => auth()->id(),
                        'log_type' => 'Pharmacy Medicine created',
                        'message' =>  'Pharmacy Medicine: ' .$medicineName  . ' created by: ' . Auth::user()->name,
                    ]);
                }
            }
             pprescription::where('id', '=', $request->pharmacy_prescription_id)->update([
                'status'=>1
             ]);

             $presData =pprescription::leftJoin('pharmacy_medicines','pharmacy_medicines.id','=','pharmacy_prescriptions.pharmacy_id')
                                      ->leftJoin('users','users.id','=','pharmacy_prescriptions.user_id')
                                      ->where('pharmacy_prescriptions.id', '=', $request->pharmacy_prescription_id)
                                      ->select('pharmacy_prescriptions.delivery_address as delivery_address','pharmacy_prescriptions.lat_long as lat_long','users.phone_number as customer_no','users.id as customer_id')
                                      ->first();

             DeliveryAgent::create([
                 'delivery_agent_id'=>$request->assigned_user,
                 'address'=>$presData->delivery_address,
                 'customer_id'=>$presData->customer_id,
                 'coordinates'=>$presData->lat_long,
                 'customer_mob'=>$presData->customer_no,
                 'delivery_status'=>0
             ]);
             

        }else if ($request->medicine) {
            
            PharmacyMedicine::create([
                'pharmacy_prescription_id' => $request->pharmacy_prescription_id,
                'medicine_name' => $request->medicine,
                'quantity' => $request->total_number,
                'amount' => $request->total_amount,
                'total' =>  $request->total_amount ,
                'start_time_1' => $request->start_time_1,
                'end_time_1' => $request->end_time_1,
                'start_time_2' => $request->start_time_2,
                'end_time_2' => $request->end_time_2,
                'start_time_3' => $request->start_time_3,
                'end_time_3' => $request->end_time_3,
                'req_unit' => null,
                'avail_unit' => null,
                'status'=> 1,
                'reffrnce'=>$request->refference
            ]);
            pprescription::where('id', '=', $request->pharmacy_prescription_id)->update([
                'status'=>1
             ]);
        } else {
            return back()->with(['error' => 'please add medicine']);
        }
       
        

        return redirect()->route('pharmacy-prescriptions.index');
    }


}
