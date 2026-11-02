<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Display the settings page
    public function index()
    {
        // Fetch the first settings record or create a default one
        $settings = Setting::firstOrCreate([], [
            'email_notifications' => true,
            'sms_notifications' => true,
            'push_notifications' => true,
            'dark_mode' => false,
            'compact_mode' => false,
            'color_theme' => 'cyan',
        ]);

        return view('settings.index', compact('settings'));
    }

    // Handle form submission
    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'dark_mode' => 'nullable|boolean',
            'compact_mode' => 'nullable|boolean',
            'color_theme' => 'required|string|in:cyan,purple,emerald,amber,rose,indigo',
        ]);

        // Fetch the first settings record or create a default one
        $settings = Setting::firstOrCreate([]);

        // Update the settings
        $settings->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'dark_mode' => $request->boolean('dark_mode'),
            'compact_mode' => $request->boolean('compact_mode'),
            'color_theme' => $validated['color_theme'] ?? 'cyan',
        ]);

        \Illuminate\Support\Facades\Cache::forget('admin_settings');

        // Redirect back with a success message
        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }

    // Quick toggle for theme or dark mode via navbar or async switch
    public function toggleTheme(Request $request)
    {
        $settings = Setting::firstOrCreate([], [
            'email_notifications' => true,
            'sms_notifications' => true,
            'push_notifications' => true,
            'dark_mode' => false,
            'compact_mode' => false,
            'color_theme' => 'cyan',
        ]);

        if ($request->has('dark_mode')) {
            $settings->dark_mode = $request->boolean('dark_mode');
        }

        if ($request->filled('color_theme') && in_array($request->input('color_theme'), ['cyan', 'purple', 'emerald', 'amber', 'rose', 'indigo'])) {
            $settings->color_theme = $request->input('color_theme');
        }

        $settings->save();
        \Illuminate\Support\Facades\Cache::forget('admin_settings');

        return response()->json([
            'status' => 'success',
            'dark_mode' => (bool)$settings->dark_mode,
            'color_theme' => $settings->color_theme,
        ]);
    }
}
