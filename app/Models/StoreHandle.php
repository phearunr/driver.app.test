<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreHandle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'sqlite';
    protected $table = 'store_handles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'store_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:i:s',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'store_name',
        // 'user_name',
        // 'status'
    ];

    protected $hidden = [
        'store',
        'user'
    ];

    // public function getUserNameAttribute(): string
    // {
    //     return isset($this->user)
    //         ? $this->user['name'] : null;
    // }

    // public function getStoreNameAttribute(): string
    // {
    //     return isset($this->store)
    //         ? $this->store['name'] : null;
    // }

    public function getStatusAttribute()
    {
        return !$this->deleted_at ? 'active' : 'trashed';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'reference_id');
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
            $builder->whereBetween('store_handles.created_at', [
                Carbon::createFromFormat('d-m-Y', $date)->startOfMonth(),
                Carbon::createFromFormat('d-m-Y', $date)->endOfMonth(),
            ]);
        }
        return $builder;
    }

    public function scopeSearch(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('name', 'REGEXP', $term . '$');
        }
        return $builder;
    }
}
