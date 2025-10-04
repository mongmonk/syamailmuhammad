<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Rules\PhoneNumber;
use App\Models\User;
use App\Support\PhoneUtil;

class ProfileController extends Controller
{

    /**
     * Tampilkan profil pengguna saat ini.
     */
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Form edit profil.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update profil pengguna (nama, email, phone, dan opsional password).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', new PhoneNumber(), Rule::unique('users', 'phone')->ignore($user->id)],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        // Jika ada permintaan ubah password, pastikan current_password valid
        if (!empty($validated['password'])) {
            if (empty($validated['current_password']) || ! Hash::check($validated['current_password'], $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai'])
                    ->withInput();
            }
            $user->password = Hash::make($validated['password']);
        }

        // Update field dasar
        $user->name = $validated['name'];
        $user->email = $validated['email'] ?? null;

        // Update nomor telepon hanya jika ada input; normalisasikan dan cek unik pada format simpan "62XXXXXXXXXX"
        if (array_key_exists('phone', $validated) && $validated['phone'] !== null && $validated['phone'] !== '') {
            $normalizedPhone = PhoneUtil::normalize($validated['phone']);
            if ($normalizedPhone === null) {
                return back()
                    ->withErrors(['phone' => 'Format nomor telepon tidak valid (gunakan 0..., 62..., atau +62...).'])
                    ->withInput();
            }

            // Pastikan unik terhadap nomor yang sudah dinormalisasi (abaikan user saat ini)
            $exists = User::where('phone', $normalizedPhone)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($exists) {
                return back()
                    ->withErrors(['phone' => 'Nomor telepon sudah digunakan.'])
                    ->withInput();
            }

            $user->phone = $normalizedPhone;
        }

        $user->save();

        return redirect()->route('profile.show')->with('status', 'Profil berhasil diperbarui.');
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($data['password'], $user->password)) {
            return back()
                ->withErrors(['password' => 'Kata sandi yang Anda masukkan salah'])
                ->withInput();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home')->with('status', 'Akun berhasil dihapus.');
    }
}