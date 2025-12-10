<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateRequest;
use App\Models\Resident;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['resident', 'issuer']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('certificate_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhere('or_number', 'like', "%{$search}%")
                    ->orWhereHas('resident', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (in_array($sort, ['type', 'date', 'status', 'resident'])) {
            if ($sort === 'type') {
                $query->orderBy('certificate_type', $direction);
            } elseif ($sort === 'date') {
                $query->orderBy('issued_date', $direction);
            } elseif ($sort === 'status') {
                $query->orderBy('status', $direction);
            } elseif ($sort === 'resident') {
                // Join residents table to sort by resident name
                $query->leftJoin('residents', 'residents.id', '=', 'certificates.resident_id')
                    ->select('certificates.*')
                    ->orderBy('residents.last_name', $direction)
                    ->orderBy('residents.first_name', $direction);
            }
        } else {
            $query->latest();
        }

        $certificates = $query->paginate(20)->withQueryString();

        return view('certificates.index', compact('certificates'));
    }

    /**
     * Show the form for creating a new certificate
     */
    public function create()
    {
        $residents = Resident::approved()->active()
            ->with('household')
            ->orderBy('last_name')
            ->get()
            ->filter(function ($r) {
                return $r->household !== null;
            })
            ->values();

        return view('certificates.create', compact('residents'));
    }

    /**
     * Store a newly created certificate
     */
    public function store(Request $request)
    {
        if (auth()->user()->isStaff()) {
            abort(403, 'Only secretary can issue certificates.');
        }
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'certificate_type' => 'required|in:barangay_clearance,certificate_of_indigency,certificate_of_residency,business_clearance,good_moral,travel_permit',
            'purpose' => 'required|string|max:500',
            'or_number' => 'nullable|string|max:50',
            'amount_paid' => 'required|numeric|min:0',
            'valid_until' => 'nullable|date|after:today',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $validated['issued_by'] = auth()->id();
        $validated['issued_date'] = now();
        $validated['status'] = 'issued';

        $certificate = Certificate::create($validated);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Certificate issued successfully!');
    }

    /**
     * Store a new certificate request (staff)
     */
    public function requestStore(Request $request)
    {
        if (! auth()->user()->isStaff()) {
            abort(403, 'Only staff can submit certificate requests.');
        }

        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'certificate_type' => 'required|in:barangay_clearance,certificate_of_indigency,certificate_of_residency,business_clearance,good_moral,travel_permit',
            'purpose' => 'required|string|max:500',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $requestData = [
            'resident_id' => $validated['resident_id'],
            'certificate_type' => $validated['certificate_type'],
            'purpose' => $validated['purpose'],
            'status' => 'pending',
            'notes' => $validated['remarks'] ?? null,
        ];

        CertificateRequest::create($requestData);

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate request submitted for approval.');
    }

    public function requestShow(\App\Models\CertificateRequest $certificateRequest)
    {
        $certificateRequest->load(['resident.household']);

        return view('certificates.request-show', compact('certificateRequest'));
    }

    /**
     * Display the specified certificate
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['resident.household.purok', 'issuer']);

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Update certificate status
     */
    public function updateStatus(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'status' => 'required|in:issued,claimed,cancelled',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $certificate->update($validated);

        return redirect()->back()
            ->with('success', 'Certificate status updated successfully!');
    }

    /**
     * Generate PDF for certificate
     */
    public function generatePdf(Certificate $certificate)
    {
        $certificate->load(['resident', 'issuer']);

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'));

        return $pdf->download("certificate-{$certificate->certificate_number}.pdf");
    }

    /**
     * Print certificate
     */
    public function print(Certificate $certificate)
    {
        $certificate->load(['resident', 'issuer']);

        return view('certificates.print', compact('certificate'));
    }
}
