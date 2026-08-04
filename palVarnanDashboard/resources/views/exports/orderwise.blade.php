<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Customer Name</th>
            <th>Mobile</th>
            <th>Total Items</th>
            <th>Total Quantity</th>
            <th>Total Value</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($results as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $order->order_id }}</td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}</td>
                <td>{{ $order->firstname ?? 'N/A' }}</td>
                <td>{{ $order->address_phone_number ?? 'N/A' }}</td>
                <td>{{ $order->total_items }}</td>
                <td>{{ $order->total_quantity }}</td>
                <td>{{ number_format($order->total_amount, 2) }}</td>
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
