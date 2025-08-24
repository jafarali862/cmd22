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
use Illuminate\Support\Carbon;

class PharmacyPrescription extends Controller
{
    public function index()
    {   
        $val=0;
        $medicines = Medicine::all();
        
        $pharmacyPrescriptions = pprescription::with(['user', 'pharmacy'])
        ->where('status', 0)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // $hasPaymentColumn = pprescription::where('status', 2)
        // ->whereIn('payment_method', [1, 2])
        // ->exists(); // ✅ Works globally

        return view('backend.pharmacypres.index', compact('pharmacyPrescriptions','medicines','val'));
    }

     public function index2()
    {   
        $val=0;
        $medicines = Medicine::all();
        
        $pharmacyPrescriptions = pprescription::with(['user', 'pharmacy'])
        ->where('status', 0)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('backend.pharmacypres2.index', compact('pharmacyPrescriptions', 'medicines','val'));
    }


    public function edit($id)
    {
        $medicines = Medicine::all();
        // $pharmacyPrescription = pprescription::findOrFail($id);
        $pharmacyPrescription = pprescription::with('deliveryAgent')->findOrFail($id);
        $users = User::all();
        $deliveryAgents=User::where('user_type',1)->get();
        $pharmacies = Pharmacy::all();
        $medicinesPhar = PharmacyMedicine::with('medicine')->withTrashed()->where('pharmacy_prescription_id', $id)->distinct()->get();

        return view('backend.pharmacyPres.edit_ppres', compact('pharmacyPrescription', 'users', 'pharmacies', 'medicinesPhar', 'medicines','deliveryAgents'));
    }

     public function edit2($id)
    {
        $medicines = Medicine::all();
        $pharmacyPrescription = pprescription::findOrFail($id);
        $users = User::all();
        $deliveryAgents=User::where('user_type',1)->get();
        $pharmacies = Pharmacy::all();
        $medicinesPhar = PharmacyMedicine::with('medicine')->withTrashed()->where('pharmacy_prescription_id', $id)->distinct()->get();

        return view('backend.pharmacyPres2.edit_ppres', compact('pharmacyPrescription', 'users', 'pharmacies', 'medicinesPhar', 'medicines','deliveryAgents'));
    }


    public function update(Request $request, $id)
    {
        //   dd($request->all());
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

     public function update2(Request $request, $id)
    {
        //   dd($request->all());
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

        return redirect()->route('pharmacy-prescriptions2.index')->with('success', 'Prescription updated successfully.');
    }


    public function pharmacyPresStatus(Request $request)
    {
        $query = $request->input('status');
       // $page = $request->input('page', 1);
        // $pPrescriptions = pprescription::where('status', '=', $query)->orderBy('created_at', 'desc')->get();
        $pPrescriptions = pprescription::where('status', '=', $query)->orderBy('created_at', 'desc')->paginate(10);
        

        $output = '';
        foreach ($pPrescriptions as $prescription) {
            $prescriptionImages = '';
            if ($prescription->prescription) {
    $images = json_decode($prescription->prescription, true);
    $imageUrls = array_map(function ($img) {
        return asset('storage/' . $img);
    }, $images);

    $encodedImageUrls = htmlspecialchars(json_encode($imageUrls), ENT_QUOTES, 'UTF-8');

    if (count($images) > 1) {
        $prescriptionImages .= '<a href="javascript:void(0);" class="show-prescriptions" data-images="' . $encodedImageUrls . '">
            <i class="fas fa-images" style="color: green; font-size: 20px;"> +' . count($images) . '</i></a>';
    } else {
        $prescriptionImages .= '<a href="javascript:void(0);" class="show-prescriptions" data-images="' . $encodedImageUrls . '">
            <i class="fas fa-images" style="color: green; font-size: 20px;"></i></a>';
    }
} else {
    $prescriptionImages = '<span>No image</span>';
}

            $output .= '<tr ' . (!empty($prescription->receipt) ? 'style="background-color: #b3b3ffc7;"' : '') . '>
           
            <td>' . htmlspecialchars($prescription->user->name) . '</td>
             <td>' . htmlspecialchars($prescription->name) . '</td> 
            <td>' . htmlspecialchars($prescription->pharmacy->pharmacy_name) . '</td>
            <td>' . $prescriptionImages . '</td>
            <td>' . htmlspecialchars($prescription->delivery_address) . '</td>
            <td>' . htmlspecialchars($prescription->lat_long) . '</td> 
            <td class="payment-method-th">' 
                . (($prescription->payment_method == 1) ? 'Online Payment' : (($prescription->payment_method == 2) ? 'Cash on Delivery' : '-')) 
            . '</td>
            <td>' . $prescription->created_at . '</td> 
            <td>
                <button class="btn btn-warning btn-sm open-edit-modal" data-id="' . $prescription->id . '" title="Edit">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>';
        
        }

        return response()->json([
            'output' => $output,
             'pagination' => $pPrescriptions->links('pagination::bootstrap-5')->render(),
        ]);
    }


       public function pharmacyPresStatus2(Request $request)
    {
        $query = $request->input('status');
       // $page = $request->input('page', 1);
        // $pPrescriptions = pprescription::where('status', '=', $query)->orderBy('created_at', 'desc')->get();
        $pPrescriptions = pprescription::where('status', '=', $query)->paginate(10);
        

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
             'pagination' => $pPrescriptions->links('pagination::bootstrap-5')->render(),
        ]);
    }

    public function pharmacyPresDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');



        $pPrescriptions = pprescription::whereBetween('created_at', [$startDate . '%', $endDate . '%'])->orderBy('created_at', 'desc')->paginate(10);

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


     public function pharmacyPresDate2(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');



        $pPrescriptions = pprescription::whereBetween('created_at', [$startDate . '%', $endDate . '%'])->orderBy('created_at', 'desc')->paginate(10);

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
        $pharmacyPrescription->status=5;
        $pharmacyPrescription->save();
        return back();
    }
    
}
