<?php

use App\Http\Controllers\API\CommonController;
use App\Http\Controllers\Api\DeliveryAgentController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [UserController::class, 'register']);

Route::post('/login', [UserController::class, 'login']);
Route::post('/verify-otp', [UserController::class, 'verifyOtp']);

Route::post('/delivery-login', [UserController::class, 'delivery_login']);

Route::post('/auth', [UserController::class, 'auth']);

Route::middleware('auth:api','throttle:60,1')->group(function () {
    Route::get('/pharmacies', [CommonController::class, 'pharmacyLister']);
    Route::get('/clinics', [CommonController::class, 'clinicLister']);
    Route::get('/clinics_tests/{id}', [CommonController::class, 'clinicTestLister']);
    Route::post('/pharmacy_prescription', [CommonController::class, 'pPrescription']);
    Route::post('/pharmacy_medicine', [CommonController::class, 'pMedicines']);
    Route::post('/clinic_prescription', [CommonController::class, 'cPrescription']);
    Route::put('/profile_update', [UserController::class, 'updateProfile']);
    Route::post('/log_out', [UserController::class, 'logOut']);
    Route::delete('/pharmacy_med_cancel/{id}', [CommonController::class, 'cancelPharMedicine']);
    Route::get('/delivery', [DeliveryAgentController::class, 'deliveryRequests']);
    Route::post('/delivery_completed/{id}', [DeliveryAgentController::class, 'deliveryCompleted']);
    Route::post('/payment_conformed/{user_id}/{pres_id}', [PaymentController::class, 'paymentConformed']);
    Route::get('/payment_history/{user_id}', [PaymentController::class, 'paymentHistory']);

    Route::get('/clinic_data/{id}', [CommonController::class, 'clinicData']);
    Route::post('/clinic_data_completed/{id}', [CommonController::class, 'clinicDataCompleted']);

    Route::post('/pharmacy_delivery_completed/{id}', [DeliveryAgentController::class, 'pharmacydeliveryCompleted']);

    Route::get('/pharmacy_data/{id}', [CommonController::class, 'pharmacyData']);

    Route::post('/clinic_data_completed_new/{id}', [CommonController::class, 'clinicDataCompletednew']);
    
    Route::get('/clinic_order_history/{id}', [CommonController::class, 'getPrescriptions']);

     Route::get('/pharmacy_order_history/{id}', [CommonController::class, 'getpharmacyPrescriptions']);
    // Route::get('/cash_on_delivery/{id}', [CommonController::class, 'getCashDelivery']);
    Route::post('/cash_on_delivery', [CommonController::class, 'pCashDelivery']);

    Route::put('/payment_method_update/{id}', [CommonController::class, 'updatePaymentMethod']);

    // Route::put('/account_update/{id}', [CommonController::class, 'updateAccountData']);
    // Route::get('/pharmacy_order_history/{id}', [CommonController::class, 'getPharmacyhistory']);

    Route::put('/clinic_order_update/{userId}/{prescriptionId}', [CommonController::class, 'updatePrescription']);

    // Delivery OTP
    Route::post('/delivery-otp', [CommonController::class, 'sendOtpToPhone']);

    Route::post('/delivery-otp-clinic', [CommonController::class, 'sendOtpToPhoneclinic']);

    Route::put('/account_update/{id}', [CommonController::class, 'updateAccountData']);
    Route::get('/account_update/{id}', [CommonController::class, 'getAccountData']);
    // Route::post('/receipt_prescription', [CommonController::class, 'receiptPrescription']);
    Route::post('/receipt_prescription/{user_id}/{pres_id}', [CommonController::class, 'receiptPrescription']);

    // Account Verification
    Route::post('/account-verification', [UserController::class, 'accountverification']);
    Route::post('/fund-transfer', [UserController::class, 'fundtransfer']);
    Route::post('/fund-transfer-status', [UserController::class, 'fundtransferstatus']);


});
