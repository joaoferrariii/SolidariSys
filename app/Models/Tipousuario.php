<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipousuario extends Model
{
    use HasFactory;
 
    protected $table = 'tipo_usuarios';
 
    protected $fillable = [
        'nome',
    ];
 
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
