<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTomany;
use App\Models\Artist;
use App\Models\Song;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    public function artists(): BelongsToMany {
        return $this->belongsToMany(Artist::class);
    }

    public function songs(): BelongsToMany {
        return $this->belongsToMany(Song::class);
    }
}
