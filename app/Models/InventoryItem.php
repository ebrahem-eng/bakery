<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'inventory_id', 'category_id', 'system_quantity', 
        'actual_quantity', 'adjustment_quantity', 
        'unit_price', 'line_total_value'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
