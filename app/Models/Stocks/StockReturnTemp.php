<?php

namespace App\Models\Stocks;


use Carbon\Carbon;
use App\Models\User;

use App\Casts\OrderStatus;
use App\Models\OD_cn\Store;
use App\Models\OD_cn\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockReturnTemp extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $connection = 'dev_mysql';
    protected $table = 'stock_returns_temp';
    protected $primaryKey = 'id';

    protected $casts = [
        'order_state' => OrderStatus::class,
        'created_at' => 'datetime:d-M-Y H:i:s'
    ];

    public $appends = [
        'preview_url'
    ];

    protected $fillable = [
        'order_id',
        'order_sn',
        'order_state',
        'buyer_id',
        'buyer_name',
        'store_id',
        'store_name',
        'total_quantity',
        'remarked',
        'updated_by'
    ];

    public function getPreviewUrlAttribute()
    {
        return 'https://ui-avatars.com/api/?name=N+O&color=7F9CF5&background=EBF4FF';
    }

    public function bayer()
    {
        return $this->belongsTo(
                Member::class,
                'buyer_id', 'member_id'
            )
            ->select([
            'member_id',
            'member_name',
            'member_mobile'
        ]);
    }

    public function store()
    {
        return $this->belongsTo(
            Store::class,
            'store_id', 'store_id'
        )->select(['store_id' ,'store_name']);
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')
                ->select(['id', 'name']);
    }

    public function products(){
        return $this->hasMany(StockReturnItemsTemp::class, 'stock_returns_temp_id', 'id')
            ->select([
                'id',
                'stock_returns_temp_id',
                'product_name As name',
                'unit_price',
                'quantity',
                'return_quantity'
            ]);
    }

    public function scopeStatus(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('order_state', '=', $term);
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['order_sn', 'total_quantity', 'store_name', 'remarked'])
            ->useLogName('stock return')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) => "A stock return {$this->name} has been {$eventName}"
            );
    }
}
