<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ContactMessage;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    /**
     * Store a newly created contact message in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|string|max:2000',
        ]);

        try {
            $contactMessage = ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'is_read' => false,
            ]);

            // Notify admins who have permission to view contact messages
            $adminsToNotify = Admin::permission('view contact messages')->where('status', 'active')->get();
            
            if ($adminsToNotify->count() > 0) {
                Notification::send($adminsToNotify, new NewContactMessageNotification($contactMessage));
            }

            return response()->json([
                'status' => 'success',
                'message' => __('Your message has been sent successfully. We will get back to you soon!')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('An error occurred while sending your message. Please try again later.')
            ], 500);
        }
    }
}
