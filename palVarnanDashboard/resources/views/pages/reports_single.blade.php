@extends('layouts.master')
@section('title') Reports @endsection

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600&family=DM+Sans:wght@400;500&display=swap');

    .orders-root { font-family: 'DM Sans', sans-serif; padding: 2rem 0; }

    .orders-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 2rem; }
    .orders-title { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 600; color: #1a1a2e; margin: 0 0 2px; }
    .orders-subtitle { font-size: 13px; color: #6c757d; margin: 0; }
    .orders-date { font-size: 12px; color: #adb5bd; background: #f8f9fa; border: 0.5px solid #e9ecef; padding: 6px 14px; border-radius: 999px; }

    .reports-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }

    .report-card {
        background: #ffffff; border: 0.5px solid #e9ecef; border-radius: 14px;
        padding: 1.4rem; cursor: pointer; text-decoration: none;
        display: flex; flex-direction: column; gap: 0;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
        position: relative; overflow: hidden;
    }
    .report-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0;
        height: 3px; border-radius: 14px 14px 0 0; opacity: 0; transition: opacity 0.2s;
    }
    .report-card:hover { border-color: #ced4da; transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,0.08); text-decoration: none; }
    .report-card:hover::before { opacity: 1; }

    .card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.9rem; }
    .card-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; }
    .card-arrow { width: 26px; height: 26px; border-radius: 50%; background: #f8f9fa; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; }
    .report-card:hover .card-arrow { opacity: 1; }

    .card-label { font-size: 11px; font-weight: 500; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 4px; }
    .card-name { font-family: 'Sora', sans-serif; font-size: 17px; font-weight: 600; color: #1a1a2e; margin-bottom: 0.5rem; }
    .card-desc { font-size: 12.5px; color: #6c757d; margin-bottom: 1.1rem; line-height: 1.55; }

    /* Mini stat row */
    .card-stats { display: flex; gap: 10px; margin-bottom: 1.1rem; }
    .card-stat { flex: 1; background: #f8f9fa; border-radius: 10px; padding: 9px 11px; }
    .stat-val { font-family: 'Sora', sans-serif; font-size: 17px; font-weight: 600; color: #1a1a2e; }
    .stat-lbl { font-size: 10px; color: #adb5bd; margin-top: 2px; }

    /* Chart */
    .card-chart { width: 100%; height: 90px; position: relative; margin-bottom: 1.1rem; }

    .card-divider { height: 0.5px; background: #e9ecef; margin-bottom: 0.85rem; }
    .card-meta { display: flex; align-items: center; justify-content: space-between; }
    .card-pill { font-size: 11px; font-weight: 500; padding: 3px 12px; border-radius: 999px; border: 0.5px solid; }
    .card-sub { font-size: 11px; color: #adb5bd; }

    /* Coral – Product */
    .theme-coral .card-icon  { background: #FAECE7; }
    .theme-coral .card-label { color: #993C1D; }
    .theme-coral .card-pill  { background: #FAECE7; color: #712B13; border-color: #F5C4B3; }
    .theme-coral::before     { background: #D85A30; }

    /* Teal – Orders */
    .theme-teal .card-icon  { background: #E1F5EE; }
    .theme-teal .card-label { color: #0F6E56; }
    .theme-teal .card-pill  { background: #E1F5EE; color: #085041; border-color: #9FE1CB; }
    .theme-teal::before     { background: #1D9E75; }

    svg.card-ico { width: 19px; height: 19px; }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Home @endslot
        @slot('title') Reports @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body orders-root">

                    <div class="orders-header">
                        <div>
                            <p class="orders-title">Reports</p>
                            <p class="orders-subtitle">Analyse product and order performance</p>
                        </div>
                        <span class="orders-date">{{ now()->format('D, d M Y') }}</span>
                    </div>

                    <div class="reports-grid">

                       
                        @can('Product Wise Report.index')
                        <a href="/productwisereport" class="report-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="3" width="6" height="6" rx="1.2" fill="#993C1D"/>
                                        <rect x="11" y="3" width="6" height="6" rx="1.2" fill="#993C1D" opacity=".5"/>
                                        <rect x="3" y="11" width="6" height="6" rx="1.2" fill="#993C1D" opacity=".5"/>
                                        <rect x="11" y="11" width="6" height="6" rx="1.2" fill="#993C1D"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Products</p>
                            <p class="card-name">Product Wise Report</p>
                            <p class="card-desc">Sales breakdown by product, top performers and revenue share across all locations.</p>

                            <div class="card-stats">
                                <div class="card-stat">
                                    <div class="stat-val">{{ $productCount ?? '—' }}</div>
                                    <div class="stat-lbl">Products</div>
                                </div>
                                <div class="card-stat">
                                    <div class="stat-val">{{ $productRevenue ?? '—' }}</div>
                                    <div class="stat-lbl">Revenue</div>
                                </div>
                                <div class="card-stat">
                                    <div class="stat-val">{{ $inStockPct ?? '—' }}</div>
                                    <div class="stat-lbl">In stock</div>
                                </div>
                            </div>

                            <div class="card-chart">
                                <canvas id="productChart" role="img" aria-label="Monthly product sales bar chart">Monthly product sales data.</canvas>
                            </div>

                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View report</span>
                                <span class="card-sub">Updated today</span>
                            </div>
                        </a>
                        @endcan

                        
                        @can('Order Wise Report.index')
                        <a href="/orderwisereport" class="report-card theme-teal">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="4" width="14" height="2" rx="1" fill="#0F6E56"/>
                                        <rect x="3" y="8.5" width="10" height="2" rx="1" fill="#0F6E56" opacity=".6"/>
                                        <rect x="3" y="13" width="12" height="2" rx="1" fill="#0F6E56" opacity=".4"/>
                                        <circle cx="15.5" cy="14" r="2.5" fill="#1D9E75"/>
                                        <path d="M14.5 14l.8.8 1.3-1.3" stroke="#fff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="card-label">Orders</p>
                            <p class="card-name">Order Wise Report</p>
                            <p class="card-desc">Order volume, status pipeline, delivery success rates and monthly trends.</p>

                            <div class="card-stats">
                                <div class="card-stat">
                                    <div class="stat-val">{{ $totalOrders ?? '—' }}</div>
                                    <div class="stat-lbl">Orders</div>
                                </div>
                                <div class="card-stat">
                                    <div class="stat-val">{{ $deliveredPct ?? '—' }}</div>
                                    <div class="stat-lbl">Delivered</div>
                                </div>
                                <div class="card-stat">
                                    <div class="stat-val">{{ $pendingOrders ?? '—' }}</div>
                                    <div class="stat-lbl">Pending</div>
                                </div>
                            </div>

                            <div class="card-chart">
                                <canvas id="orderChart" role="img" aria-label="Monthly order trend line chart">Monthly order trend data.</canvas>
                            </div>

                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View report</span>
                                <span class="card-sub">Updated today</span>
                            </div>
                        </a>
                        @endcan


                        @can('Inventory Report.index')
                        <a href="/reports/inventory" class="report-card theme-coral">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="3" width="14" height="14" rx="2" fill="#993C1D"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2"/>
                                    </svg>
                                </div>
                            </div>

                            <p class="card-label">Inventory</p>
                            <p class="card-name">Inventory Report</p>
                            <p class="card-desc">Stock on hand, low stock alerts and dead stock analysis.</p>

                            <div class="card-stats">
                                <div class="card-stat">
                                    <div class="stat-val">{{ $totalProducts ?? '—' }}</div>
                                    <div class="stat-lbl">Products</div>
                                </div>
                                <div class="card-stat">
                                    <div class="stat-val">{{ $lowStockCount ?? '—' }}</div>
                                    <div class="stat-lbl">Low Stock</div>
                                </div>
                                <div class="card-stat">
                                    <div class="stat-val">{{ $deadStockCount ?? '—' }}</div>
                                    <div class="stat-lbl">Dead Stock</div>
                                </div>
                            </div>

                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View report</span>
                                <span class="card-sub">Updated today</span>
                            </div>
                        </a>
                        @endcan

                        @can('Purchase Report.index')
                        <a href="/reports/purchase" class="report-card theme-teal">
                            <div class="card-top">
                                <div class="card-icon">
                                    <svg class="card-ico" viewBox="0 0 20 20" fill="none">
                                        <rect x="3" y="4" width="14" height="2" fill="#0F6E56"/>
                                        <rect x="3" y="8" width="10" height="2" fill="#0F6E56"/>
                                        <rect x="3" y="12" width="12" height="2" fill="#0F6E56"/>
                                    </svg>
                                </div>
                                <div class="card-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2.5 9.5L9.5 2.5M9.5 2.5H4.5M9.5 2.5V7.5" stroke="#6c757d" stroke-width="1.2"/>
                                    </svg>
                                </div>
                            </div>

                            <p class="card-label">Purchase</p>
                            <p class="card-name">Purchase Report</p>
                            <p class="card-desc">Supplier-wise purchase insights and cost variance tracking.</p>

                            <div class="card-stats">
                                <div class="card-stat">
                                    <div class="stat-val">{{ $supplierCount ?? '—' }}</div>
                                    <div class="stat-lbl">Suppliers</div>
                                </div>
                                <div class="card-stat">
                                    <div class="stat-val">{{ $totalPurchase ?? '—' }}</div>
                                    <div class="stat-lbl">Purchase</div>
                                </div>
                                <div class="card-stat">
                                    <div class="stat-val">{{ $avgVariance ?? '—' }}</div>
                                    <div class="stat-lbl">Variance</div>
                                </div>
                            </div>

                            <div class="card-divider"></div>
                            <div class="card-meta">
                                <span class="card-pill">View report</span>
                                <span class="card-sub">Updated today</span>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <script>

        const productData = {!! json_encode($productMonthly ?? [42, 68, 55, 80, 63, 91]) !!};
        const orderData   = {!! json_encode($orderMonthly   ?? [38, 52, 47, 70, 65, 88]) !!};
        const months      = {!! json_encode($months         ?? ['Jan','Feb','Mar','Apr','May','Jun']) !!};

        new Chart(document.getElementById('productChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    data: productData,
                    backgroundColor: '#F0997B',
                    borderColor: '#D85A30',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#adb5bd' } },
                    y: { display: false }
                }
            }
        });

        new Chart(document.getElementById('orderChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    data: orderData,
                    borderColor: '#1D9E75',
                    backgroundColor: 'rgba(29,158,117,0.09)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#1D9E75'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#adb5bd' } },
                    y: { display: false }
                }
            }
        });
    </script>
@endsection