<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     // Validate input
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     // Find user
    //     $user = User::where('email', $request->email)->first();

    //     // Check credentials
    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid email or password'
    //         ], 401);
    //     }

    //     // Create Sanctum token
    //     $token = $user->createToken('api-token')->plainTextToken;

    //     // Return response
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Login successful',
    //         'token' => $token,
    //         'user' => $user
    //     ], 200);
    // }
   public function login(Request $request)
{
    // Validate input
   $validated= $request->validate([
        'email' => 'required|email',
        'password' => 'required|string'
    ]);

    // Check credentials and generate JWT token
    
$token = auth('api')->attempt([
'email'=>$validated['email'],
'password'=>$validated['password'],
]);
    
    ///////////////OR/////////////////////////////////
//$token = auth('api')->attempt([
  //      'email' => $request->email,
    //    'password' => $request->password
    //]);


    // Invalid credentials
    if (!$token) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }
//"Get the user who has just been authenticated by the JWT token/guard, and store that user in $user."
    // Get authenticated user
    $user = auth('api')->user();

    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user
    ], 200);
}

    public function logout(Request $request)
{
       auth('api')->logout();

    return response()->json([
        'success' => true,
        'message' => 'Logout successful'
    ], 200);
}

///show current profile 
public function profile()
{
   // $user = $request->user();
$user = auth('api')->user();
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
        ]
    ], 200);
}

public function updateProfile(Request $request)
{
    $user = auth('api')->user();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
    ]);

    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
        ]
    ], 200);
}

//assign roles
public function assignRole(Request $request, $id)
{
    ///validate the data entered from postman/frontend
    $validated = $request->validate([
        'role' => 'required|in:admin,hod,employee',
    ]);
//check the previously role and store in user
    $user = User::findOrFail($id);
///do updation by change previously role to new role entered
   $user->update([
    'role' => $validated['role'],
]);

    return response()->json([
        'success' => true,
        'message' => 'User role updated successfully.',
        'data' => $user,
    ]);

//     if (! $user->is_active) {
//     return response()->json([
//         'success' => false,
//         'message' => 'Your account has been deactivated.'
//     ], 403);
// }
}

public function deactivate($id)
{
    $user = User::findOrFail($id);

    // Prevent admin from deactivating themselves
    if ($user->id === auth()->id()) {
        return response()->json([
            'message' => 'You cannot deactivate your own account.'
        ], 403);
    }

    $user->is_active = false;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User deactivated successfully.',
        'data' => $user,
    ]);
}

public function activate($id)
{
    $user = User::findOrFail($id);

    $user->is_active = true;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User activated successfully.',
        'data' => $user,
    ]);
}

public function users()
{
    $users = User::select('id', 'name', 'email', 'role', 'is_active')->get();

    return response()->json([
        'success' => true,
        'data' => $users
    ]);
}
}

;