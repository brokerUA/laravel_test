<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $positions = Position::select('id', 'title as name')->get();

        if (!$positions->count()) {
            return response()->json([
                'success' => false,
                'message' => "Positions not found"
            ], 422);
        }

        return response()->json([
            'success' => true,
            'positions' => $positions
        ], 200);
    }
}
