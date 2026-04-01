<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        // Filters
        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(25);

        // Get unique values for filter selects
        $events = Activity::distinct()->pluck('event')->filter();
        $subjectTypes = Activity::distinct()->pluck('subject_type')->filter()->map(function ($type) {
            return class_basename($type);
        })->unique();
        $causers = Activity::with('causer')->distinct('causer_id')->pluck('causer_id')->filter();
        $admins = Admin::whereIn('id', $causers)->get();

        return view('Admin.ActivityLog.index', compact('activities', 'events', 'subjectTypes', 'admins'));
    }

    public function show(Activity $activity)
    {
        $activity->load('causer', 'subject');

        return view('Admin.ActivityLog.show', compact('activity'));
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return back()->with('success_message', __('Activity log entry deleted.'));
    }

    public function clear()
    {
        Activity::truncate();

        return back()->with('success_message', __('All activity logs have been cleared.'));
    }
}
