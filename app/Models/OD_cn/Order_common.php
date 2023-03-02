<?php

namespace App\Models\OD_cn;

use App\Casts\AsSerialize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order_common extends Model
{
    use HasFactory;
    protected $connection = 'live_mysql';
    protected $table = 'order_common';
    protected $casts = [
        'reciver_info' => AsSerialize::class
    ];
}
