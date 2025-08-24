<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as Logs;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClinicController extends Controller
{
    public function index()
    {
        try {
            $clinics = Clinic::all();
            return view('backend.clinic.clinic', compact('clinics'));
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }

    public function index2()
    {
        try {
            $clinics = Clinic::all();
            return view('backend.clinic2.clinic', compact('clinics'));
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }



    public function createClinic()
    {
        try {
            return view('backend.clinic.clinic-add');
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }

     public function createClinic2()
    {
        try {
            return view('backend.clinic2.clinic-add');
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }



    public function store(Request $request)
    {
        try {
            $request->validate([
                'clinic_name' => 'required|string|max:255',
                'tests.*' => 'nullable',
                'clinic_address' => 'required|string',
                'city' => 'required|string|max:100',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email',
                'clinic_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $clinicPhotoPath = null;
            if ($request->hasFile('clinic_photo')) {
                $clinicPhoto = $request->file('clinic_photo');
                $clinicPhotoPath = $clinicPhoto->store('clinic_photos', 'public');
            }

            Clinic::create([
                'clinic_name' => $request->clinic_name,
                'tests' => json_encode($request->tests),
                'clinic_address' => $request->clinic_address,
                'city' => $request->city,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'clinic_photo' => $clinicPhotoPath,
            ]);

            Log::create([
                'user_id' => auth()->id(),
                'log_type' => 'clinic created',
                'message' =>'clinic: '. $request->clinic_name .' created by '. Auth::user()->name  ,
            ]);

            return redirect()->route('clinic')->with('success', 'Clinic added successfully!');
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }


     public function store2(Request $request)
    {
        try {
            $request->validate([
                'clinic_name' => 'required|string|max:255',
                'tests.*' => 'nullable',
                'clinic_address' => 'required|string',
                'city' => 'required|string|max:100',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email',
                'clinic_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $clinicPhotoPath = null;
            if ($request->hasFile('clinic_photo')) {
                $clinicPhoto = $request->file('clinic_photo');
                $clinicPhotoPath = $clinicPhoto->store('clinic_photos', 'public');
            }

            Clinic::create([
                'clinic_name' => $request->clinic_name,
                'tests' => json_encode($request->tests),
                'clinic_address' => $request->clinic_address,
                'city' => $request->city,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'clinic_photo' => $clinicPhotoPath,
            ]);

            Log::create([
                'user_id' => auth()->id(),
                'log_type' => 'clinic created',
                'message' =>'clinic: '. $request->clinic_name .' created by '. Auth::user()->name  ,
            ]);

            return redirect()->route('clinic2')->with('success', 'Clinic added successfully!');
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }


    public function edit($id)
    {
        try {
            $clinic = Clinic::findOrFail($id);
            return view('backend.clinic.clinic-edit', compact('clinic'));
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }

        public function edit2($id)
    {
        try {
            $clinic = Clinic::findOrFail($id);
            return view('backend.clinic.clinic-edit', compact('clinic'));
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'clinic_name' => 'required|string|max:255',
                'tests.*' => 'nullable',
                'clinic_address' => 'required|string',
                'city' => 'required|string|max:100',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'clinic_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $clinic = Clinic::findOrFail($id);

            if ($request->hasFile('clinic_photo')) {

                if ($clinic->clinic_photo) {
                    Storage::delete('public/' . $clinic->clinic_photo);
                }
                $clinicPhotoPath = $request->file('clinic_photo')->store('clinic_photos', 'public');
            } else {
                $clinicPhotoPath = $clinic->clinic_photo;
            }
            $clinic->update([
                'clinic_name' => $request->clinic_name,
                'tests' => json_encode($request->tests),
                'clinic_address' => $request->clinic_address,
                'city' => $request->city,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'clinic_photo' => $clinicPhotoPath,
            ]);

            Log::create([
                'user_id' => auth()->id(),
                'log_type' => 'clinic Updated',
                'message' => 'clinic: '.$clinic->clinic_name .' updated by: '. Auth::user()->name  ,
            ]);

            return redirect()->route('clinic')->with('success', 'Clinic updated successfully.');
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }

     public function update2(Request $request, $id)
    {
        try {
            $request->validate([
                'clinic_name' => 'required|string|max:255',
                'tests.*' => 'nullable',
                'clinic_address' => 'required|string',
                'city' => 'required|string|max:100',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'clinic_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $clinic = Clinic::findOrFail($id);

            if ($request->hasFile('clinic_photo')) {

                if ($clinic->clinic_photo) {
                    Storage::delete('public/' . $clinic->clinic_photo);
                }
                $clinicPhotoPath = $request->file('clinic_photo')->store('clinic_photos', 'public');
            } else {
                $clinicPhotoPath = $clinic->clinic_photo;
            }
            $clinic->update([
                'clinic_name' => $request->clinic_name,
                'tests' => json_encode($request->tests),
                'clinic_address' => $request->clinic_address,
                'city' => $request->city,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'clinic_photo' => $clinicPhotoPath,
            ]);

            Log::create([
                'user_id' => auth()->id(),
                'log_type' => 'clinic Updated',
                'message' => 'clinic: '.$clinic->clinic_name .' updated by: '. Auth::user()->name  ,
            ]);

            return redirect()->route('clinic2')->with('success', 'Clinic updated successfully.');
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }

    public function destroy($id)
    {
        try {
            $clinic = Clinic::findOrFail($id);
            $clinicName=Clinic::where('id',$id)->first();

            Log::create([
                'user_id' => auth()->id(),
                'log_type' => 'clinic Deleted',
                'message' => 'Clinic: '. $clinicName->clinic_name .' deleted by: '. Auth::user()->name  ,
            ]);

            $clinic->delete();
            
            return redirect()->route('clinic')->with('success', 'Clinic deleted successfully.');
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }

       public function destroy2($id)
    {
        try {
            $clinic = Clinic::findOrFail($id);
            $clinicName=Clinic::where('id',$id)->first();

            Log::create([
                'user_id' => auth()->id(),
                'log_type' => 'clinic Deleted',
                'message' => 'Clinic: '. $clinicName->clinic_name .' deleted by: '. Auth::user()->name  ,
            ]);

            $clinic->delete();
            
            return redirect()->route('clinic2')->with('success', 'Clinic deleted successfully.');
        } catch (Exception $e) {
            Logs::error($e);
            return  $e->getMessage() ;
        }
    }

}
