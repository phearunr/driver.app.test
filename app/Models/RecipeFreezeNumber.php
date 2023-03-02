<?php

namespace App\Models;

use App\Casts\AsDatetimeable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecipeFreezeNumber extends Model
{
    use HasFactory;

    protected $connection = 'sqlite';
    protected $table = 'recipe_freeze_numbers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'store_id',
        'order_sn',
        'order_date',
        'number',
        'full_number'
    ];

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $model->full_number  = date('ym', strtotime($model->order_date)).'-'.str_pad($model->number, 5, '0',STR_PAD_LEFT);
        });
    }
}
