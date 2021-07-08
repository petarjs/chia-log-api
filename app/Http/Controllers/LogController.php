<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Models\LogLine;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function store(LogRequest $request) {
        LogLine::create($request->all());
    }
    
    public function index() {
        $logLines = LogLine::all();

        return view('logs.index', compact('logLines'));
    }
}
