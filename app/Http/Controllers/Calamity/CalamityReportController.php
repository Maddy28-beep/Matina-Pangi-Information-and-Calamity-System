<?php

namespace App\Http\Controllers\Calamity;

use App\Exports\CalamitiesExport;
use App\Http\Controllers\Controller;
use App\Models\Calamity;
use App\Models\DamageAssessment;
use App\Models\ReliefDistribution;
use App\Models\RescueOperation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CalamityReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Calamity::query()->withCount('affectedHouseholds');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('calamity_name', 'like', "%$search%")
                    ->orWhere('calamity_type', 'like', "%$search%")
                    ->orWhere('affected_areas', 'like', "%$search%")
                    ->orWhere('affected_puroks', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->filled('calamity_type')) {
            $query->where('calamity_type', $request->get('calamity_type'));
        }
        if ($request->filled('severity')) {
            $query->where('severity_level', $request->get('severity'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $from = $request->get('from');
        $to = $request->get('to');
        if ($from && $to) {
            $query->whereBetween('date_occurred', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('date_occurred', '>=', $from);
        } elseif ($to) {
            $query->whereDate('date_occurred', '<=', $to);
        }

        $calamities = $query->latest('date_occurred')->paginate(15)->appends($request->query());

        return view('calamity.reports.index', compact('calamities'));
    }

    public function exportIndexPdf(Request $request)
    {
        $query = Calamity::query()->withCount('affectedHouseholds')->with(['reporter']);
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('calamity_name', 'like', "%$search%")
                    ->orWhere('calamity_type', 'like', "%$search%")
                    ->orWhere('affected_areas', 'like', "%$search%")
                    ->orWhere('affected_puroks', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }
        if ($request->filled('calamity_type')) {
            $query->where('calamity_type', $request->get('calamity_type'));
        }
        if ($request->filled('severity')) {
            $query->where('severity_level', $request->get('severity'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $from = $request->get('from');
        $to = $request->get('to');
        if ($from && $to) {
            $query->whereBetween('date_occurred', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('date_occurred', '>=', $from);
        } elseif ($to) {
            $query->whereDate('date_occurred', '<=', $to);
        }

        $calamities = $query->latest('date_occurred')->get();

        $totalIncidents = $calamities->count();
        $totalAffectedHouseholds = $calamities->sum(function ($c) {
            return $c->affected_households_count ?? ($c->affectedHouseholds()->count());
        });
        $byType = $calamities->groupBy('calamity_type')->map->count()->sortDesc();
        $bySeverity = $calamities->groupBy('severity_level')->map->count()->sortDesc();
        $dates = $calamities->pluck('date_occurred')->filter();
        $dateFrom = $dates->min();
        $dateTo = $dates->max();

        $ids = $calamities->pluck('id');
        $affectedHouseholds = \App\Models\CalamityAffectedHousehold::with('household')
            ->whereIn('calamity_id', $ids)->get();
        $totalAffectedResidents = $affectedHouseholds->sum(function ($ah) {
            return $ah->household?->residents()->count() ?? 0;
        });
        $familiesEvacuated = $affectedHouseholds->where('evacuation_status', 'evacuated')->count();
        $totalEvacuees = $affectedHouseholds->where('evacuation_status', 'evacuated')
            ->sum(function ($ah) {
                return $ah->household?->residents()->count() ?? 0;
            });
        $partiallyDamaged = $affectedHouseholds->whereIn('damage_level', ['minor', 'moderate', 'severe'])->count();
        $totallyDamaged = $affectedHouseholds->where('damage_level', 'total')->count();

        $estimatedDamageCost = \App\Models\DamageAssessment::whereIn('calamity_id', $ids)->sum('estimated_cost');
        $reliefDistributions = \App\Models\ReliefDistribution::with('item')
            ->whereIn('calamity_id', $ids)->get();
        $totalReliefDistributed = $reliefDistributions->sum('quantity');
        $reliefByItem = $reliefDistributions->groupBy('item_id')->map(function ($group) {
            $name = optional($group->first()->item)->name ?? ('Item #'.$group->first()->item_id);

            return ['name' => $name, 'quantity' => $group->sum('quantity')];
        })->values();

        $rescues = \App\Models\RescueOperation::with(['affectedHousehold', 'evacuationCenter'])
            ->whereHas('affectedHousehold', function ($q) use ($ids) {
                $q->whereIn('calamity_id', $ids);
            })
            ->get();
        $totalRescues = $rescues->count();
        $evacuationCenterOccupancy = $rescues->whereNotNull('evacuation_center_id')
            ->groupBy('evacuation_center_id')->map->count();

        $metrics = compact(
            'totalIncidents', 'totalAffectedHouseholds', 'byType', 'bySeverity', 'dateFrom', 'dateTo',
            'totalAffectedResidents', 'familiesEvacuated', 'totalEvacuees', 'partiallyDamaged', 'totallyDamaged',
            'estimatedDamageCost', 'totalReliefDistributed', 'reliefByItem', 'totalRescues', 'evacuationCenterOccupancy'
        );

        $pdf = Pdf::loadView('calamity.reports.index_pdf', compact('calamities', 'metrics'));

        return $pdf->download('Calamity_Reports.pdf');
    }

    public function exportIndexExcel(Request $request)
    {
        $query = Calamity::query()->withCount('affectedHouseholds')->with(['reporter']);
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('calamity_name', 'like', "%$search%")
                    ->orWhere('calamity_type', 'like', "%$search%")
                    ->orWhere('affected_areas', 'like', "%$search%")
                    ->orWhere('affected_puroks', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }
        if ($request->filled('calamity_type')) {
            $query->where('calamity_type', $request->get('calamity_type'));
        }
        if ($request->filled('severity')) {
            $query->where('severity_level', $request->get('severity'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $from = $request->get('from');
        $to = $request->get('to');
        if ($from && $to) {
            $query->whereBetween('date_occurred', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('date_occurred', '>=', $from);
        } elseif ($to) {
            $query->whereDate('date_occurred', '<=', $to);
        }

        $calamities = $query->latest('date_occurred')->get();

        return Excel::download(new CalamitiesExport($calamities), 'Calamity_Reports.xlsx');
    }

    public function show(Calamity $calamity)
    {
        $data = $this->buildReportData($calamity);

        return view('calamity.reports.show', $data);
    }

    public function pdf(Calamity $calamity)
    {
        $data = $this->buildReportData($calamity);
        $data['exporter'] = auth()->user()->name ?? 'System';
        $data['barangayCaptain'] = config('app.barangay_captain') ?? env('BARANGAY_CAPTAIN_NAME', 'Barangay Captain');
        $pdf = Pdf::loadView('calamity.reports.pdf', $data);
        $filename = 'Calamity_Report_'.$calamity->id.'_'.$calamity->date_occurred?->format('Ymd').'.pdf';

        return $pdf->download($filename);
    }

    private function buildReportData(Calamity $calamity): array
    {
        $calamity->load(['affectedHouseholds.household', 'reporter']);

        $affectedHouseholds = $calamity->affectedHouseholds;
        $totalAffectedHouseholds = $affectedHouseholds->count();
        $totalAffectedResidents = $affectedHouseholds->sum(fn ($ah) => $ah->household?->residents()->count() ?? 0);

        $evacuatedHouseholds = $affectedHouseholds->where('evacuation_status', 'evacuated');
        $totalFamiliesEvacuated = $evacuatedHouseholds->count();
        $totalEvacuees = $evacuatedHouseholds->sum(fn ($ah) => $ah->household?->residents()->count() ?? 0);

        $damageAssessments = DamageAssessment::where('calamity_id', $calamity->id)->get();
        $partiallyDamaged = $affectedHouseholds->whereIn('damage_level', ['minor', 'moderate', 'severe'])->count();
        $totallyDamaged = $affectedHouseholds->where('damage_level', 'total')->count();
        $estimatedDamageCost = $damageAssessments->sum('estimated_cost');

        $reliefDistributions = ReliefDistribution::with(['item', 'household'])
            ->where('calamity_id', $calamity->id)
            ->get();
        $totalReliefDistributed = $reliefDistributions->sum('quantity');
        $reliefSummaryPerHousehold = $reliefDistributions->groupBy('household_id');

        $rescueOperations = RescueOperation::with(['affectedHousehold.household', 'rescuer', 'evacuationCenter'])
            ->whereHas('affectedHousehold', fn ($q) => $q->where('calamity_id', $calamity->id))
            ->get();
        $totalRescues = $rescueOperations->count();
        $rescueSummaryByHousehold = $rescueOperations->groupBy('calamity_affected_household_id');
        $evacuationCenterOccupancy = $rescueOperations->whereNotNull('evacuation_center_id')
            ->groupBy('evacuation_center_id')
            ->map->count();

        return compact(
            'calamity',
            'affectedHouseholds',
            'totalAffectedHouseholds',
            'totalAffectedResidents',
            'totalFamiliesEvacuated',
            'totalEvacuees',
            'partiallyDamaged',
            'totallyDamaged',
            'estimatedDamageCost',
            'reliefSummaryPerHousehold',
            'totalReliefDistributed',
            'rescueOperations',
            'totalRescues',
            'rescueSummaryByHousehold',
            'evacuationCenterOccupancy'
        );
    }
}
