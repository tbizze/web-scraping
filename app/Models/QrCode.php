<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'name_file', 'status', 'content', 'grupo', 'carne', 'pagseguro_id', 'transaction_id', 'pessoa_id'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class)->withDefault('N/D');
    }
}
