<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $guard = 'admin';
    protected $guard_name = 'admin';

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
            'password'          => 'hashed',
        ];
    }
}