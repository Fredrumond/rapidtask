<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function timeMembros(): HasMany
    {
        return $this->hasMany(TimeMembro::class, 'usuario_id');
    }

    public function times(): BelongsToMany
    {
        return $this->belongsToMany(Time::class, 'time_membro', 'usuario_id', 'time_id')
            ->withPivot('nivel_id')
            ->withTimestamps();
    }

    public function belongsToTime(int $timeId): bool
    {
        return $this->timeMembros()->where('time_id', $timeId)->exists();
    }

    public function isAdminOf(int $timeId): bool
    {
        return $this->timeMembros()
            ->where('time_id', $timeId)
            ->where('nivel_id', 1)
            ->exists();
    }

    public function currentMembership(): ?TimeMembro
    {
        $teamId = current_time_id();

        if ($teamId === null) {
            return null;
        }

        return $this->timeMembros()->where('time_id', $teamId)->first();
    }
}
