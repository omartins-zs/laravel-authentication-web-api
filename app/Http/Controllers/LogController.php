<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemLog;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = SystemLog::orderBy('created_at', 'desc')->get();

        return response()->json([
            'logs' => $logs,
        ], 200);
    }
}
