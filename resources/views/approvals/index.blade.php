@extends('layouts.app')

@section('title', 'Pending Approvals')

@push('styles')
<style>
:root{--ds-font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,Liberation Sans,sans-serif;--ds-font-size-base:0.95rem;--ds-font-size-title:1rem;--ds-font-size-header:0.85rem;--ds-line-height:1.5;--ds-card-header-height:52px;--ds-card-body-min-height:380px}
.ds-page{font-family:var(--ds-font-family);font-size:var(--ds-font-size-base);line-height:var(--ds-line-height)}
.ds-card{display:flex;flex-direction:column;height:100%}
.ds-card .card-header{min-height:var(--ds-card-header-height);display:flex;align-items:center}
.ds-card .card-body{min-height:var(--ds-card-body-min-height);overflow:auto}
.ds-card h6{font-size:var(--ds-font-size-title);font-weight:600}
.ds-table thead th{font-size:var(--ds-font-size-header);font-weight:600;letter-spacing:.02em}
.ds-table tbody td{vertical-align:middle;white-space:nowrap;text-overflow:ellipsis;overflow:hidden}
@media (max-width: 991.98px){.ds-card .card-body{min-height:300px}}
@media (max-width: 575.98px){.ds-card .card-body{min-height:260px}}
/* Active input feedback for rejection reason */
.form-control.is-focused{border-color:#0d6efd;box-shadow:0 0 0 .2rem rgba(13,110,253,.25)}
</style>
@endpush

@section('content')<div class="ds-page">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history"></i> Pending Approvals</h2>
    <a href="{{ route('archived.index') }}" class="btn btn-secondary">
        <i class="bi bi-archive"></i> View Archived Records
    </a>
</div>

 

<!-- Pending Approvals Grid -->
<div class="card shadow-sm border-0">
  <div class="card-header bg-success text-white py-2">
    <h6 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-check2-circle"></i> Pending Approvals</h6>
  </div>
  <div class="card-body">
    <div class="d-flex flex-wrap gap-2 mb-3">
      <div class="input-group" style="max-width: 320px;">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input id="approvalSearch" type="text" class="form-control" placeholder="Search approvals">
      </div>
      <div class="input-group" style="max-width: 220px;">
        <span class="input-group-text"><i class="bi bi-funnel"></i></span>
        <select id="approvalTypeFilter" class="form-select">
          <option value="all" selected>All types</option>
          <option value="resident">Residents</option>
          <option value="household">Households</option>
          <option value="transfer">Transfers</option>
          <option value="certificate">Certificates</option>
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-sm align-middle sortable-table ds-table table-clickable" id="approvalsTable">
        <thead class="table-light">
          <tr>
            <th>Type</th>
            <th>Identifier</th>
            <th>Details</th>
            <th>Requester</th>
            <th>Date</th>
            <th>Last Update</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="approvalsTableBody">
          @forelse($pending as $item)
          @php $type = $item['type']; $model = $item['model']; @endphp
          <tr data-type="{{ $type }}"
              @if($type === 'resident')
                data-href="{{ route('residents.show', $model) }}"
              @elseif($type === 'household')
                data-href="{{ route('households.show', $model) }}"
              @elseif($type === 'certificate')
                data-href="{{ route('certificates.requests.show', $model) }}"
              @else
                data-href="{{ route('resident-transfers.show', $model) }}"
              @endif
          >
            <td class="text-capitalize">{{ $type }}</td>
            <td>
              @if($type === 'resident')
                <strong>{{ $model->resident_id }}</strong>
              @elseif($type === 'household')
                <strong>{{ $model->household_id }}</strong>
              @elseif($type === 'certificate')
                <strong>{{ optional($model->resident)->full_name }}</strong>
              @else
                <strong>{{ optional($model->resident)->full_name }}</strong>
              @endif
            </td>
            <td class="text-truncate" style="max-width: 320px;">
              @if($type === 'resident')
                {{ $model->full_name }} • HH: <a href="{{ route('households.show', $model->household) }}">{{ optional($model->household)->household_id }}</a>
              @elseif($type === 'household')
                {{ $model->full_address }} • Head: {{ optional($model->head)->full_name }} • Members: {{ $model->total_members }}
              @elseif($type === 'certificate')
                {{ ucwords(str_replace('_',' ', $model->certificate_type)) }} • {{ $model->purpose }}
              @else
                {{ $model->transfer_type === 'transfer_in' ? 'Internal' : 'External' }} • From: {{ optional($model->oldHousehold)->household_id }} → To: {{ optional($model->newHousehold)->household_id }}
              @endif
            </td>
            <td>{{ $item['requester'] ?: 'N/A' }}</td>
            <td data-sort-value="{{ $item['created_at']->timestamp }}">{{ $item['created_at']->format('M d, Y h:i A') }}</td>
            <td data-sort-value="{{ $item['updated_at']->timestamp }}">{{ $item['updated_at']->format('M d, Y h:i A') }}</td>
            <td>
              <div class="btn-group btn-group-sm">
                @if($type === 'resident')
                  <form action="{{ route('approvals.resident.approve', $model) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Approve</button></form>
                  <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectResidentModal{{ $model->id }}"><i class="bi bi-x-circle"></i> Reject</button>
                @elseif($type === 'household')
                  <form action="{{ route('approvals.household.approve', $model) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Approve</button></form>
                  <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectHouseholdModal{{ $model->id }}"><i class="bi bi-x-circle"></i> Reject</button>
                @elseif($type === 'certificate')
                  <form action="{{ route('approvals.certificate.approve', $model) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Approve</button></form>
                  <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectCertificateModal{{ $model->id }}"><i class="bi bi-x-circle"></i> Reject</button>
                @else
                  <form action="{{ route('resident-transfers.approve', $model) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Approve</button></form>
                  <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectTransferModal{{ $model->id }}"><i class="bi bi-x-circle"></i> Reject</button>
                @endif
              </div>

              @if($type === 'resident')
              <div class="modal fade" id="rejectResidentModal{{ $model->id }}" tabindex="-1" data-bs-backdrop="false" aria-labelledby="rejectResidentLabel{{ $model->id }}" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
                <form action="{{ route('approvals.resident.reject', $model) }}" method="POST" class="js-reject-form" novalidate>@csrf
                  <div class="modal-header"><h5 class="modal-title" id="rejectResidentLabel{{ $model->id }}">Reject Resident</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                  <div class="modal-body"><p>Reject <strong>{{ $model->full_name }}</strong>?</p><div class="mb-3"><label class="form-label">Rejection Reason <span class="text-danger">*</span></label><textarea class="form-control" name="rejection_reason" rows="3" required minlength="3"></textarea><div class="invalid-feedback">A rejection reason is required.</div><div class="alert alert-danger d-none js-reject-error" role="alert"></div></div></div>
                  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger" data-submit><span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span><span>Reject & Archive</span></button></div>
                </form>
              </div></div></div>
              @elseif($type === 'household')
              <div class="modal fade" id="rejectHouseholdModal{{ $model->id }}" tabindex="-1" data-bs-backdrop="false" aria-labelledby="rejectHouseholdLabel{{ $model->id }}" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
                <form action="{{ route('approvals.household.reject', $model) }}" method="POST" class="js-reject-form" novalidate>@csrf
                  <div class="modal-header"><h5 class="modal-title" id="rejectHouseholdLabel{{ $model->id }}">Reject Household</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                  <div class="modal-body"><p>Reject household <strong>{{ $model->household_id }}</strong>?</p><p class="text-danger small">This will also reject all {{ $model->total_members }} member(s).</p><div class="mb-3"><label class="form-label">Rejection Reason <span class="text-danger">*</span></label><textarea class="form-control" name="rejection_reason" rows="3" required minlength="3"></textarea><div class="invalid-feedback">A rejection reason is required.</div><div class="alert alert-danger d-none js-reject-error" role="alert"></div></div></div>
                  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger" data-submit><span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span><span>Reject & Archive</span></button></div>
                </form>
              </div></div></div>
              @elseif($type === 'certificate')
              <div class="modal fade" id="rejectCertificateModal{{ $model->id }}" tabindex="-1" data-bs-backdrop="false" aria-labelledby="rejectCertificateLabel{{ $model->id }}" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
                <form action="{{ route('approvals.certificate.reject', $model) }}" method="POST" class="js-reject-form" novalidate>@csrf
                  <div class="modal-header"><h5 class="modal-title" id="rejectCertificateLabel{{ $model->id }}">Reject Certificate Request</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                  <div class="modal-body"><p>Reject certificate request for <strong>{{ optional($model->resident)->full_name }}</strong>?</p><div class="mb-3"><label class="form-label">Rejection Reason <span class="text-danger">*</span></label><textarea class="form-control js-rejection-reason" name="rejection_reason" rows="3" required minlength="3" maxlength="500"></textarea><div class="d-flex justify-content-between"><div class="invalid-feedback">A rejection reason is required.</div><small class="text-muted"><span class="js-char-count">0</span>/500</small></div><div class="alert alert-danger d-none js-reject-error" role="alert"></div></div></div>
                  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger" data-submit><span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span><span>Reject</span></button></div>
                </form>
              </div></div></div>
              @else
              <div class="modal fade" id="rejectTransferModal{{ $model->id }}" tabindex="-1" data-bs-backdrop="false" aria-labelledby="rejectTransferLabel{{ $model->id }}" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
                <form action="{{ route('resident-transfers.reject', $model) }}" method="POST" class="js-reject-form" novalidate>@csrf
                  <div class="modal-header"><h5 class="modal-title" id="rejectTransferLabel{{ $model->id }}">Reject Transfer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                  <div class="modal-body"><p>Reject transfer for <strong>{{ optional($model->resident)->full_name }}</strong>?</p><div class="mb-3"><label class="form-label">Remarks <span class="text-danger">*</span></label><textarea class="form-control" name="remarks" rows="3" required minlength="3"></textarea><div class="invalid-feedback">A rejection reason is required.</div><div class="alert alert-danger d-none js-reject-error" role="alert"></div></div></div>
                  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger" data-submit><span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span><span>Reject</span></button></div>
                </form>
              </div></div></div>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-5">No pending approvals.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $pending->links() }}
    </div>
  </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  function makeSortable(table){
    const ths = table.querySelectorAll('thead th');
    ths.forEach((th, idx)=>{
      th.style.cursor = 'pointer';
      th.addEventListener('click', ()=>{
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const asc = th.dataset.sortOrder !== 'asc';
        ths.forEach(h=>{ h.dataset.sortOrder = ''; });
        th.dataset.sortOrder = asc ? 'asc' : 'desc';
        rows.sort((a,b)=>{
          const av = a.children[idx].getAttribute('data-sort-value') || a.children[idx].textContent.trim();
          const bv = b.children[idx].getAttribute('data-sort-value') || b.children[idx].textContent.trim();
          const an = Number(av), bn = Number(bv);
          const isNum = !isNaN(an) && !isNaN(bn);
          if(isNum){ return asc ? an - bn : bn - an; }
          return asc ? av.localeCompare(bv) : bv.localeCompare(av);
        });
        rows.forEach(r=>tbody.appendChild(r));
      });
    });
  }
  document.querySelectorAll('.sortable-table').forEach(makeSortable);

  // Client-side filter
  const filterSelect = document.getElementById('approvalTypeFilter');
  const searchInput = document.getElementById('approvalSearch');
  function applyFilter(){
    const type = (filterSelect?.value || 'all');
    const q = (searchInput?.value || '').toLowerCase();
    document.querySelectorAll('#approvalsTableBody tr').forEach(row => {
      const rowType = row.getAttribute('data-type');
      const text = row.textContent.toLowerCase();
      const typeOk = type === 'all' || rowType === type;
      const searchOk = !q || text.includes(q);
      row.style.display = (typeOk && searchOk) ? '' : 'none';
    });
  }
  filterSelect?.addEventListener('change', applyFilter);
  searchInput?.addEventListener('input', applyFilter);

  function refreshTable(){
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
      .then(r=>r.text())
      .then(html=>{
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const bodyNew = doc.querySelector('#approvalsTableBody');
        const body = document.querySelector('#approvalsTableBody');
        if(bodyNew && body){ body.innerHTML = bodyNew.innerHTML; }
        document.querySelectorAll('.sortable-table').forEach(makeSortable);
        applyFilter();
      })
      .catch(()=>{});
  }
  setInterval(refreshTable, 30000);

  // Stable rejection reason input behavior (spacebar focus fix + char counter)
  document.querySelectorAll('.modal .js-rejection-reason').forEach(function(textarea){
    const container = textarea.closest('.mb-3');
    const countEl = container ? container.querySelector('.js-char-count') : null;
    const maxLen = parseInt(textarea.getAttribute('maxlength') || '500', 10);

    function updateCount(){
      const len = textarea.value.length;
      if(countEl){ countEl.textContent = String(len > maxLen ? maxLen : len); }
      if(len > maxLen){ textarea.value = textarea.value.substring(0, maxLen); }
    }

    updateCount();
    textarea.addEventListener('input', updateCount);

    textarea.addEventListener('focus', function(){
      textarea.classList.add('is-focused');
    });
    textarea.addEventListener('blur', function(){
      textarea.classList.remove('is-focused');
    });

    textarea.addEventListener('keydown', function(e){
      if(e.key === ' ' || e.code === 'Space' || e.keyCode === 32 || e.key === 'Enter'){
        e.stopPropagation();
      }
    });

    textarea.addEventListener('keyup', function(e){
      if(e.key === ' ' || e.code === 'Space' || e.keyCode === 32){
        // Ensure focus remains on the textarea after spacebar
        textarea.focus({ preventScroll: true });
      }
    });
  });
});
</script>
@endpush
