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

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   $today= Carbon::now()->toDateString();
        $users=User::all();
        $userCount=count($users);
        $clinic=Clinic::all();
        $clinicCount=count($clinic);
        $pharmacy=Pharmacy::all();
        $pharmacyCount=count($pharmacy);
        $medicine=Medicine::all();
        $medicineCount=count($medicine);
        $pharmacyPres=PharmacyPrescription::where("created_at","like",$today.'%')->get();
        $pharmacyPresCount=count( $pharmacyPres);
        $clinicPres=ClinicPrescription::where("created_at","like",$today.'%')->get();
        $clinicPresCount=count( $clinicPres);
        return view('backend.home',compact('userCount','clinicCount','pharmacyCount','medicineCount','pharmacyPresCount','clinicPresCount'));
    }
}
