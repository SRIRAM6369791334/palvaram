@extends('layouts.master')
@section('title')
    Pal Varnan
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/gridjs/gridjs.min.css') }}">
@endsection


@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Order Wise Reports
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Order Wise Report</h4>

                    <a href="{{ url('/reports') }}" class="btn btn-light btn-sm" style="background: #0c716a; color: #fff;">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                    
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <button id="export-excel" class="btn btn-success w-100">Export Excel</button>
                        </div>
                        <div class="col-md-2" style="display:none;">
                            <button id="export-pdf" class="btn btn-danger w-100">Export PDF</button>
                        </div>
                    </div>



                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filter">Filter by:</label>
                            <select id="filter" class="form-control">
                                <option value="this_month" {{ $filter == 'this_month' ? 'selected' : '' }}>This Month
                                </option>
                                <option value="last_month">Last Month</option>
                                <option value="this_week">This Week</option>
                                <option value="custom">Custom Date</option>
                            </select>
                        </div>
                        <div class="col-md-3 custom-date d-none">
                            <label for="from-date">From:</label>
                            <input type="date" id="from-date" class="form-control">
                        </div>
                        <div class="col-md-3 custom-date d-none">
                            <label for="to-date">To:</label>
                            <input type="date" id="to-date" class="form-control">
                        </div>
                        <div class="col-md-2 custom-date d-none">
                            <label>&nbsp;</label>
                            <button id="apply-filter" class="btn btn-primary w-100">Apply</button>
                        </div>
                    </div>
                    <div class="row mb-3" id="summary-section">
                        <div class="col-md-3">
                            <div class="alert alert-info">
                                <strong>Total Orders:</strong> <span
                                    id="total-orders">{{ $initialResults['summary']['total_orders'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-success">
                                <strong>Total Order Value:</strong> ₹<span
                                    id="total-value">{{ number_format($initialResults['summary']['total_value'] ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="order-report">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Country</th>
                                    <th>Order ID</th>
                                    <th>Order Date</th>
                                    <th>Customer Name</th>
                                    <th>Mobile</th>
                                    <th>Total Items</th>
                                    <th>Total Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Total Gst</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                    {{-- <th>Payment Method</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($initialResults['orders'] as $index => $order)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $order->country_name ?? 'N/A' }}</td> 
                                        <td>{{ $order->order_id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}</td>
                                       <td>{{ $order->firstname ?? 'N/A' }}</td>
                                        <td>{{ $order->address_phone_number ?? 'N/A' }}</td>
                                        <td>{{ $order->total_items }}</td>
                                        <td>{{ $order->total_quantity }}</td>
                                        <td>₹{{ number_format($order->subtotal, 2) }}</td>
                                        <td>₹{{ number_format($order->gst_amount, 2) }}</td>
                                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                        <td>{{ ucfirst($order->payment_status) }}</td>
                                        {{-- <td>{{ $order->payment_status ?? 'N/A' }}</td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No Data Found</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
   <script>
    $(document).ready(function () {
        function loadReport(filter, from = '', to = '') {
            const countryId = $('#country-filter').val();
            $.ajax({
                url: '{{ route('order.wise.report.filter') }}',
                data: {
                    filter: filter,
                    from: from,
                    to: to,
                    country_id: countryId 
                },
                success: function (data) {
                    let tbody = $('#order-report tbody');
                    tbody.empty();

                    // Update summary
                    $('#total-orders').text(data.summary.total_orders);
                    $('#total-value').text(parseFloat(data.summary.total_value).toFixed(2));

                    let orders = data.orders;

                    if (orders.length > 0) {
                        $.each(orders, function (index, item) {
                            tbody.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.country_name ?? 'N/A'}</td> 
                                    <td>${item.order_id}</td>
                                    <td>${new Date(item.created_at).toLocaleDateString('en-GB')}</td>
                                    <td>${item.firstname ?? 'N/A'}</td>
                                    <td>${item.address_phone_number ?? 'N/A'}</td>
                                    <td>${item.total_items}</td>
                                    <td>${item.total_quantity}</td>
                                    <td>₹${parseFloat(item.subtotal).toFixed(2)}</td>
                                    <td>₹${parseFloat(item.gst_amount).toFixed(2)}</td>
                                    <td>₹${parseFloat(item.total_amount).toFixed(2)}</td>
                                    <td>${item.payment_status}</td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr><td colspan="10" class="text-center">No Data Found</td></tr>');
                    }
                }
            });
        }

        $('#country-filter').on('change', function () {
            const filter = $('#filter').val();
            if (filter === 'custom') {
                const from = $('#from-date').val();
                const to = $('#to-date').val();
                if (from && to) loadReport('custom', from, to);
            } else {
                loadReport(filter);
            }
        });

        // Load initial data from the default selected filter
        const initialFilter = $('#filter').val();
        if (initialFilter !== 'custom') {
            loadReport(initialFilter);
        }

        // Handle filter change
       $('#filter').on('change', function () {
    const selected = $(this).val();

    if (selected === 'custom') {
        $('.custom-date').removeClass('d-none');
    } else {
        $('.custom-date').addClass('d-none');
        loadReport(selected); // Load only when user manually changes
    }
});

        // Apply custom filter
        $('#apply-filter').on('click', function () {
            const from = $('#from-date').val();
            const to = $('#to-date').val();

            if (!from || !to) {
                alert("Please select both From and To dates.");
                return;
            }

            loadReport('custom', from, to);
        });

        // Export buttons
        $('#export-excel').click(function () {
            const filter = $('#filter').val();
            const from = $('#from-date').val();
            const to = $('#to-date').val();
            const countryId = $('#country-filter').val();
            let url = '{{ route('order.wise.report.export.excel') }}' + '?filter=' + filter;

            if (filter === 'custom') {
                url += '&from=' + from + '&to=' + to;
            }

            if (countryId) url += '&country_id=' + countryId;
            window.location.href = url;
        });

        $('#export-pdf').click(function () {
            const filter = $('#filter').val();
            const from = $('#from-date').val();
            const to = $('#to-date').val();
            const countryId = $('#country-filter').val();
            let url = '{{ route('order.wise.report.export.pdf') }}' + '?filter=' + filter;

            if (filter === 'custom') {
                url += '&from=' + from + '&to=' + to;
            }

            if (countryId) url += '&country_id=' + countryId;
            window.location.href = url;
        });
    });
</script>


    <script src="{{ URL::asset('assets/libs/gridjs/gridjs.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
@endsection
