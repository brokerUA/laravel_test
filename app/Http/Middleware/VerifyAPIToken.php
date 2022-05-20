<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class VerifyAPIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $model = null;

        $bearer = $request->header('Authorization');

        if ($bearer) {
            $bearer = str_replace('Bearer ', '', $bearer);

            foreach (ApiKey::select('id', 'key')->cursor() as $apiKey) {
                if (Crypt::decryptString($apiKey->key) === $bearer) {
                    $model = $apiKey;
                    break;
                }
            }

        }

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'The token expired.'
            ], 401);
        }

        ApiKey::where('id', $model->id)->delete();

        return $next($request);
    }
}
