<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'image_url',
        'description',
        'longueur',
        'duree',
        'user_id',
        'theme_id',
    ];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function endroits()
    {
        return $this->belongsToMany(Endroit::class, 'endroit_sentier')->withTimestamps();
    }
}