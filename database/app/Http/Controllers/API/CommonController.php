<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\Clinic;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use App\Models\PharmacyMedicine;
use App\Models\ClinicPrescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PharmacyPrescription;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    public function pharmacyLister()
    {
        try {
            $userId = Auth::user()->id;           
            $pharmacies = Pharmacy::leftJoin('pharmacy_prescriptions as pp', 'pp.pharmacy_id', '=', 'pharmacies.id')
                ->leftJoin('pharmacy_medicines as pm', function($join) {
                    $join->on('pm.pharmacy_prescription_id', '=', 'pp.id')
                         ->whereNull('pm.deleted_at'); 
                })
                ->select(
                    'pharmacies.id as id',
                    'pharmacy_name',
                    'pharmacy_address',
                    'pharmacy_photo',
                    'pharmacies.city as city',
                    'pharmacies.email as email',
                    'pharmacies.phone_number as phone_number',
                    DB::raw("MAX(IF(pp.user_id = $userId  AND pm.medicine_name IS NOT NULL, 1, 0)) as med_status") 
                )
                ->groupBy('id', 'pharmacy_name', 'pharmacy_address', 'pharmacy_photo', 'pharmacies.city', 'pharmacies.email', 'pharmacies.phone_number')
                ->distinct()
                ->get();

               
                
            return response()->json(['pharmacys' => $pharmacies]);
        } catch (Exception $e) {
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

        try {

            $clinics = Clinic::where('id', $id)->pluck('tests')->first();
            $tests = explode(',', json_decode($clinics));
            $tests = array_map('trim', $tests);
            return response()->json(['tests' => $tests]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pPrescription(Request $request)
    {

        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'pharmacy_id' => 'required',
                'prescription.*' => 'required|image',
                'delivery_address' => 'required|string',
                'lat_long' => 'required|string',
            ]);

            $prescriptionPaths = [];
            foreach ($request->file('prescription') as $file) {
                $prescriptionPaths[] = $file->store('prescriptions', 'public');
            }

            PharmacyPrescription::create([
                'user_id' => $request->user_id,
                'pharmacy_id' => $request->pharmacy_id,
                'prescription' => json_encode($prescriptionPaths),
                'delivery_address' => $request->delivery_address,
                'lat_long' => $request->lat_long,
            ]);
            return response()->json(['success' => 'Prescription added successfully.'], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pMedicines(Request $request)
    {
       try {
            $request->validate([
                'user_id' => 'required',
                'pharmacy_id' => 'required',
            ]);

            $presID=PharmacyPrescription::where('user_id', $request->user_id)
                                        ->where('pharmacy_id', $request->pharmacy_id)
                                        ->latest()
                                        ->first();

                                     

            $medicinesPhar = PharmacyMedicine::where('pharmacy_prescription_id', $presID->id)
                                                ->select(
                                                    'id',
                                                    'medicine_name',
                                                    'quantity',
                                                    'total',
                                                    'start_time_1',
                                                    'end_time_1',
                                                    'start_time_2',
                                                    'end_time_2',
                                                    'start_time_3',
                                                    'end_time_3',
                                                    'req_unit',
                                                    'avail_unit',
                                                    'amount'
                                                )
                                                ->get();


            $medicines = $medicinesPhar->map(function ($medicine) {
                if ($medicine->req_unit == $medicine->avail_unit) {
                    $medicine->status = 0;
                } elseif ($medicine->req_unit > $medicine->avail_unit) {
                    $medicine->status = 1;
                } elseif ($medicine->avail_unit == 0) {
                    $medicine->status = 2;
                }
                return $medicine->only(['id','medicine_name', 'quantity', 'amount', 'total', 'req_unit', 'avail_unit', 'status']);
            });


            $timeFrame = [];

            if ($medicinesPhar->isNotEmpty()) {
                $firstMedicine = $medicinesPhar->first();

                $timeFrame = [
                    'start_time_1' => $firstMedicine->start_time_1 ? date('h:i A', strtotime($firstMedicine->start_time_1)) : 'N/A',
                    'end_time_1' => $firstMedicine->end_time_1 ? date('h:i A', strtotime($firstMedicine->end_time_1)) : 'N/A',
                    'start_time_2' => $firstMedicine->start_time_2 ? date('h:i A', strtotime($firstMedicine->start_time_2)) : 'N/A',
                    'end_time_2' => $firstMedicine->end_time_2 ? date('h:i A', strtotime($firstMedicine->end_time_2)) : 'N/A',
                    'start_time_3' => $firstMedicine->start_time_3 ? date('h:i A', strtotime($firstMedicine->start_time_3)) : 'N/A',
                    'end_time_3' => $firstMedicine->end_time_3 ? date('h:i A', strtotime($firstMedicine->end_time_3)) : 'N/A'
                ];
            }

            if (count($medicines) == 0) {
                $medicines = ['No Medicine Available'];
            }

            return response()->json([
                'medicines' => $medicines,
                'time_frame' => $timeFrame,
                'pres_id'=>$presID->id
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cPrescription(Request $request)
    {

        try {

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
            ]);


            $prescriptionPaths = [];
            if ($request->hasFile('prescription')) {
                foreach ($request->file('prescription') as $file) {
                    $prescriptionPaths[] = $file->store('clinic_prescriptions', 'public');
                }
            }


            ClinicPrescription::create([
                'user_id' => $request->user_id,
                'clinic_id' => $request->clinic_id,
                'name' => $request->name,
                'age' => $request->age,
                'gender' => $request->gender,
                'prescription' => json_encode($prescriptionPaths),
                'test' => json_encode($request->test),
                'address' => $request->address,
                'lat_long' => $request->lat_long,
            ]);


            return response()->json(['success' => 'Prescriptions added successfully.'], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function cancelPharMedicine($id)
    {
        try{
             PharmacyMedicine::where('pharmacy_prescription_id','=',$id)
                                ->delete();   
              return response()->json(['success' => 'Order Cancelled Successfully.'], 200);
        }catch(Exception $e){
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
