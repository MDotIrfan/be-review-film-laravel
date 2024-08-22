<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Genres extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
    ];

    public function list_movies() {
        return $this->hasMany(Movie::class, 'genre_id');
    }
}
