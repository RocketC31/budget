<?php

namespace App\Models;

use App\Events\TransactionCreated;
use App\Events\TransactionDeleted;
use App\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_EARNING = "earning";
    public const TYPE_SPENDING = "spending";

    protected $dates = ['happened_on', 'deleted_at'];

    protected $fillable = [
        'space_id',
        'import_id',
        'recurring_id',
        'tag_id',
        'happened_on',
        'description',
        'amount',
        'type'
    ];

    protected $appends = [ 'formatted_amount', 'tag' ];

    protected $dispatchesEvents = [
        'created' => TransactionCreated::class,
        'deleted' => TransactionDeleted::class
    ];

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return Helper::formatNumber($this->amount / 100);
    }

    public function getFormattedHappenedOnAttribute()
    {
        $secondsDifference = strtotime(date('Y-m-d')) - strtotime($this->happened_on);

        return ($secondsDifference / 60 / 60 / 24) . ' days ago';
    }

    public function getTagAttribute()
    {
        if ($this->tag_id) {
            return $this->tag()->first();
        }
        return null;
    }

    // Relations
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'transaction_id');
    }

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public function recurring()
    {
        return $this->belongsTo(Recurring::class);
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
