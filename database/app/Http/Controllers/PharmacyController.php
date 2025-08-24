<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;

class PharmacyController extends Controller
{
    public function index()
    {
        $pharmacies = Pharmacy::all();
        return view('backend.pharmacy.index', compact('pharmacies'));
    }


    public function create()
    {
        return view('backend.pharmacy.add_pharmacy');
    }


    public function store(Request $request)
    {
        $request->validate([
            'pharmacy_name' => 'required|string|max:255',
            'pharmacy_address' => 'required|string',
            'city' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|unique:pharmacies,email',
            'pharmacy_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $pharmacyPhotoPath = null;
        if ($request->hasFile('pharmacy_photo')) {
            $clinicPhoto = $request->file('pharmacy_photo');
            $pharmacyPhotoPath = $clinicPhoto->store('pharmacy_photo', 'public');
        }

        Pharmacy::create([
            'pharmacy_name' => $request->pharmacy_name,
            'pharmacy_address' => $request->pharmacy_address,
            'city' => $request->city,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'pharmacy_photo' => $pharmacyPhotoPath
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'Pharmacy created',
            'message' =>  'Pharmacy: ' . $request->pharmacy_name . ' created by ' . Auth::user()->name,
        ]);

        return redirect()->route('pharmacies.index')->with('success', 'Pharmacy added successfully.');
    }


    public function edit($id)
    {
        $pharmacy = Pharmacy::findOrFail($id);
        return view('backend.pharmacy.edit_pharmacy', compact('pharmacy'));
    }


    public function update(Request $request, $id)
    {
        $pharmacy = Pharmacy::findOrFail($id);

        $request->validate([
            'pharmacy_name' => 'required|string|max:255',
            'pharmacy_address' => 'required|string',
            'city' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|unique:pharmacies,email,' . $pharmacy->id,
            'pharmacy_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $pharmacy = Pharmacy::findOrFail($id);

        if ($request->hasFile('pharmacy_photo')) {

            if ($pharmacy->clinic_photo) {
                Storage::delete('public/' . $pharmacy->pharmacy_photo);
            }
            $pharmacyPhotoPath = $request->file('pharmacy_photo')->store('pharmacy_photo', 'public');
        } else {
            $pharmacyPhotoPath = $pharmacy->clinic_photo;
        }

        $pharmacy->update([
            'pharmacy_name' => $request->pharmacy_name,
            'pharmacy_address' => $request->pharmacy_address,
            'city' => $request->city,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'pharmacy_photo' => $pharmacyPhotoPath
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'Pharmacy created',
            'message' =>  'Pharmacy: ' . $request->pharmacy_name . ' updated by ' . Auth::user()->name,
        ]);

        return redirect()->route('pharmacies.index')->with('success', 'Pharmacy updated successfully.');
    }


    public function destroy($id)
    {
        $pharmacy = Pharmacy::findOrFail($id);
        $pharmacy->delete();

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'Pharmacy created',
            'message' =>  'Pharmacy: ' . $pharmacy->pharmacy_name . ' deleted by ' . Auth::user()->name,
        ]);

        return redirect()->route('pharmacies.index')->with('success', 'Pharmacy deleted successfully.');
    }
}
