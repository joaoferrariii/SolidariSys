<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doacao extends Model
{
    use HasFactory;
 
    protected $table = 'doacoes';
 
    protected $fillable = [
        'quantidade',
        'data_doacao',
        'user_id',
        'doador_id',
        'item_id',
    ];
 
    protected function casts(): array
    {
        return [
            'data_doacao' => 'date',
        ];
    }
 
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
 
    public function doador(): BelongsTo
    {
        return $this->belongsTo(Doador::class);
    }
 
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
