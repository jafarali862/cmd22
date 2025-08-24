<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use App\Imports\MedicinesImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Log;

class MedicineController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->get('search');

        $pharmacies = Pharmacy::all();
        if ($search) {
            $medicines = Medicine::with('pharmacy')->where('medicine_name', 'like', '%' . $search . '%')
                ->orWhere('manufacturer', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->paginate(15);
        } else {

            $medicines = Medicine::with('pharmacy')->paginate(15);
        }

        return view('backend.medicine.index', compact('medicines', 'pharmacies'));
    }

    public function index2(Request $request)
    {
        $search = $request->get('search');

        $pharmacies = Pharmacy::all();
        if ($search) {
            $medicines = Medicine::with('pharmacy')->where('medicine_name', 'like', '%' . $search . '%')
                ->orWhere('manufacturer', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->paginate(15);
        } else {

            $medicines = Medicine::with('pharmacy')->paginate(15);
        }

        return view('backend.medicine2.index', compact('medicines', 'pharmacies'));
    }


    public function create()
    {
        $pharmacies = Pharmacy::all();
        return view('backend.medicine.create',compact('pharmacies'));
    }

    public function create2()
    { 
        $pharmacies = Pharmacy::all();
        return view('backend.medicine2.create',compact('pharmacies'));
    }


    public function store(Request $request)
    {


        $request->validate([
            'medicine_name' => 'required',
            'pharmacy_id' => 'required',          
            'amount' => 'required|numeric|min:1',
            'quantity' => 'required',
            'expiry_date' => 'required',
            'manufacturer' => 'required',
            'description' => 'required'
        ]);

        Medicine::create([
            'medicine_name' => $request->medicine_name,
            'pharmacy_id' => $request->pharmacy_id,
            'amount' => $request->amount,
            'quantity' => $request->quantity,
            'expiry_date' => $request->expiry_date,
            'manufacturer' => $request->manufacturer,
            'description' => $request->description,
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'medicine added',
            'message' =>  'Medicine: ' . $request->medicine_name . ' added by: ' . Auth::user()->name,
        ]);

        return redirect()->route('medicines.index')->with('success', 'Medicine added successfully!');
    }


    public function store2(Request $request)
    {


        $request->validate([
            'medicine_name' => 'required',
            'amount' => 'required|numeric|min:1',
            'quantity' => 'required',
            'expiry_date' => 'required',
            'manufacturer' => 'required',
            'description' => 'required'
        ]);

        Medicine::create([
            'medicine_name' => $request->medicine_name,
            'amount' => $request->amount,
            'quantity' => $request->quantity,
            'expiry_date' => $request->expiry_date,
            'manufacturer' => $request->manufacturer,
            'description' => $request->description,
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'medicine added',
            'message' =>  'Medicine: ' . $request->medicine_name . ' added by: ' . Auth::user()->name,
        ]);

        return redirect()->route('medicines2.index')->with('success', 'Medicine added successfully!');
    }


    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        $pharmacies = Pharmacy::all();
        return view('backend.medicine.edit', compact('medicine','pharmacies'));
    }

     public function edit2($id)
    {
        $medicine = Medicine::findOrFail($id);
        $pharmacies = Pharmacy::all();
        return view('backend.medicine2.edit', compact('medicine','pharmacies'));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'medicine_name' => 'required',
            'pharmacy_id' => 'required',   
            'amount' => 'required|string',
            'quantity' => 'required',
            'description' => 'required',
            'expiry_date' => 'required',
            'manufacturer' => 'required',
            'description' => 'required'

        ]);


        $medicine = Medicine::findOrFail($id);
        $medicine->update([
            'medicine_name' => $request->input('medicine_name'),
            'pharmacy_id' => $request->input('pharmacy_id'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'quantity' => $request->input('quantity', $medicine->quantity),
            'expiry_date' => $request->input('expiry_date', $medicine->expiry_date),
            'manufacturer' => $request->input('manufacturer', $medicine->manufacturer),
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'medicine updated',
            'message' =>  'Medicine: ' . $request->medicine_name . ' updated by: ' . Auth::user()->name,
        ]);


        return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully.');
    }


    public function update2(Request $request, $id)
    {

        $request->validate([
            'medicine_name' => 'required',
            'pharmacy_id' => 'required',   
            'amount' => 'required|string',
            'quantity' => 'required',
            'description' => 'required',
            'expiry_date' => 'required',
            'manufacturer' => 'required',
            'description' => 'required'

        ]);


        $medicine = Medicine::findOrFail($id);
        $medicine->update([
            'medicine_name' => $request->input('medicine_name'),
            'pharmacy_id' => $request->input('pharmacy_id'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'quantity' => $request->input('quantity', $medicine->quantity),
            'expiry_date' => $request->input('expiry_date', $medicine->expiry_date),
            'manufacturer' => $request->input('manufacturer', $medicine->manufacturer),
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'medicine updated',
            'message' =>  'Medicine: ' . $request->medicine_name . ' updated by: ' . Auth::user()->name,
        ]);


        return redirect()->route('medicines2.index')->with('success', 'Medicine updated successfully.');
    }

    public function import(Request $request)
    {

        $request->validate([
            'pharmacy_id' => 'required',
            'file' => 'required|mimes:xls,xlsx'
        ]);
    
         
        // dd($request->all());

        Excel::import(new MedicinesImport($request->pharmacy_id), $request->file('file'));

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'medicine Imported',
            'message' =>  'Medicine xls uploaded by: ' . Auth::user()->name,
        ]);

        return redirect()->route('medicines.index')->with('success', 'Medicines imported successfully.');
    }

     public function import2(Request $request)
    {

        $request->validate([
            'pharmacy_id' => 'required',
            'file' => 'required|mimes:xls,xlsx'
        ]);


        Excel::import(new MedicinesImport($request->pharmacy_id), $request->file('file'));

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'medicine Imported',
            'message' =>  'Medicine xls uploaded by: ' . Auth::user()->name,
        ]);

        return redirect()->route('medicines2.index')->with('success', 'Medicines imported successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        $medicines = Medicine::where('medicine_name', 'like', '%' . $query . '%')->paginate(15);

        $output = '';
        foreach ($medicines as $medicine) {
            $output .= '
                        <tr>
                            <td>' . $medicine->pharmacy->pharmacy_name . '</td>
                            <td>' . $medicine->medicine_name . '</td>
                            <td>' . $medicine->amount . '</td>
                            <td>' . $medicine->quantity . '</td>
                            <td>' . $medicine->expiry_date . '</td>
                            <td>' . $medicine->description . '</td>  
                            <td>' . $medicine->manufacturer . '</td>
                            <td>
                                <a href="' . route('medicines.edit', $medicine->id) . '" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="' . route('medicines.destroy', $medicine->id) . '" method="POST" style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this medicine?\');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>';
        }

        return response([
            'output' => $output,
            'pagination' => $medicines->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }

      public function search2(Request $request)
    {
        $query = $request->input('search');
        $medicines = Medicine::where('medicine_name', 'like', '%' . $query . '%')->paginate(15);

        $output = '';
        foreach ($medicines as $medicine) {
            $output .= '
                        <tr>
                            <td>' . $medicine->pharmacy->pharmacy_name . '</td>
                            <td>' . $medicine->medicine_name . '</td>
                            <td>' . $medicine->amount . '</td>
                            <td>' . $medicine->quantity . '</td>
                            <td>' . $medicine->expiry_date . '</td>
                            <td>' . $medicine->description . '</td>  
                            <td>' . $medicine->manufacturer . '</td>
                            <td>
                                <a href="' . route('medicines.edit', $medicine->id) . '" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="' . route('medicines.destroy', $medicine->id) . '" method="POST" style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this medicine?\');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>';
        }

        return response([
            'output' => $output,
            'pagination' => $medicines->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }



    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'medicine deleted',
            'message' =>  'Medicine: '.$medicine->medicine_name. ' deleted by ' . Auth::user()->name,
        ]);
        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully');
    }

       public function destroy2($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        Log::create([
            'user_id' => auth()->id(),
            'log_type' => 'medicine deleted',
            'message' =>  'Medicine: '.$medicine->medicine_name. ' deleted by ' . Auth::user()->name,
        ]);
        return redirect()->route('medicines2.index')->with('success', 'Medicine deleted successfully');
    }


    public function pharmacySearch(Request $request)
    {

        $pharmacy_id = $request->input('pharmacy_id');
        $medicines = Medicine::leftJoin('pharmacies', 'pharmacies.id', '=', 'medicines.pharmacy_id')->where('pharmacy_id', $pharmacy_id)->select('pharmacies.pharmacy_name', 'medicines.*')->paginate(15);

        $output = '';
        foreach ($medicines as $medicine) {
            $output .= '
                        <tr>
                            <td>' . $medicine->pharmacy_name . '</td>
                            <td>' . $medicine->medicine_name . '</td>
                            <td>' . $medicine->amount . '</td>
                            <td>' . $medicine->quantity . '</td>
                            <td>' . $medicine->expiry_date . '</td>
                            <td>' . $medicine->description . '</td>  
                            <td>' . $medicine->manufacturer . '</td>
                            <td>
                                <a href="' . route('medicines.edit', $medicine->id) . '" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="' . route('medicines.destroy', $medicine->id) . '" method="POST" style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this medicine?\');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>';
        }

        return response([
            'output' => $output,
            'pagination' => $medicines->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }


     public function pharmacySearch2(Request $request)
    {

        $pharmacy_id = $request->input('pharmacy_id');
        $medicines = Medicine::leftJoin('pharmacies', 'pharmacies.id', '=', 'medicines.pharmacy_id')->where('pharmacy_id', $pharmacy_id)->select('pharmacies.pharmacy_name', 'medicines.*')->paginate(15);

        $output = '';
        foreach ($medicines as $medicine) {
            $output .= '
                        <tr>
                            <td>' . $medicine->pharmacy_name . '</td>
                            <td>' . $medicine->medicine_name . '</td>
                            <td>' . $medicine->amount . '</td>
                            <td>' . $medicine->quantity . '</td>
                            <td>' . $medicine->expiry_date . '</td>
                            <td>' . $medicine->description . '</td>  
                            <td>' . $medicine->manufacturer . '</td>
                            <td>
                                <a href="' . route('medicines.edit', $medicine->id) . '" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="' . route('medicines.destroy', $medicine->id) . '" method="POST" style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this medicine?\');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>';
        }

        return response([
            'output' => $output,
            'pagination' => $medicines->links('pagination::bootstrap-5')->toHtml(),
        ]);
    }

}
