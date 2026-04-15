<?php

namespace App\Traits;

use App\Models\WorkDay;
use Carbon\Carbon;

trait SyncsWithActiveWorkDay
{
    protected static function bootSyncsWithActiveWorkDay()
    {
        static::creating(function ($model) {
            $activeWorkDay = WorkDay::where('status', 'active')->first();

            if ($activeWorkDay) {
                // If the model relates to a work day, automatically set the ID if it hasn't been set manually
                if (in_array('work_day_id', $model->getFillable()) && empty($model->work_day_id)) {
                    $model->work_day_id = $activeWorkDay->id;
                }

                // If the active work day's designated calendar date differs from the current live server date
                $workDayDate = $activeWorkDay->start_time;
                $currentLiveDate = Carbon::now();

                if (!$currentLiveDate->isSameDay($workDayDate)) {
                    // Option A: Shift the Y-m-d to match the workday, but retain the H:i:s of reality.
                    $shiftedTimestamp = $currentLiveDate->copy()
                        ->year($workDayDate->year)
                        ->month($workDayDate->month)
                        ->day($workDayDate->day);

                    // Ensure created_at / updated_at map directly to that timeframe
                    $model->created_at = $shiftedTimestamp;
                    $model->updated_at = $shiftedTimestamp;
                }
            }
        });
    }
}
