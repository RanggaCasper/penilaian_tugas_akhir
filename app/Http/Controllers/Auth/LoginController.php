<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\SIONService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\RequestException;

class LoginController extends Controller
{
    protected $sion;

    public function __construct(SIONService $sion) {
        $this->sion = $sion;
    }

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('auth.login');
    }
    
    /**
     * Handle an incoming login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        $nim = $credentials['nim'];
        $password = $credentials['password'];
        $response = $this->sion->getMahasiswaProfile($nim);

        try {
            if ($response) {
                $user = User::where('nim', $nim)->first();

                if ($user) {
                    if (is_null($user->password)) {
                        if ($response['passwSION'] === md5($password)) {
                            $user->password = bcrypt($password);
                            $user->save();

                            Auth::login($user, true);
                            Session::regenerate();

                            return response()->json([
                                'status' => true,
                                'message' => "Welcome, {$user->name}",
                                'redirect_url' => route('user.dashboard'),
                            ]);
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'Kredensial yang anda masukan tidak cocok.',
                            ], 404);
                        }
                    } else {
                        if (Hash::check($password, $user->password)) {
                            Auth::login($user, true);
                            Session::regenerate();

                            return response()->json([
                                'status' => true,
                                'message' => "Welcome, {$user->name}",
                                'redirect_url' => route('user.dashboard'),
                            ]);
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => 'Kredensial yang anda masukan tidak cocok.',
                            ], 404);
                        }
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Kredensial yang anda masukan tidak cocok.',
                    ], 404);
                }
            }

            return response()->json([
                'status' => false,
                'message' => 'Kredensial yang anda masukan tidak cocok.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'message' => "Logout berhasil, session anda telah dihapus",
            'redirect_url' => route('login'),
        ]);
    }
}
