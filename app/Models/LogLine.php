<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogLine extends Model
{
    use HasFactory;

    protected $fillable = ['line', 'machine'];

    public function plot() {
        return $this->belongsTo(Plot::class);
    }
}
