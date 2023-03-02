<?php

namespace App\Models\OD_cn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goods_common extends Model
{
    use HasFactory;
    protected $connection = 'live_mysql';
    protected $table = 'goods_common';
    protected $primaryKey = 'goods_commonid';

    public function languages(){
        return $this->hasMany(
            Goods_common_lang::class,
            'goods_commonid', 'goods_commonid'
        )->select(['goods_commonid', 'language_id','goods_name']);
    }
}
