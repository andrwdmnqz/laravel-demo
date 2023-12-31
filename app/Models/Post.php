<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body', 'user_id', 'image', 'video'];

    public function getAuthor() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
