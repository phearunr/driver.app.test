<?php

namespace App\Models\Drivers;

use Carbon\Carbon;

use App\Models\User;
use App\Casts\DropoffStatus;
use Spatie\Activitylog\LogOptions;

use Illuminate\Database\Eloquent\Model;
use App\Models\Drivers\DropoffRecipeItem;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DropoffClaim extends Model
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
            'scanouterBy',
            'dropofferBy',
            'dropoffRecipes' =>
            function ($join) {
                $join->join(
                    'dropoff_recipe_items',
                    'dropoff_recipe_items.dropoff_recipe_id',
                    '=',
                    'dropoff_recipes.id'
                );
                $join->join(
                    'recipes',
                    'recipes.id',
                    '=',
                    'dropoff_recipes.id'
                );
                $join->select(
                    'recipes.recipe_number',
                    'recipes.order_sn',
                    'dropoff_recipes.id',
                    'dropoff_claim_id',
                    'recipe_id',
                    'product_id',
                    'product_name',
                    'order_quantity',
                    'drop_off_quantity',
                    'drop_off_quantity_status',
                );
            }
        ])
            ->scanOutBy(request('scanOutBy'))
            ->dropoffBy(request('dropoffBy'))
            ->status(request('status'))
            ->month(request('month'))
            ->search(request('term'));
    }

    public function dropoffRecipes()
    {
        return $this->hasMany(DropoffRecipe::class);
    }

    public function recipeItems()
    {
        return $this->hasManyThrough(DropoffRecipeItem::class, 'dropoff_recipes');
    }

    public function scanouterBy(): object
    {
        return $this->belongsTo(
            User::class,
            'scanouted_by',
            'id'
        )->select([
            'id',
            'name'
        ]);
    }

    public function dropofferBy(): object
    {
        return $this->belongsTo(
            User::class,
            'dropoffed_by',
            'id'
        )->select([
            'id',
            'name'
        ]);
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
            $builder->whereHas('dropoffRecipes.recipe', function ($query) use ($term) {
                $query->where('order_sn', 'like',  '%'. $term);
                $query->orWhere('recipe_number', 'like', '%' . $term );
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
