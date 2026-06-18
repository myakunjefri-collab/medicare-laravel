<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role === 'pasien') return redirect('/pasien');
            if ($role === 'dokter') return redirect('/dokter');
            if ($role === 'admin') return redirect('/admin');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $credentials['email'];
        $password = $credentials['password'];

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $login, 'password' => $password, 'is_active' => true])) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            if ($role === 'pasien') {
                return redirect()->intended('/pasien');
            } elseif ($role === 'dokter') {
                return redirect()->intended('/dokter');
            } else {
                return redirect()->intended('/admin');
            }
        }

        return back()->with('error', 'Email/Username atau password salah!')->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'gender' => 'required|string',
            'alamat' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
            'agree' => 'accepted',
        ], [
            'email.unique' => 'Email sudah terdaftar! Silakan gunakan email lain.',
            'confirm_password.same' => 'Konfirmasi password tidak cocok!',
            'password.min' => 'Password minimal 6 karakter!',
            'agree.accepted' => 'Anda harus menyetujui syarat & ketentuan.',
        ]);

        if ($validator->fails()) {
            return back()->with('register_error', $validator->errors()->first())->withInput();
        }

        User::create([
            'name' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
            'age' => $request->age,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'alamat' => $request->alamat,
            'is_active' => true,
        ]);

        return back()->with('register_success', 'Pendaftaran berhasil! Silakan login dengan akun Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
