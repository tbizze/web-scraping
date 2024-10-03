<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'name_file', 'status', 'content', 'grupo', 'carne', 'pagseguro_id', 'transaction_id'];

    // public function transaction(): BelongsTo
    // {
    //     return $this->belongsTo(Transaction::class)->withDefault('N/D');
    // }
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
