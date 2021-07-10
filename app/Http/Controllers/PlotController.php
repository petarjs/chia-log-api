<?php

namespace App\Http\Controllers;

use App\Models\Plot;
use Illuminate\Http\Request;

class PlotController extends Controller
{
    public function index() {
        $plots = Plot::all();
        return view('plots.index', compact('plots'));
    }
    
    public function details($id) {
        $plot = Plot::find($id);
        return view('plots.details', compact('plot'));
    }
}
