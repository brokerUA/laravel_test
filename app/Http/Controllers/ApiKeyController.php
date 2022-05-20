<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $token = Str::random(30);

        ApiKey::create([
            'key' => Crypt::encryptString($token)
        ]);

        return response()->json([
            'success' => true,
            'token' => $token
        ], 200);
    }
}
