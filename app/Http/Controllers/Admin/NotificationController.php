<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display the Notification Center.
     */
    public function index()
    {
        // Show all notifications (read and unread) for the logged in admin
        $notifications = auth('admin')->user()->notifications()->paginate(15);
        
        return view('Admin.Notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = auth('admin')->user()->notifications()->findOrFail($id);
        
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        auth('admin')->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => __('All notifications marked as read.'),
        ]);
    }

    /**
     * Delete a notification completely.
     */
    public function destroy($id)
    {
        abort_if(!auth('admin')->user()->can('manage notifications'), 403, __('Access Denied'));

        $notification = auth('admin')->user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', __('Notification deleted successfully.'));
    }

    /**
     * Delete all notifications for the user.
     */
    public function deleteAll()
    {
        abort_if(!auth('admin')->user()->can('manage notifications'), 403, __('Access Denied'));

        auth('admin')->user()->notifications()->delete();

        return redirect()->back()->with('success', __('All notifications deleted successfully.'));
    }
}
