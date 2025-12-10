<?php

namespace App\Exports;

use App\Models\Resident;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ResidentsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return Resident::with('household')->get();
    }

    public function map($resident): array
    {
        return [
            $resident->resident_id,
            $resident->full_name,
            $resident->birthdate->format('Y-m-d'),
            $resident->age,
            ucfirst($resident->sex),
            ucfirst($resident->civil_status),
            $resident->household->household_id,
            $resident->household->full_address,
            ucfirst($resident->household_role),
            $resident->is_household_head ? 'Yes' : 'No',
            $resident->contact_number,
            $resident->email,
            $resident->is_pwd ? 'Yes' : 'No',
            $resident->is_senior_citizen ? 'Yes' : 'No',
            $resident->is_teen ? 'Yes' : 'No',
            $resident->is_voter ? 'Yes' : 'No',
            $resident->precinct_number,
            $resident->is_4ps_beneficiary ? 'Yes' : 'No',
            $resident->occupation,
            $resident->employment_status,
            $resident->monthly_income,
            $resident->educational_attainment,
            $resident->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Resident ID',
            'Full Name',
            'Birthdate',
            'Age',
            'Sex',
            'Civil Status',
            'Household ID',
            'Address',
            'Household Role',
            'Is Household Head',
            'Contact Number',
            'Email',
            'PWD',
            'Senior Citizen',
            'Teen',
            'Voter',
            'Precinct Number',
            '4Ps Beneficiary',
            'Occupation',
            'Employment Status',
            'Monthly Income',
            'Educational Attainment',
            'Registered Date',
        ];
    }

    public function title(): string
    {
        return 'Residents List';
    }
}
