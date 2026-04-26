<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Display the about page
     */
    public function about()
    {
        return view('landing.about');
    }

    /**
     * Display the programs page
     */
    public function programs()
    {
        return view('landing.programs');
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        return view('landing.contact');
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Here you would typically send an email or save to database
        // For now, we'll just redirect back with success message

        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
