<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Product Name</th>
            <th>Variant</th>
            <th>Total Quantity</th>
            <th>Total Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($results as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->variant_label }}</td>
                <td>{{ $item->total_quantity }}</td>
                <td>{{ number_format($item->total_value, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
