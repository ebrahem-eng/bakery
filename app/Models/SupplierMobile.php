<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierMobile extends Model
{
    protected $fillable = ['supplier_id', 'mobile_number'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
