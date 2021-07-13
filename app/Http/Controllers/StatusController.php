<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Models\ApiKey;
use App\Models\Status;

class StatusController extends Controller
{
    public function store(StatusRequest $request) {
        $data = $request->all();
        $key = $request->header('X-Authorization');
        $apiKey = ApiKey::where('key', $key)->first();

        if (!$apiKey) {
            abort(401);
        }

        $machine = $apiKey->name;
        $data['machine'] = $machine;
        $status = Status::create($data);
    }

    public function disks($machine = 'chia-1') {
        $data = Status::where('machine', $machine)->orderBy('created_at', 'desc')->first();
        return view('status.disks', compact('data'));
    }

    public function sensors($machine = 'chia-1') {
        $data = Status::where('machine', $machine)->orderBy('created_at', 'desc')->first();
        return view('status.sensors', compact('data'));
    }
    
    public function farm($machine = 'chia-1') {
        $data = Status::where('machine', $machine)->orderBy('created_at', 'desc')->first();
        return view('status.farm', compact('data'));
    }
}
