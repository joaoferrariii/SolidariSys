<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doador extends Model
{
    use HasFactory;
 
    protected $table = 'doadores';
 
    protected $fillable = [
        'nome',
        'cpf',
        'email',
        'telefone',
    ];
 
    public function doacoes(): HasMany
    {
        return $this->hasMany(Doacao::class);
    }
}
