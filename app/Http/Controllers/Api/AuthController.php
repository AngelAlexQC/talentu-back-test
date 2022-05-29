<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  /**
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request)
  {
    $credentials = $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (!$token = auth()->attempt($credentials)) {
      throw new AuthenticationException('Invalid credentials.');
    }

    $user = User::whereEmail($request->email)->firstOrFail();

    $token = $user->createToken('auth-token');

    return response()->json([
      'token' => $token->plainTextToken,
    ]);
  }

  /**
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function register(Request $request)
  {
    $credentials = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $token = $user->createToken('auth-token');

    return response()->json([
      'token' => $token->plainTextToken,
    ], 201);
  }

  /**
   * Get the authenticated User.
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function user(Request $request)
  {
    return response()->json($request->user());
  }
}
