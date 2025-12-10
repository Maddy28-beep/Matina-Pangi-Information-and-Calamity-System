@extends('layouts.app')

@section('title', 'Announcement Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-megaphone"></i> Announcement Details</h2>
    <a href="{{ route('announcements.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Announcement</h5></div>
            <div class="card-body">
                <p><strong>Title:</strong><br>{{ $announcement->title }}</p>
                <p><strong>Urgency:</strong><br>{{ $announcement->urgency ?: 'Normal' }}</p>
                <p><strong>Status:</strong><br><span class="badge bg-{{ $announcement->status==='sent'?'success':'secondary' }}">{{ ucfirst($announcement->status) }}</span></p>
                <p><strong>Sent At:</strong><br>{{ optional($announcement->sent_at)->format('Y-m-d H:i') }}</p>
                <p><strong>Message:</strong><br>{{ $announcement->message }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Recipients ({{ $announcement->recipients->count() }})</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Household</th>
                            <th>Contact</th>
                            <th>Email</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($announcement->recipients as $r)
                            <tr>
                                <td>{{ $r->resident->full_name ?? ($r->resident->first_name.' '.$r->resident->last_name) }}</td>
                                <td>{{ optional($r->resident->household)->household_id }}</td>
                                <td>{{ $r->resident->contact_number }}</td>
                                <td>{{ $r->resident->email }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection