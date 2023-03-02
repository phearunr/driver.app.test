<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Currency extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'name',
        'symbol'
    ];

    protected $appends = [
        'status',
        'rates'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'rates' => 'collection'
    ];


    public function exchange_rates():object
    {
        return $this->hasMany(
            ExchangeRate::class,
            'currency_id', 'id'
        );
    }

    public function getRatesAttribute(): object
    {
        return $this->exchange_rates->pluck('amount');
    }

    public function getStatusAttribute(): String
    {
        return !$this->deleted_at ? 'active' : 'trashed';
    }

    public function scopeStatus(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->onlyTrashed();
        }
        return $builder;
    }

    public function scopeMonth(Builder $builder, $date):object
    {
        if (!is_null($date)) {
            $builder->whereBetween('created_at', [
                Carbon::createFromFormat('d-m-Y', $date)->startOfMonth(),
                Carbon::createFromFormat('d-m-Y', $date)->endOfMonth(),
            ]);
        }
        return $builder;
    }

    public function scopeSearch(Builder $builder, $term):object
    {
        if (!is_null($term)) {
            $builder->where('symbol', 'like', '%'. $term);
        }
        return $builder;
    }
    public function getActivitylogOptions(): LogOptions
    {
        // Chain fluent methods for configuration options
        return LogOptions::defaults()
            ->logOnly(['name', 'rates'])
            ->useLogName('currency')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) => "The currency {$this->name} has been {$eventName}"
            );
    }
}
