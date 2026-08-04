@extends('layouts.master')
@section('title')
    Location
@endsection

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600&family=DM+Sans:wght@400;500&display=swap');

    .orders-root {
        font-family: 'DM Sans', sans-serif;
        padding: 2rem 0;
    }

    .orders-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .orders-title {
        font-family: 'Sora', sans-serif;
        font-size: 22px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 2px;
    }

    .orders-subtitle {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
    }

    .orders-date {
        font-size: 12px;
        color: #adb5bd;
        background: #f8f9fa;
        border: 0.5px solid #e9ecef;
        padding: 6px 14px;
        border-radius: 999px;
    }

    /* Section block */
    .loc-section {
        margin-bottom: 2.25rem;
    }

    .loc-section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1rem;
    }

    .loc-section-label {
        font-family: 'Sora', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #343a40;
        margin: 0;
    }

    .loc-section-line {
        flex: 1;
        height: 0.5px;
        background: #e9ecef;
    }

    .loc-section-count {
        font-size: 11px;
        color: #adb5bd;
        background: #f8f9fa;
        border: 0.5px solid #e9ecef;
        padding: 2px 10px;
        border-radius: 999px;
    }

    /* Grid */
    .orders-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    /* Card */
    .order-card {
        background: #ffffff;
        border: 0.5px solid #e9ecef;
        border-radius: 12px;
        padding: 1.25rem;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 0;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
        position: relative;
        overflow: hidden;
    }

    .order-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 12px 12px 0 0;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .order-card:hover {
        border-color: #ced4da;
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.07);
        text-decoration: none;
    }

    .order-card:hover::before { opacity: 1; }

    .card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .card-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-arrow {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .order-card:hover .card-arrow { opacity: 1; }

    .card-label {
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .card-name {
        font-family: 'Sora', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 1rem;
    }

    .card-divider {
        height: 0.5px;
        background: #e9ecef;
        margin-bottom: 0.85rem;
    }

    .card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-pill {
        font-size: 11px;
        font-weight: 500;
        padding: 3px 10px;
        border-radius: 999px;
        border: 0.5px solid;
    }

    .card-count-badge {
        font-size: 11px;
        font-weight: 500;
        color: #6c757d;
    }

    /* Theme: Purple – Countries */
    .theme-purple .card-icon  { background: #EEEDFE; }
    .theme-purple .card-label { color: #534AB7; }
    .theme-purple .card-pill  { background: #EEEDFE; color: #3C3489; border-color: #CECBF6; }
    .theme-purple::before     { background: #7F77DD; }

    /* Theme: Blue – Warehouses */
    .theme-blue .card-icon  { background: #E6F1FB; }
    .theme-blue .card-label { color: #185FA5; }
    .theme-blue .card-pill  { background: #E6F1FB; color: #0C447C; border-color: #B5D4F4; }
    .theme-blue::before     { background: #378ADD; }

    /* Theme: Green – Branches */
    .theme-green .card-icon  { background: #EAF3DE; }
    .theme-green .card-label { color: #3B6D11; }
    .theme-green .card-pill  { background: #EAF3DE; color: #27500A; border-color: #C0DD97; }
    .theme-green::before     { background: #639922; }

    svg.card-ico { width: 18px; height: 18px; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Home @endslot
        @slot('title') Location @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body orders-root">

                    <div class="orders-header">
                        <div>
                            <p class="orders-title">Location Management</p>
                            <p class="orders-subtitle">Browse countries, warehouses and branches</p>
                        </div>
                        <span class="orders-date">{{ now()->format('D, d M Y') }}</span>
                    </div>

                   
                    @php
                        $arrowSvg = '<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                    @endphp

                   
                    <div class="loc-section">
                        <div class="loc-section-header">
                            <span class="loc-section-label">Countries</span>
                            <div class="loc-section-line"></div>
                            @can('Countries.index')
                            <a href="{{ route('countries.index') }}" class="btn btn-sm btn-success" style="font-size:11px; padding:3px 12px; border-radius:999px;">
                                <i class="mdi mdi-plus"></i> Add
                            </a>
                            @endcan
                            <span class="loc-section-count">{{ $countries->count() }} total</span>
                        </div>

                        <div class="orders-grid">
                            @can('Countries.index')
                            @forelse($countries as $country)
                                <a href="{{ route('countries.index') }}" class="order-card theme-purple">
                                    <div class="card-top">
                                        <div class="card-icon">
                                            <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                                <circle cx="10" cy="10" r="7" stroke="#534AB7" stroke-width="1.5"/>
                                                <path d="M3 10h14M10 3c-2 2-3 4.5-3 7s1 5 3 7M10 3c2 2 3 4.5 3 7s-1 5-3 7" stroke="#534AB7" stroke-width="1.2" stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                        <div class="card-arrow">{!! $arrowSvg !!}</div>
                                    </div>
                                    <p class="card-label">Country</p>
                                    <p class="card-name">{{ $country->name }}</p>
                                    <div class="card-divider"></div>
                                    <div class="card-meta">
                                        <span class="card-pill">{{ $country->currency ?? 'N/A' }}</span>
                                        <span class="card-count-badge">Country</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted">No countries found.</p>
                            @endforelse
                            @endcan
                        </div>
                    </div>

                   
                    <div class="loc-section">
                        <div class="loc-section-header">
                            <span class="loc-section-label">Warehouses</span>
                            <div class="loc-section-line"></div>
                            @can('Warehouse.index')
                            <a href="{{ route('warehouse.index') }}" class="btn btn-sm btn-success" style="font-size:11px; padding:3px 12px; border-radius:999px;">
                                <i class="mdi mdi-plus"></i> Add
                            </a>
                            @endcan
                            <span class="loc-section-count">{{ $warehouses->count() }} total</span>
                        </div>

                        <div class="orders-grid">
                            @can('Warehouse.index')
                            @forelse($warehouses as $warehouse)
                                <a href="{{ route('warehouse.index') }}" class="order-card theme-blue">
                                    <div class="card-top">
                                        <div class="card-icon">
                                            <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                                <path d="M2 17V8l8-5 8 5v9H2z" stroke="#185FA5" stroke-width="1.5" stroke-linejoin="round"/>
                                                <rect x="7" y="11" width="6" height="6" rx="0.75" stroke="#185FA5" stroke-width="1.2"/>
                                            </svg>
                                        </div>
                                        <div class="card-arrow">{!! $arrowSvg !!}</div>
                                    </div>
                                    <p class="card-label">Warehouse</p>
                                    <p class="card-name">{{ $warehouse->warehouse_name }}</p>
                                    <div class="card-divider"></div>
                                    <div class="card-meta">
                                        <span class="card-pill">{{ optional($warehouse->country)->name ?? 'N/A' }}</span>
                                        <span class="card-count-badge">Warehouse</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted">No warehouses found.</p>
                            @endforelse
                            @endcan
                        </div>
                    </div>

                    
                    <div class="loc-section">
                        <div class="loc-section-header">
                            <span class="loc-section-label">Stores</span>
                            <div class="loc-section-line"></div>
                            @can('Warehouse Branch.index')
                            <a href="{{ route('warehouse-branch.index') }}" class="btn btn-sm btn-success" style="font-size:11px; padding:3px 12px; border-radius:999px;">
                                <i class="mdi mdi-plus"></i> Add
                            </a>
                            @endcan
                            <span class="loc-section-count">{{ $branches->count() }} total</span>
                        </div>

                        <div class="orders-grid">
                            @can('Warehouse Branch.index')
                            @forelse($branches as $branch)
                                <a href="{{ route('warehouse-branch.index') }}" class="order-card theme-green">
                                    <div class="card-top">
                                        <div class="card-icon">
                                            <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                                <rect x="3" y="12" width="5" height="5" rx="1" stroke="#3B6D11" stroke-width="1.5"/>
                                                <rect x="12" y="12" width="5" height="5" rx="1" stroke="#3B6D11" stroke-width="1.5"/>
                                                <rect x="7.5" y="3" width="5" height="5" rx="1" stroke="#3B6D11" stroke-width="1.5"/>
                                                <path d="M10 8v3M5.5 12v-2h9v2" stroke="#3B6D11" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="card-arrow">{!! $arrowSvg !!}</div>
                                    </div>
                                    <p class="card-label">Branch</p>
                                    <p class="card-name">{{ $branch->branch_name }}</p>
                                    <div class="card-divider"></div>
                                    <div class="card-meta">
                                        <span class="card-pill">{{ optional($branch->warehouse)->warehouse_name ?? 'N/A' }}</span>
                                        <span class="card-count-badge">Branch</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted">No branches found.</p>
                            @endforelse
                            @endcan
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
@endsection