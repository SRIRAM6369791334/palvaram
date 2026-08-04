@extends('layouts.master')
@section('title') Users & Access @endsection

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600&family=DM+Sans:wght@400;500&display=swap');

    .orders-root { font-family: 'DM Sans', sans-serif; padding: 2rem 0; }
    .orders-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 2rem; }
    .orders-title { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 600; color: #1a1a2e; margin: 0 0 2px; }
    .orders-subtitle { font-size: 13px; color: #6c757d; margin: 0; }
    .orders-date { font-size: 12px; color: #adb5bd; background: #f8f9fa; border: 0.5px solid #e9ecef; padding: 6px 14px; border-radius: 999px; }

    .orders-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }

    .order-card {
        background: #ffffff; border: 0.5px solid #e9ecef; border-radius: 12px;
        padding: 1.25rem; cursor: pointer; text-decoration: none;
        display: flex; flex-direction: column; gap: 0;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
        position: relative; overflow: hidden;
    }
    .order-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0;
        height: 3px; border-radius: 12px 12px 0 0; opacity: 0; transition: opacity 0.2s;
    }
    .order-card:hover { border-color: #ced4da; transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.07); text-decoration: none; }
    .order-card:hover::before { opacity: 1; }

    .card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .card-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .card-arrow { width: 24px; height: 24px; border-radius: 50%; background: #f8f9fa; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; }
    .order-card:hover .card-arrow { opacity: 1; }

    .card-label { font-size: 11px; font-weight: 500; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 4px; }
    .card-name { font-family: 'Sora', sans-serif; font-size: 15px; font-weight: 600; color: #1a1a2e; margin-bottom: 1rem; }
    .card-divider { height: 0.5px; background: #e9ecef; margin-bottom: 0.85rem; }
    .card-meta { display: flex; align-items: center; justify-content: space-between; }
    .card-pill { font-size: 11px; font-weight: 500; padding: 3px 10px; border-radius: 999px; border: 0.5px solid; }
    .card-count-badge { font-size: 11px; font-weight: 500; color: #6c757d; }

    /* Blue – Customers */
    .theme-blue .card-icon  { background: #E6F1FB; }
    .theme-blue .card-label { color: #185FA5; }
    .theme-blue .card-pill  { background: #E6F1FB; color: #0C447C; border-color: #B5D4F4; }
    .theme-blue::before     { background: #378ADD; }

    /* Purple – Dashboard Users */
    .theme-purple .card-icon  { background: #EEEDFE; }
    .theme-purple .card-label { color: #534AB7; }
    .theme-purple .card-pill  { background: #EEEDFE; color: #3C3489; border-color: #CECBF6; }
    .theme-purple::before     { background: #7F77DD; }

    /* Amber – Roles & Permissions */
    .theme-amber .card-icon  { background: #FAEEDA; }
    .theme-amber .card-label { color: #854F0B; }
    .theme-amber .card-pill  { background: #FAEEDA; color: #633806; border-color: #FAC775; }
    .theme-amber::before     { background: #EF9F27; }

    svg.card-ico { width: 18px; height: 18px; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Home @endslot
        @slot('title') Users & Access @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body orders-root">

                    <div class="orders-header">
                        <div>
                            <p class="orders-title">Users & Access</p>
                            <p class="orders-subtitle">Manage customers, dashboard users and roles</p>
                        </div>
                        <span class="orders-date">{{ now()->format('D, d M Y') }}</span>
                    </div>

                    <div class="orders-grid">

                        @can('Customers.index')
                        <a href="{{ route('customers.index') }}" class="order-card theme-blue">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="7" r="3" stroke="#185FA5" stroke-width="1.5"/>
                                        <path d="M3 17c0-3.314 3.134-6 7-6s7 2.686 7 6" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Customers</p>
                            <p class="card-name">Customers</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Manage</span>
                                <span class="card-count-badge">{{ \App\Models\User::count() }} total</span>
                            </div>
                        </a>
                        @endcan


                        @can('Users.index')
                        <a href="{{ route('users.index') }}" class="order-card theme-purple">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="8" cy="7" r="2.5" stroke="#534AB7" stroke-width="1.5"/>
                                        <path d="M2 17c0-2.761 2.686-5 6-5" stroke="#534AB7" stroke-width="1.5" stroke-linecap="round"/>
                                        <circle cx="14" cy="13" r="3" stroke="#534AB7" stroke-width="1.5"/>
                                        <path d="M14 11.5v1.5l1 1" stroke="#534AB7" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Admin</p>
                            <p class="card-name">Dashboard Users</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Manage</span>
                                <span class="card-count-badge">{{ \App\Models\User::count() }} total</span>
                            </div>
                        </a>
                        @endcan

                        @can('Roles.index')
                        <a href="{{ route('roles.index') }}" class="order-card theme-amber">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="10" r="2" stroke="#854F0B" stroke-width="1.5"/>
                                        <path d="M10 3v2M10 15v2M3 10h2M15 10h2" stroke="#854F0B" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M5.05 5.05l1.41 1.41M13.54 13.54l1.41 1.41M5.05 14.95l1.41-1.41M13.54 6.46l1.41-1.41" stroke="#854F0B" stroke-width="1.2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Access</p>
                            <p class="card-name">Roles & Permissions</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Manage</span>
                                <span class="card-count-badge">{{ \Spatie\Permission\Models\Role::count() }} roles</span>
                            </div>
                        </a>
                        @endcan

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
@endsection