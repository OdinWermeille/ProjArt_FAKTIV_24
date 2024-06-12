<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endroit extends Model
{
    use HasFactory;

    // Attributs modifiables
    protected $fillable = ['nom', 'image_url', 'description', 'localite', 'coordonneesX', 'coordonneesY', 'user_id'];

    // Relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le modèle Sentier
    public function sentiers()
    {
        return $this->belongsToMany(Sentier::class);
    }
}
