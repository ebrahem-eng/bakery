<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['first_name', 'last_name', 'title'];

    public function mobiles()
    {
        return $this->hasMany(SupplierMobile::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }
}
