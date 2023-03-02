<?php

namespace App\Models\Stocks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReturnItemsTemp extends Model
{
    use HasFactory;

    protected $connection = 'dev_mysql';
    protected $table = 'stock_return_items_temp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'return_quantity',
        'stock_returns_temp_id',
        'updated_by'
    ];
}
