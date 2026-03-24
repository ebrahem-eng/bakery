<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMobile extends Model
{
    protected $fillable = ['admin_id', 'mobile_number'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
