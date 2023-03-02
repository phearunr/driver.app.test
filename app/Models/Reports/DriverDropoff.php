<?php

namespace App\Models\Reports;

use Carbon\Carbon;
use App\Models\User;
use App\Casts\DropoffStatus;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriverDropoff extends Model
{
    use HasFactory;

    protected $casts = [
        'scanouted_at' => 'datetime:Y-m-d H:i',
        'dropoffed_at' => 'datetime:Y-m-d H:i',
        'status' => DropoffStatus::class
    ];

    protected $fillable = [
        'scanouted_by',
        'scanouted_at',
        'dropoffed_by',
        'dropoffed_at',
        'comments',
    ];

    public function scopeForm(Builder $builder)
    {
        return $builder->with([
            'scanouterBy:id,name',
            'dropofferBy:id,name',
            'items' => function ($join) {
                $join->join(
                    'recipes',
                    'driver_dropoff_items.recipe_id',
                    '=',
                    'recipes.id'
                );
            }
        ])
        ->scanOutBy(request('scanOutBy'))
        ->dropoffBy(request('dropoffBy'))
        ->status(request('status'))
        ->month(request('month'))
        ->search(request('term'))
        ->withSum('items', 'order_quantity')
        ->withSum('items', 'drop_off_quantity');
    }

    public function items(): object
    {
        return $this->hasMany(DriverDropoffItems::class, 'driver_dropoff_id', 'id');
    }

    public function scanouterBy(): object
    {
        return $this->belongsTo(User::class, 'scanouted_by', 'id');
    }

    public function dropofferBy(): object
    {
        return $this->belongsTo(User::class, 'dropoffed_by', 'id');
    }

    public function scopeScanOutBy(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('scanouted_by', '=', $term);
        }
        return $builder;
    }

    public function scopeDropoffBy(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('dropoffed_by', '=', $term);
        }
        return $builder;
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
            $builder->whereHas('items.recipe_numbers', function($query) use ($term){
                $query->where('order_sn', 'like',  $term .'%');
                $query->orWhere('recipe_numbers', 'like', '%'. $term . '%');
            });
        }
        return $builder;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['scanouted_at', 'dropoffed_at', 'status'])
            ->useLogName('driver drop-off')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) => "A driver drop-off {$this->name} has been {$eventName}"
            );
    }
}
