<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddGuard extends Model
{
   protected $fillable = [
      'full_name',
      'psira_number',
      'certificate_file',
      'wage_rate',
      'rate_type',
      'area_of_operation',
      'controller_assignment',
   ];
   protected $cast = [
      'created_at' => 'datetime',
      'updated_at' => 'datetime',
      'wage_rate' => 'integer',
      'certificate_file' => 'string',
   ];
}
