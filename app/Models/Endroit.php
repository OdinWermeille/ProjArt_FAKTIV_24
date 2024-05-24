<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endroit extends Model
{
    use HasFactory;

    protected $fillable=['nom','image_url','description', 'localite', 'coordonneesX', 'coordonneesY']; 
    
    public function user() {
        return $this->belongsTo(User::class); 
    } 

    public function sentiers() {
        return $this->belongsToMany(Sentier::class);
    }
}
