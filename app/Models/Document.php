<?php

namespace App\Models;

use App\Models\User;
use App\Models\DocumentStatus;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

    protected $table = 'documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'id_copy',
        'coida_certificate',
        'uif_certificate',
        'psira_certificate',
        'firearm_competency',
        'statement_of_results',
        'id_status',
        'coida_status',
        'uif_status',
        'psira_status',
        'firearm_status',
        'statement_status',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
