<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stock In-Out</title>
</head>
<body>

    <table>
        <thead>
            <tr>
                <th colspan="10"><b>ប័ណ្ណប្រគល់ទំនិញ និងការទូទាត់</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4"> អតិថិជនៈ {{$order['buyer_name']}} </td>
                <td colspan="2"></td>
                <td colspan="4">លេខរៀងៈ {{$order['recipe_number']}} </td>
            </tr>
            <tr>
                <td colspan="4"> លេខទូរស័ព្ទៈ {{ $order['bayer_mobile'] }} </td>
                <td colspan="2"></td>
                <td colspan="4">លេខយោងៈ {{ $order['recipe_sn'] }} </td>
            </tr>
            <tr>
                <td rowspan="2" colspan="6"> អាសយដ្ឋានៈ {{ $order['buyer_address']}} </td>
                <td colspan="4">កាលបរិច្ឆេទៈ {{ $order['order_date']}} </td>
            </tr>
            <tr>
                <td colspan="2">អត្រាប្ដូរប្រាក់ $1=</td>
                <td align="center">{{ $order['exchange_rate']}}</td>
                <td align="left">KHR</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table border="1" cellspacing="15px">
        <thead>
            <tr>
                <th>ល.រ</th>
                <th colspan="6">ឈ្មោះទំនិញ</th>
                <th>ចំនួន</th>
                <th>តម្លៃ​ឯកតា</th>
                <th>សរុប</th>
            </tr>
        </thead>
        <tbody>
            @php $no= 0 @endphp
            @for ($i = 0; $i <= count($order['products']) ; $i++)
                @php $no++; @endphp
                @if(isset($order['products'][$i]))
                    <tr>
                        <td align="center">{{$no}}</td>
                        <td align="left" colspan="6">
                            {{ $order['products'][$i]['name'] }}
                        </td>
                        <td align="center">{{ $order['products'][$i]['quantity'] }}</td>
                        <td align="center">{{ $order['products'][$i]['unit_price'] }}</td>
                        <td align="center">{{ $order['products'][$i]['total_price'] }}</td>
                    </tr>
                @endif
            @endfor
            <tr>
                <td align="center">{{ $no}}</td>
                <td align="left" colspan="6">Delivery fee</td>
                <td align="center"></td>
                <td align="center">{{ $order['delivery_fee'] }}</td>
                <td align="center">{{ $order['delivery_fee'] }}</td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td>សរុបរួម ($)</td>
                {{-- <td>{{ $order['total_quantity'] }}</td> --}}
                <td align="center"> {{ $order['grand_total_price'] }}</td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td>សរុបរួម (KHR)</td>
                {{-- <td>{{ $order['total_quantity'] }}</td> --}}
                <td align="center">{{ $order['grand_total_price_riel_recipe'] }}</td>
            </tr>

            <tr>
                <td colspan="2"><b>បរិយាយ:</b></td>
            </tr>
            <tr>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="2"><b>ការទូទាត់:</b></td>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="2">☐ សាច់ប្រាក់:</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">☐ ធនាគារ:</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">☐ ផ្សេងៗ:</td>
                <td></td>
            </tr>
            <tr><td></td></tr>
            <tr>
                <td colspan="2"><b>កំណត់ចំណាំ:</b></td>
            </tr>
            <tr><td rowspan="2" colspan="10"></td></tr>
            <tr><td></td></tr>
            <tr>
                <td colspan="10">
                    *ពណ៌សគណនេយ្យករ ពណ៌ផ្កាឈូកអតិថិជន ពណ៌លឿងឃ្លាំង ពណ៌ខៀវទុកក្នុងគល់បញ្ជី។
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">អ្នកទិញ </th>
                <th colspan="2">អ្នកដឹក</th>
                <th colspan="3">ឃ្លាំង</th>
                <th colspan="2">អ្នកលក់</th>
            </tr>
            <tr><td rowspan="3" colspan="10"></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>


            <tr>
                <td colspan="3">ឈ្មោះ</td>
                <td colspan="2">ឈ្មោះ</td>
                <td colspan="3">ឈ្មោះ</td>
                <td colspan="2">ឈ្មោះ</td>
            </tr>
            <tr>
                <td colspan="3">កាលបរិច្ឆេទ</td>
                <td colspan="2">កាលបរិច្ឆេទ</td>
                <td colspan="3">កាលបរិច្ឆេទ</td>
                <td colspan="2">កាលបរិច្ឆេទ</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
