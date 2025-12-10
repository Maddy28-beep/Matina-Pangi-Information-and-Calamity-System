{{-- Reusable Search Filter Component --}}
@props([
    'route' => '',
    'title' => 'Search & Filter',
    'icon' => 'bi-funnel-fill',
    'fields' => [],
    'advanced' => false,
    'inline' => false,
    'exportRoute' => null,
])

@php
    $advancedToggleId = $advanced ? 'advanced-toggle-' . uniqid() : null;
    $advancedRegionId = $advancedToggleId ? $advancedToggleId . '-panel' : null;
@endphp

<section class="ds-card ds-card--form" aria-label="{{ $title }} filters">
    <header class="ds-card__header">
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i class="{{ $icon }}"></i>
            <span>{{ $title }}</span>
        </h3>
        @if($advanced)
            <button
                type="button"
                class="btn btn-outline-secondary btn-sm"
                data-advanced-toggle
                data-advanced-target="#{{ $advancedRegionId }}"
                id="{{ $advancedToggleId }}"
                aria-expanded="false"
                aria-controls="{{ $advancedRegionId }}">
                <i class="bi bi-gear"></i>
                <span class="ms-1">Advanced</span>
            </button>
        @endif
    </header>

    <div class="ds-card__body">
        <form action="{{ $route }}" method="GET" class="ds-filter-form" novalidate>
            @if($inline)
                <div class="ds-inline-grid" style="display:grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap:12px; align-items:end; width:100%;">
                    @foreach($fields as $field)
                        @php $type = $field['type'] ?? 'text'; @endphp
                        <div style="width:100%">
                            <label class="form-label small" for="filter-{{ $field['name'] }}">{{ $field['label'] }}</label>

                            @if($type === 'text')
                                <input
                                    type="text"
                                    class="form-control"
                                    id="filter-{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    value="{{ request($field['name']) }}">

                            @elseif($type === 'select')
                                <select
                                    class="form-select"
                                    id="filter-{{ $field['name'] }}"
                                    name="{{ $field['name'] }}">
                                    <option value="">{{ $field['placeholder'] ?? 'All' }}</option>
                                    @foreach(($field['options'] ?? []) as $value => $label)
                                        <option value="{{ $value }}" {{ request($field['name']) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif($type === 'date')
                                <input
                                    type="date"
                                    class="form-control"
                                    id="filter-{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    value="{{ request($field['name']) }}">

                            @elseif($type === 'number')
                                <input
                                    type="number"
                                    class="form-control"
                                    id="filter-{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    value="{{ request($field['name']) }}"
                                    min="{{ $field['min'] ?? '' }}"
                                    max="{{ $field['max'] ?? '' }}">
                            @endif
                        </div>
                    @endforeach

                    @if(isset($exportRoute) && $exportRoute)
                        <div class="btn-group ms-auto">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ $exportRoute }}?{{ http_build_query(request()->all()) }}">
                                        <i class="bi bi-file-excel"></i> Export to Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ $exportRoute }}?format=pdf&{{ http_build_query(request()->all()) }}">
                                        <i class="bi bi-file-pdf"></i> Export to PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <div class="ds-toolbar__group d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            <span>Search</span>
                        </button>
                        <a href="{{ $route }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            <span>Clear</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="ds-form-grid">
                    @foreach($fields as $field)
                        <div class="ds-form-grid__item">
                            <label class="form-label small" for="filter-{{ $field['name'] }}">{{ $field['label'] }}</label>

                            @if(($field['type'] ?? 'text') === 'text')
                                <input
                                    type="text"
                                    class="form-control"
                                    id="filter-{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    value="{{ request($field['name']) }}">

                            @elseif(($field['type'] ?? 'text') === 'select')
                                <select
                                    class="form-select"
                                    id="filter-{{ $field['name'] }}"
                                    name="{{ $field['name'] }}">
                                    <option value="">{{ $field['placeholder'] ?? 'All' }}</option>
                                    @foreach(($field['options'] ?? []) as $value => $label)
                                        <option value="{{ $value }}" {{ request($field['name']) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif(($field['type'] ?? 'text') === 'date')
                                <input
                                    type="date"
                                    class="form-control"
                                    id="filter-{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    value="{{ request($field['name']) }}">

                            @elseif(($field['type'] ?? 'text') === 'number')
                                <input
                                    type="number"
                                    class="form-control"
                                    id="filter-{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    value="{{ request($field['name']) }}"
                                    min="{{ $field['min'] ?? '' }}"
                                    max="{{ $field['max'] ?? '' }}">
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="ds-card__footer">
                    @if(isset($exportRoute) && $exportRoute)
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ $exportRoute }}?{{ http_build_query(request()->all()) }}">
                                        <i class="bi bi-file-excel"></i> Export to Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ $exportRoute }}?format=pdf&{{ http_build_query(request()->all()) }}">
                                        <i class="bi bi-file-pdf"></i> Export to PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                    <div class="ds-toolbar__group">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            <span>Search</span>
                        </button>
                        <a href="{{ $route }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            <span>Clear</span>
                        </a>
                    </div>
                </div>
            @endif

            @if($advanced)
                <div id="{{ $advancedRegionId }}" class="ds-advanced" hidden>
                    <hr>
                    <div class="ds-form-grid">
                        {{ $advancedSlot ?? '' }}
                    </div>
                </div>
            @endif
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-advanced-toggle]').forEach(function(toggleBtn) {
        const targetSelector = toggleBtn.getAttribute('data-advanced-target');
        const target = targetSelector ? document.querySelector(targetSelector) : null;
        if (!target) {
            return;
        }

        toggleBtn.addEventListener('click', function() {
            const isHidden = target.hasAttribute('hidden');
            if (isHidden) {
                target.removeAttribute('hidden');
                toggleBtn.setAttribute('aria-expanded', 'true');
                toggleBtn.innerHTML = '<i class="bi bi-gear-fill"></i><span class="ms-1">Hide Advanced</span>';
            } else {
                target.setAttribute('hidden', '');
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.innerHTML = '<i class="bi bi-gear"></i><span class="ms-1">Advanced</span>';
            }
        });
    });

    document.querySelectorAll('.auto-submit').forEach(function(select) {
        select.addEventListener('change', function() {
            const form = select.closest('form');
            if (form) form.submit();
        });
    });
});
</script>
@endpush
