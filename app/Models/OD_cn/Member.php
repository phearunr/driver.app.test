<?php

namespace App\Models\OD_cn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $connection = 'live_mysql';
    protected $table = 'member';
    protected $primaryKey = 'member_id';

    public function pickup_address():object
    {
        return $this->belongsTo(
            Delivery_staff_order::class,
            'order_id', 'order_id'
        )->select(['order_id', 'pickup_phone','pickup_address']);
    }
}
