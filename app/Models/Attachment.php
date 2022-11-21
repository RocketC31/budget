<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_type',
        'transaction_id',
        'file_path'
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    // Accessors
    public function getFileB64Attribute()
    {
        $file = Storage::get($this->file_path);

        if (!$file) {
            return null;
        }

        $type = pathinfo($this->file_path, PATHINFO_EXTENSION);

        return 'data:image/' . $type . ';base64,' . base64_encode($file);
    }

    public function getFileTypeAttribute()
    {
        $parts = explode('.', $this->file_path);

        return $parts[count($parts) - 1];
    }
}
