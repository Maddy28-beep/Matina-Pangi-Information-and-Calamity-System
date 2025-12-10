<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CalamitiesExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $calamities;

    public function __construct($calamities)
    {
        $this->calamities = collect($calamities);
    }

    public function collection()
    {
        return $this->calamities;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Date Occurred',
            'Severity',
            'Reported By',
            'Affected Areas',
            'Affected Puroks',
            'Total Affected Households',
            'Status',
        ];
    }

    public function map($calamity): array
    {
        return [
            $calamity->id,
            $calamity->calamity_name,
            $calamity->calamity_type,
            optional($calamity->date_occurred)->format('Y-m-d'),
            $calamity->severity_level,
            optional($calamity->reporter)->name,
            $calamity->affected_areas,
            is_array($calamity->affected_puroks) ? implode(', ', $calamity->affected_puroks) : null,
            $calamity->affected_households_count ?? $calamity->affectedHouseholds()->count(),
            $calamity->status,
        ];
    }
}
