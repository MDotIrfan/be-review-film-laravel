<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\User;
use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $roleuser = Roles::where('name', 'user')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleuser->id,
        ]);

        $user->generateOtpCode();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Register Berhasil',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function generateOtpCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userData = User::where('email', $request->email)->first();

        $userData->generateOtpCode();

        return response()->json([
            "message" => "Berhasil generate ulang OTP code",
            "data" => $userData
        ]);
    }

    public function verifikasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = OtpCode::where("otp", $request->otp)->first();
        $now = Carbon::now();

        if (!$otp) {
            return response()->json([
                "message" => "OTP tidak ditemukan",
            ], 404);
        } else if ($now > $otp->valid_until) {
            return response()->json([
                "message" => "OTP sudah kedaluwarsa",
            ], 400);
        }

        $user = User::find($otp->user_id);
        $user->email_verified_at = $now;

        $otp->delete();
        $user->save();

        return response()->json([
            "message" => "Berhasil verifikasi akun"
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'User Invalid'], 401);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            // 'message' => 'User Berhasil Login',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Logout Berhasil']);
    }

    public function me()
    {
        $user = User::with('roles', 'profile')->find(Auth::guard('api')->id());

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'Berhasil Get User',
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {

        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user_id = auth()->user()->id;

        $user = User::find($user_id);

        //mengupdate data genre
        $user->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Berhasil Update user',
        ], 200);

    }
}
