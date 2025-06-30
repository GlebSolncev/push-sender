<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    public function lead(Request $request): JsonResponse
    {
        Log::log('debug', time() . ' |-| ' . json_encode($request->all()));

        return response()->json([
            'status' => true
        ]);
    }
}