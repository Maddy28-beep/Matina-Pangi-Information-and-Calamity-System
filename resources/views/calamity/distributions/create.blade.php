@extends('layouts.app')

@section('title', 'Add Relief Distribution')

@section('content')<div class="ds-page">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('web.relief-distributions.index') }}">Relief Distribution</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add</li>
      </ol>
    </nav>

    <div class="card">
      <div class="card-body">
        @php
          $calamities = \App\Models\Calamity::orderByDesc('date_occurred')->get(['id', 'calamity_name']);
          $households = \App\Models\Household::approved()->orderBy('household_id')->get(['id', 'household_id']);
          $items = \App\Models\ReliefItem::orderBy('name')->get(['id', 'name']);
          $staffUsers = \App\Models\User::orderBy('name')->get(['id', 'name']);
        @endphp
        <form method="POST" action="{{ route('web.relief-distributions.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Calamity</label>
              <select name="calamity_id" class="form-select">
                <option value="">None</option>
                @foreach($calamities as $c)
                  <option value="{{ $c->id }}" @selected(old('calamity_id') == $c->id)>{{ $c->calamity_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Household</label>
              <select name="household_id" class="form-select" required>
                <option value="">Select household</option>
                @foreach($households as $hh)
                  <option value="{{ $hh->id }}" @selected(old('household_id') == $hh->id)>{{ $hh->household_id }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Item</label>
              <select name="relief_item_id" class="form-select" required>
                <option value="">Select item</option>
                @foreach($items as $it)
                  <option value="{{ $it->id }}" @selected(old('relief_item_id') == $it->id)>{{ $it->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Quantity</label>
              <input type="number" name="quantity" class="form-control" min="1" value="{{ old('quantity') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Date Distributed</label>
              <input type="date" name="distributed_at" class="form-control" value="{{ old('distributed_at') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Staff</label>
              <select name="staff_in_charge" class="form-select">
                <option value="">Current user</option>
                @foreach($staffUsers as $u)
                  <option value="{{ $u->id }}" @selected(old('staff_in_charge') == $u->id)>{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('web.relief-distributions.index') }}" class="btn btn-secondary">Cancel</a>
            <button class="btn btn-primary" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection