@extends('layouts.app')

@section('title', 'Security Audit')

@section('content')
<div class="ds-page">
    <div class="page-header mb-3">
        <div class="page-header__title"><i class="bi bi-shield-lock"></i> Security Audit</div>
        <div class="page-header__meta"><span>Review MFA and access logs</span></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-2">Calamity Heads Without MFA</h6>
                    <ul class="list-group list-group-flush">
                        @forelse($calamityHeadsWithoutMfa as $u)
                            <li class="list-group-item">{{ $u->name }} • {{ $u->email }}</li>
                        @empty
                            <li class="list-group-item">All calamity heads have MFA enabled.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-2">Recent Access Logs</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAccessLogs as $log)
                                    <tr>
                                        <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ optional($log->user)->name ?? 'N/A' }}</td>
                                        <td>{{ $log->action }}</td>
                                        <td>{{ $log->description }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4">No recent logs.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

