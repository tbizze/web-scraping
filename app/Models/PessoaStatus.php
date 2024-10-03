<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PessoaStatus extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'notes'];

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class);
    }
}
