<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeetingBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MeetingBookingController extends Controller
{
    /**
     * Store a new meeting booking (public API).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'company' => 'nullable|string|max:255',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $booking = MeetingBooking::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'preferred_date' => $request->preferred_date,
                'preferred_time' => $request->preferred_time,
                'subject' => $request->subject ?? 'Meeting Request',
                'message' => $request->message,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Meeting request sent successfully! We\'ll get back to you soon.',
                'data' => [
                    'id' => $booking->id,
                    'preferred_date' => $booking->preferred_date->format('Y-m-d'),
                    'preferred_time' => $booking->preferred_time,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * List all meeting bookings (admin).
     */
    public function index(Request $request)
    {
        $query = MeetingBooking::query();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');
        $bookings = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    /**
     * Show a single meeting booking (admin).
     */
    public function show(MeetingBooking $meeting_booking)
    {
        if ($meeting_booking->status === 'new') {
            $meeting_booking->markAsRead();
        }

        return response()->json([
            'success' => true,
            'data' => $meeting_booking,
        ]);
    }

    /**
     * Update meeting booking status / notes (admin).
     */
    public function update(Request $request, MeetingBooking $meeting_booking)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,read,confirmed,replied,closed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting_booking->update($request->only(['status', 'admin_notes']));

        if ($request->status === 'replied') {
            $meeting_booking->markAsReplied();
        }

        return response()->json([
            'success' => true,
            'message' => 'Meeting booking updated successfully',
            'data' => $meeting_booking,
        ]);
    }

    /**
     * Delete a meeting booking (admin).
     */
    public function destroy(MeetingBooking $meeting_booking)
    {
        $meeting_booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meeting booking deleted successfully',
        ]);
    }

    /**
     * Statistics for dashboard (admin).
     */
    public function statistics()
    {
        $stats = [
            'total' => MeetingBooking::count(),
            'new' => MeetingBooking::new()->count(),
            'read' => MeetingBooking::read()->count(),
            'confirmed' => MeetingBooking::confirmed()->count(),
            'replied' => MeetingBooking::replied()->count(),
            'closed' => MeetingBooking::closed()->count(),
            'today' => MeetingBooking::whereDate('created_at', today())->count(),
            'this_week' => MeetingBooking::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => MeetingBooking::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
