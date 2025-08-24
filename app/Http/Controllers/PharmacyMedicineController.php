<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAgent;
// use App\Models\Log;
use Illuminate\Http\Request;
use App\Models\PharmacyMedicine;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Auth;
use App\Models\PharmacyPrescription as pprescription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PharmacyMedicineController extends Controller
{
    public function addMedicine(Request $request)
    {
   
        $request->validate([
        'pharmacy_prescription_id' => 'required',
        'payment_method.*' => 'nullable|string',
        'total_amount.*' => 'nullable|numeric',
        ]);

            $prescription = pprescription::findOrFail($request->pharmacy_prescription_id);
            $prescription->status = $request->status;
        
            if (!is_null($request->payment_method)) 
            {
                $prescription->payment_method = $request->payment_method;
            }
        
            if (!is_null($request->total_amount)) 
            {
                $prescription->total_amount = $request->total_amount;
            }
        
            if (!is_null($request->expect_date)) 
            {
                $prescription->expect_date = $request->expect_date;
            }
        
            if (!is_null($request->assigned_user)) 
            {
                $prescription->delivery_id = $request->assigned_user;
            }
        
            $prescription->save();
        
        
            $presData = pprescription::leftJoin('pharmacy_medicines', 'pharmacy_medicines.id', '=', 'pharmacy_prescriptions.pharmacy_id')
                        ->leftJoin('users as customer', 'customer.id', '=', 'pharmacy_prescriptions.user_id')
                        ->leftJoin('users as delivery', 'delivery.id', '=', 'pharmacy_prescriptions.delivery_id')
                        ->where('pharmacy_prescriptions.id', $request->pharmacy_prescription_id)
                        ->select('pharmacy_prescriptions.delivery_address as delivery_address',
                        'pharmacy_prescriptions.lat_long as lat_long',
                        'customer.phone_number as customer_no',
                        'customer.id as customer_id',
                        'delivery.phone_number as delivery_no',
                        'delivery.name as delivery_name')
                        ->first();


        if ($request->status == 1 && $presData && $presData->customer_no) 
        { 

        $orderId = 'ORD' . str_pad($prescription->id, 5, '0', STR_PAD_LEFT);
        $templateMessage = "Your prescription for order $orderId has been approved by our pharmacist. Your medicines will be prepared shortly. Areacode SCB";

        $response = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
            'username'    => 'ascbcomed',
            'password'    => 'Comed@123',
            'mobile'      => $presData->customer_no,
            'message'     => $templateMessage,
            'sendername'  => 'ARDSCB',
            'routetype'   => 1,
            'tid'         => '1207175446508518236',
            'var1'        => $orderId,
        ]);

        Log::info('Attempting to send SMS to user', [
            'mobile' => $presData->customer_no,
            'orderId' => $orderId,
            'message_sent' => $templateMessage,
            'sms_api_status' => $response->status(),
            'sms_api_response' => $response->body(),
        ]);

        }


        if ($request->status == 3 && !empty($prescription->delivery_id) && $presData && $presData->customer_no) 
        {

        $orderId = 'ORD' . str_pad($prescription->id, 5, '0', STR_PAD_LEFT);

        $deliveryUser = User::find($prescription->delivery_id);
        $deliveryName = $deliveryUser ? $deliveryUser->name : 'our delivery partner';

        $deliveryMessage = "Your pharmacy order ID: $orderId is out for delivery. Our delivery partner $deliveryName will deliver your order soon. Areacode SCB";

        $response2 = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
        'username'    => 'ascbcomed',
        'password'    => 'Comed@123',
        'mobile'      => $presData->customer_no,
        'message'     => $deliveryMessage,
        'sendername'  => 'ARDSCB',
        'routetype'   => 1,
        'tid'         => '1207175446231076349',
        'var1'        => $orderId,
        'var2'        => $deliveryName,
        ]);

        Log::info('Delivery SMS sent', [
        'mobile' => $presData->customer_no,
        'orderId' => $orderId,
        'delivery_person' => $deliveryName,
        'message_sent' => $deliveryMessage,
        'sms_api_status' => $response2->status(),
        'sms_api_response' => $response2->body(),
        ]);

        }


        if ($request->status == 3 && !empty($prescription->delivery_id) && $presData && $presData->delivery_no) 
        {

            $pharmacy = Pharmacy::find($prescription->pharmacy_id);
            $pharmacy_name = $pharmacy ? $pharmacy->pharmacy_name : 'Unknown Pharmacy';
        
            $deliveryboyMessage = "New pharmacy delivery assigned: Order ID: $orderId from $pharmacy_name. Please check your app for details. Areacode SCB";
        
            $response3 = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
                'username'    => 'ascbcomed',
                'password'    => 'Comed@123',
                'mobile'      => $presData->delivery_no,
                'message'     => $deliveryboyMessage,
                'sendername'  => 'ARDSCB',
                'routetype'   => 1,
                'tid'         => '1207175446450937525',
                'var1'        => $orderId,
                'var2'        => $pharmacy_name,
            ]);
        
            Log::info('Delivery SMS sent', [
                'mobile' => $presData->delivery_no,
                'orderId' => $orderId,
                'pharmacy_name' => $pharmacy_name,
                'message_sent' => $deliveryboyMessage,
                'sms_api_status' => $response3->status(),
                'sms_api_response' => $response3->body(),
            ]);

        }


        if ($request->status == 4 && $presData && $presData->customer_no) 
        {

        $otp = rand(100000, 999999); 

        $orderId = 'ORC' . str_pad($prescription->id, 5, '0', STR_PAD_LEFT);

        $deliverycompleteMessage = "Your OTP for confirming pharmacy order ID: $orderId delivery is $otp. Please share this with our delivery agent to complete the delivery. Areacode SCB";

        $response2 = Http::get('http://smsgt.niveosys.com/SMS_API/sendsms.php', [
        'username'    => 'ascbcomed',
        'password'    => 'Comed@123',
        'mobile'      => $presData->customer_no,
        'message'     => $deliverycompleteMessage,
        'sendername'  => 'ARDSCB',
        'routetype'   => 1,
        'tid'         => '1207175446609878410',
        'var1'        => $orderId,
        'var2'        => $otp,
        ]);

        Log::info('Delivery SMS sent', [
        'mobile' => $presData->customer_no,
        'orderId' => $orderId,
        'otp' => $otp,
        'message_sent' => $deliverycompleteMessage,
        'sms_api_status' => $response2->status(),
        'sms_api_response' => $response2->body(),
        ]);
        
        }

        return redirect()->route('pharmacy-prescriptions.index')->with('success', 'Prescription updated successfully.');

        }


}
