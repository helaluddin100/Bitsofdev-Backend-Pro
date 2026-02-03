<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingBooking;
use Illuminate\Http\Request;

class MeetingBookingController extends Controller
{
    /**
     * Display a listing of meeting bookings.
     */
    public function index(Request $request)
    {
        $query = MeetingBooking::query();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
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
        $stats = $this->getStatistics();

        return view('admin.meeting-bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Display the specified meeting booking.
     */
    public function show(MeetingBooking $meeting_booking)
    {
        if ($meeting_booking->status === 'new') {
            $meeting_booking->markAsRead();
        }

        return view('admin.meeting-bookings.show', compact('meeting_booking'));
    }

    /**
     * Show the form for editing the specified meeting booking.
     */
    public function edit(MeetingBooking $meeting_booking)
    {
        return view('admin.meeting-bookings.edit', compact('meeting_booking'));
    }

    /**
     * Update the specified meeting booking (status, admin_notes).
     */
    public function update(Request $request, MeetingBooking $meeting_booking)
    {
        $request->validate([
            'status' => 'required|in:new,read,confirmed,replied,closed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $meeting_booking->update($request->only(['status', 'admin_notes']));

        if ($request->status === 'replied') {
            $meeting_booking->markAsReplied();
        }

        return redirect()->route('admin.meeting-bookings.show', $meeting_booking)
            ->with('success', 'Meeting booking updated successfully.');
    }

    /**
     * Remove the specified meeting booking.
     */
    public function destroy(MeetingBooking $meeting_booking)
    {
        $meeting_booking->delete();

        return redirect()->route('admin.meeting-bookings.index')
            ->with('success', 'Meeting booking deleted successfully.');
    }

    private function getStatistics()
    {
        return [
            'total' => MeetingBooking::count(),
            'new' => MeetingBooking::new()->count(),
            'read' => MeetingBooking::read()->count(),
            'confirmed' => MeetingBooking::confirmed()->count(),
            'replied' => MeetingBooking::replied()->count(),
            'closed' => MeetingBooking::closed()->count(),
            'today' => MeetingBooking::whereDate('created_at', today())->count(),
        ];
    }
}
