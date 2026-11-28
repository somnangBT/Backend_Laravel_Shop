<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Support\TelegramNotifier;
use App\Support\VerificationDispatcher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Web login method - Admin only
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $allowedStaff = ['admin', 'manager', 'cashier', 'kitchen'];
            if (!in_array(Auth::user()->role, $allowedStaff)) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. Staff privileges required.',
                ]);
            }
            
            $request->session()->regenerate();

            // Redirect based on staff role
            if (Auth::user()->role === 'kitchen') {
                return redirect()->intended('/orders/kitchen')->with('success', 'Welcome to Kitchen Display System');
            }

            return redirect()->intended('/dashboard')->with('success', 'Login successful');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials. Please try again.',
        ]);
    }

    // Web logout method (unchanged)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Logged out successfully');
    }

    // Web register method (unchanged)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Registration successful');
    }

    // API login method (supports Email or Phone Number)
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'password' => 'required|string',
        ]);

        $rawLogin = trim((string) ($request->input('contact') ?? $request->input('email') ?? ''));
        if ($rawLogin === '') {
            return response()->json(['message' => 'Email or phone number is required.'], 422);
        }

        $parsed = VerificationDispatcher::normalizeContact($rawLogin);
        $user = null;

        if ($parsed['type'] === 'email') {
            $user = User::where('email', $parsed['value'])->first();
        } elseif ($parsed['type'] === 'phone' && Schema::hasColumn('users', 'phone')) {
            $user = User::where('phone', $parsed['value'])
                ->orWhere('phone', $rawLogin)
                ->first();
        } else {
            $user = User::where('email', strtolower($rawLogin))->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        $lastOrder = \App\Models\Order::where('user_id', $user->id)
            ->whereNotNull('shipping_address')
            ->where('shipping_address', '!=', '')
            ->orderByDesc('id')
            ->first(['customer_name', 'phone', 'shipping_address', 'payment_method']);

        $resolvedPhone = Schema::hasColumn('users', 'phone')
            ? ($user->phone ?: ($lastOrder->phone ?? null))
            : ($lastOrder->phone ?? null);

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $resolvedPhone,
                'address' => $lastOrder->shipping_address ?? null,
                'saved_delivery' => $lastOrder ? [
                    'customer_name' => $lastOrder->customer_name ?: $user->name,
                    'phone' => $resolvedPhone,
                    'shipping_address' => $lastOrder->shipping_address,
                    'payment_method' => $lastOrder->payment_method,
                ] : null,
                'role' => $user->role,
            ],
        ]);
    }

    // Report which registration OTP channels (Gmail SMTP / SMS Gateway) are configured
    public function apiVerificationChannels()
    {
        return response()->json([
            'status' => 'success',
            'email_ready' => VerificationDispatcher::isEmailMailerConfigured(),
            'sms_ready' => VerificationDispatcher::isSmsConfigured(),
            'sms_provider' => config('services.sms.provider', 'none'),
        ]);
    }

    // API logout method
    public function apiLogout(Request $request)
    {
        $request->user()->tokens->each(function ($token) {
            $token->delete(); // Revoke all tokens
        });

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    // Send 6-digit registration verification OTP to Email (Gmail) or Phone (SMS)
    public function apiSendRegisterCode(Request $request)
    {
        $rawContact = trim((string) ($request->input('contact') ?? $request->input('email') ?? ''));
        $contact = VerificationDispatcher::normalizeContact($rawContact);

        if ($contact['type'] === 'invalid') {
            return response()->json([
                'status' => 'error',
                'message' => 'សូមបញ្ចូលអ៊ីមែល Gmail ឬ លេខទូរស័ព្ទឱ្យបានត្រឹមត្រូវ។',
            ], 422);
        }

        if ($contact['type'] === 'email') {
            if (User::where('email', $contact['value'])->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'អ៊ីមែលនេះមានគណនីរួចហើយ។ សូមចូលប្រើប្រាស់ ឬប្រើអ៊ីមែលផ្សេង។',
                ], 422);
            }

            if (!VerificationDispatcher::isEmailMailerConfigured()) {
                return response()->json([
                    'status' => 'error',
                    'not_configured' => true,
                    'contact_type' => 'email',
                    'message' => 'មិនទាន់បានកំណត់ Gmail SMTP លើ Server ទេ — សូមបន្ថែម MAIL_MAILER=smtp និង Gmail App Password ក្នុង Vercel Env Vars ជាមុន។',
                ], 503);
            }
        }

        if ($contact['type'] === 'phone') {
            if (!Schema::hasColumn('users', 'phone')) {
                return response()->json([
                    'status' => 'error',
                    'not_configured' => true,
                    'contact_type' => 'phone',
                    'message' => 'Database មិនទាន់បានបន្ថែមប្រព័ន្ធលេខទូរស័ព្ទ (Migration) ទេ។ សូមចុះឈ្មោះតាម Gmail សិន!',
                ], 503);
            }

            if (User::where('phone', $contact['value'])->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'លេខទូរស័ព្ទនេះមានគណនីរួចហើយ។',
                ], 422);
            }

            if (!VerificationDispatcher::isSmsConfigured()) {
                return response()->json([
                    'status' => 'error',
                    'not_configured' => true,
                    'contact_type' => 'phone',
                    'message' => 'សេវាកម្មផ្ញើសារ SMS ទៅទូរស័ព្ទមិនទាន់បានភ្ជាប់ API Key (PlasGate / MekongSMS / Twilio) នៅឡើយទេ។ សូមប្រើ Gmail ដើម្បីចុះឈ្មោះសិន!',
                ], 503);
            }
        }

        $tokenKey = 'reg:' . $contact['value'];
        $existing = DB::table('password_reset_tokens')->where('email', $tokenKey)->first();

        if ($existing && !empty($existing->created_at) && Carbon::parse($existing->created_at)->addSeconds(60)->isFuture()) {
            return response()->json([
                'status' => 'error',
                'message' => 'សូមរង់ចាំ ១ នាទី មុននឹងស្នើសុំលេខកូដថ្មីម្ដងទៀត។',
            ], 429);
        }

        $code = (string) random_int(100000, 999999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $tokenKey],
            ['token' => Hash::make($code), 'created_at' => Carbon::now()]
        );

        $dispatch = VerificationDispatcher::sendCode($contact['type'], $contact['value'], $code, 'register');
        if (!$dispatch['ok']) {
            DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();
            return response()->json([
                'status' => 'error',
                'not_configured' => !$dispatch['configured'],
                'contact_type' => $contact['type'],
                'message' => $dispatch['error'] ?? 'មិនអាចផ្ញើលេខកូដផ្ទៀងផ្ទាត់បានទេ។',
            ], $dispatch['configured'] ? 502 : 503);
        }

        return response()->json([
            'status' => 'success',
            'contact_type' => $contact['type'],
            'contact' => $contact['value'],
            'message' => $contact['type'] === 'email'
                ? "លេខកូដ ៦ ខ្ទង់ត្រូវបានផ្ញើទៅ Gmail ({$contact['value']}) រួចរាល់! សូមពិនិត្យ Inbox ឬ Spam ។"
                : "លេខកូដ ៦ ខ្ទង់ត្រូវបានផ្ញើជាសារ SMS ទៅលេខ {$contact['value']} រួចរាល់!",
        ]);
    }

    // API register method (verifies 6-digit OTP sent to Gmail or Phone SMS)
    public function apiRegister(Request $request)
    {
        $rawContact = trim((string) ($request->input('contact') ?? $request->input('email') ?? ''));
        $contact = VerificationDispatcher::normalizeContact($rawContact);

        $validator = Validator::make(array_merge($request->all(), ['contact' => $rawContact]), [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'code' => 'required|string|size:6',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($contact['type'] === 'invalid') {
            return response()->json([
                'message' => 'សូមបញ្ចូលអ៊ីមែល Gmail ឬ លេខទូរស័ព្ទឱ្យបានត្រឹមត្រូវ។',
            ], 422);
        }

        $tokenKey = 'reg:' . $contact['value'];
        $record = DB::table('password_reset_tokens')->where('email', $tokenKey)->first();

        if (
            !$record ||
            empty($record->created_at) ||
            Carbon::parse($record->created_at)->addMinutes(15)->isPast() ||
            !Hash::check(trim((string) $request->code), $record->token)
        ) {
            return response()->json([
                'message' => 'លេខកូដផ្ទៀងផ្ទាត់មិនត្រឹមត្រូវ ឬផុតកំណត់ហើយ។ សូមស្នើសុំកូដថ្មី។',
            ], 422);
        }

        if ($contact['type'] === 'email' && User::where('email', $contact['value'])->exists()) {
            return response()->json([
                'message' => 'អ៊ីមែលនេះមានគណនីរួចហើយ។',
            ], 422);
        }

        if ($contact['type'] === 'phone') {
            if (!Schema::hasColumn('users', 'phone')) {
                return response()->json([
                    'message' => 'Database មិនទាន់បានបន្ថែមប្រព័ន្ធលេខទូរស័ព្ទ (Migration) ទេ។',
                ], 503);
            }
            if (User::where('phone', $contact['value'])->exists()) {
                return response()->json([
                    'message' => 'លេខទូរស័ព្ទនេះមានគណនីរួចហើយ។',
                ], 422);
            }
        }

        $userData = [
            'name' => trim($request->name),
            'email' => $contact['type'] === 'email' ? $contact['value'] : null,
            'email_verified_at' => $contact['type'] === 'email' ? Carbon::now() : null,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ];
        if ($contact['type'] === 'phone' && Schema::hasColumn('users', 'phone')) {
            $userData['phone'] = $contact['value'];
        }

        $user = User::create($userData);
        DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => Schema::hasColumn('users', 'phone') ? ($user->phone ?? null) : null,
                'role' => $user->role ?? 'user',
            ],
        ], 201);
    }

    public function getAllUsers(Request $request)
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = User::select('id', 'name', 'email', 'role', 'email_verified_at', 'created_at', 'updated_at')->get();

        return response()->json($users);
    }

    public function addUser(Request $request)
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'sometimes|in:admin,cashier,kitchen,user',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
        ]);

        return response()->json([
            'message' => 'User added successfully',
            'user' => $user,
        ], 201);
    }

    public function deleteUser(Request $request, $id)
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = \App\Models\User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ((int) $request->user()->id === (int) $user->id) {
            return response()->json(['message' => 'Cannot delete your own account'], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $authUser = $request->user();
        if (!$authUser || (!$authUser->isAdmin() && (int) $authUser->id !== (int) $id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = \App\Models\User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|min:6',
            'role' => 'sometimes|in:admin,cashier,kitchen,user',
        ]);

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;

        // Only admins may change roles (prevents privilege escalation)
        if ($authUser->isAdmin() && $request->filled('role')) {
            $user->role = $request->role;
        }

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Display User & Role Management list for Web Admin
     */
    public function userIndex(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->input('role') !== 'all') {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('id', 'desc')->paginate(15);
        $roleCounts = [
            'admin' => User::where('role', 'admin')->count(),
            'cashier' => User::where('role', 'cashier')->count(),
            'kitchen' => User::where('role', 'kitchen')->count(),
            'user' => User::where('role', 'user')->count(),
            'all' => User::count(),
        ];

        return view('users.index', compact('users', 'roleCounts'));
    }

    /**
     * Update a user's role from Web Admin
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,cashier,kitchen,user',
        ]);

        if (Auth::id() === $user->id && $request->role !== 'admin') {
            return back()->with('error', 'You cannot change your own admin role.');
        }

        $user->update(['role' => $request->role]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Role updated to {$user->role} successfully.",
                'user' => $user
            ]);
        }

        return back()->with('success', "Role for {$user->name} has been updated to {$user->role}.");
    }

    /**
     * API: Request a 6-digit password reset code
     */
    public function apiForgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($validated['email']));
        $existing = DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($existing && !empty($existing->created_at) && Carbon::parse($existing->created_at)->addSeconds(60)->isFuture()) {
            return response()->json([
                'status' => 'error',
                'message' => 'សូមរង់ចាំ ១ នាទី មុននឹងស្នើសុំលេខកូដម្ដងទៀត។',
            ], 429);
        }

        $mailer = config('mail.default');
        $canSendEmail = !in_array($mailer, ['log', 'array', null, ''], true);

        $user = User::where('email', $email)->first();
        if ($user) {
            $code = (string) random_int(100000, 999999);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => Hash::make($code), 'created_at' => Carbon::now()]
            );

            if ($canSendEmail) {
                try {
                    Mail::raw(
                        "LuckShop Password Reset Code: {$code}\n\nThis code expires in 15 minutes.",
                        function ($msg) use ($email) {
                            $msg->to($email)->subject('LuckShop — Password Reset Code');
                        }
                    );
                } catch (\Exception $e) {
                    Log::warning('Password reset mail failed: ' . $e->getMessage());
                    $canSendEmail = false;
                }
            }

            TelegramNotifier::sendText(
                "🔑 សំណើសុំកំណត់ពាក្យសម្ងាត់ថ្មី (LuckShop)\n"
                . "👤 អ្នកប្រើ: {$user->name}\n"
                . "📧 អ៊ីមែល: {$email}\n"
                . "🔢 លេខកូដ OTP: {$code}\n"
                . "⏳ សុពលភាព: ១៥ នាទី"
            );
        }

        return response()->json([
            'status' => 'success',
            'channel' => $canSendEmail ? 'email' : 'telegram_support',
            'message' => $canSendEmail
                ? 'ប្រសិនបើអ៊ីមែលនេះមានក្នុងប្រព័ន្ធ លេខកូដ ៦ ខ្ទង់ត្រូវបានផ្ញើទៅអ៊ីមែលរបស់អ្នកហើយ។'
                : 'សំណើកូដ ៦ ខ្ទង់ត្រូវបានបញ្ជូនទៅ Telegram អ្នកគ្រប់គ្រងហាងហើយ — សូមទាក់ទងហាងដើម្បីទទួលលេខកូដបញ្ចូលខាងក្រោម។',
        ]);
    }

    /**
     * API: Reset password using 6-digit verification code
     */
    public function apiResetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = strtolower(trim($validated['email']));
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (
            !$record ||
            empty($record->created_at) ||
            Carbon::parse($record->created_at)->addMinutes(15)->isPast() ||
            !Hash::check($validated['code'], $record->token)
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'លេខកូដមិនត្រឹមត្រូវ ឬផុតកំណត់ហើយ។ សូមស្នើសុំកូដថ្មី។',
            ], 422);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'រកមិនឃើញគណនីនេះទេ។',
            ], 404);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'បានកំណត់ពាក្យសម្ងាត់ថ្មីជោគជ័យ! សូមចូលគណនីម្ដងទៀត។',
        ]);
    }

    /**
     * API: Change password for authenticated user
     */
    public function apiChangePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::guard('sanctum')->user() ?? Auth::user();
        if (!$user || !Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'ពាក្យសម្ងាត់បច្ចុប្បន្នមិនត្រឹមត្រូវទេ។',
            ], 422);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'បានផ្លាស់ប្តូរពាក្យសម្ងាត់ជោគជ័យ!',
        ]);
    }

}
