<?php

namespace App\Models;

use App\Models\Store;
use App\Casts\UserStatus;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use App\Models\Drivers\DropoffClaim;
use App\Models\Drivers\DropoffRecipe;

use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use HasRoles;
    use LogsActivity;

    protected $connection = 'sqlite';
    protected $table = 'users';

    protected static $recordEvents = [
        'created',
        'updated',
        'deleted'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'password', 'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'pivot'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:d-M-Y H:i:s',
        'status' => UserStatus::class
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    protected $appends = [
        'profile_photo_url',
        'last_activity',
        //'role',
    ];

    public function getRoleAttribute()
    {
        return isset($this->roles)
            ? $this->roles()->pluck('name')[0] : null;
    }

    public function getPermissionsAttribute(): object
    {
        return isset($this->roles)
            ? collect($this->roles->pluck('permissions')[0])->map(function ($item) {
                return $item['slug'];
            }) : [];
    }


    public function DropoffClaim()
    {
        return $this->hasMany(DropoffClaim::class, 'dropoffed_by', 'id');
    }

    public function dropoffs()
    {
        return $this->hasManyThrough(DropoffRecipe::class, DropoffClaim::class, 'dropoffed_by', '');
    }


    public function store_handles(): object
    {
        return $this->belongsToMany(
            Store::class,
            'store_handles'
        )
            ->whereNull('store_handles.deleted_at')
            ->withTimestamps();
    }

    public function last_login_at()
    {
        return $this->belongsTo(
            LastLoginAt::class,
            'id',
            'user_id'
        );
    }

    public function getLastActivityAttribute()
    {
        return empty($this->last_login_at)
            ? NULL : date('d-M-Y H:i:s', $this->last_login_at['last_activity']);
    }

    public function scopeHasRole(Builder $builder, $term): object
    {
        $builder->when($term, function ($query) use ($term) {
            $query->whereHas('roles', function ($q) use ($term) {
                $q->where('id', '=',  $term);
            });
        });
        return $builder;
    }

    public function scopeStatus(Builder $builder, $status)
    {
        if (!is_null($status)) {
            if (!is_null($status)) {
                switch ($status) {
                    case 1:
                        $builder->where('status', '=', 0);
                        break;
                    case 2:
                        $builder->where('status', '=', 0);
                        break;
                    case 3:
                        $builder->where('status', '=', 1);
                        break;
                    case 4:
                        $builder->where('status', '=', 0);
                        break;
                    case 5:
                        $builder->onlyTrashed();
                        break;
                }
            }
            return $builder;
        }
        return $builder;
    }

    public function scopeMonth(Builder $builder, $date): object
    {
        if (!is_null($date)) {
            switch ($date) {
                case 'logged-in-today':
                    $builder->whereDate('last_activity', today());
                    break;
                case 'logged-in-last-7-days':
                    $builder->whereDate('last_activity', '>', today()->subDays(7));
                    break;
                case 'logged-in-last-30-days':
                    $builder->whereDate('last_activity', '>', today()->subDays(30));
                    break;
                case 'not-logged-in-for-30-days':
                    $builder->where('last_activity', '<', today()->subDays(30))
                        ->orWhere('last_activity', null);
                    break;
            }
        }
        return $builder;
    }

    public function scopeSearch(Builder $builder, $term): object
    {
        if (!is_null($term)) {
            $builder->where('name', 'like', '%' . $term . '%');
            $builder->orWhere('email', 'like', '%' . $term . '%');
        }
        return $builder;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->useLogName('user')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) => "The user {$this->name} has been {$eventName}"
            );
    }
}
