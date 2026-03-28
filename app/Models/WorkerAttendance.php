<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerAttendance extends Model
{
    protected $fillable = [
        'worker_id',
        'work_day_id',
        'arrival_time',
        'admin_id',
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
