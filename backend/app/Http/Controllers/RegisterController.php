<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email',
            'password'    => 'required|string|min:6',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:255',
            'yearOfbirth' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra email đã tồn tại chưa
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'errors' => ['email' => ['Email đã tồn tại trong hệ thống']]
            ], 409);
        }

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'phone'       => $request->phone,
            'address'     => $request->address,
            'yearOfbirth' => $request->yearOfbirth,
            'role'        => 'user'
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => $user
        ], 201);
    }
}
