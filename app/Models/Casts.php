<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casts extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'age',
        'bio'
    ];

    public function list_movies() {
        return $this->belongsToMany(Movie::class, "cast_movies", "cast_id", "movie_id");
    }
}
