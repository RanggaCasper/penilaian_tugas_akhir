<?php

namespace App\Http\Controllers\Special\Api;

use App\Models\ApiKey;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function index()
    {
        $data = ApiKey::where('user_id', Auth::user()->id)->first();
        return view('special.api.setting', compact('data'));
    }

    public function regenerate(Request $request)
    {
        try {
            $data = ApiKey::where('user_id', Auth::id())->first();

            if ($data) {
                $data->update([
                    'api_key' => Str::random(64),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'API Key tidak ditemukan.'
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'API Key berhasil diubah.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'errors' => "Terjadi kesalahan saat mengubah data."    
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = ApiKey::where('user_id', Auth::id())->first();
            $ipsArray = array_map('trim', explode(',', $request->ips));
            $data->update([
                'ips' => $ipsArray,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Whitelist IP berhasil diubah.'
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'errors' => "Terjadi kesalahan saat mengubah data."    
            ], 500);
        }
    }
}
