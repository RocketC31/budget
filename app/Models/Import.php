<?php

namespace App\Models;

use App\Events\ImportCreated;
use App\Events\ImportDeleted;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'space_id',
        'name',
        'file',
        'status',
        'column_happened_on',
        'column_description',
        'column_amount'
    ];

    protected $dispatchesEvents = [
        'created' => ImportCreated::class,
        'deleted' => ImportDeleted::class
    ];

    protected $appends = [ "spendings" ];

    public function getSpendingsAttribute(): Collection
    {
        return $this->spendings()->get();
    }

    // Relations
    public function spendings()
    {
        return $this->hasMany(Spending::class);
    }

    // Scopes
    public function scopeOfSpace($query, $spaceId)
    {
        return $query->where('space_id', $spaceId);
    }
}
