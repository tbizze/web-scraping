<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;
    protected $fillable = ['path', 'name_file', 'status', 'content', 'grupo', 'carne', 'pagseguro_id'];
}
