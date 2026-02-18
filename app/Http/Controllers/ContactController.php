<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Log the contact message (can be replaced with email/DB storage later)
        Log::info('Contact form submission', $validated);

        return redirect()->route('contact')
            ->with('success', 'Thank you for your message! We\'ll get back to you soon.');
    }
}
