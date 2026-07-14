<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Email atau password salah', null, 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $branch = null;
        if ($user->branch_id) {
            $branch = Branch::find($user->branch_id);
        }

        return ApiResponse::success([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? 'user',
                'branch_id' => $user->branch_id,
                'branch' => $branch ? ['id' => $branch->id, 'name' => $branch->name] : null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Berhasil logout');
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('roles', 'branches');

        $branch = null;
        if ($user->branch_id) {
            $branch = Branch::find($user->branch_id);
        }

        return ApiResponse::success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'photo' => $user->photo ? Storage::url($user->photo) : null,
            'role' => $user->roles->first()?->name ?? 'user',
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'branch' => $branch ? ['id' => $branch->id, 'name' => $branch->name] : null,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('photos', 'public');
        }

        $user->save();

        return ApiResponse::success(null, 'Profil berhasil diperbarui');
    }
}
