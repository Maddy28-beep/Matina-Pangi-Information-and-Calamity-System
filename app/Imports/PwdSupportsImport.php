<?php

namespace App\Imports;

use App\Models\PwdSupport;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class PwdSupportsImport implements SkipsOnError, SkipsOnFailure, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    use SkipsErrors;

    private $residents;

    private $rows = 0;

    private $imported = 0;

    private $skipped = 0;

    private $errors = [];

    public function __construct()
    {
        // Cache residents for lookup
        $this->residents = Resident::all(['id', 'first_name', 'last_name', 'middle_name'])
            ->keyBy(function ($resident) {
                return Str::slug($resident->full_name);
            });
    }

    public function model(array $row)
    {
        $this->rows++;

        // Skip if required fields are missing
        if (empty($row['pwd_id']) || empty($row['resident_name'])) {
            $this->skipped++;
            $this->errors[] = "Row {$this->rows}: Missing required fields (PWD ID or Resident Name)";

            return null;
        }

        // Find resident by name
        $residentSlug = Str::slug($row['resident_name']);
        $resident = $this->residents->get($residentSlug);

        if (! $resident) {
            $this->skipped++;
            $this->errors[] = "Row {$this->rows}: Resident '{$row['resident_name']}' not found";

            return null;
        }

        // Check if PWD ID already exists
        if (PwdSupport::where('pwd_id_number', $row['pwd_id'])->exists()) {
            $this->skipped++;
            $this->errors[] = "Row {$this->rows}: PWD ID '{$row['pwd_id']}' already exists";

            return null;
        }

        // Parse dates
        $dateRegistered = ! empty($row['date_registered']) ?
            Carbon::parse($row['date_registered'])->format('Y-m-d') :
            now()->format('Y-m-d');

        $pwdIdExpiry = ! empty($row['pwd_id_expiry']) ?
            Carbon::parse($row['pwd_id_expiry'])->format('Y-m-d') :
            Carbon::parse($dateRegistered)->addYears(3)->format('Y-m-d');

        $this->imported++;

        return new PwdSupport([
            'resident_id' => $resident->id,
            'pwd_id_number' => $row['pwd_id'],
            'disability_type' => strtolower($row['disability_type'] ?? 'other'),
            'severity' => $row['severity'] ?? 'Moderate',
            'date_registered' => $dateRegistered,
            'pwd_id_expiry' => $pwdIdExpiry,
            'aid_status' => $row['aid_status'] ?? 'Pending',
            'disability_description' => $row['disability_description'] ?? null,
            'assistive_device' => $row['assistive_device'] ?? null,
            'assigned_worker' => $row['assigned_worker'] ?? auth()->user()?->name ?? 'System',
            'assistance_received' => $row['assistance_received'] ?? null,
            'medical_needs' => $row['medical_needs'] ?? null,
            'notes' => $row['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'pwd_id' => ['required', 'string', 'max:50'],
            'resident_name' => ['required', 'string', 'max:255'],
            'disability_type' => ['nullable', 'string', 'in:visual,hearing,mobility,intellectual,psychosocial,chronic,multiple,other'],
            'severity' => ['nullable', 'string', 'in:Mild,Moderate,Severe'],
            'date_registered' => ['nullable', 'date'],
            'pwd_id_expiry' => ['nullable', 'date'],
            'aid_status' => ['nullable', 'string', 'in:Pending,Approved,Rejected,Completed'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'pwd_id.required' => 'PWD ID is required',
            'resident_name.required' => 'Resident name is required',
            'disability_type.in' => 'Invalid disability type. Must be one of: visual, hearing, mobility, intellectual, psychosocial, chronic, multiple, other',
            'severity.in' => 'Invalid severity. Must be one of: Mild, Moderate, Severe',
            'date_registered.date' => 'Invalid date format for date registered',
            'pwd_id_expiry.date' => 'Invalid date format for PWD ID expiry',
            'aid_status.in' => 'Invalid aid status. Must be one of: Pending, Approved, Rejected, Completed',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Handle failures during import
     *
     * @param  \Maatwebsite\Excel\Validators\Failure[]  $failures
     * @return void
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = sprintf(
                'Error on row %s, column %s: %s',
                $failure->row(),
                $failure->attribute(),
                $failure->errors()[0] ?? 'Unknown error'
            );
        }

        $this->skipped++;
    }
}
