<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'work_day_id',
        'supplier_id',
        'category_id',
        'currency_id',
        'exchange_rate',
        'quantity',
        'unit_price',
        'total_cost',
        'paid_amount',
        'unloading_fee',
        'material_type_name',
        'boxes_count',
        'box_weight',
        'unloading_fee_payer',
        'unloading_fee_currency_id',
        'unloading_fee_exchange_rate',
        'notes',
    ];

    public function unloadingFeeCurrency() { return $this->belongsTo(Currency::class, 'unloading_fee_currency_id'); }
    public function admin() { return $this->belongsTo(Admin::class); }
    public function workDay() { return $this->belongsTo(WorkDay::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function currency() { return $this->belongsTo(Currency::class); }
}
