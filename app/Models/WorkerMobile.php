<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerMobile extends Model
{
    use HasFactory;

    protected $fillable = ['worker_id', 'mobile_number'];

    public function worker() { return $this->belongsTo(Worker::class); }
}
