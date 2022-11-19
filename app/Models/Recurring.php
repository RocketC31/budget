<?php

namespace App\Models;

use App\Events\RecurringCreated;
use App\Events\RecurringDeleted;
use App\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recurring extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'space_id',
        'type',
        'interval',
        'day',
        'starts_on',
        'ends_on',
        'last_used_on',
        'tag_id',
        'description',
        'amount',
        'currency_id'
    ];

    protected $dates = ['deleted_at'];

    protected $dispatchesEvents = [
        'created' => RecurringCreated::class,
        'deleted' => RecurringDeleted::class
    ];

    protected $appends = ['formatted_amount', 'tag'];

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return Helper::formatNumber($this->amount / 100);
    }

    public function getTagAttribute()
    {
        if ($this->tag_id) {
            return $this->tag()->first();
        }
        return null;
    }

    public function getDueDaysAttribute()
    {
        if ($this->starts_on <= date('Y-m-d') && ($this->ends_on >= date('Y-m-d') || !$this->ends_on)) {
            if (date('j') > $this->day) {
                return date('t') - date('j') + $this->day;
            }

            return $this->day - date('j');
        }

        return 0;
    }

    public function getStatusAttribute()
    {
        return $this->starts_on <= date('Y-m-d') && ($this->ends_on >= date('Y-m-d') || !$this->ends_on);
    }

    // Relations
    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function spendings()
    {
        return $this->hasMany(Transaction::class)->where('type', 'spendings');
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    // Scopes
    public function scopeOfSpace($query, $spaceId)
    {
        return $query->where('space_id', $spaceId);
    }
}
