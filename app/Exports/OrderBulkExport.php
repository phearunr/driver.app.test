<?php

namespace App\Exports;

use App\Models\OD_cn\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class OrderBulkExport implements
    FromCollection,
    ShouldAutoSize,
    WithColumnFormatting
{
    private $orders;

    public function __construct(
        $orders
    ) {
        $this->orders = $orders;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::query()
        ->whereIn(
            'order.order_id',
            preg_split("/[,]/", $this->orders['orderIds'])
        )
        ->form()
        ->get()
        ->map(function($item){
            return [
                'order_sn' => $item['order_sn'],
                'order_date' => $item['order_date'],
                'order_state' => $item['order_state'],
                'store_name' => $item['store_name'],
                'buyer_name' => $item['buyer_name'],
                'total_qauntity' => $item['products_sum_goods_num'],
                'total_cost' => $item['goods_amount'],
                'grand_total_price' => $item['grand_total_price'],
            ];
        });
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
