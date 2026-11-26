<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Support\TelegramNotifier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

    // API login method
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
    
            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
        }
    
        return response()->json([
            'message' => 'Invalid credentials.',
        ], 401);
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

    // API register method
    public function apiRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role for API registration
        ]);

        // Log the user in and create a token
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
            ],
        ]);
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
