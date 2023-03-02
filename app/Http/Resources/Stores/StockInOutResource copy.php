<?php

namespace App\Http\Resources\Stores;

use Illuminate\Http\Resources\Json\JsonResource;

class StockInOutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'store' => [
                'store_contact' => $this->store['member']['member_mobile'],
                'store_name' => $this->store['store_name'],
                'store_address' => $this->store['store_address']
            ],
            'order_id' => $this->order_id,
            'order_sn' => $this->order_sn,
            'order_date' => $this->order_date,
            'products' => $this->products,
            'total_qauntity' => $this->products_sum_goods_num,
            'total_price' => $this->goods_amount,
            'grand_total' => $this->order_amount,
            'consignor' => [
                'name' => NULL,
                'date' => now()
            ],
            'recipient' => [
                'name' => NULL,
                'date' => now()
            ],
            'supervisor' => [
                'name' => NULL,
                'date' => now()
            ],
            'buyer' => [
                'name' => NULL,
                'date' => now()
            ],

        ];
        // return parent::toArray($request);
    }
}
