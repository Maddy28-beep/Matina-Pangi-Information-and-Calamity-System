<?php

namespace App\Exports;

use App\Models\Household;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class HouseholdsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return Household::with(['head', 'residents'])->get();
    }

    public function map($household): array
    {
        return [
            $household->household_id,
            $household->full_address,
            $household->purok,
            ucfirst($household->housing_type),
            $household->has_electricity ? 'Yes' : 'No',
            $household->electric_account_number,
            $household->total_members,
            ucfirst($household->household_type),
            $household->head ? $household->head->full_name : 'N/A',
            $household->head ? $household->head->contact_number : 'N/A',
            number_format($household->total_income, 2),
            $household->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Household ID',
            'Address',
            'Purok',
            'Housing Type',
            'Has Electricity',
            'Electric Account Number',
            'Total Members',
            'Household Type',
            'Household Head',
            'Contact Number',
            'Total Monthly Income',
            'Registered Date',
        ];
    }

    public function title(): string
    {
        return 'Households List';
    }
}
