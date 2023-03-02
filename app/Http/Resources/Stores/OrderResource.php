<?php

namespace App\Http\Resources\Stores;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_id' => $this->order_id,
            'order_sn' => $this->order_sn,
            'order_date' => $this->order_date,
            'store_id' => $this->store_id,
            'store_name' => $this->store_name,
            'store_name' => $this->store_name,
            'buyer_id' => $this->buyer_id,
            'buyer_name' => $this->buyer_name,
            'preview_url' => $this->preview_url,
            'total_quantity' => $this->products_sum_goods_num,
            'cost_price' => $this->goods_amount,
            'delivery_fee' => $this->delivery_fee,
            'country_fee_home' => $this->shipping_fee_home,
            'total_price' => $this->order_amount,
            'total_delivery_fee' => $this->total_delivery_fee,
            'grand_total_price' => $this->grand_total_price,
            'grand_total_price_riel' => number_format($this->grand_total_price_riel, 0, ',', ',')  ?? 0,
            'order_state' => $this->order_state,
            'order_state_id' => $this->order_state_id,
            'confirm_cash_pay_time' => $this->confirm_cash_pay_time
        ];

    }
}
