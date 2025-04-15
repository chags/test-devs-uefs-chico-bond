<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}