<?php  

namespace App\Http\Middleware;  

use Closure;  
use App\Models\ApiKey;  
use Illuminate\Http\Request;  
use Symfony\Component\HttpFoundation\Response;  

class CheckSignature  
{  
    /**  
     * Handle an incoming request.  
     *  
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next  
     */  
    public function handle(Request $request, Closure $next)  
    {  
        try {  
            $key = $request->header('key');  
            $sign = $request->header('signature');  

            if (!$key || !$sign) {  
                return response()->json([  
                    'status' => false,  
                    'message' => 'Unauthorized'  
                ], 401);  
            }  

            $data = ApiKey::where('api_key', $key)->first();  

            if (!$data) {  
                return response()->json([  
                    'status' => false,  
                    'message' => 'Invalid API Key'  
                ], 401);  
            }  

            if (md5($data->api_id . ':' . $data->api_key) !== $sign) {  
                return response()->json([  
                    'status' => false,  
                    'message' => 'Invalid Signature'  
                ], 401);  
            }  

            $userIp = $request->ip();  
            $whiteIps = $data->ips;  
    
            if (empty($whiteIps) || (is_array($whiteIps) && count(array_filter($whiteIps)) === 0)) {  
                return $next($request);  
            }  

            if (!in_array($userIp, $whiteIps)) {  
                return response()->json([  
                    'status' => false,  
                    'message' => 'Access denied: IP ' . $userIp . ' not allowed!'  
                ], 403);  
            }  

            return $next($request);  
        } catch (\Exception $e) {  
            return response()->json([  
                'status' => false,  
                'message' => 'An error occurred while processing the request.',  
            ], 500);  
        }  
    }  
}