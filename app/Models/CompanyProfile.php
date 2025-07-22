<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $table = 'company_profiles';
    protected $fillable = [
        'user_id',
        'business_name',
        'owner_name',
        'area_of_operation',
        'company_location',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'service_offered' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
