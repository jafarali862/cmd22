<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Medicine;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use App\Models\ClinicPrescription;
use App\Models\PharmacyPrescription;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {   
        $today= Carbon::now()->toDateString();
         $users = User::where('login_id', auth()->id())->get();
        $userCount=count($users);
        $clinic=Clinic::where('user_id', auth()->id())->get();     
        $clinicCount=count($clinic);
        $pharmacy=Pharmacy::where('user_id', auth()->id())->get();
        $pharmacyCount=count($pharmacy);
        $medicine=Medicine::all();
        $medicineCount=count($medicine);
        $pharmacyPres=PharmacyPrescription::where("created_at","like",$today.'%')->get();
        $pharmacyPresCount=count( $pharmacyPres);
        $clinicPres=ClinicPrescription::where("created_at","like",$today.'%')->get();
        $clinicPresCount=count( $clinicPres);
        return view('backend.home_customer',compact('userCount','clinicCount','pharmacyCount','medicineCount','pharmacyPresCount','clinicPresCount'));
        
    }
}
