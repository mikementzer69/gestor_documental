<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    //
    public function index()
    {
        $logs = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('activity-log', compact('logs'));
    }
}
