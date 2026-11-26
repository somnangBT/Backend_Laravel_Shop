<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Support\TelegramNotifier;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Display the messages list
    public function index()
    {
        $messages = Message::orderBy('id', 'desc')->get();
        return view('messages.index', compact('messages'));
    }

    // Show the form for composing a new message
    public function create()
    {
        return view('messages.create');
    }

    // Handle form submission
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Save the data to the database
        Message::create($request->only(['to', 'subject', 'message']));

        // Redirect to the messages list page
        return redirect()->route('messages.index')->with('success', 'Message sent successfully!');
    }

    /**
     * API: Store customer Contact Us message & notify Admin Telegram
     */
    public function storeContactApi(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:180',
            'message' => 'required|string|max:3000',
        ]);

        $subject = trim($validated['subject'] ?? '') ?: 'សារទំនាក់ទំនងពីអតិថិជន';
        $saved = Message::create([
            'to' => strtolower(trim($validated['email'])),
            'subject' => '[Contact] ' . $subject . ' (' . trim($validated['name']) . ')',
            'message' => trim($validated['message']),
        ]);

        TelegramNotifier::sendText(
            "📩 សារទំនាក់ទំនងថ្មីពី LuckShop\n"
            . "👤 ឈ្មោះ: " . trim($validated['name']) . "\n"
            . "📧 អ៊ីមែល: " . trim($validated['email']) . "\n"
            . "📌 ប្រធានបទ: {$subject}\n"
            . "💬 សារ:\n" . trim($validated['message'])
        );

        return response()->json([
            'status' => 'success',
            'message' => 'សាររបស់អ្នកត្រូវបានផ្ញើដោយជោគជ័យ!',
            'data' => ['id' => $saved->id],
        ], 201);
    }

    /**
     * API: Subscribe email to promotions / newsletter
     */
    public function subscribeNewsletterApi(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($validated['email']));
        $subject = '[Newsletter] ការចុះឈ្មោះទទួលប្រូម៉ូសិន';

        $record = Message::firstOrCreate(
            ['to' => $email, 'subject' => $subject],
            ['message' => "អ៊ីមែល {$email} បានចុះឈ្មោះទទួលព័ត៌មានប្រូម៉ូសិនពី Footer ។"]
        );

        if ($record->wasRecentlyCreated) {
            TelegramNotifier::sendText(
                "🔔 មានអ្នកចុះឈ្មោះទទួលប្រូម៉ូសិនថ្មី!\n📧 អ៊ីមែល: {$email}"
            );
        }

        return response()->json([
            'status' => 'success',
            'already_subscribed' => !$record->wasRecentlyCreated,
            'message' => $record->wasRecentlyCreated
                ? 'បានចុះឈ្មោះទទួលព័ត៌មានប្រូម៉ូសិនដោយជោគជ័យ!'
                : 'អ៊ីមែលនេះបានចុះឈ្មោះរួចរាល់ហើយ!',
        ]);
    }
}