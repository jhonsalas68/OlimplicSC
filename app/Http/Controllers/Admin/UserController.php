<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('username', 'ilike', '%' . $request->search . '%');
        }

        $users = $query->with('roles')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'SuperAdmin')->get();
        $categories = Category::orderBy('edad_min')->get();
        return view('admin.users.create', compact('roles', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'apellido_paterno'=> 'required|string|max:255',
            'apellido_materno'=> 'nullable|string|max:255',
            'ci'              => 'required|string|max:20|unique:users,ci',
            'email'           => 'required|email|unique:users,email',
            'is_active'       => 'boolean',
            'role'            => 'required|string|exists:roles,name|not_in:SuperAdmin',
            'category_id'     => 'nullable|exists:categories,id',
        ]);

        $initials = collect([$validated['name'], $validated['apellido_paterno'], $validated['apellido_materno']])
            ->filter()
            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        $username = $initials;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $initials . $counter++;
        }

        $user = User::create([
            'name'             => $validated['name'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'username'         => $username,
            'email'            => $validated['email'],
            'ci'               => $validated['ci'],
            'password'         => Hash::make($validated['ci']),
            'is_active'        => $request->boolean('is_active', true),
            'category_id'      => $validated['category_id'] ?? null,
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', "Usuario creado. Username: $username | Clave inicial: " . $validated['ci']);
    }

    public function edit(User $user)
    {
        if ($user->hasRole('SuperAdmin')) {
            return back()->with('error', 'La cuenta de SuperAdmin es intocable y no puede ser editada.');
        }

        $roles = Role::where('name', '!=', 'SuperAdmin')->get();
        $categories = Category::orderBy('edad_min')->get();
        return view('admin.users.edit', compact('user', 'roles', 'categories'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->hasRole('SuperAdmin')) {
            return back()->with('error', 'La cuenta de SuperAdmin es intocable y no puede ser editada.');
        }

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'ci'               => 'required|string|max:20|unique:users,ci,' . $user->id,
            'password'         => 'nullable|string|min:6|confirmed',
            'role'             => 'required|string|exists:roles,name|not_in:SuperAdmin',
            'category_id'      => 'nullable|exists:categories,id',
        ]);

        $userData = [
            'name'             => $validated['name'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'email'            => $validated['email'],
            'ci'               => $validated['ci'],
            'is_active'        => $request->has('is_active'),
            'category_id'      => $validated['category_id'] ?? null,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('SuperAdmin')) {
            return back()->with('error', 'La cuenta de SuperAdmin es intocable y no puede ser eliminada.');
        }
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}
