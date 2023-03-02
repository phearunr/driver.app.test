<?php

namespace App\Models;

use App\Casts\AsDatetimeable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecipeItem extends Model
{
    use HasFactory;

    protected $connection = 'sqlite';
    protected $table = 'recipe_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'recipe_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'total_price'
    ];
}
