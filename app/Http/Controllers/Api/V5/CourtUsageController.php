<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CourtUsageController extends Controller
{
    // Empty controller as per NestJS implementation
    public function index(): JsonResponse
    {
        return response()->json([]);
    }
}

