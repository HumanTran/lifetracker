<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserController
{
    private function fail(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return back()->with('error', $message);
    }

    private function activeAdminCount(): int
    {
        return User::where('role', 'admin')->where('status', 'active')->count();
    }

    public function index(Request $request)
    {
        $query = User::query();
        $search = trim((string) $request->query('search', ''));
        $role = $request->query('role');
        $status = $request->query('status');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (in_array($role, ['admin', 'user'], true)) {
            $query->where('role', $role);
        }

        if (in_array($status, ['active', 'banned'], true)) {
            $query->where('status', $status);
        }

        $requestedPerPage = (int) $request->query('per_page', 8);
        $perPage = in_array($requestedPerPage, [5, 8], true) ? $requestedPerPage : 8;

        $users = $query->orderBy('id', 'desc')
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => Str::slug($validated['name'], '-').'_'.time().'@admin.local',
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'status' => 'active',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,banned',
        ]);

        $user = User::findOrFail($id);
        $removesActiveAdmin = ($user->role ?? null) === 'admin'
            && ($user->status ?? null) === 'active'
            && ($validated['role'] !== 'admin' || $validated['status'] !== 'active');

        if ($user->id === Auth::id() && ($validated['role'] !== 'admin' || $validated['status'] !== 'active')) {
            return $this->fail($request, 'Không thể tự hạ quyền hoặc khóa tài khoản đang đăng nhập.');
        }

        if ($removesActiveAdmin && $this->activeAdminCount() <= 1) {
            return $this->fail($request, 'Cần giữ lại ít nhất một tài khoản admin đang hoạt động.');
        }

        $user->update([
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return $this->fail($request, 'Không thể xóa tài khoản đang đăng nhập.');
        }

        if (($user->role ?? null) === 'admin' && ($user->status ?? null) === 'active' && $this->activeAdminCount() <= 1) {
            return $this->fail($request, 'Cần giữ lại ít nhất một tài khoản admin đang hoạt động.');
        }

        $deletedId = $user->id;
        $user->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted_id' => $deletedId,
            ]);
        }

        return back();
    }
}
