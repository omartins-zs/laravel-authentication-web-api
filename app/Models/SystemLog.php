<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
     use HasFactory;

    protected $fillable = [
        'level',
        'message',
        'context',
    ];

    // public $timestamps = false;
}
