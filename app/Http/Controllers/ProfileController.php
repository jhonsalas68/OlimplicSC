<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\FileStorageHelper;

class ProfileController extends Controller
{
    use FileStorageHelper;
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile using Cloudinary facade.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:5120',
        ]);

        $user = auth()->user();
        $user->name = $request->name;

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $this->deleteFile($user->avatar);
            }
            // 1. Sube el archivo a Cloudflare R2
            $path = Storage::disk('r2')->putFile('avatars', $request->file('avatar'));
            
            // 2. Obtiene el link público
            $url = Storage::disk('r2')->url($path);

            // 3. Lo guarda en tu base de datos
            $user->avatar = $url;
        }

        $user->save();

        return back()->with('status', '¡Imagen guardada para siempre!');
    }
}
