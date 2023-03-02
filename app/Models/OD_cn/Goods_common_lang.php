<?php

namespace App\Models\OD_cn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goods_common_lang extends Model
{
    use HasFactory;
    protected $connection = 'live_mysql';
    protected $table = 'goods_common_lang';
    protected $primaryKey = 'goods_commonid';
}

