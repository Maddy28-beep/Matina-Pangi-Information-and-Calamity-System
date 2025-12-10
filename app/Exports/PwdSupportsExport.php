<?php

namespace App\Exports;

use App\Models\PwdSupport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PwdSupportsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return PwdSupport::with('resident')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'PWD ID',
            'Resident Name',
            'Birthdate',
            'Age',
            'Gender',
            'Contact Number',
            'Address',
            'Disability Type',
            'Severity',
            'Date Registered',
            'PWD ID Expiry',
            'Aid Status',
            'Assigned Worker',
            'Assistive Device',
            'Medical Needs',
            'Notes',
            'Created At',
            'Last Updated',
        ];
    }

    public function map($pwd): array
    {
        return [
            $pwd->pwd_id_number,
            $pwd->resident ? $pwd->resident->full_name : 'N/A',
            $pwd->resident ? $pwd->resident->birthdate?->format('Y-m-d') : 'N/A',
            $pwd->resident ? $pwd->resident->age : 'N/A',
            $pwd->resident ? $pwd->resident->gender : 'N/A',
            $pwd->resident ? $pwd->resident->contact_number : 'N/A',
            $pwd->resident ? $pwd->resident->full_address : 'N/A',
            ucfirst($pwd->disability_type),
            $pwd->severity,
            $pwd->date_registered?->format('Y-m-d'),
            $pwd->pwd_id_expiry?->format('Y-m-d'),
            $pwd->aid_status,
            $pwd->assigned_worker,
            $pwd->assistive_device,
            $pwd->medical_needs,
            $pwd->notes,
            $pwd->created_at?->format('Y-m-d H:i:s'),
            $pwd->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9EAD3'],
                ],
            ],
            // Freeze the first row
            'A1' => ['freeze' => true],
        ];
    }
}
