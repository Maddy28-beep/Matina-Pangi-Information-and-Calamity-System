<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Resident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display dashboard
     */
    public function index(Request $request)
    {
        if (! (auth()->user()->isSecretary() || auth()->user()->isStaff())) {
            abort(403, 'Access Denied: This dashboard is restricted to Secretary and Staff accounts');
        }
        \App\Models\AuditLog::logAction('dashboard_access', 'User', auth()->id(), 'Secretary dashboard accessed');
        $stats = [
            'total_residents' => Resident::approved()->count(),
            'total_households' => Household::approved()->count(),
            'total_pwd' => Resident::approved()->active()->pwd()->count(),
            'total_senior_citizens' => Resident::approved()->active()->seniorCitizens()->count(),
            'total_teens' => Resident::approved()->active()->teens()->count(),
            'total_voters' => Resident::approved()->active()->voters()->count(),
            'total_4ps' => Resident::approved()->active()->where('is_4ps_beneficiary', true)->count(),
            'male_count' => Resident::approved()->active()->where('sex', 'male')->count(),
            'female_count' => Resident::approved()->active()->where('sex', 'female')->count(),
            'average_household_size' => Household::approved()->avg('total_members'),
            'total_household_income' => Resident::approved()->active()->sum('monthly_income'),
        ];

        // Filters
        $status = $request->query('status'); // approved/pending
        $purok = $request->query('purok');
        $from = $request->query('from_date');
        $to = $request->query('to_date');
        $type = $request->query('type'); // residents/households

        // Data query
        $resQuery = Resident::query()->with('household');
        if ($status === 'approved') {
            $resQuery->approved();
        } elseif ($status === 'pending') {
            $resQuery->where('approval_status', 'pending');
        }
        if ($purok) {
            $resQuery->whereHas('household', function ($q) use ($purok) {
                $q->where('purok', $purok);
            });
        }
        if ($from) {
            $resQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $resQuery->whereDate('created_at', '<=', $to);
        }

        $recentResidents = Resident::with('household')->approved()->latest()->take(5)->get();
        $records = $resQuery->latest()->paginate(10)->withQueryString();

        // Age distribution
        $ageDistribution = [
            'children' => Resident::approved()->active()->where('age', '<', 13)->count(),
            'teens' => Resident::approved()->active()->teens()->count(),
            'adults' => Resident::approved()->active()->whereBetween('age', [20, 59])->count(),
            'seniors' => Resident::approved()->active()->seniorCitizens()->count(),
        ];

        // Charts
        $monthlyResidents = Resident::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as c')
            ->groupBy('ym')->orderBy('ym')->get()->map(fn ($r) => [$r->ym, (int) $r->c])->toArray();
        $bySex = Resident::selectRaw('sex, COUNT(*) as c')->groupBy('sex')->get()->map(fn ($r) => [$r->sex, (int) $r->c])->toArray();

        return view('dashboard', compact('stats', 'recentResidents', 'ageDistribution', 'records', 'monthlyResidents', 'bySex', 'status', 'purok', 'from', 'to', 'type'));
    }
}
