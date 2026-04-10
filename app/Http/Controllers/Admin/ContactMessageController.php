<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        // Optional filtering by read status
        if ($request->has('status') && $request->status !== 'all') {
            $isRead = $request->status === 'read' ? true : false;
            $query->where('is_read', $isRead);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $messages = $query->latest()->paginate(15)->withQueryString();

        return view('Admin.ContactMessages.index', compact('messages'));
    }

    /**
     * Display the specified contact message.
     */
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read when viewed
        if (!$contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }

        return view('Admin.ContactMessages.show', compact('contactMessage'));
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        abort_if(!auth('admin')->user()->can('delete contact messages'), 403, __('Access Denied'));

        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', __('Message deleted successfully.'));
    }
}
