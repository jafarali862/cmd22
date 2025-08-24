<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use App\Models\Medicine;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use App\Models\PharmacyMedicine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PharmacyPrescription as pprescription;

class PharmacyPrescription extends Controller
{
    public function index()
    {   
        $val=0;
        $medicines = Medicine::all();
        $pharmacyPrescriptions = pprescription::leftJoin('users','users.id','=','pharmacy_prescriptions.user_id')
                                                ->leftjoin('pharmacies','pharmacies.id','=','pharmacy_prescriptions.pharmacy_id')
                                                ->leftJoin('pharmacy_medicines','pharmacy_medicines.pharmacy_prescription_id','=','pharmacy_prescriptions.id')
                                                ->where('pharmacy_prescriptions.status','=',0)
                                                ->orderBy('pharmacy_prescriptions.created_at', 'desc')
                                                ->select('users.name as name','pharmacies.pharmacy_name as pharmacy','pharmacy_prescriptions.prescription as prescription','pharmacy_prescriptions.id as id','pharmacy_prescriptions.delivery_address','pharmacy_prescriptions.lat_long','pharmacy_prescriptions.created_at')
                                                ->paginate(15);
        return view('backend.pharmacypres.index', compact('pharmacyPrescriptions', 'medicines','val'));
    }

    public function edit($id)
    {
        $medicines = Medicine::all();
        $pharmacyPrescription = pprescription::findOrFail($id);
        
        $users = User::all();
        $deliveryAgents=User::where('user_type',1)->get();
        $pharmacies = Pharmacy::all();
        $medicinesPhar = PharmacyMedicine::with('medicine')->withTrashed()->where('pharmacy_prescription_id', $id)->distinct()->get();

        return view('backend.pharmacyPres.edit_ppres', compact('pharmacyPrescription', 'users', 'pharmacies', 'medicinesPhar', 'medicines','deliveryAgents'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([

            'delivery_address' => 'required|string',
        ]);

        $pharmacyPrescription = pprescription::findOrFail($id);
        $pharmacyPrescription->delivery_address = $request->delivery_address;
        $pharmacyPrescription->save();

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'Pharmacy Pescription Address Updated',
            'message' =>  'Pharmacy Pescription Address Updated: ' . $request->delivery_address . 'created by ' . Auth::user()->name,
        ]);

        return redirect()->route('pharmacy-prescriptions.index')->with('success', 'Prescription updated successfully.');
    }

    public function pharmacyPresStatus(Request $request)
    {
        $query = $request->input('status');
        $page = $request->input('page', 1);
        $pPrescriptions = pprescription::where('status', '=', $query)->orderBy('created_at', 'desc')->get();
         

        $output = '';
        foreach ($pPrescriptions as $prescription) {
            $prescriptionImages = '';
            if ($prescription->prescription) {
                $images = json_decode($prescription->prescription, true);
                
                if(count($images) > 1){
                    $prescriptionImages .= ' <i class="fas fa-images" style="color: green; font-size: 20px;"> +'. count($images) .'</i>';
                }else{
                    $prescriptionImages .= '<i class="fas fa-images" style="color: green; font-size: 20px;"></i>';
                }
            } else {
                $prescriptionImages = '<span>No image</span>';
            }

            $output .= '
                        <tr>
                            <td>' . $prescription->user->name . '</td>
                            <td>' . $prescription->pharmacy->pharmacy_name . '</td>
                            <td>' . $prescriptionImages . '</td>
                            <td>' . $prescription->delivery_address . '</td>
                            <td>' . $prescription->lat_long . '</td> 
                            <td>' . $prescription->created_at.'</td> 
                            <td>
                                <button class="btn btn-warning btn-sm open-edit-modal" data-id="' . $prescription->id . '" title="Edit">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>';
        }

        return response()->json([
            'output' => $output,
        ]);
    }

    public function pharmacyPresDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');



        $pPrescriptions = pprescription::whereBetween('created_at', [$startDate . '%', $endDate . '%'])->orderBy('created_at', 'desc')->paginate(15);

        $output = '';
        foreach ($pPrescriptions as $prescription) {
            $prescriptionImages = '';
            if ($prescription->prescription) {
                $images = json_decode($prescription->prescription, true);
                
                if(count($images) > 1){
                    $prescriptionImages .= ' <i class="fas fa-images" style="color: green; font-size: 20px;"> +'. count($images) .'</i>';
                }else{
                    $prescriptionImages .= '<i class="fas fa-images" style="color: green; font-size: 20px;"></i>';
                }
            } else {
                $prescriptionImages = '<span>No image</span>';
            }

            $output .= '
            <tr>
                <td>' . $prescription->user->name . '</td>
                <td>' . $prescription->pharmacy->pharmacy_name . '</td>
                <td>' . $prescriptionImages . '</td>
                <td>' . $prescription->delivery_address . '</td>
                <td>' . $prescription->lat_long . '</td>
                <td>' . $prescription->created_at.'</td> 
                <td>
                    <button class="btn btn-warning btn-sm open-edit-modal" data-id="' . $prescription->id . '" title="Edit">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>';
        }

        return response([
            'output' => $output,
            //'pagination' => $pPrescriptions->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }

    public function rejected($id)
    {
        $pharmacyPrescription = pprescription::findOrFail($id);
        $pharmacyPrescription->status=4;
        $pharmacyPrescription->save();
        return back();
    }
}
