@extends('layouts.app')

@section('title', 'Announcements')

@push('styles')
<style>
/* Live Search Styling */
.live-search-container .input-group {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-radius: 0.5rem;
    overflow: hidden;
}

.live-search-container .form-control {
    border: 1px solid #E5E7EB;
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    font-weight: 500;
}

.live-search-container .form-control:focus {
    box-shadow: none;
    border-color: #4A6F52;
}

.live-search-container .input-group-text {
    border: 1px solid #E5E7EB;
    border-right: none;
    padding: 0.75rem 1rem;
}

.live-search-container .clear-search-btn {
    border: 1px solid #E5E7EB;
    border-left: none;
    background: white !important;
    color: #6B7280 !important;
    padding: 0.5rem 1rem !important;
}

.live-search-container .clear-search-btn:hover {
    background: #F9FAFB !important;
    color: #4A6F52 !important;
}

.search-result-count {
    font-size: 0.875rem;
    color: #4A6F52;
    font-weight: 600;
}

.announcements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.announcement-card {
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    border-top: 3px solid #4A6F52;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    transition: all 0.2s ease;
    overflow: hidden;
}

.announcement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 111, 82, 0.12);
    border-top-color: #5a9275;
}

.announcement-card-body {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.announcement-urgency {
    margin-bottom: 0;
}

.urgency-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.6875rem;
    padding: 4px 10px;
    font-weight: 700;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.urgency-high,
.urgency-urgent {
    background-color: #EF4444;
    color: white;
}

.urgency-medium {
    background-color: #F59E0B;
    color: white;
}

.urgency-low,
.urgency-normal {
    background-color: #10B981;
    color: white;
}

.announcement-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
    line-height: 1.4;
}

.announcement-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    background: #f9fafb;
    padding: 0.75rem;
    border-radius: 8px;
}

.meta-item {
    font-size: 0.8125rem;
    color: #6b7280;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-item i {
    color: #4A6F52;
    width: 14px;
    font-size: 0.875rem;
}

.announcement-status {
    margin-top: 0.5rem;
}

.announcement-status .badge {
    font-size: 0.6875rem;
    padding: 4px 10px;
    font-weight: 600;
    border-radius: 6px;
}

.status-badge-sent {
    background-color: #4A6F52 !important;
    color: white !important;
}

.status-badge-draft {
    background-color: #9CA3AF !important;
    color: white !important;
}

.status-badge-scheduled {
    background-color: #3B82F6 !important;
    color: white !important;
}

.announcement-actions {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f3f4f6;
}

.announcement-actions .btn {
    width: 100%;
    font-weight: 600;
    background-color: #4A6F52 !important;
    border: none !important;
    color: white !important;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.announcement-actions .btn:hover {
    background-color: #5a9275 !important;
}
    background-color: #4A6F52 !important;
    border: none !important;
    color: white !important;
    padding: 0.625rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.announcement-actions .btn:hover {
    background-color: #5a9275 !important;
    transform: translateY(-1px);
}

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
}

.empty-state i {
    font-size: 4rem;
    color: #D1D5DB;
    margin-bottom: 1.5rem;
}

.empty-state p {
    color: #6B7280;
    font-size: 1.125rem;
    margin-bottom: 1.5rem;
    font-weight: 500;
}

@media (max-width: 991px) {
    .announcements-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 576px) {
    .announcements-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    
    .announcement-card-body {
        padding: 1.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-megaphone"></i> Announcements</h2>
    <a href="{{ route('announcements.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
</div>

<!-- Live Search Box -->
<div class="live-search-container mb-4">
    <div class="input-group">
        <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" 
               class="form-control border-start-0 live-search-input" 
               placeholder="🔍 Search announcements by title, urgency, status, date..." 
               data-target-grid=".announcements-grid"
               autocomplete="off">
        <button class="btn btn-outline-secondary clear-search-btn" type="button" title="Clear search">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <small class="text-muted ms-2">
        <span class="search-result-count"></span>
    </small>
</div>

<!-- Announcements Grid -->
@if($announcements->count())
    <div class="announcements-grid">
        @foreach($announcements as $a)
        @php
            $searchText = implode(' ', [
                $a->title,
                $a->urgency ?: 'Normal',
                ucfirst($a->status),
                optional($a->sent_at)->format('M d Y') ?: 'Not sent',
                optional($a->sent_at)->format('h:i A') ?: ''
            ]);
        @endphp
        <div class="announcement-card" data-search-text="{{ $searchText }}">
            <div class="announcement-card-body">
                @if($a->urgency && $a->urgency !== 'Normal')
                <div class="announcement-urgency">
                    <span class="urgency-badge urgency-{{ strtolower($a->urgency) }}">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $a->urgency }}
                    </span>
                </div>
                @endif
                
                <h4 class="announcement-title">{{ $a->title }}</h4>
                
                <div class="announcement-meta">
                    <div class="meta-item">
                        <i class="bi bi-calendar-fill"></i>
                        {{ optional($a->sent_at)->format('M d, Y') ?: 'Not sent yet' }}
                    </div>
                    @if($a->sent_at)
                    <div class="meta-item">
                        <i class="bi bi-clock-fill"></i>
                        {{ $a->sent_at->format('h:i A') }}
                    </div>
                    @endif
                </div>
                
                <div class="announcement-status">
                    <span class="badge status-badge-{{ strtolower($a->status) }}">
                        {{ ucfirst($a->status) }}
                    </span>
                </div>
                
                <div class="announcement-actions">
                    <a href="{{ route('announcements.show', $a) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-4">
        {{ $announcements->links() }}
    </div>
    
    <!-- Search Empty State -->
    <div class="search-empty-state" style="display: none;">
        <div class="empty-state">
            <i class="bi bi-search"></i>
            <p>No announcements match your search.</p>
            <small>Try adjusting your search terms.</small>
        </div>
    </div>
@else
    <div class="empty-state">
        <i class="bi bi-megaphone"></i>
        <p>No announcements yet.</p>
        <a href="{{ route('announcements.create') }}" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
            <i class="bi bi-plus-circle" style="font-size: 0.9rem;"></i> Create First Announcement
        </a>
    </div>
@endif
@endsection