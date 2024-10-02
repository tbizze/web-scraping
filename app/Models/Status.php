<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'notes'];

    protected $appends = [
        'short_description'
    ];

    // Cria uma propriedade nova para a classe.
    protected function shortDescription(): Attribute
    {
        return Attribute::make(
            // Limita a description a 20 caracteres.
            get: fn() => substr($this->description, 0, 20),
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
