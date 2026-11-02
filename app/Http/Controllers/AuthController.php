<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        ]);
    }

    

    public function getAllUsers()
{
    // Select only the columns you want
    $users = User::select('id', 'name', 'email', 'role', 'email_verified_at', 'created_at', 'updated_at')->get();

    return response()->json($users);
}

public function addUser(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'sometimes|in:admin,user',
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



public function deleteUser($id)
{
    $user = \App\Models\User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    $user->delete();

    return response()->json([
        'message' => 'User deleted successfully'
    ]);
}


public function updateUser(Request $request, $id)
{
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
    $user->role = $request->role ?? $user->role;

    if ($request->filled('password')) {
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
    }

    $user->save();

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user
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

}
