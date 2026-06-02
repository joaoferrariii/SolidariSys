<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
 
    protected $table = 'itens';
 
    protected $fillable = [
        'nome_item',
        'categoria_id',
    ];
 
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
 
    public function doacoes(): HasMany
    {
        return $this->hasMany(Doacao::class);
    }
}
