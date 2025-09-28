<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('active', true)->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'in:user,admin',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'yearOfbirth' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        $validated['password'] = bcrypt($validated['password']);
        if (!isset($validated['role'])) {
            $validated['role'] = 'user';
        }
        $validated['active'] = true;

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy user'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy user'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:6',
            'role' => 'sometimes|in:user,admin',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'yearOfbirth' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy user'], 404);
        }
        $user->active = false;
        $user->save();
        return response()->json(['message' => 'Đã chuyển trạng thái khách hàng thành không hoạt động']);
    }
}
