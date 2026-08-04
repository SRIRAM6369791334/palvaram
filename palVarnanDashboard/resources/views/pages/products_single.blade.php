@extends('layouts.master')

@section('title')
    Products
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

    /* Blue – Categories */
    .theme-blue .card-icon  { background: #E6F1FB; }
    .theme-blue .card-label { color: #185FA5; }
    .theme-blue .card-pill  { background: #E6F1FB; color: #0C447C; border-color: #B5D4F4; }
    .theme-blue::before     { background: #378ADD; }

    /* Green – Subcategories */
    .theme-green .card-icon  { background: #EAF3DE; }
    .theme-green .card-label { color: #3B6D11; }
    .theme-green .card-pill  { background: #EAF3DE; color: #27500A; border-color: #C0DD97; }
    .theme-green::before     { background: #639922; }

    /* Amber – Brands */
    .theme-amber .card-icon  { background: #FAEEDA; }
    .theme-amber .card-label { color: #854F0B; }
    .theme-amber .card-pill  { background: #FAEEDA; color: #633806; border-color: #FAC775; }
    .theme-amber::before     { background: #EF9F27; }

    /* Teal – Products */
    .theme-teal .card-icon  { background: #E1F5EE; }
    .theme-teal .card-label { color: #0F6E56; }
    .theme-teal .card-pill  { background: #E1F5EE; color: #085041; border-color: #9FE1CB; }
    .theme-teal::before     { background: #1D9E75; }

    /* Purple – Product Variant */
    .theme-purple .card-icon  { background: #EEEDFE; }
    .theme-purple .card-label { color: #534AB7; }
    .theme-purple .card-pill  { background: #EEEDFE; color: #3C3489; border-color: #CECBF6; }
    .theme-purple::before     { background: #7F77DD; }

    svg.card-ico { width: 18px; height: 18px; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Home @endslot
        @slot('title') Products @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body orders-root">

                    <div class="orders-header">
                        <div>
                            <p class="orders-title">Product Management</p>
                            <p class="orders-subtitle">Manage your categories, brands and product catalogue</p>
                        </div>
                        <span class="orders-date">{{ now()->format('D, d M Y') }}</span>
                    </div>

                    <div class="orders-grid">

                       
                        @can('Categories.index')
                        <a href="{{ route('categories.index') }}" class="order-card theme-blue">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="3" width="6" height="6" rx="1.5" stroke="#185FA5" stroke-width="1.5"/>
                                        <rect x="11" y="3" width="6" height="6" rx="1.5" stroke="#185FA5" stroke-width="1.5"/>
                                        <rect x="3" y="11" width="6" height="6" rx="1.5" stroke="#185FA5" stroke-width="1.5"/>
                                        <rect x="11" y="11" width="6" height="6" rx="1.5" stroke="#185FA5" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Catalogue</p>
                            <p class="card-name">Categories</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Categories</span>
                            </div>
                        </a>
                        @endcan

                       
                        @can('Subcategories.index')
                        <a href="{{ route('subcategories.index') }}" class="order-card theme-green">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M4 5h12M4 10h8M4 15h5" stroke="#3B6D11" stroke-width="1.5" stroke-linecap="round"/>
                                        <circle cx="15" cy="14" r="3" stroke="#3B6D11" stroke-width="1.2"/>
                                        <path d="M15 12.5v1.5l1 1" stroke="#3B6D11" stroke-width="1" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Catalogue</p>
                            <p class="card-name">Subcategories</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Subcategories</span>
                            </div>
                        </a>
                        @endcan

                        
                        @can('Brands.index')
                        <a href="{{ route('brands.index') }}" class="order-card theme-amber">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 3l1.8 3.6L16 7.6l-3 2.9.7 4.1L10 12.5l-3.7 2.1.7-4.1L4 7.6l4.2-.6L10 3z" stroke="#854F0B" stroke-width="1.5" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Identity</p>
                            <p class="card-name">Brands</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Brands</span>
                            </div>
                        </a>
                        @endcan

                      
                        @can('Products.index')
                        <a href="{{ route('products.index') }}" class="order-card theme-teal">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <path d="M4 4h5v5H4zM11 4h5v5h-5zM4 11h5v5H4z" stroke="#0F6E56" stroke-width="1.5" stroke-linejoin="round"/>
                                        <path d="M13.5 11v6M11 13.5h5" stroke="#0F6E56" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Inventory</p>
                            <p class="card-name">Products</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Products</span>
                            </div>
                        </a>
                        @endcan

                       
                        @can('Product Varient.index')
                        <a href="{{ route('productvarient.index') }}" class="order-card theme-purple">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <circle cx="6" cy="10" r="2.5" stroke="#534AB7" stroke-width="1.5"/>
                                        <circle cx="14" cy="6" r="2.5" stroke="#534AB7" stroke-width="1.5"/>
                                        <circle cx="14" cy="14" r="2.5" stroke="#534AB7" stroke-width="1.5"/>
                                        <path d="M8.5 9l3-2M8.5 11l3 2" stroke="#534AB7" stroke-width="1.2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Variants</p>
                            <p class="card-name">Product Variant</p>
                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View all</span>
                                <span class="card-count-badge">Variants</span>
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