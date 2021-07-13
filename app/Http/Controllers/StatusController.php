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
        $apiKey = ApiKey::where('key', $key)->get();

        if (!$apiKey) {
            abort(401);
        }

        $machine = $apiKey->name;
        $data['machine'] = $machine;
        $status = Status::create($data);
    }
}
