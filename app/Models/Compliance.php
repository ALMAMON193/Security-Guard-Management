<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compliance extends Model
{
    protected $fillable = [
        'user_id',
        'enable_statutory_deductions',
        'service_offered',
        'grade_of_guard',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
