<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\OD_cn\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'sqlite';
    protected $table = 'stores';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'reference_id',
        'owner_id',
        'category_id'
    ];

    protected $appends = ['status', 'owner'];

    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:i:s',
    ];

    public function getStatusAttribute()
    {
        return !$this->deleted_at ? 'active' : 'trashed';
    }

    public function getOwnerAttribute()
    {
        return $this->storeOwner ? $this->storeOwner['member_name'] : NULL;
    }

    public function storeOwner()
    {
        return $this->belongsTo(
            Member::class,
            'owner_id',
            'member_id'
        )
            ->select([
                'member_id',
                'member_name',
                'member_mobile',
                'member_email'
            ]);
    }

    public function storeHandles()
    {
        return $this->belongsToMany(
            Store::class,
            'store_handles'
        )
            ->whereNull('store_handles.deleted_at')
            ->withTimestamps();
    }


    public function scopeStatus(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->onlyTrashed();
        }
        return $builder;
    }

    public function scopeMonth(Builder $builder, $date)
    {
        if (!is_null($date)) {
            $builder->whereBetween('stores.created_at', [
                Carbon::createFromFormat('d-m-Y', $date)->startOfMonth(),
                Carbon::createFromFormat('d-m-Y', $date)->endOfMonth(),
            ]);
        }
        return $builder;
    }

    public function scopeSearch(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('stores.name', 'like', '%' . $term);
        }
        return $builder;
    }

    public function scopeHandlers(Builder $builder)
    {
        $builder->whereHas('storeHandles', function ($query) {
            $query->where(['store_handles.user_id' => auth()->id()]);
        });
        return $builder;
    }
}
