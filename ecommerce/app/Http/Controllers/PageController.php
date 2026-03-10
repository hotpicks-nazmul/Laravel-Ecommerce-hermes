<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Faq;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    /**
     * Display about page.
     */
    public function about()
    {
        $page = Page::where('slug', 'about')->first();
        return view('themes.general.pages.about', compact('page'));
    }

    /**
     * Display contact page.
     */
    public function contact()
    {
        $settings = Setting::whereIn('key', ['contact_email', 'contact_phone', 'contact_address'])->pluck('value', 'key');
        return view('themes.general.pages.contact', compact('settings'));
    }

    /**
     * Send contact form.
     */
    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $contactEmail = Setting::where('key', 'contact_email')->value('value');

        if ($contactEmail) {
            Mail::raw($request->message, function ($mail) use ($request, $contactEmail) {
                $mail->to($contactEmail)
                    ->from($request->email, $request->name)
                    ->subject($request->subject);
            });
        }

        return back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }

    /**
     * Display FAQ page.
     */
    public function faq()
    {
        $page = Page::where('slug', 'faq')->first();
        $faqs = Faq::active()->ordered()->get();
        return view('themes.general.pages.faq', compact('page', 'faqs'));
    }

    /**
     * Display terms page.
     */
    public function terms()
    {
        $page = Page::where('slug', 'terms')->first();
        return view('themes.general.pages.terms', compact('page'));
    }

    /**
     * Display privacy page.
     */
    public function privacy()
    {
        $page = Page::where('slug', 'privacy')->first();
        return view('themes.general.pages.privacy', compact('page'));
    }

    /**
     * Display a dynamic page by slug.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->where('status', 'published')->firstOrFail();
        return view('themes.general.pages.show', compact('page'));
    }

    /**
     * Subscribe to newsletter.
     */
    public function newsletterSubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Store newsletter subscription in settings or a dedicated table
        // For now, we'll just return a success message
        // You can create a newsletter_subscribers table if needed

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
