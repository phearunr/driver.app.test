<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug',
    ];

    protected $hidden = [
        'pivot',
        // 'created_at',
        'updated_at',
    ];

    protected $appends = [
        'permissions',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:d-M-Y H:i:s',
    ];

    public function getStatusAttribute()
    {
        return !$this->deleted_at ? 'active' : 'trashed';
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function getPermissionsAttribute()
    {
        return $this->permissions() ?? collect($this->permissions()->pluck('name'))->implode(',');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
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
            $builder->where('name', 'REGEXP', $term . '$');
        }
        return $builder;
    }
}
