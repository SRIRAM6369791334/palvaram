@extends('layouts.master')

@section('title')
    Orders
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

    .orders-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

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

    /* Blue – All Orders */
    .theme-blue .card-icon  { background: #E6F1FB; }
    .theme-blue .card-label { color: #185FA5; }
    .theme-blue .card-pill  { background: #E6F1FB; color: #0C447C; border-color: #B5D4F4; }
    .theme-blue::before     { background: #378ADD; }

    /* Green – New Orders */
    .theme-green .card-icon  { background: #EAF3DE; }
    .theme-green .card-label { color: #3B6D11; }
    .theme-green .card-pill  { background: #EAF3DE; color: #27500A; border-color: #C0DD97; }
    .theme-green::before     { background: #639922; }

    /* Amber – Package Orders */
    .theme-amber .card-icon  { background: #FAEEDA; }
    .theme-amber .card-label { color: #854F0B; }
    .theme-amber .card-pill  { background: #FAEEDA; color: #633806; border-color: #FAC775; }
    .theme-amber::before     { background: #EF9F27; }

    /* Teal – Dispatched Orders */
    .theme-teal .card-icon  { background: #E1F5EE; }
    .theme-teal .card-label { color: #0F6E56; }
    .theme-teal .card-pill  { background: #E1F5EE; color: #085041; border-color: #9FE1CB; }
    .theme-teal::before     { background: #1D9E75; }

    /* Purple – Out for Delivery */
    .theme-purple .card-icon  { background: #EEEDFE; }
    .theme-purple .card-label { color: #534AB7; }
    .theme-purple .card-pill  { background: #EEEDFE; color: #3C3489; border-color: #CECBF6; }
    .theme-purple::before     { background: #7F77DD; }

    /* Coral – Delivered Orders */
    .theme-coral .card-icon  { background: #FAECE7; }
    .theme-coral .card-label { color: #993C1D; }
    .theme-coral .card-pill  { background: #FAECE7; color: #712B13; border-color: #F5C4B3; }
    .theme-coral::before     { background: #D85A30; }

    svg.card-ico { width: 18px; height: 18px; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Home @endslot
        @slot('title') Orders @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body orders-root">

                    <div class="orders-header">
                        <div>
                            <p class="orders-title">Order Management</p>
                            <p class="orders-subtitle">Track and manage orders across all stages</p>
                        </div>
                        <span class="orders-date">{{ now()->format('D, d M Y') }}</span>
                    </div>

                    <div class="orders-grid">

                      
                        @can('All Orders.index')
                        <a href="{{ route('all-orders.index') }}" class="order-card theme-blue">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="4" width="14" height="2" rx="1" fill="#185FA5"/>
                                        <rect x="3" y="9" width="14" height="2" rx="1" fill="#185FA5"/>
                                        <rect x="3" y="14" width="9" height="2" rx="1" fill="#185FA5"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">All</p>
                            <p class="card-name">All Orders</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Orders</span>
                            </div>
                        </a>
                        @endcan

                      
                        @can('New Orders.index')
                        <a href="{{ route('productOrders.index') }}" class="order-card theme-green">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="10" r="7" stroke="#3B6D11" stroke-width="1.5"/>
                                        <path d="M10 7v6M7 10h6" stroke="#3B6D11" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">New</p>
                            <p class="card-name">New Orders</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Review now</span>
                                <span class="card-count-badge">Orders</span>
                            </div>
                        </a>
                        @endcan

                        
                        @can('Package Orders.index')
                        <a href="{{ route('productpacking.index') }}" class="order-card theme-amber">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="7" width="14" height="10" rx="1.5" stroke="#854F0B" stroke-width="1.5"/>
                                        <path d="M7 7V5.5A3 3 0 0 1 13 5.5V7" stroke="#854F0B" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M3 10h14" stroke="#854F0B" stroke-width="1.2"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Packing</p>
                            <p class="card-name">Package Orders</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">In progress</span>
                                <span class="card-count-badge">Orders</span>
                            </div>
                        </a>
                        @endcan

                       
                        @can('Dispatched Orders.index')
                        <a href="{{ route('productdispatch.index') }}" class="order-card theme-teal">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M3 11V9a1 1 0 0 1 1-1h8l3 3v3H3v-3z" stroke="#0F6E56" stroke-width="1.5" stroke-linejoin="round"/>
                                        <circle cx="6.5" cy="15" r="1.5" stroke="#0F6E56" stroke-width="1.2"/>
                                        <circle cx="13.5" cy="15" r="1.5" stroke="#0F6E56" stroke-width="1.2"/>
                                        <path d="M11 8V5H4v3" stroke="#0F6E56" stroke-width="1.2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Dispatched</p>
                            <p class="card-name">Dispatched Orders</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">En route</span>
                                <span class="card-count-badge">Orders</span>
                            </div>
                        </a>
                        @endcan

                       
                        @can('Out For Delivery Orders.index')
                        <a href="{{ route('productdelivery.index') }}" class="order-card theme-purple">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 3C6.686 3 4 5.686 4 9c0 4.5 6 9 6 9s6-4.5 6-9c0-3.314-2.686-6-6-6z" stroke="#534AB7" stroke-width="1.5"/>
                                        <circle cx="10" cy="9" r="2" stroke="#534AB7" stroke-width="1.2"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Delivery</p>
                            <p class="card-name">Out for Delivery</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Last mile</span>
                                <span class="card-count-badge">Orders</span>
                            </div>
                        </a>
                        @endcan

                     
                        @can('Delivered Orders.index')
                        <a href="{{ route('productcomplete.index') }}" class="order-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="10" r="7" stroke="#993C1D" stroke-width="1.5"/>
                                        <path d="M6.5 10.5l2.5 2.5 4-4.5" stroke="#993C1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Completed</p>
                            <p class="card-name">Delivered Orders</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Done</span>
                                <span class="card-count-badge">Orders</span>
                            </div>
                        </a>
                        @endcan

                        @can('Purchase Orders.index')
                        <a href="/purchase-orders" class="order-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="4" y="3" width="12" height="14" rx="1.5" stroke="#993C1D" stroke-width="1.5"/>
                                        <path d="M7 7h6M7 10h6M7 13h4" stroke="#993C1D" stroke-width="1.2" stroke-linecap="round"/>
                                        <circle cx="15" cy="15" r="3.5" fill="#FAECE7" stroke="#993C1D" stroke-width="1"/>
                                        <path d="M14 15l.8.8 1.3-1.3" stroke="#993C1D" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Procurement</p>
                            <p class="card-name">Products Purchase Orders</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Products Purchase Orders</span>
                            </div>
                        </a>
                        @endcan

                        <a href="/gift-purchase-orders" class="order-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="4" y="3" width="12" height="14" rx="1.5" stroke="#993C1D" stroke-width="1.5"/>
                                        <path d="M7 7h6M7 10h6M7 13h4" stroke="#993C1D" stroke-width="1.2" stroke-linecap="round"/>
                                        <circle cx="15" cy="15" r="3.5" fill="#FAECE7" stroke="#993C1D" stroke-width="1"/>
                                        <path d="M14 15l.8.8 1.3-1.3" stroke="#993C1D" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Procurement</p>
                            <p class="card-name">Gift Product Purchase Orders</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Gift Product Purchase Orders</span>
                            </div>
                        </a>
                                                
                        @can('Other Orders.index')
                        <a href="{{ route('other-orders.index') }}" class="order-card theme-red">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="10" r="7" stroke="#991B1B" stroke-width="1.5"/>
                                        <path d="M10 7v4" stroke="#991B1B" stroke-width="1.5" stroke-linecap="round"/>
                                        <circle cx="10" cy="14" r="0.75" fill="#991B1B"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Issues</p>
                            <p class="card-name">Other Orders</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">Cancelled · Return · Unavailable</span>
                                <span class="card-count-badge">Orders</span>
                            </div>
                        </a>
                        @endcan
                        
                        @can('Supplier Details.index')
                        <a href="/supplier" class="order-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="4" y="3" width="12" height="14" rx="1.5" stroke="#993C1D" stroke-width="1.5"/>
                                        <path d="M7 7h6M7 10h6M7 13h4" stroke="#993C1D" stroke-width="1.2" stroke-linecap="round"/>
                                        <circle cx="15" cy="15" r="3.5" fill="#FAECE7" stroke="#993C1D" stroke-width="1"/>
                                        <path d="M14 15l.8.8 1.3-1.3" stroke="#993C1D" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Procurement</p>
                            <p class="card-name">Supplier Details</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Supplier Details</span>
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