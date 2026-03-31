<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletters.
     */
    public function index(Request $request)
    {
        $query = Newsletter::query()->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->search) {
            $query->where('subject', 'like', "%{$request->search}%");
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $newsletters = $query->paginate(25);

        // Statistics
        $stats = [
            'total' => Newsletter::count(),
            'draft' => Newsletter::where('status', 'draft')->count(),
            'sent' => Newsletter::where('status', 'sent')->count(),
            'scheduled' => Newsletter::where('status', 'scheduled')->count(),
            'failed' => Newsletter::where('status', 'failed')->count(),
        ];

        return view('admin.marketing.newsletters.index', compact('newsletters', 'stats'));
    }

    /**
     * Show the form for creating a new newsletter.
     */
    public function create()
    {
        return view('admin.marketing.newsletters.create');
    }

    /**
     * Store a newly created newsletter.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients_type' => 'required|in:all,subscribers,users',
        ]);

        $newsletter = Newsletter::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'recipients_type' => $request->recipients_type,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.marketing.newsletters.index')
            ->with('success', 'Newsletter created successfully.');
    }

    /**
     * Show the form for editing a newsletter.
     */
    public function edit(Newsletter $newsletter)
    {
        return view('admin.marketing.newsletters.edit', compact('newsletter'));
    }

    /**
     * Update the specified newsletter.
     */
    public function update(Request $request, Newsletter $newsletter)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients_type' => 'required|in:all,subscribers,users',
        ]);

        $newsletter->update([
            'subject' => $request->subject,
            'content' => $request->content,
            'recipients_type' => $request->recipients_type,
        ]);

        return redirect()->route('admin.marketing.newsletters.index')
            ->with('success', 'Newsletter updated successfully.');
    }

    /**
     * Remove the specified newsletter.
     */
    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return redirect()->route('admin.marketing.newsletters.index')
            ->with('success', 'Newsletter deleted successfully.');
    }

    /**
     * Send the newsletter to recipients.
     */
    public function send(Request $request, Newsletter $newsletter)
    {
        $request->validate([
            'recipients_type' => 'required|in:all,subscribers,users',
        ]);

        // Get recipients based on type
        $recipients = $this->getRecipients($request->recipients_type);

        if ($recipients->isEmpty()) {
            return redirect()->route('admin.marketing.newsletters.index')
                ->with('error', 'No recipients found for the selected type.');
        }

        // Update newsletter recipients type
        $newsletter->update(['recipients_type' => $request->recipients_type]);

        // Send emails
        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipient) {
            try {
                Mail::send([], [], function ($message) use ($recipient, $newsletter) {
                    $message->to($recipient->email)
                        ->subject($newsletter->subject)
                        ->html($newsletter->content);
                });
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send newsletter email: ' . $e->getMessage());
                $failedCount++;
            }
        }

        // Update newsletter status
        $newsletter->update([
            'status' => $failedCount > 0 && $sentCount === 0 ? 'failed' : 'sent',
            'sent_at' => now(),
            'recipients_count' => $sentCount,
        ]);

        if ($sentCount > 0) {
            return redirect()->route('admin.marketing.newsletters.index')
                ->with('success', "Newsletter sent to {$sentCount} recipients." . ($failedCount > 0 ? " {$failedCount} failed." : ''));
        }

        return redirect()->route('admin.marketing.newsletters.index')
            ->with('error', 'Failed to send newsletter. Please try again.');
    }

    /**
     * Preview the newsletter.
     */
    public function preview(Newsletter $newsletter)
    {
        return view('admin.marketing.newsletters.preview', compact('newsletter'));
    }

    /**
     * Get recipients based on type.
     */
    private function getRecipients(string $type)
    {
        switch ($type) {
            case 'subscribers':
                return Subscriber::active()->get();
            case 'users':
                return User::where('role', 'customer')->where('status', 'active')->get();
            case 'all':
            default:
                // Get both subscribers and users
                $subscribers = Subscriber::active()->get()->map(function ($sub) {
                    return (object) ['email' => $sub->email, 'name' => $sub->name];
                });
                $users = User::where('role', 'customer')->where('status', 'active')->get()->map(function ($user) {
                    return (object) ['email' => $user->email, 'name' => $user->name];
                });
                return $subscribers->merge($users);
        }
    }

    /**
     * Get subscriber count by type (AJAX).
     */
    public function getRecipientCount(Request $request)
    {
        $type = $request->recipients_type ?? 'all';
        
        switch ($type) {
            case 'subscribers':
                $count = Subscriber::active()->count();
                break;
            case 'users':
                $count = User::where('role', 'customer')->where('status', 'active')->count();
                break;
            case 'all':
            default:
                $subscribersCount = Subscriber::active()->count();
                $usersCount = User::where('role', 'customer')->where('status', 'active')->count();
                $count = $subscribersCount + $usersCount;
                break;
        }

        return response()->json(['count' => $count]);
    }

    /**
     * Duplicate a newsletter.
     */
    public function duplicate(Newsletter $newsletter)
    {
        $newNewsletter = $newsletter->replicate();
        $newNewsletter->subject = $newsletter->subject . ' (Copy)';
        $newNewsletter->status = 'draft';
        $newNewsletter->sent_at = null;
        $newNewsletter->recipients_count = 0;
        $newNewsletter->created_by = auth()->id();
        $newNewsletter->save();

        return redirect()->route('admin.marketing.newsletters.index')
            ->with('success', 'Newsletter duplicated successfully.');
    }
}
