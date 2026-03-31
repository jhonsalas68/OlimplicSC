<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Traits\CloudinaryHelper;

class UserController extends Controller
{
    use CloudinaryHelper;
    public function index(Request $request)
    {
        if (!$request->has('role_id') && !$request->has('search')) {
            $roles = Role::whereNotIn('name', ['Student'])->withCount('users')->get();
            return view('admin.users.roles', compact('roles'));
        }

        $query = User::query();

        if ($request->filled('role_id')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('id', $request->role_id);
            });
            $selectedRole = Role::find($request->role_id);
        } else {
            $selectedRole = null;
        }

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('username', 'ilike', '%' . $request->search . '%')
                  ->orWhere('email', 'ilike', '%' . $request->search . '%')
                  ->orWhere('ci', 'ilike', '%' . $request->search . '%');
        }

        $users = $query->with('roles')->latest()->paginate(10);
        return view('admin.users.index', compact('users', 'selectedRole'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('name', ['SuperAdmin', 'Student'])->get();
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
            'role'            => 'required|string|exists:roles,name|not_in:SuperAdmin,Student',
            'category_id'     => 'nullable|exists:categories,id',
            'avatar'          => 'nullable|image|max:2048',
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

        $avatarUrl = null;
        if ($request->hasFile('avatar')) {
            $response = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($request->file('avatar')->getRealPath(), [
                'folder' => 'avatars'
            ]);
            $avatarUrl = $response['secure_url'];
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
            'avatar'           => $avatarUrl,
        ]);

        $user->syncRoles([$validated['role']]);

        \App\Services\ActivityLogger::log(
            'creacion_usuario', 
            "Nuevo usuario creado: {$user->name} con el rol {$validated['role']}.",
            $user,
            ['rol' => $validated['role'], 'username' => $username]
        );

        return redirect()->route('users.index')->with('success', "Usuario creado. Username: $username | Clave inicial: " . $validated['ci']);
    }

    public function edit(User $user)
    {
        if ($user->hasRole('SuperAdmin')) {
            return back()->with('error', 'La cuenta de SuperAdmin es intocable y no puede ser editada.');
        }

        $roles = Role::whereNotIn('name', ['SuperAdmin', 'Student'])->get();
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
            'role'             => 'required|string|exists:roles,name|not_in:SuperAdmin,Student',
            'category_id'      => 'nullable|exists:categories,id',
            'avatar'           => 'nullable|image|max:2048',
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
        
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $this->deleteFromCloudinary($user->avatar);
            }
            $response = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($request->file('avatar')->getRealPath(), [
                'folder' => 'avatars'
            ]);
            $userData['avatar'] = $response['secure_url'];
        }

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);

        \App\Services\ActivityLogger::log(
            'edicion_usuario', 
            "Usuario actualizado: {$user->name}. Rol asignado: {$validated['role']}.",
            $user,
            ['rol' => $validated['role']]
        );

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
        if ($user->avatar) {
            $this->deleteFromCloudinary($user->avatar);
        }
        
        \App\Services\ActivityLogger::log(
            'eliminacion_usuario', 
            "Usuario eliminado del sistema: {$user->name} ({$user->username}).",
            null,
            ['nombre' => $user->name, 'username' => $user->username]
        );

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }
}
