@extends('layouts.master')

@section('title')
    {{ $order->po_code }} — Purchase Order
@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection


@section('content')

@component('components.breadcrumb')
    @slot('li_1') <a href="/purchase-orders">Purchase Orders</a> @endslot
    @slot('title') {{ $order->po_code }} @endslot
@endcomponent

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

.po-wrap { font-family: 'DM Sans', sans-serif; }

/* ── HERO ── */
.po-hero {
    background: #0a0a0f;
    border-radius: 20px;
    padding: 40px 44px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.po-hero-grid {
    position: absolute; inset: 0;
    
    background-size: 40px 40px;
}
.po-hero-glow {
    position: absolute;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%);
    top: -200px; right: -100px;
    pointer-events: none;
}
.po-hero-glow2 {
    position: absolute;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(20,184,166,0.1) 0%, transparent 70%);
    bottom: -100px; left: 200px;
    pointer-events: none;
}
.po-eyebrow {
    font-size: 11px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #fff;
    margin-bottom: 8px;
    font-weight: 500;
}
.po-title {
    font-family: 'DM Mono', monospace;
    font-size: 38px;
    font-weight: 500;
    color: #fff;
    letter-spacing: -1px;
    line-height: 1;
    margin-bottom: 20px;
}
.po-status-pill {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 5px 14px 5px 10px;
    border-radius: 100px;
    font-size: 12px; font-weight: 600;
    letter-spacing: 0.3px;
    backdrop-filter: blur(10px);
}
.po-status-pill.pending  { background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.25); color: #fbbf24; }
.po-status-pill.ordered  { background: rgba(56,189,248,0.12); border: 1px solid rgba(56,189,248,0.25); color: #38bdf8; }
.po-status-pill.received { background: rgba(52,211,153,0.12); border: 1px solid rgba(52,211,153,0.25); color: #34d399; }
.po-status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; animation: blink 2s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

.po-hero-divider {
    width: 1px; background: rgba(255,255,255,0.08);
    margin: 0 40px; align-self: stretch;
}
.po-meta-label { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #fff; margin-bottom: 6px; }
.po-meta-value { font-size: 15px; font-weight: 500; color: rgba(255,255,255,0.9); }
.po-meta-sub   { font-size: 12px; color: #fff; margin-top: 2px; }
.po-total-label { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: #fff; margin-bottom: 4px; }
.po-total-value {
    font-family: 'DM Mono', monospace;
    font-size: 36px; font-weight: 500;
    color: #fff; line-height: 1;
}
.po-total-currency { font-size: 20px; opacity: 0.5; margin-right: 2px; }

/* ── STAT CARDS ── */
.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.stat-card {
    background: #fff;
    border: 1px solid #ebebeb;
    border-radius: 14px;
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
}
.stat-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); transform: translateY(-1px); }
.stat-card-accent {
    position: absolute; top: 0; left: 0; right: 0;
    height: 2px; border-radius: 14px 14px 0 0;
}
.stat-card-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; margin-bottom: 12px;
}
.stat-card-num { font-size: 26px; font-weight: 600; color: #0a0a0f; line-height: 1; margin-bottom: 4px; font-family: 'DM Mono', monospace; }
.stat-card-lbl { font-size: 16px; letter-spacing: 0.3px; }

/* ── TABLE SECTION ── */
.table-section {
    background: #fff;
    border: 1px solid #ebebeb;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 20px;
}
.table-section-head {
    padding: 20px 24px 16px;
    border-bottom: 1px solid #f5f5f5;
    display: flex; align-items: center; justify-content: space-between;
}
.table-section-title {
    font-size: 14px; font-weight: 600; color: #0a0a0f;
    display: flex; align-items: center; gap: 10px;
}
.table-section-title i { color: #6366f1; font-size: 18px; }
.item-count-badge {
    background: #f3f4f6; color: #6b7280;
    border-radius: 100px; padding: 2px 10px;
    font-size: 12px; font-weight: 500;
}
.po-table { width: 100%; border-collapse: collapse; }
.po-table thead th {
    padding: 11px 20px;
    font-size: 14px; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase;
    
    background: #fafafa;
    border-bottom: 1px solid #f0f0f0;
    white-space: nowrap;
}
.po-table tbody tr { transition: background .15s; }
.po-table tbody tr:hover { background: #fafbff; }
.po-table tbody td {
    padding: 15px 20px;
    font-size: 13.5px; color: #374151;
    border-bottom: 1px solid #f5f5f5;
    vertical-align: middle;
}
.po-table tbody tr:last-child td { border-bottom: none; }
.po-table tfoot td {
    padding: 16px 20px;
    background: #fafafa;
    border-top: 1px solid #ebebeb;
}
.sno { font-family: 'DM Mono', monospace; font-size: 13px; }
.product-cell { font-weight: 600; color: #111827; font-size: 14px; }
.chip {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 100px;
    font-size: 11.5px; font-weight: 500;
    white-space: nowrap;
}
.chip-variant  { background: #eef2ff; color: #4f46e5; font-size: 13px; }
.chip-cat      { background: #f0fdf4; color: #16a34a; font-size: 13px; }
.chip-subcat   { background: #fff7ed; color: #c2410c; font-size: 13px; }
.chip-none     { color: #d1d5db; font-size: 12px; }
.unit-box {
    display: inline-block;
    background: #f9fafb; border: 1px solid #e5e7eb;
    border-radius: 6px; padding: 2px 10px;
    font-size: 13px; font-weight: 600; color: #374151;
    font-family: 'DM Mono', monospace;
}
.qty-val { font-family: 'DM Mono', monospace; font-size: 15px; font-weight: 600; color: #111827; }
.price-val { font-family: 'DM Mono', monospace; color: #6b7280; font-size: 13px; }
.total-val { font-family: 'DM Mono', monospace; font-size: 14px; font-weight: 600; color: #111827; }
.grand-total-val {
    font-family: 'DM Mono', monospace;
    font-size: 18px; font-weight: 600; color: #6366f1;
}

/* ── BACK BTN ── */
.back-link {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 13px; font-weight: 500; color: #374151;
    text-decoration: none;
    transition: all .2s;
    font-family: 'DM Sans', sans-serif;
}
.back-link:hover { background: #f9fafb; border-color: #d1d5db; color: #111827; text-decoration: none; }

@media(max-width:768px) {
    .po-title { font-size: 26px; }
    .stat-grid { grid-template-columns: repeat(2,1fr); }
    .po-hero { padding: 24px 20px; }
    .po-total-value { font-size: 26px; }
    .po-hero-divider { display: none; }
}
</style>

<div class="po-wrap">

{{-- ══ HERO ══ --}}
<div class="po-hero">
    <div class="po-hero-grid"></div>
    <div class="po-hero-glow"></div>
    <div class="po-hero-glow2"></div>

    <div style="position:relative;z-index:1;display:flex;align-items:flex-start;gap:0;flex-wrap:wrap;">

        {{-- Left: code + status --}}
        <div style="flex:1;min-width:220px;">
            <div class="po-eyebrow">Purchase Order</div>
            <div class="po-title">{{ $order->po_code }}</div>
            <span class="po-status-pill {{ $order->status }}">
                <span class="po-status-dot"></span>
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <div class="po-hero-divider d-none d-md-block"></div>

        {{-- Mid: supplier + location --}}
        <div style="flex:1;min-width:200px;padding:4px 0;">
            <div class="po-meta-label">Supplier</div>
            <div class="po-meta-value">{{ $order->supplier_name }}</div>
            <div class="po-meta-label">Country</div>
            <div class="po-meta-sub">
                <i class="mdi mdi-map-marker-outline"></i>
                {{ $order->country->name ?? '—' }}
            </div>

            <div style="margin-top:20px;">
                <div class="po-meta-label">Warehouse</div>
                <div class="po-meta-value">{{ $order->warehouse->warehouse_name ?? '—' }}</div>
                <div class="po-meta-sub">
                    <i class="mdi mdi-calendar-outline"></i>
                    {{ $order->created_at->format('d M Y') }}
                </div>
            </div>
        </div>

        <div class="po-hero-divider d-none d-md-block"></div>

        {{-- Right: total --}}
        <div style="flex:0 0 auto;text-align:right;padding:4px 0;">
            <div class="po-total-label">Grand Total</div>
            <div class="po-total-value">
                <span class="po-total-currency">₹</span>{{ number_format($order->total_amount, 2) }}
            </div>
            <div style="margin-top:8px;color:#fff;font-size:12px;">
                {{ $order->items->count() }} line item{{ $order->items->count() != 1 ? 's' : '' }}
            </div>
        </div>

    </div>
</div>

{{-- ══ STATS ══ --}}
<div class="stat-grid">

    <div class="stat-card">
        <div class="stat-card-accent" style="background:linear-gradient(90deg,#6366f1,#8b5cf6);"></div>
        <div class="stat-card-icon" style="background:#eef2ff;">
            <i class="mdi mdi-package-variant" style="color:#6366f1;"></i>
        </div>
        <div class="stat-card-num">{{ $order->items->count() }}</div>
        <div class="stat-card-lbl">Total Items</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-accent" style="background:linear-gradient(90deg,#14b8a6,#06b6d4);"></div>
        <div class="stat-card-icon" style="background:#f0fdfa;">
            <i class="mdi mdi-counter" style="color:#14b8a6;"></i>
        </div>
        <div class="stat-card-num">{{ $order->items->sum('quantity') }}</div>
        <div class="stat-card-lbl">Total Quantity</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-accent" style="background:linear-gradient(90deg,#f59e0b,#f97316);"></div>
        <div class="stat-card-icon" style="background:#fffbeb;">
            <i class="mdi mdi-currency-inr" style="color:#f59e0b;"></i>
        </div>
        <div class="stat-card-num">{{ number_format($order->items->avg('price'), 0) }}</div>
        <div class="stat-card-lbl">Avg Unit Price</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-accent" style="background:linear-gradient(90deg,#10b981,#34d399);"></div>
        <div class="stat-card-icon" style="background:#f0fdf4;">
            <i class="mdi mdi-calendar-check-outline" style="color:#10b981;"></i>
        </div>
        <div class="stat-card-num">{{ $order->created_at->format('d M') }}</div>
        <div class="stat-card-lbl">Ordered On</div>
    </div>

</div>

{{-- ══ ITEMS TABLE ══ --}}
<div class="table-section">

    <div class="table-section-head">
        <div class="table-section-title">
            <i class="mdi mdi-format-list-bulleted-square"></i>
            Order Items
        </div>
        <span class="item-count-badge">{{ $order->items->count() }} items</span>
    </div>

    <div style="overflow-x:auto;">
        <table class="po-table">
            <thead>
                <tr>
                    <th style="width:50px;text-align:center;">S.No.</th>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th style="text-align:center;">Unit</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->items as $i => $item)
                <tr>
                    <td style="text-align:center;"><span class="sno">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span></td>

                    <td><span class="product-cell">{{ $item->product->product_name ?? '—' }}</span></td>

                    <td>
                        @if($item->variant)
                            <span class="chip chip-variant">
                                <i class="mdi mdi-tag-outline" style="font-size:11px;"></i>
                                {{ $item->variant->varient_name ?? $item->variant->varient }}
                            </span>
                        @else
                            <span class="chip-none">—</span>
                        @endif
                    </td>

                    <td>
                        @if($item->category)
                            <span class="chip chip-cat">{{ $item->category->category_name }}</span>
                        @else
                            <span class="chip-none">—</span>
                        @endif
                    </td>

                    <td>
                        @if($item->subCategory)
                            <span class="chip chip-subcat">{{ $item->subCategory->subcategory_name }}</span>
                        @else
                            <span class="chip-none">—</span>
                        @endif
                    </td>

                    <td style="text-align:center;"><span class="unit-box">{{ $item->unit->short_name ?? '—' }}</span></td>
                    <td style="text-align:center;"><span class="qty-val">{{ $item->quantity }}</span></td>
                    <td style="text-align:right;"><span class="price-val">₹{{ number_format($item->price, 2) }}</span></td>
                    <td style="text-align:right;"><span class="total-val">₹{{ number_format($item->total, 2) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:60px 20px;color:#d1d5db;">
                        <i class="mdi mdi-package-variant-closed" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                        No items found.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" style="text-align:right;font-size:12px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">
                        Grand Total
                    </td>
                    <td style="text-align:right;">
                        <span class="grand-total-val">₹{{ number_format($order->total_amount, 2) }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

{{-- ══ BACK ══ --}}
<div style="margin-bottom:8px;">
    <a href="/purchase-orders" class="back-link">
        <i class="mdi mdi-arrow-left"></i> Back to Purchase Orders
    </a>
</div>

</div>

@endsection

@section('script')

<script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/app.js') }}"></script>

@endsection