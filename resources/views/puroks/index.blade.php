@extends('layouts.app')

@section('content')<div class="ds-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-geo-alt-fill"></i> Purok Management</h2>
        @if(auth()->user()->isSecretary())
        <a href="{{ route('puroks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Purok
        </a>
        @endif
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($puroks as $purok)
        <div class="col-md-4 mb-4">
            <div class="card purok-card h-100 clickable-card" data-href="{{ route('puroks.show', $purok) }}">
                <div class="card-body">
                    <h5 class="purok-title">{{ $purok->purok_name }}</h5>
                    <p class="purok-code">{{ $purok->purok_code }}</p>
                    
                    <div class="purok-stats mb-3">
                        <div class="stat-item">
                            <div class="stat-number">{{ $purok->households_count ?? 0 }}</div>
                            <div class="stat-label">Households</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $purok->residents_count ?? 0 }}</div>
                            <div class="stat-label">Residents</div>
                        </div>
                    </div>

                    @if($purok->purok_leader_name)
                    <div class="leader-info mb-3">
                        <div class="leader-label">Purok Leader</div>
                        <p class="leader-name">{{ $purok->purok_leader_name }}</p>
                        @if($purok->purok_leader_contact)
                        <small class="leader-contact">{{ $purok->purok_leader_contact }}</small>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isSecretary())
                    <div class="purok-actions">
                        <button onclick="event.stopPropagation(); window.location.href='{{ route('puroks.edit', $purok) }}'" class="btn btn-sm btn-outline-secondary" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <form action="{{ route('puroks.update-counts', $purok) }}" method="POST" class="d-inline" onclick="event.stopPropagation();">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Update Counts">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h3 class="empty-state-title">No puroks found</h3>
                <p class="empty-state-description">
                    Get started by adding your first purok to the system.
                </p>
                @if(auth()->user()->isSecretary())
                <a href="{{ route('puroks.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add First Purok
                </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
.purok-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    border-top: 3px solid #4A6F52;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    cursor: pointer;
}

.purok-card:hover {
    box-shadow: 0 4px 12px rgba(74, 111, 82, 0.12);
    border-top-color: #5a9275;
    transform: translateY(-2px);
}

.purok-title {
    color: #1a202c;
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.purok-code {
    text-align: left;
    background: #f9fafb;
    color: #6b7280;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-block;
    margin-bottom: 1.25rem;
    font-family: 'Courier New', monospace;
}

.purok-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.stat-item {
    background: #f9fafb;
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #f3f4f6;
}

.stat-number {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1a202c;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.leader-info {
    background: #f9fafb;
    padding: 0.875rem;
    border-radius: 8px;
    border: 1px solid #f3f4f6;
    margin-bottom: 1rem;
}

.leader-label {
    font-size: 0.6875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.375rem;
}

.leader-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
    font-size: 0.9375rem;
}

.leader-contact {
    color: #6b7280;
    font-size: 0.8125rem;
}

.purok-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    padding-top: 0.875rem;
    border-top: 1px solid #f3f4f6;
}

.purok-actions .btn {
    border-radius: 6px;
    font-weight: 600;
    padding: 0.375rem 0.875rem;
    font-size: 0.875rem;
}

.clickable-card {
    cursor: pointer;
}
</style>
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make cards clickable
    document.querySelectorAll('.clickable-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Don't navigate if clicking on a button or form
            if (!e.target.closest('button, form, a')) {
                const href = this.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            }
        });
    });
});
</script>

@endsection
