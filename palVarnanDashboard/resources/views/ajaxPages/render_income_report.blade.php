<table role="grid" id="milkOrderReportTable" class="gridjs-table table table-responsive"
    style="border: 1px solid rgb(204, 204, 204); height: auto;">
    <thead class="gridjs-thead">
        <tr class="gridjs-tr">
            <th data-column-id="s.no" class="gridjs-th gridjs-th-sort" tabindex="0"
                style="background-color: rgba(0, 0, 0, 0.1); color: rgb(0, 0, 0); border-bottom: 3px solid rgb(204, 204, 204); text-align: center; border-right: 0.5px solid rgb(204, 204, 204);">
                <div class="gridjs-th-content">S.NO</div>
            </th>
            <th data-column-id="username" class="gridjs-th gridjs-th-sort" tabindex="0"
                style="background-color: rgba(0, 0, 0, 0.1); color: rgb(0, 0, 0); border-bottom: 3px solid rgb(204, 204, 204); text-align: center; border-right: 0.5px solid rgb(204, 204, 204);">
                <div class="gridjs-th-content">Order ID</div>
            </th>
            <th data-column-id="phoneNumber" class="gridjs-th gridjs-th-sort" tabindex="0"
                style="background-color: rgba(0, 0, 0, 0.1); color: rgb(0, 0, 0); border-bottom: 3px solid rgb(204, 204, 204); text-align: center; border-right: 0.5px solid rgb(204, 204, 204);">
                <div class="gridjs-th-content">Ordered Date</div>
            </th>
            <th data-column-id="action" class="gridjs-th"
                style="background-color: rgba(0, 0, 0, 0.1); color: rgb(0, 0, 0); border-bottom: 3px solid rgb(204, 204, 204); text-align: center; border-right: 0.5px solid rgb(204, 204, 204);">
                <div class="gridjs-th-content">Customer</div>
            </th>
            <th data-column-id="action" class="gridjs-th"
                style="background-color: rgba(0, 0, 0, 0.1); color: rgb(0, 0, 0); border-bottom: 3px solid rgb(204, 204, 204); text-align: center; border-right: 0.5px solid rgb(204, 204, 204);">
                <div class="gridjs-th-content">Category</div>
            </th>
            <th data-column-id="orderManually" class="gridjs-th"
                style="background-color: rgba(0, 0, 0, 0.1); color: rgb(0, 0, 0); border-bottom: 3px solid rgb(204, 204, 204); text-align: center; border-right: 0.5px solid rgb(204, 204, 204);">
                <div class="gridjs-th-content">Amount</div>
            </th>
        </tr>
    </thead>
    <tbody class="gridjs-tbody">


        @foreach ($productOrders as $productOrder)
            <tr class="gridjs-tr">
                <td data-column-id="s.no" class="gridjs-td"
                    style="text-align: center; border-right: 0.5px solid rgb(204, 204, 204); border-bottom: 0.5px solid rgb(204, 204, 204);">
                    {{ $loop->iteration }}</td>
                <td data-column-id="username" class="gridjs-td"
                    style="text-align: center; border-right: 0.5px solid rgb(204, 204, 204); border-bottom: 0.5px solid rgb(204, 204, 204);">
                    <a href="productOrders/{{ $productOrder->order_id }}">{{ $productOrder->order_id }} </a>
                </td>
                <td data-column-id="phoneNumber" class="gridjs-td"
                    style="text-align: center; border-right: 0.5px solid rgb(204, 204, 204); border-bottom: 0.5px solid rgb(204, 204, 204);">
                    {{ $productOrder->date_ordered_on }}</td>
                <td data-column-id="action" class="gridjs-td"
                    style="text-align: center; border-right: 0.5px solid rgb(204, 204, 204); border-bottom: 0.5px solid rgb(204, 204, 204);">
                    <div style="word-wrap: break-word;"> {{ $productOrder->customer->name }}</div>

                </td>
                <td data-column-id="orderManually" class="gridjs-td"
                    style="text-align: center; border-right: 0.5px solid rgb(204, 204, 204); border-bottom: 0.5px solid rgb(204, 204, 204);">
                    PRODUCT
                </td>
                <td data-column-id="orderManually" class="gridjs-td"
                    style="text-align: center; border-right: 0.5px solid rgb(204, 204, 204); border-bottom: 0.5px solid rgb(204, 204, 204);">
                    {{ $productOrder->total_amount}}
                </td>


            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th data-column-id="orderManually" class="gridjs-th"
                style="background-color: rgba(0, 0, 0, 0.1); color: rgb(0, 0, 0); border-bottom: 3px solid rgb(204, 204, 204); text-align: center; border-right: 0.5px solid rgb(204, 204, 204);">
                <div class="gridjs-th-content">Total</div>
            </th>
            <th data-column-id="orderManually" class="gridjs-th"
                style="background-color: rgba(0, 0, 0, 0.1); color: rgb(0, 0, 0); border-bottom: 3px solid rgb(204, 204, 204); text-align: center; border-right: 0.5px solid rgb(204, 204, 204);">

            </th>
        </tr>
    </tfoot>
</table>
