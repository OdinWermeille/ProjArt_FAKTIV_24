<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentier extends Model
{
    use HasFactory;

    // Attributs modifiables
    protected $fillable = [
        'nom',
        'image_url',
        'description',
        'longueur',
        'duree',
        'user_id',
        'theme_id',
    ];

    // Relation avec le modèle Theme
    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    // Relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le modèle Endroit via la table pivot 'endroit_sentier'
    public function endroits()
    {
        return $this->belongsToMany(Endroit::class, 'endroit_sentier')->withTimestamps();
    }
}
