<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';
    protected $fillable = ['name', 'slug','model_type', 'model_id'];

    protected $appends = ['status'];

    protected $hidden = [
        'pivot'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:i:s',
    ];
    public function getStatusAttribute()
    {
        return !$this->deleted_at ? 'active' : 'trashed';
    }


    public function scopeStatus(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('status', '=', $term);
        }
        return $builder;
    }

    public function scopeMonth(Builder $builder, $date)
    {
        if (!is_null($date)) {
            $builder->whereBetween('created_at', [
                Carbon::createFromFormat('d-m-Y', $date)->startOfMonth(),
                Carbon::createFromFormat('d-m-Y', $date)->endOfMonth(),
            ]);
        }
        return $builder;
    }

    public function scopeSearch(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('order_sn', 'REGEXP', $term.'$');
            $builder->orWhere('order_id', '=', $term);
            $builder->orWhere('buyer_name', 'REGEXP', $term .'$');
        }
        return $builder;
    }

}
