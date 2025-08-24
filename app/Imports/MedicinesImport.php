<?php

namespace App\Imports;

use Log;
use Carbon\Carbon;
use App\Models\Medicine;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\ToModel;

class MedicinesImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected $pharmacy_id;

    public function __construct($pharmacy_id)
    {
        $this->pharmacy_id = $pharmacy_id;
    }

    public function model(array $row)
    {

        if (isset($row[0]) && is_numeric($row[0])) {
            $expiry_date = isset($row[5]) && is_numeric($row[5]) ? Carbon::createFromFormat('Y-m-d', Carbon::createFromTimestamp(0)->addDays($row[5] - 25569)->toDateString())->format('Y-m-d') : $row[5] ?? null;

            $existingMedicine = Medicine::where('medicine_name', $row[1])->where('description',$row[3])->where('pharmacy_id',$this->pharmacy_id)->first();
            if (!$existingMedicine) {
                return new Medicine([
                    'medicine_name' => $row[1],
                    'amount' => is_numeric($row[6] ?? null) ? $row[6] : 0,
                    'quantity' => is_numeric($row[4] ?? null) ? $row[4] : 0,
                    'expiry_date' => $expiry_date,
                    'manufacturer' => $row[2],
                    'description' => $row[3],
                    'pharmacy_id' => $this->pharmacy_id,
                ]);
            } 
        }
    }
}
