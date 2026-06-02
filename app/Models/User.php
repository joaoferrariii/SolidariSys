<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'tipo_usuario_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relacionamentos
    public function tipoUsuario(): BelongsTo
    {
        return $this->belongsTo(TipoUsuario::class);
    }

    public function doacoes(): HasMany
    {
        return $this->hasMany(Doacao::class);
    }

    // Helpers de nível
    public function isAdmin(): bool
    {
        return $this->tipo_usuario_id === 1;
    }

    public function isModerador(): bool
    {
        return $this->tipo_usuario_id === 2;
    }

    public function isComum(): bool
    {
        return $this->tipo_usuario_id === 3;
    }
}