<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'logo',
        'name',
        'requisition_id',
        'account_id',
        'space_id',
        'link'
    ];

    protected $dates = ['happened_on', 'deleted_at'];

    // Scopes
    public function scopeOfSpace($query, $spaceId)
    {
        return $query->where('space_id', $spaceId);
    }
}
