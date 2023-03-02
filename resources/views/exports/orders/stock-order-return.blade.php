<table>
    <thead>
        <tr>
            <th>Buyer</th>
            <th>Order no</th>
            <th>Store</th>
            <th>Status</th>
            <th>Total Qty</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{$order['buyer_name']}}</td>
                <td>{{$order['order_sn']}}</td>
                <td>{{$order['store_name']}}</td>
                <td>{{$order['order_state']}}</td>
                <td>{{$order['total_quantity']}}</td>
                <td>{{$order['created_at']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
