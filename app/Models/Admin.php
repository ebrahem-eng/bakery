<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\CausesActivity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use CausesActivity, HasFactory, HasRoles, LogsActivity, Notifiable;

    protected $guard = 'admin';

    protected $guard_name = 'admin';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $eventName) => "Admin account was {$eventName}");
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'title',
        'address',
        'email',
        'email_verified_at',
        'password',
        'gender',
        'status',
        'age',
        'birthday',
        'img',
        'last_login_at',
        'last_login_ip',
    ];

    public function mobiles()
    {
        return $this->hasMany(AdminMobile::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
