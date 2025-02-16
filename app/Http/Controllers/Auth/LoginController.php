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
    public function login(Request $request)
    {
        if (env('APP_ENV') == 'production') {
            $request->validate([  
                'g-recaptcha-response' => 'required|captcha'  
            ], [  
                'g-recaptcha-response.required' => 'The reCAPTCHA field is required.',  
                'g-recaptcha-response.captcha' => 'The reCAPTCHA verification failed. Please try again.'  
            ]); 
        }

        $credentials = $request->validate([
            'identity' => 'required',
            'password' => 'required',
        ]);

        $fields = ['email', 'identity', 'secondary_identity'];

        foreach ($fields as $field) {
            if (Auth::attempt([$field => $credentials['identity'], 'password' => $credentials['password']])) {
                $request->session()->regenerate();

                $user = Auth::user();
                $role = strtolower($user->role->name);

                return response()->json([
                    'status' => true,
                    'message' => "Welcome, {$user->username}",
                    'redirect_url' => route("{$role}.dashboard"),
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Kredensial yang Anda masukkan salah.',
        ], 422);
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
