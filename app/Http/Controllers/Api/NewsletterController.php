<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Subscribe email to newsletter (public).
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid email address.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;

        try {
            $existing = NewsletterSubscriber::where('email', $email)->first();

            if ($existing) {
                if ($existing->status === 'subscribed') {
                    return response()->json([
                        'success' => true,
                        'message' => 'You are already subscribed to our newsletter.',
                    ], 200);
                }
                $existing->update(['status' => 'subscribed']);
                return response()->json([
                    'success' => true,
                    'message' => 'You have been resubscribed to our newsletter. Thank you!',
                ], 200);
            }

            NewsletterSubscriber::create([
                'email' => $email,
                'status' => 'subscribed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing! You will receive our latest updates and insights.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
