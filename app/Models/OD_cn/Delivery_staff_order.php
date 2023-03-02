<?php

namespace App\Models\OD_cn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery_staff_order extends Model
{
    use HasFactory;
    protected $connection = 'live_mysql';
    protected $table = 'delivery_staff_order';
    protected $primaryKey = 'delivery_order_id';
}
