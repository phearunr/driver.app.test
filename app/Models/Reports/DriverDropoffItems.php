<?php

namespace App\Models\Reports;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDropoffItems extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'recipe_id',
        'product_id',
        'product_name',
        'order_quantity',
        'drop_off_quantity',
        'drop_off_quantity_status',
        'drop_offed_at'
    ];

    public function recipe_numbers(): object
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }
}
