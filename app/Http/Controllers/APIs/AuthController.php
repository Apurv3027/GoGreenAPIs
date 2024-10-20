<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helper\Helper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function registerUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fullname' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'mobile_number' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                    ],
                    302,
                );
            }

            // $userData = User::create($request->toarray());
            $userData = User::addUser($request);

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'created successfully.',
                    'data' => $userData,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ],
                302,
            );
        }
    }

    public function loginUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                    ],
                    400,
                );
            }

            // Attempt to log the user in
            $credentials = [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('authToken')->plainTextToken;

                // Store the token in the remember_token field
                $user->remember_token = $token;
                $user->save();

                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Login successful',
                        'data' => [
                            'user' => $user,
                            'token' => $token,
                        ],
                    ],
                    200,
                );
            } else {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Invalid login credentials',
                    ],
                    401,
                );
            }
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
