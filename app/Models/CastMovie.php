<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastMovie extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'cast_id',
        'movie_id',
    ];

    public function cast() {
        return $this->hasOne(Casts::class, 'id','cast_id');
    }

    public function movie() {
        return $this->hasOne(Movie::class, 'id','movie_id');
    }
}
