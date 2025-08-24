<?php
namespace App\Exports;

use App\Models\PharmacyPrescription;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DaybookExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PharmacyPrescription::with(['user', 'pharmacy','deliveryAgent'])->where('status', '!=', 5);

        if (!empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $query->whereBetween('created_at', [
                $this->filters['from_date'] . ' 00:00:00',
                $this->filters['to_date'] . ' 23:59:59',
            ]);
        }

        return $query->get()->map(function ($item) {
            return [
                // 'ID' => $item->id,
                'Patient Name' => $item->name ?? 'N/A',
                'Pharmacy' => $item->pharmacy->pharmacy_name ?? 'N/A',
                'Payment Method' => $item->payment_method == 1 ? 'Online Payment' : ($item->payment_method == 2 ? 'Cash on Delivery' : 'N/A'),
                'Amount Collected' => $item->total_amount,
                'Delivery Agent' => $item->deliveryAgent->name ?? 'N/A',
                'Address' => $item->delivery_address,
                'Date' => $item->created_at->format('d-m-Y H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            // 'ID',
            'Patient Name',
            'Pharmacy',
            'Payment Method',
            'Amount Collected',
            'Delivery Agent',
            'Address',
            'Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

