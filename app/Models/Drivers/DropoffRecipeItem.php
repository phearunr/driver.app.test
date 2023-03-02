<?php

namespace App\Models\Drivers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropoffRecipeItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'product_name',
        'order_quantity',
        'drop_off_quantity',
        'drop_off_quantity_status',
    ];
}
