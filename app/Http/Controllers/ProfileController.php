<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index() {
        return view('profile.profile');
    }

    public function store(Request $request) {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|same:confirm_password',
        ]);
        try {
            $user = User::find(Auth::user()->id);

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password lama salah.'
                ], 422);
            }
    
            $user->update(['password' => Hash::make($request->password)]);
    
            return response()->json([
                'status' => true,
                'message' => 'Password berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui password. Silakan coba lagi.'
            ], 500);    
        }
    }
}
