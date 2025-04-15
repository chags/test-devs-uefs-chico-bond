<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'slug', 'user_id'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // App\Models\Post.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}