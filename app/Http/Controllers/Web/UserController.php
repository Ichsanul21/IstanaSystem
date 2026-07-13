<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('branch')
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $branches = Branch::where('is_active', true)->get();
        $roles = Role::all();

        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $roles = \Spatie\Permission\Models\Role::all();

        return view('users.create', compact('branches', 'roles'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'branch_id' => $request->branch_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $branches = Branch::where('is_active', true)->get();
        $roles = \Spatie\Permission\Models\Role::all();

        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        if ($user->is_protected) {
            return back()->withErrors(['user' => 'Protected users cannot be deactivated.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus');
    }
}
