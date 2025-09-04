<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    
    protected $fillable = [
        'json_data',
    ];

    protected $casts = [
        'json_data' => 'array',
    ];
}
