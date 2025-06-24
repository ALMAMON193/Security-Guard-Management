<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shifts';
    protected $fillable = ['user_id','name', 'date', 'start_time', 'end_time', 'shift_schedule', 'status'];
   public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
