<?php

namespace App\Models;

use App\Models\Reports\DriverDropoff;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Recipe extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $connection = 'sqlite';
    protected $table = 'recipes';
    protected $primaryKey = 'id';

    protected $casts = [
        'order_date' => 'datetime:d-m-Y H:i:s',
        'created_at' => 'datetime:d-d-Y H:i:s'
    ];

    protected $fillable = [
        'recipe_number',
        'store_id',
        'store_name',
        'order_sn',
        'order_date',
        'order_state',
        'buyer_id',
        'buyer_name',
        'bayer_mobile' ,
        'buyer_address',
        'exchange_rate',
        'exchange_rate_id',
        'total_quantity',
        'delivery_fee',
        'grand_total_price',
        'grand_total_price_riel',
        'comment',
        'downloaded',
        'authorized_by'
    ];

    public function recipeItems():object
    {
        return $this->hasMany(RecipeItem::class, 'recipe_id', 'id');
    }

    public function authorizedby():object
    {
        return $this->belongsTo(User::class, 'authorized_by', 'id')->select([
            'id',
            'name'
        ]);
    }

    public function dropoffItems():object
    {
        return $this->hasMany(DriverDropoff::class, 'recipe_id', 'id');
    }

    public function scopeAuthorizedBy(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('authorized_by', '=', $term);
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
            $builder->where('name', 'like', '%'. $term . '%');
            $builder->orWhere('recipe_number', 'like', '%'. $term );
            $builder->orWhere('order_sn', 'like', '%'. $term );
            $builder->orWhere('buyer_name', 'like', '%'. $term . '%');
            $builder->orWhere('store_name', 'like', '%'. $term . '%');
        }
        return $builder;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['recipe_number', 'order_sn', 'downloaded','store_name'])
            ->useLogName('recipe')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) => "A recipe {$this->name} has been {$eventName}"
            );
    }
}
