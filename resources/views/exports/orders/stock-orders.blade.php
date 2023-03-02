<table>
    <thead>
        <tr>
            <th>Buyer</th>
            <th>Order no</th>
            <th>Store</th>
            <th>Status</th>
            <th>Date</th>
            <th>Total Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{$order['buyer_name']}}</td>
                <td>{{$order['order_sn']}}</td>
                <td>{{$order['store_name']}}</td>
                <td>{{$order['order_state']}}</td>
                <td>{{$order['order_date']}}</td>
                <td>{{$order['total_quantity']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
