<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
// use App\Models\Log;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Http\Request;
use App\Models\ClinicPrescription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ClinicPrescriptionController extends Controller
{
    public function index()
    {


    // $status = $request->input('status_filter', 0);

    // $clinicPrescriptions = ClinicPrescription::where('status', $status)->orderBy('created_at', 'desc')->paginate(10);

    return view('backend.clinicpres.index');

    }

    public function index2()
    {
        $clinicPrescriptions = ClinicPrescription::paginate(10);
        return view('backend.clinicpres2.index', compact('clinicPrescriptions'));
    }

    public function edit($id)
    {
        // $clinicPrescription = ClinicPrescription::findOrFail($id);

        $clinicPrescription = ClinicPrescription::with('deliveryAgent')->findOrFail($id);

        $deliveryAgents=User::where('user_type',1)->get();

        $status = $clinicPrescription->status;
        if ($status != 2 && $status != 3) {
            // ClinicPrescription::where('id', $id)->update([
            //     'status' => 1,
            // ]);
        }

        $cPres=ClinicPrescription::where('id',$id)->first();
        // Log::create([
        //     'user_id' => auth()->id(),
        //     'log_type' => 'clinic Prescription Processed',
        //     'message' =>  'clinic Prescription user:'.$cPres->user->name. ' processed by: ' . Auth::user()->name,
        // ]);

        $users = User::all();
        $clinic = Clinic::all();
        return view('backend.clinicPres.edit_cpres', compact('clinicPrescription', 'users', 'clinic','deliveryAgents'));
    }


      public function edit2($id)
    {
        $clinicPrescription = ClinicPrescription::findOrFail($id);
        $status = $clinicPrescription->status;
        if ($status != 2 && $status != 3) {
            // ClinicPrescription::where('id', $id)->update([
            //     'status' => 1,
            // ]);
        }

        $cPres=ClinicPrescription::where('id',$id)->first();
        // Log::create([
        //     'user_id' => auth()->id(),
        //     'log_type' => 'clinic Prescription Processed',
        //     'message' =>  'clinic Prescription user:'.$cPres->user->name. ' processed by: ' . Auth::user()->name,
        // ]);

        $users = User::all();
        $clinic = Clinic::all();
        return view('backend.clinicPres2.edit_cpres', compact('clinicPrescription', 'users', 'clinic'));
    }

   public function update(Request $request, $id)
   {

    // $request->validate([
    //     'address' => 'required|string',
    //     'status' => 'nullable',
    //     'pres_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // adjust types & size as needed
    // ]);

    // $ClinicPrescriptions = ClinicPrescription::findOrFail($id);
    // $ClinicPrescriptions->address = $request->address;
    // $ClinicPrescriptions->status = $request->status;

    // if ($request->filled('delivery_id')) {
    //     $ClinicPrescriptions->delivery_id = $request->delivery_id;
    // }
    
    // if ($request->hasFile('pres_upload')) 
    // {
    //     $file = $request->file('pres_upload');
    //     $filename = time() . '_' . $file->getClientOriginalName();
    //     $path = $file->storeAs('prescriptions', $filename, 'public');
    //     $ClinicPrescriptions->pres_upload = $path;
    // }

    // $ClinicPrescriptions->save();

    // Log::create([
    //     'user_id' => auth()->id(),
    //     'log_type' => 'clinic Prescription updated',
    //     'message' =>  'clinic address:'.$request->address. ' and status:'.$request->status .' updated by: ' . auth()->user()->name,
    // ]);

    // return redirect()->route('clinic-prescriptions.index')->with('success', 'Prescription updated successfully.');


    $request->validate([
        'address' => 'required|string',
        'status' => 'nullable',
        'pres_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    $ClinicPrescriptions = ClinicPrescription::findOrFail($id);
    $ClinicPrescriptions->address = $request->address;
    $ClinicPrescriptions->status = $request->status;

    if ($request->filled('delivery_id')) 
    {
    $ClinicPrescriptions->delivery_id = $request->delivery_id;
    }

    if ($request->hasFile('pres_upload')) 
    {
        $file = $request->file('pres_upload');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('prescriptions', $filename, 'public');
        $ClinicPrescriptions->pres_upload = $path;
    }

    $ClinicPrescriptions->save();

    $user = \App\Models\User::find($ClinicPrescriptions->user_id);
    $user2 = \App\Models\User::find($ClinicPrescriptions->delivery_id);
    $clinic = \App\Models\Clinic::find($ClinicPrescriptions->clinic_id);
    $clinicName = $clinic->clinic_name ?? 'Clinic';
    $testId = 'CP' . str_pad($ClinicPrescriptions->id, 5, '0', STR_PAD_LEFT);

        if ($request->status == 1 && $user && $user->phone_number) 
        {

        $templateMessage = "Your Test ID: $testId has been accepted and is being processed after pickup from $clinicName. You’ll receive results soon. Areacode SCB";

        $response = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
            'username'   => 'ascbcomed',
            'password'   => 'Comed@123',
            'mobile'     => $user->phone_number,
            'message'    => $templateMessage,
            'sendername' => 'ARDSCB',
            'routetype'  => 1,
            'tid'        => '1207175446222684723',
            'var1'       => $testId,
            'var2'       => $clinicName,
        ]);

        Log::info('Accepted Test SMS sent', [
            'mobile' => $user->phone_number,
            'testId' => $testId,
            'clinicName' => $clinicName,
            'message_sent' => $templateMessage,
            'sms_api_status' => $response->status(),
            'sms_api_response' => $response->body(),
        ]);

        }


        if (!empty($ClinicPrescriptions->delivery_id) && $user && $user->phone_number) 
        {

        $deliveryUser = \App\Models\User::find($ClinicPrescriptions->delivery_id);
        $deliveryName = $deliveryUser ? $deliveryUser->name : 'our partner';

        $scheduledMessage = "Your test has been scheduled Test ID: $testId. Our partner $deliveryName from $clinicName will visit shortly for sample collection. Areacode SCB";

        $response2 = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
            'username'   => 'ascbcomed',
            'password'   => 'Comed@123',
            'mobile'     => $user->phone_number,
            'message'    => $scheduledMessage,
            'sendername' => 'ARDSCB',
            'routetype'  => 1,
            'tid'        => '1207175446666883293',
            'var1'       => $testId,
            'var2'       => $deliveryName,
            'var3'       => $clinicName,
        ]);

        Log::info('Scheduled Sample Collection SMS sent', [
            'mobile' => $user->phone_number,
            'testId' => $testId,
            'delivery_person' => $deliveryName,
            'clinicName' => $clinicName,
            'message_sent' => $scheduledMessage,
            'sms_api_status' => $response2->status(),
            'sms_api_response' => $response2->body(),
        ]);

        }

        if ($request->status == 2 && !empty($ClinicPrescriptions->delivery_id) && $user2 && $user2->phone_number) 
        {

        // $testId = 'CP' . str_pad($ClinicPrescriptions->id, 5, '0', STR_PAD_LEFT);

        // $deliveryboyMessage = "New sample collection assigned: Test ID: $testId from $clinicName. Please check your app for details. Areacode SCB";

        // $response3 = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
        // 'username'    => 'ascbcomed',
        // 'password'    => 'Comed@123',
        // 'mobile'      => $user2->phone_number,
        // 'message'     => $deliveryboyMessage,
        // 'sendername'  => 'ARDSCB',
        // 'routetype'   => 1,
        // 'tid'         => '1207175446404757540',
        // 'var1'        => $testId,
        // 'var2'        => $clinicName,
        // ]);

        // Log::info('Delivery SMS sent', [
        // 'mobile' => $user2->phone_number,
        // 'testId' => $testId,
        // 'clinicName' => $clinicName,
        // 'message_sent' => $deliveryboyMessage,
        // 'sms_api_status' => $response3->status(),
        // 'sms_api_response' => $response3->body(),
        // ]);



         $otp = rand(100000, 999999); 

        $testId = 'CP' . str_pad($ClinicPrescriptions->id, 5, '0', STR_PAD_LEFT);
        $deliverycompleteMessage = "Your OTP for confirming sample collection Test ID: $testId is $otp. Please provide this to our agent to verify your pickup. Areacode SCB"; 

        $response2 = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
        'username'    => 'ascbcomed',
        'password'    => 'Comed@123',
        'mobile'      => $user2->phone_number,
        'message'     => $deliverycompleteMessage,
        'sendername'  => 'ARDSCB',
        'routetype'   => 1,
        'tid'         => '1207175446753325392', // Out-for-delivery TID
        'var1'        => $testId,
        'var2'        => $otp,
        ]);

        Log::info('Delivery SMS sent', [
        'mobile'            => $user2->phone_number,
        'testId'            => $testId,
        // 'clinicName'        => $clinicName,
        'otp'               => $otp,
        'message_sent'      => $deliverycompleteMessage,
        'sms_api_status'    => $response2->status(),
        'sms_api_response'  => $response2->body(),
        ]);


        
        }


        if ($request->status == 4 && $user && $user->phone_number) 
        {

        // $otp = rand(100000, 999999); 

        // $testId = 'CP' . str_pad($ClinicPrescriptions->id, 5, '0', STR_PAD_LEFT);
        // $deliverycompleteMessage = "Your OTP for confirming sample collection Test ID: $testId is $otp. Please provide this to our agent to verify your pickup. Areacode SCB"; 

        // $response2 = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
        // 'username'    => 'ascbcomed',
        // 'password'    => 'Comed@123',
        // 'mobile'      => $user->phone_number,
        // 'message'     => $deliverycompleteMessage,
        // 'sendername'  => 'ARDSCB',
        // 'routetype'   => 1,
        // 'tid'         => '1207175446753325392', // Out-for-delivery TID
        // 'var1'        => $testId,
        // 'var2'        => $otp,
        // ]);

        // Log::info('Delivery SMS sent', [
        // 'mobile'            => $user->phone_number,
        // 'testId'            => $testId,
        // 'clinicName'        => $clinicName,
        // 'otp'               => $otp,
        // 'message_sent'      => $deliverycompleteMessage,
        // 'sms_api_status'    => $response2->status(),
        // 'sms_api_response'  => $response2->body(),
        // ]);

        }

        return redirect()->route('clinic-prescriptions.index')->with('success', 'Prescription updated successfully.');
        }


    public function update2(Request $request, $id)
    {
        $request->validate([

            'address' => 'required|string',
            'status' => 'nullable'
        ]);

        $ClinicPrescriptions = ClinicPrescription::findOrFail($id);
        $ClinicPrescriptions->address = $request->address;
        $ClinicPrescriptions->status = $request->status;
        //   $ClinicPrescriptions->start_time = $request->start_time;

               if ($request->start_time) {
        try {
            // Try 12-hour format first
            $time = Carbon::createFromFormat('h:i A', $request->start_time);
        } catch (\Exception $e1) {
            try {
                // Fallback to 24-hour format
                $time = Carbon::createFromFormat('H:i', $request->start_time);
            } catch (\Exception $e2) {
                return response()->json(['error' => 'Invalid time format. Please use HH:MM or HH:MM AM/PM.'], 422);
            }
        }

        // Save time in AM/PM format (as string)
        $ClinicPrescriptions->start_time = $time->format('h:i A');
    }
        $ClinicPrescriptions->save();

        // Log::create([
        //     'user_id' => auth()->id(),
        //     'log_type' => 'clinic Prescription updated',
        //     'message' =>  'clinic address:'.$request->address. 'and status:'.$request->status .' updated by: ' . Auth::user()->name,
        // ]);
        return redirect()->route('clinic-prescriptions2.index')->with('success', 'Prescription updated successfully.');
    }


    public function clinicPresStatus(Request $request)
    {
        $query = $request->input('status');

        $cPrescriptions = ClinicPrescription::where('status', '=', $query)->orderBy('created_at', 'desc')->paginate(10);

        $output = '';


        $output = '';
        foreach ($cPrescriptions as $prescription) {
     $prescriptionImages = '';
 
     if ($prescription->prescription) {
         $images = json_decode($prescription->prescription, true);
 
         // Convert image paths to full URLs
         $imageUrls = array_map(function ($img) {
             return asset('storage/' . $img);
         }, $images);
 
         // Encode safely for HTML attribute
         $encodedImageUrls = htmlspecialchars(json_encode($imageUrls), ENT_QUOTES, 'UTF-8');
 
         // Build clickable icon
         $prescriptionImages .= '<a href="javascript:void(0);" class="show-prescriptions" data-images="' . $encodedImageUrls . '">
             <i class="fas fa-images" style="color: green; font-size: 20px;">' 
             . (count($images) > 1 ? ' +' . count($images) : '') . 
             '</i></a>';
     } else {
         $prescriptionImages = '<span>No image</span>';
     }

        // foreach ($cPrescriptions as $prescription) {
        //     $prescriptionImages = '';
        //     if ($prescription->prescription) {
        //         $images = json_decode($prescription->prescription, true);
             
        //         if(count($images) > 1){
        //             $prescriptionImages .= ' <i class="fas fa-images" style="color: green; font-size: 20px;"> +'. count($images) .'</i>';
        //         }else{
        //             $prescriptionImages .= '<i class="fas fa-images" style="color: green; font-size: 20px;"></i>';
        //         }
        //     } else {
        //         $prescriptionImages = '<span>No image</span>';
        //     }

            if ($prescription->test) {
                $tests = json_decode($prescription->test, true);
                foreach ($tests as $test) {
                    $test;
                }
            } else {
                $test = '<span>No Tests</span>';
            }


            $output .= '
                        <tr>
                            <td>' . $prescription->user->name . '</td>
                            <td>' . $prescription->name . '</td>
                            <td>' . $prescription->age . '</td>
                            <td>' . $prescription->gender . '</td>
                            <td>' . $prescription->clinic->clinic_name . '</td>
                            <td>' . $prescriptionImages . '</td>
                            <td>' . $prescription->address . '</td>
                            <td>' . $test . '</td>
                            <td>' . $prescription->lat_long . '</td>  
                            <td>
                                <button class="btn btn-warning btn-sm open-edit-modal" data-id="' . $prescription->id . '" title="Edit">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>';
        }

        return response([
            'output' => $output,
            'pagination' => $cPrescriptions->links('pagination::bootstrap-5')->render(),
            // 'pagination' => $cPrescriptions->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }

       public function clinicPresStatus2(Request $request)
    {
        $query = $request->input('status');

        $cPrescriptions = ClinicPrescription::where('status', '=', $query)->paginate(10);

        $output = '';
        foreach ($cPrescriptions as $prescription) {
            $prescriptionImages = '';
            if ($prescription->prescription) {
                $images = json_decode($prescription->prescription, true);
                // foreach ($images as $image) {
                //     $prescriptionImages .= '<img src="' . asset('storage/' . $image) . '" alt="Prescription Image" width="100" height="70" class="mr-1 mb-1">';
                // }
                if(count($images) > 1){
                    $prescriptionImages .= ' <i class="fas fa-images" style="color: green; font-size: 20px;"> +'. count($images) .'</i>';
                }else{
                    $prescriptionImages .= '<i class="fas fa-images" style="color: green; font-size: 20px;"></i>';
                }
            } else {
                $prescriptionImages = '<span>No image</span>';
            }

            if ($prescription->test) {
                $tests = json_decode($prescription->test, true);
                foreach ($tests as $test) {
                    $test;
                }
            } else {
                $test = '<span>No Tests</span>';
            }


            $output .= '
                        <tr>
                            <td>' . $prescription->user->name . '</td>
                            <td>' . $prescription->name . '</td>
                            <td>' . $prescription->age . '</td>
                            <td>' . $prescription->gender . '</td>
                            <td>' . $prescription->clinic->clinic_name . '</td>
                            <td>' . $prescriptionImages . '</td>
                            <td>' . $prescription->address . '</td>
                            <td>' . $test . '</td>
                            <td>' . $prescription->lat_long . '</td>  
                            <td>
                                <button class="btn btn-warning btn-sm open-edit-modal" data-id="' . $prescription->id . '" title="Edit">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>';
        }

        return response([
            'output' => $output,
            'pagination' => $cPrescriptions->links('pagination::bootstrap-5')->render(),
            // 'pagination' => $cPrescriptions->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }

    public function clinicPresDate(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');



        $cPrescriptions = ClinicPrescription::whereBetween('created_at', [$startDate . '%', $endDate . '%'])->paginate(10);

        $output = '';
        foreach ($cPrescriptions as $prescription) {
            $prescriptionImages = '';
            if ($prescription->prescription) {
                $images = json_decode($prescription->prescription, true);
                // foreach ($images as $image) {
                //     $prescriptionImages .= '<img src="' . asset('storage/' . $image) . '" alt="Prescription Image" width="100" height="70" class="mr-1 mb-1">';
                // }
                if(count($images) > 1){
                    $prescriptionImages .= ' <i class="fas fa-images" style="color: green; font-size: 20px;"> +'. count($images) .'</i>';
                }else{
                    $prescriptionImages .= '<i class="fas fa-images" style="color: green; font-size: 20px;"></i>';
                }
            } else {
                $prescriptionImages = '<span>No image</span>';
            }

            if ($prescription->test) {
                $tests = json_decode($prescription->test, true);
                foreach ($tests as $test) {
                    $test;
                }
            } else {
                $test = '<span>No Tests</span>';
            }

            $output .= '
            <tr>
                            <td>' . $prescription->user->name . '</td>
                            <td>' . $prescription->name . '</td>
                            <td>' . $prescription->age . '</td>
                            <td>' . $prescription->gender . '</td>
                            <td>' . $prescription->clinic->clinic_name . '</td>
                            <td>' . $prescriptionImages . '</td>
                            <td>' . $prescription->address . '</td>
                            <td>' . $test . '</td>
                            <td>' . $prescription->lat_long . '</td>  
                            <td>
                                <button class="btn btn-warning btn-sm open-edit-modal" data-id="' . $prescription->id . '" title="Edit">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>';
        }

        return response([
            'output' => $output,
            'pagination' => $cPrescriptions->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }


      public function clinicPresDate2(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');



        $cPrescriptions = ClinicPrescription::whereBetween('created_at', [$startDate . '%', $endDate . '%'])->paginate(10);

        $output = '';
        foreach ($cPrescriptions as $prescription) {
            $prescriptionImages = '';
            if ($prescription->prescription) {
                $images = json_decode($prescription->prescription, true);
                // foreach ($images as $image) {
                //     $prescriptionImages .= '<img src="' . asset('storage/' . $image) . '" alt="Prescription Image" width="100" height="70" class="mr-1 mb-1">';
                // }
                if(count($images) > 1){
                    $prescriptionImages .= ' <i class="fas fa-images" style="color: green; font-size: 20px;"> +'. count($images) .'</i>';
                }else{
                    $prescriptionImages .= '<i class="fas fa-images" style="color: green; font-size: 20px;"></i>';
                }
            } else {
                $prescriptionImages = '<span>No image</span>';
            }

            if ($prescription->test) {
                $tests = json_decode($prescription->test, true);
                foreach ($tests as $test) {
                    $test;
                }
            } else {
                $test = '<span>No Tests</span>';
            }

            $output .= '
            <tr>
                            <td>' . $prescription->user->name . '</td>
                            <td>' . $prescription->name . '</td>
                            <td>' . $prescription->age . '</td>
                            <td>' . $prescription->gender . '</td>
                            <td>' . $prescription->clinic->clinic_name . '</td>
                            <td>' . $prescriptionImages . '</td>
                            <td>' . $prescription->address . '</td>
                            <td>' . $test . '</td>
                            <td>' . $prescription->lat_long . '</td>  
                            <td>
                                <button class="btn btn-warning btn-sm open-edit-modal" data-id="' . $prescription->id . '" title="Edit">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>';
        }

        return response([
            'output' => $output,
            'pagination' => $cPrescriptions->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }

}
