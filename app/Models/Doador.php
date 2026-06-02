<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
