<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'summary',
        'poster',
        'genre_id',
        'year'
    ];

    public function genre() {
        return $this->hasOne(Genres::class, 'id','genre_id');
    }

    public function list_cast() {
        return $this->belongsToMany(Casts::class, "cast_movies", "movie_id", "cast_id");
    }

    public function list_reviews() {
        return $this->hasMany(Review::class, 'movie_id');
    }
}
