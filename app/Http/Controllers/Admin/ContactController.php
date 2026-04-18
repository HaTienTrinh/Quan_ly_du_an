<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $keyword = trim((string) $request->string('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%'.$keyword.'%')
                  ->orWhere('email', 'like', '%'.$keyword.'%')
                  ->orWhere('subject', 'like', '%'.$keyword.'%')
                  ->orWhere('message', 'like', '%'.$keyword.'%');
            });
        }

        $contacts = $query->paginate(15)->withQueryString();

        $unreadCount = Contact::where('status', 'unread')->count();

        return view('admin.contacts.index', compact('contacts', 'unreadCount'));
    }

    public function show(Contact $contact)
    {
        if ($contact->status === 'unread') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function reply(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'admin_reply' => ['required', 'string', 'max:2000'],
        ]);

        $contact->update([
            'admin_reply' => $validated['admin_reply'],
            'status'      => 'replied',
            'replied_at'  => now(),
        ]);

        return back()->with('success', 'Đã lưu phản hồi thành công.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Đã xóa liên hệ.');
    }
}
