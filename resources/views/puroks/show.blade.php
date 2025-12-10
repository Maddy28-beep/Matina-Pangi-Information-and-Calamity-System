@extends('layouts.app')

@section('content')<div class="ds-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $purok->purok_name }}</h2>
        <div class="d-flex gap-2">
            @if(auth()->user()->isSecretary())
            <a href="{{ route('puroks.edit', $purok) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            @endif
            <a href="{{ route('puroks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $purok->households->count() }}</h3>
                    <p class="text-muted mb-0">Households</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $purok->residents->count() }}</h3>
                    <p class="text-muted mb-0">Residents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $purok->residents->where('sex', 'male')->count() }}</h3>
                    <p class="text-muted mb-0">Male</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $purok->residents->where('sex', 'female')->count() }}</h3>
                    <p class="text-muted mb-0">Female</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Purok Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-nowrap"><strong>Code:</strong></td>
                            <td>{{ $purok->purok_code }}</td>
                        </tr>
                        @if($purok->purok_leader_name)
                        <tr>
                            <td class="text-nowrap"><strong>Leader:</strong></td>
                            <td>{{ $purok->purok_leader_name }}</td>
                        </tr>
                        @endif
                        @if($purok->purok_leader_contact)
                        <tr>
                            <td class="text-nowrap"><strong>Contact:</strong></td>
                            <td>{{ $purok->purok_leader_contact }}</td>
                        </tr>
                        @endif
                        @if($purok->description)
                        <tr>
                            <td colspan="2">
                                <strong>Description:</strong><br>
                                {{ $purok->description }}
                            </td>
                        </tr>
                        @endif
                        @if($purok->boundaries)
                        <tr>
                            <td colspan="2">
                                <strong>Boundaries:</strong><br>
                                {{ $purok->boundaries }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Households in this Purok <small class="text-white">(Click row to view details)</small></h5>
                </div>
                <div class="card-body">
                    @if($purok->households->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Household ID</th>
                                    <th>Head</th>
                                    <th>Members</th>
                                    <th>Address</th>
                                    <th class="text-center">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purok->households as $household)
                                <tr onclick="window.location='{{ route('households.show', $household) }}'" style="cursor: pointer;" class="table-row-clickable">
                                    <td><strong>{{ $household->household_id }}</strong></td>
                                    <td>{{ $household->head->full_name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ $household->total_members }}</span></td>
                                    <td>{{ Str::limit($household->address, 30) }}</td>
                                    <td class="text-center">
                                        <i class="bi bi-arrow-right-circle text-primary" style="font-size: 1.25rem;"></i>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">No households in this purok yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
