<?php

namespace App\Http\Controllers;

use App\Exports\CensusExport;
use App\Exports\HouseholdsExport;
use App\Exports\ResidentsExport;
use App\Models\AuditLog;
use App\Models\Household;
use App\Models\Resident;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CensusController extends Controller
{
    /**
     * Display census dashboard
     */
    public function index(Request $request)
    {
        $year = (int) ($request->get('year', now()->year));

        $census = [
            'total_population' => Resident::approved()->whereYear('approved_at', $year)->count(),
            'total_households' => Household::approved()->whereYear('approved_at', $year)->count(),
            'average_household_size' => round(Household::approved()->whereYear('approved_at', $year)->avg('total_members'), 2),

            'active_residents' => Resident::approved()->whereYear('approved_at', $year)->active()->count(),
            'reallocated_residents' => Resident::approved()->whereYear('approved_at', $year)->reallocated()->count(),
            'deceased_residents' => Resident::approved()->whereYear('approved_at', $year)->deceased()->count(),

            'male_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('sex', 'male')->count(),
            'female_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('sex', 'female')->count(),

            'children_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('age', '<', 13)->count(),
            'youth_count' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [13, 17])->count(),
            'adults_18_59_count' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [18, 59])->count(),
            'seniors_count' => Resident::approved()->whereYear('approved_at', $year)->active()->seniorCitizens()->count(),

            'pwd_count' => Resident::approved()->whereYear('approved_at', $year)->active()->pwd()->count(),
            'senior_citizens_count' => Resident::approved()->whereYear('approved_at', $year)->active()->seniorCitizens()->count(),
            'voters_count' => Resident::approved()->whereYear('approved_at', $year)->active()->voters()->count(),
            'non_voters_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('is_voter', false)->count(),
            'fourps_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('is_4ps_beneficiary', true)->count(),

            'single_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('civil_status', 'single')->count(),
            'married_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('civil_status', 'married')->count(),
            'widowed_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('civil_status', 'widowed')->count(),
            'separated_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('civil_status', 'separated')->count(),

            'employed_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'employed')->count(),
            'unemployed_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'unemployed')->count(),
            'self_employed_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'self-employed')->count(),
            'students_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'student')->count(),
            'retired_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'retired')->count(),

            'owned_houses' => Household::approved()->whereYear('approved_at', $year)->where('housing_type', 'owned')->count(),
            'rented_houses' => Household::approved()->whereYear('approved_at', $year)->where('housing_type', 'rented')->count(),
            'rent_free_houses' => Household::approved()->whereYear('approved_at', $year)->where('housing_type', 'rent-free')->count(),
            'with_electricity' => Household::approved()->whereYear('approved_at', $year)->where('has_electricity', true)->count(),
            'without_electricity' => Household::approved()->whereYear('approved_at', $year)->where('has_electricity', false)->count(),

            'solo_households' => Household::approved()->whereYear('approved_at', $year)->where('household_type', 'solo')->count(),
            'family_households' => Household::approved()->whereYear('approved_at', $year)->where('household_type', 'family')->count(),
            'extended_households' => Household::approved()->whereYear('approved_at', $year)->where('household_type', 'extended')->count(),
        ];

        $ageDistribution = [
            'age_0_12' => Resident::approved()->whereYear('approved_at', $year)->active()->where('age', '<', 13)->count(),
            'age_13_19' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [13, 19])->count(),
            'age_20_29' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [20, 29])->count(),
            'age_30_39' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [30, 39])->count(),
            'age_40_49' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [40, 49])->count(),
            'age_50_59' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [50, 59])->count(),
            'age_60_plus' => Resident::approved()->whereYear('approved_at', $year)->active()->where('age', '>=', 60)->count(),
        ];

        return view('census.index', compact('census', 'ageDistribution', 'year'));
    }

    /**
     * Export census snapshot to PDF
     */
    public function exportPdf()
    {
        // Allow secretary and staff
        if (! (auth()->user()->isSecretary() || auth()->user()->isStaff())) {
            abort(403, 'Unauthorized action.');
        }

        $census = $this->getCensusData();

        $pdf = Pdf::loadView('census.pdf', compact('census'));

        AuditLog::logAction(
            'export',
            'Census',
            null,
            'Exported census snapshot to PDF'
        );

        return $pdf->download('census-snapshot-'.date('Y-m-d').'.pdf');
    }

    /**
     * Export census snapshot to Excel
     */
    public function exportExcel()
    {
        // Allow secretary and staff
        if (! (auth()->user()->isSecretary() || auth()->user()->isStaff())) {
            abort(403, 'Unauthorized action.');
        }

        AuditLog::logAction(
            'export',
            'Census',
            null,
            'Exported census snapshot to Excel'
        );

        return Excel::download(new CensusExport, 'census-snapshot-'.date('Y-m-d').'.xlsx');
    }

    /**
     * Export residents list to PDF
     */
    public function exportResidentsPdf()
    {
        // Only secretary can export
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $residents = Resident::with('household')->get();

        $pdf = Pdf::loadView('residents.pdf', compact('residents'))
            ->setPaper('a4', 'landscape');

        AuditLog::logAction(
            'export',
            'Resident',
            null,
            'Exported residents list to PDF'
        );

        return $pdf->download('residents-list-'.date('Y-m-d').'.pdf');
    }

    /**
     * Export residents list to Excel
     */
    public function exportResidentsExcel()
    {
        // Only secretary can export
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        AuditLog::logAction(
            'export',
            'Resident',
            null,
            'Exported residents list to Excel'
        );

        return Excel::download(new ResidentsExport, 'residents-list-'.date('Y-m-d').'.xlsx');
    }

    /**
     * Export households list to PDF
     */
    public function exportHouseholdsPdf()
    {
        // Only secretary can export
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $households = Household::with(['head', 'residents'])->get();

        $pdf = Pdf::loadView('households.pdf', compact('households'))
            ->setPaper('a4', 'landscape');

        AuditLog::logAction(
            'export',
            'Household',
            null,
            'Exported households list to PDF'
        );

        return $pdf->download('households-list-'.date('Y-m-d').'.pdf');
    }

    /**
     * Export households list to Excel
     */
    public function exportHouseholdsExcel()
    {
        // Only secretary can export
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        AuditLog::logAction(
            'export',
            'Household',
            null,
            'Exported households list to Excel'
        );

        return Excel::download(new HouseholdsExport, 'households-list-'.date('Y-m-d').'.xlsx');
    }

    /**
     * Get census data
     */
    private function getCensusData()
    {
        return [
            'total_population' => Resident::count(),
            'total_households' => Household::count(),
            'average_household_size' => round(Household::avg('total_members'), 2),
            'male_count' => Resident::where('sex', 'male')->count(),
            'female_count' => Resident::where('sex', 'female')->count(),
            'children_count' => Resident::where('age', '<', 13)->count(),
            'teens_count' => Resident::teens()->count(),
            'adults_count' => Resident::whereBetween('age', [20, 59])->count(),
            'seniors_count' => Resident::seniorCitizens()->count(),
            'pwd_count' => Resident::pwd()->count(),
            'voters_count' => Resident::voters()->count(),
            'non_voters_count' => Resident::where('is_voter', false)->count(),
            'fourps_count' => Resident::where('is_4ps_beneficiary', true)->count(),
            'single_count' => Resident::where('civil_status', 'single')->count(),
            'married_count' => Resident::where('civil_status', 'married')->count(),
            'widowed_count' => Resident::where('civil_status', 'widowed')->count(),
            'employed_count' => Resident::where('employment_status', 'employed')->count(),
            'unemployed_count' => Resident::where('employment_status', 'unemployed')->count(),
            'owned_houses' => Household::where('housing_type', 'owned')->count(),
            'rented_houses' => Household::where('housing_type', 'rented')->count(),
            'with_electricity' => Household::where('has_electricity', true)->count(),
            'total_income' => Resident::sum('monthly_income'),
            'average_income' => round(Resident::avg('monthly_income'), 2),
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];
    }

    private function getCensusDataByYear(int $year)
    {
        return [
            'total_population' => Resident::approved()->whereYear('approved_at', $year)->count(),
            'total_households' => Household::approved()->whereYear('approved_at', $year)->count(),
            'average_household_size' => round(Household::approved()->whereYear('approved_at', $year)->avg('total_members'), 2),

            'active_residents' => Resident::approved()->whereYear('approved_at', $year)->active()->count(),
            'reallocated_residents' => Resident::approved()->whereYear('approved_at', $year)->reallocated()->count(),
            'deceased_residents' => Resident::approved()->whereYear('approved_at', $year)->deceased()->count(),

            'male_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('sex', 'male')->count(),
            'female_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('sex', 'female')->count(),

            'children_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('age', '<', 13)->count(),
            'youth_count' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [13, 17])->count(),
            'adults_18_59_count' => Resident::approved()->whereYear('approved_at', $year)->active()->whereBetween('age', [18, 59])->count(),
            'seniors_count' => Resident::approved()->whereYear('approved_at', $year)->active()->seniorCitizens()->count(),

            'pwd_count' => Resident::approved()->whereYear('approved_at', $year)->active()->pwd()->count(),
            'senior_citizen_members' => Resident::approved()->whereYear('approved_at', $year)->active()->seniorCitizens()->count(),
            'voters_count' => Resident::approved()->whereYear('approved_at', $year)->active()->voters()->count(),
            'non_voters_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('is_voter', false)->count(),
            'fourps_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('is_4ps_beneficiary', true)->count(),

            'single_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('civil_status', 'single')->count(),
            'married_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('civil_status', 'married')->count(),
            'widowed_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('civil_status', 'widowed')->count(),
            'separated_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('civil_status', 'separated')->count(),

            'employed_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'employed')->count(),
            'unemployed_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'unemployed')->count(),
            'self_employed_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'self-employed')->count(),
            'students_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'student')->count(),
            'retired_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('employment_status', 'retired')->count(),

            'owned_houses' => Household::approved()->whereYear('approved_at', $year)->where('housing_type', 'owned')->count(),
            'rented_houses' => Household::approved()->whereYear('approved_at', $year)->where('housing_type', 'rented')->count(),
            'rent_free_houses' => Household::approved()->whereYear('approved_at', $year)->where('housing_type', 'rent-free')->count(),
            'with_electricity' => Household::approved()->whereYear('approved_at', $year)->where('has_electricity', true)->count(),
            'without_electricity' => Household::approved()->whereYear('approved_at', $year)->where('has_electricity', false)->count(),

            'solo_households' => Household::approved()->whereYear('approved_at', $year)->where('household_type', 'solo')->count(),
            'family_households' => Household::approved()->whereYear('approved_at', $year)->where('household_type', 'family')->count(),
            'extended_households' => Household::approved()->whereYear('approved_at', $year)->where('household_type', 'extended')->count(),

            'pregnant_women' => null,
            'infants_count' => Resident::approved()->whereYear('approved_at', $year)->active()->where('age', '<', 1)->count(),
            'malnourished_children_count' => null,
            'common_illness_cases' => null,
            'fully_immunized_children' => null,

            'total_income' => Resident::approved()->whereYear('approved_at', $year)->active()->sum('monthly_income'),
            'average_income' => round(Resident::approved()->whereYear('approved_at', $year)->active()->avg('monthly_income'), 2),
        ];
    }
}
