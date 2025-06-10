<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;





class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'steam_id',
        'steam_name',
        'discord_handle',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function ownedTeam()
    {
        return $this->hasOne(Team::class, 'owner_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isCaptain()
    {
        return $this->team && $this->id === $this->team->owner_id;
    }

    public function matchStats()
    {
        return $this->hasMany(PlayerMatchStat::class);
    }

    public function lobbies() {
        return $this->belongsToMany(Lobby::class)
            ->withPivot('role','slot_index')
            ->withTimestamps();
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? asset('storage/profiles/' . $this->avatar)
            : asset('images/default-avatar.png');
    }

}
