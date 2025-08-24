
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PharmacyPrescription;
use App\Http\Controllers\PharmacyMedicineController;
use App\Http\Controllers\ClinicPrescriptionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('register', function() {
    return redirect()->route('login'); 
});

Route::middleware(['is_admin'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/clinic', [ClinicController::class, 'index'])->name('clinic');
    Route::get('/add_clinic', [ClinicController::class, 'createClinic'])->name('add-new-clinics');
    Route::post('/clinics/store', [ClinicController::class, 'store'])->name('clinics.store');
    Route::get('/clinics/{id}/edit', [ClinicController::class, 'edit'])->name('clinics.edit');
    Route::put('/clinics/{id}', [ClinicController::class, 'update'])->name('clinics.update');
    Route::delete('/clinics/{id}', [ClinicController::class, 'destroy'])->name('clinics.destroy');

    Route::get('/clinic-prescriptions', [ClinicPrescriptionController::class, 'index'])->name('clinic-prescriptions.index');
    Route::get('/clinic-prescriptions/{clinicPrescription}/edit', [ClinicPrescriptionController::class, 'edit'])->name('clinic-prescriptions.edit');
    Route::put('/clinic-prescriptions/{clinicPrescription}', [ClinicPrescriptionController::class, 'update'])->name('clinic-prescriptions.update');
    Route::get('/clinic-prescriptions/status', [ClinicPrescriptionController::class, 'clinicPresStatus'])->name('clinic-prescription.status');
    Route::get('/clinic-prescriptions/date-match', [ClinicPrescriptionController::class, 'clinicPresDate'])->name('clinic-prescription.dateMatch');

    Route::get('pharmacies', [PharmacyController::class, 'index'])->name('pharmacies.index');
    Route::get('pharmacies/create', [PharmacyController::class, 'create'])->name('pharmacies.create');
    Route::post('pharmacies', [PharmacyController::class, 'store'])->name('pharmacies.store');
    Route::get('pharmacies/{id}/edit', [PharmacyController::class, 'edit'])->name('pharmacies.edit');
    Route::put('pharmacies/{id}', [PharmacyController::class, 'update'])->name('pharmacies.update');
    Route::delete('pharmacies/{id}', [PharmacyController::class, 'destroy'])->name('pharmacies.destroy');

    Route::get('/pharmacy-prescriptions', [PharmacyPrescription::class, 'index'])->name('pharmacy-prescriptions.index');
    Route::get('/pharmacy-prescriptions/{pharmacyPrescription}/edit', [PharmacyPrescription::class, 'edit'])->name('pharmacy-prescriptions.edit');
    Route::put('/pharmacy-prescriptions/{pharmacyPrescription}', [PharmacyPrescription::class, 'update'])->name('pharmacy-prescriptions.update');
    Route::get('/pharmacy-prescription/status', [PharmacyPrescription::class, 'pharmacyPresStatus'])->name('pharmacy-prescription.status');
    Route::get('/pharmacy-prescription/date-match', [PharmacyPrescription::class, 'pharmacyPresDate'])->name('pharmacy-prescription.dateMatch');
    Route::get('/pharmacy-prescription/rejected/{id}', [PharmacyPrescription::class, 'rejected'])->name('pharmacy-prescription.rejected');

    Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
    Route::get('/medicines-create', [MedicineController::class, 'create'])->name('medicines.create');
    Route::post('/medicines-store', [MedicineController::class, 'store'])->name('medicines.store');
    Route::get('/medicines-edit/{id}', [MedicineController::class, 'edit'])->name('medicines.edit');
    Route::put('/medicines-update/{id}', [MedicineController::class, 'update'])->name('medicines.update');
    Route::delete('/medicines-distroy/{id}', [MedicineController::class, 'destroy'])->name('medicines.destroy');
    Route::post('medicines/import', [MedicineController::class, 'import'])->name('medicines.import');
    Route::get('/medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
    Route::get('/medicines/pharmacy', [MedicineController::class, 'pharmacySearch'])->name('medicines.pharmacy');
    
    Route::post('/pharmacy-medicines/store', [PharmacyMedicineController::class, 'addMedicine'])->name('pharmacy-medicines.store');
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy');

    Route::get('/day-book', [PharmacyController::class, 'daybook'])->name('day-book');
    Route::get('/daybook/export', [PharmacyController::class, 'export'])->name('daybook.export');
    Route::get('/report', [PharmacyController::class, 'report'])->name('report');
});



// User Routes

Route::middleware(['is_user'])->group(function () {
    Route::get('/home2', [App\Http\Controllers\CustomerController::class, 'index'])->name('home2');
    Route::get('/users2', [UserController::class, 'index2'])->name('users2.index');
    Route::get('/users2/create', [UserController::class, 'create2'])->name('users2.create');
    Route::post('/users2', [UserController::class, 'store2'])->name('users2.store');
    Route::get('/users2/{id}/edit', [UserController::class, 'edit2'])->name('users2.edit');
    Route::put('/users2/{id}', [UserController::class, 'update2'])->name('users2.update');
    Route::delete('/users2/{id}', [UserController::class, 'destroy2'])->name('users2.destroy');


    
    Route::get('/clinic2', [ClinicController::class, 'index2'])->name('clinic2');
    Route::get('/add_clinic2', [ClinicController::class, 'createClinic2'])->name('add-new-clinics2');
    Route::post('/clinics2/store', [ClinicController::class, 'store2'])->name('clinics2.store');
    Route::get('/clinics2/{id}/edit', [ClinicController::class, 'edit2'])->name('clinics2.edit');
    Route::put('/clinics2/{id}', [ClinicController::class, 'update2'])->name('clinics2.update');
    Route::delete('/clinics2/{id}', [ClinicController::class, 'destroy'])->name('clinics2.destroy');


    Route::get('pharmacies2', [PharmacyController::class, 'index2'])->name('pharmacies2.index');
    Route::get('pharmacies2/create', [PharmacyController::class, 'create2'])->name('pharmacies2.create');
    Route::post('pharmacies2', [PharmacyController::class, 'store2'])->name('pharmacies2.store');
    Route::get('pharmacies2/{id}/edit', [PharmacyController::class, 'edit2'])->name('pharmacies2.edit');
    Route::put('pharmacies2/{id}', [PharmacyController::class, 'update2'])->name('pharmacies2.update');
    Route::delete('pharmacies2/{id}', [PharmacyController::class, 'destroy2'])->name('pharmacies2.destroy');


    Route::get('/clinic-prescriptions2', [ClinicPrescriptionController::class, 'index2'])->name('clinic-prescriptions2.index');
    Route::get('/clinic-prescriptions2/{clinicPrescription}/edit', [ClinicPrescriptionController::class, 'edit2'])->name('clinic-prescriptions2.edit');
    Route::put('/clinic-prescriptions2/{clinicPrescription}', [ClinicPrescriptionController::class, 'update2'])->name('clinic-prescriptions2.update');
    Route::get('/clinic-prescriptions2/status', [ClinicPrescriptionController::class, 'clinicPresStatus2'])->name('clinic-prescription2.status');
    Route::get('/clinic-prescriptions2/date-match', [ClinicPrescriptionController::class, 'clinicPresDate2'])->name('clinic-prescription2.dateMatch');


    Route::get('/pharmacy-prescriptions2', [PharmacyPrescription::class, 'index2'])->name('pharmacy-prescriptions2.index');
    Route::get('/pharmacy-prescriptions2/{pharmacyPrescription}/edit', [PharmacyPrescription::class, 'edit2'])->name('pharmacy-prescriptions2.edit');
    Route::put('/pharmacy-prescriptions2/{pharmacyPrescription}', [PharmacyPrescription::class, 'update2'])->name('pharmacy-prescriptions2.update');
    Route::get('/pharmacy-prescription2/status', [PharmacyPrescription::class, 'pharmacyPresStatus2'])->name('pharmacy-prescription2.status');
    Route::get('/pharmacy-prescription2/date-match', [PharmacyPrescription::class, 'pharmacyPresDate2'])->name('pharmacy-prescription2.dateMatch');


    Route::get('/medicines2', [MedicineController::class, 'index2'])->name('medicines2.index');
    Route::get('/medicines2-create', [MedicineController::class, 'create2'])->name('medicines2.create');
    Route::post('/medicines2-store', [MedicineController::class, 'store2'])->name('medicines2.store');
    Route::get('/medicines2-edit/{id}', [MedicineController::class, 'edit2'])->name('medicines2.edit');
    Route::put('/medicines2-update/{id}', [MedicineController::class, 'update2'])->name('medicines2.update');
    Route::delete('/medicines2-distroy/{id}', [MedicineController::class, 'destroy2'])->name('medicines2.destroy');
    Route::post('medicines2/import', [MedicineController::class, 'import2'])->name('medicines2.import');
    Route::get('/medicines2/search', [MedicineController::class, 'search2'])->name('medicines2.search');
    Route::get('/medicines2/pharmacy', [MedicineController::class, 'pharmacySearch2'])->name('medicines2.pharmacy');
    
    Route::post('/pharmacy-medicines2/store', [PharmacyMedicineController::class, 'addMedicine2'])->name('pharmacy-medicines2.store');



    // Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
    // Route::get('/medicines-create', [MedicineController::class, 'create'])->name('medicines.create');
    // Route::post('/medicines-store', [MedicineController::class, 'store'])->name('medicines.store');
    // Route::get('/medicines-edit/{id}', [MedicineController::class, 'edit'])->name('medicines.edit');
    // Route::put('/medicines-update/{id}', [MedicineController::class, 'update'])->name('medicines.update');
    // Route::delete('/medicines-distroy/{id}', [MedicineController::class, 'destroy'])->name('medicines.destroy');
    // Route::post('medicines/import', [MedicineController::class, 'import'])->name('medicines.import');
    // Route::get('/medicines/search', [MedicineController::class, 'search'])->name('medicines.search');
    // Route::get('/medicines/pharmacy', [MedicineController::class, 'pharmacySearch'])->name('medicines.pharmacy');
    

    // Route::post('/pharmacy-medicines/store', [PharmacyMedicineController::class, 'addMedicine'])->name('pharmacy-medicines.store');
    // Route::delete('/clinics/{id}', [ClinicController::class, 'destroy'])->name('clinics.destroy');
    // Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy');
   
});