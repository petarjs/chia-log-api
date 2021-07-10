<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'config',
        'copy_speed',
        'copy_time',
        'p1_time',
        'total_time',
        'complete',
    ];

    public function logLines() {
        return $this->hasMany(LogLine::class);
    }
}
