<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class WorkerAttendance extends Model
{
    use \App\Traits\SyncsWithActiveWorkDay;

    protected $fillable = [
        'worker_id',
        'work_day_id',
        'arrival_time',
        'departure_time',
        'admin_id',
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime',
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
