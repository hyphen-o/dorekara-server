<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTomany;
use App\Models\User;

class Artist extends Model
{
    use HasFactory;

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }
}
