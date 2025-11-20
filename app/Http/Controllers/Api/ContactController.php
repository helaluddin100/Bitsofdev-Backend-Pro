<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactFormSubmitted;
use App\Mail\ContactFormNotification;

class ContactController extends Controller
{
    /**
     * Store a newly created contact form submission.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'project_type' => 'required|string|in:web-development,mobile-app,ui-ux-design,consulting,other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contact = Contact::create($request->all());

            // Send email notification to admin
            $this->sendAdminNotification($contact);

            // Send confirmation email to user
            $this->sendUserConfirmation($contact);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully! We will get back to you soon.',
                'data' => $contact
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all contacts for admin dashboard.
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        // Sort by latest first
        $query->orderBy('created_at', 'desc');

        $contacts = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    /**
     * Get a specific contact.
     */
    public function show(Contact $contact)
    {
        // Mark as read if status is new
        if ($contact->status === 'new') {
            $contact->markAsRead();
        }

        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    /**
     * Update contact status and add admin notes.
     */
    public function update(Request $request, Contact $contact)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,read,replied,closed',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $contact->update($request->only(['status', 'admin_notes']));

        if ($request->status === 'replied') {
            $contact->markAsReplied();
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data' => $contact
        ]);
    }

    /**
     * Delete a contact.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }

    /**
     * Get contact statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => Contact::count(),
            'new' => Contact::new()->count(),
            'read' => Contact::read()->count(),
            'replied' => Contact::replied()->count(),
            'closed' => Contact::closed()->count(),
            'today' => Contact::whereDate('created_at', today())->count(),
            'this_week' => Contact::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Contact::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Send admin notification email.
     */
    private function sendAdminNotification($contact)
    {
        try {
            $adminEmail = config('mail.admin_email', 'admin@sparkedev.com');
            
            Mail::to($adminEmail)->send(new ContactFormNotification($contact));
        } catch (\Exception $e) {
            \Log::error('Failed to send admin notification: ' . $e->getMessage());
        }
    }

    /**
     * Send user confirmation email.
     */
    private function sendUserConfirmation($contact)
    {
        try {
            Mail::to($contact->email)->send(new ContactFormSubmitted($contact));
        } catch (\Exception $e) {
            \Log::error('Failed to send user confirmation: ' . $e->getMessage());
        }
    }
}
