<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        // Remover avatar se solicitado
        if ($request->has('remove_avatar')) {
            // Deletar avatar antigo
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
            
            // Se tiver profissional vinculado, remover também
            if ($user->profissional) {
                $user->profissional->update(['avatar' => null]);
            }
        }
        // Upload avatar se fornecido
        elseif ($request->hasFile('avatar')) {
            \Log::info('Upload de avatar iniciado', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'file_name' => $request->file('avatar')->getClientOriginalName(),
                'file_size' => $request->file('avatar')->getSize(),
                'file_type' => $request->file('avatar')->getMimeType()
            ]);
            
            // Deletar avatar antigo
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
                \Log::info('Avatar antigo deletado: ' . $user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            
            \Log::info('Avatar salvo com sucesso', [
                'avatar_path' => $avatarPath,
                'full_path' => storage_path('app/public/' . $avatarPath),
                'file_exists' => file_exists(storage_path('app/public/' . $avatarPath))
            ]);
            
            // Se tiver profissional vinculado, atualizar também
            if ($user->profissional) {
                $user->profissional->update(['avatar' => $avatarPath]);
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
