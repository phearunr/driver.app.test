<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLogger extends Activity
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'sqlite';
    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    protected $appends = [
        'status',
        'changes'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
    ];

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
            $builder->where('log_name', 'like', '%'. $term);
        }
        return $builder;
    }


}
