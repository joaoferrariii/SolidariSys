<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
 
    protected $table = 'categorias';
 
    protected $fillable = [
        'nome_categoria',
    ];
 
    public function itens(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
