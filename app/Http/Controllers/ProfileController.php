<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Traits\CloudinaryHelper;

class ProfileController extends Controller
{
    use CloudinaryHelper;
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
                $this->deleteFromCloudinary($user->avatar);
            }
            // 1. Sube el archivo directo a tu cuenta de Cloudinary
            $response = Cloudinary::uploadApi()->upload($request->file('avatar')->getRealPath(), [
                'folder' => 'avatars'
            ]);
            
            // 2. Obtiene el link eterno (el que empieza con https)
            $url = $response['secure_url'];

            // 3. Lo guarda en tu base de datos
            $user->avatar = $url;
        }

        $user->save();

        return back()->with('status', '¡Imagen guardada para siempre!');
    }
}
