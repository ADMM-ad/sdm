<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:30',
            'username' => 'required|string|max:30|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,customerservice,editor',
            'kode_voucher' => 'nullable|integer',
                ], [
    'name.required' => 'Nama wajib diisi.',
    'name.max' => 'Nama anda terlalu panjang, max 30 kata.',
    'username.required' => 'Username wajib diisi.',
    'username.max' => 'Username anda terlalu panjang, max 30 kata.',
    'username.unique' => 'Username sudah digunakan.',
    'password.min' => 'Password minimal 6 karakter.',
    'password.confirmed' => 'Konfirmasi password tidak cocok.',
    'kode_voucher.integer' => 'Kode voucher harus berupa angka.',
    ]);
        

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'kode_voucher' => $request->kode_voucher,
        ]);

        return redirect()->route('user.create')->with('success', 'User berhasil didaftarkan!');
    }

 public function showLoginForm()
    {
        return view('auth.login'); // Sesuaikan dengan file login kamu
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Arahkan sesuai role
           $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('dashboard.admin');
    } elseif ($user->role === 'customerservice') {
        return redirect()->route('dashboard.customerservice');
    } elseif ($user->role === 'editor') {
        return redirect()->route('dashboard.editor');
    } else {
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'username' => 'Role tidak dikenali.',
        ]);
    }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }


public function indexCS()
{
    $users = User::where('role', 'customerservice')->get();
    return view('user.usercs', compact('users'));
}

public function editVoucher($id)
{
    $user = User::findOrFail($id);
    return view('user.edit-voucher', compact('user'));
}

public function updateVoucher(Request $request, $id)
{
    $request->validate([
        'kode_voucher' => 'nullable|integer',
    ], [
        'kode_voucher.integer' => 'Kode voucher harus berupa angka.',
    ]);

    $user = User::findOrFail($id);
    $user->kode_voucher = $request->kode_voucher;
    $user->save();

    return redirect()->route('user.cs')->with('success', 'Kode voucher berhasil diperbarui.');
}

public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('user.cs')->with('success', 'User berhasil dihapus.');
}

public function indexEd()
{
    $users = User::where('role', 'editor')->get();
    return view('user.usereditor', compact('users'));
}

public function destroyEd($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('user.editor')->with('success', 'User berhasil dihapus.');
}


}
