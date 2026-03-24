<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkDay extends Model
{
    protected $fillable = [
        'start_time', 'end_time', 'status', 'is_holiday', 'holiday_reason',
        'opened_by', 'closed_by', 'total_expenses_at_close', 'total_sales_at_close',
        'carried_over_bundles', 'carried_over_money'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_holiday' => 'boolean',
    ];

    public function openedBy()
    {
        return $this->belongsTo(Admin::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(Admin::class, 'closed_by');
    }
}
