<?php

namespace App\Models\Drivers;

use App\Models\Role;
use App\Models\User;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends User
{
    use HasFactory;
    use HasRoles;

    protected $primaryKey = 'id';
    protected $table = 'users';

    public function DropoffClaim()
    {
        return $this->hasMany(DropoffClaim::class, 'dropoffed_by', 'id');
    }

    public function dropoffs()
    {
        return $this->hasManyThrough(DropoffRecipe::class, DropoffClaim::class, 'dropoffed_by');
    }

    // public function role()
    // {
    //     return $this->belongsToMany(Role::class, 'model_has_roles');
    // }

    // public static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         $model->whereHas(['roles' => fn ($q) => $q->where('role_id', 7)]);
    //     });
    // }
}
