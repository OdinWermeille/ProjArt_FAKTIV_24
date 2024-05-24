<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sentier;

class Theme extends Model
{
    use HasFactory;

    protected $fillable=['nom']; 
    
    public function sentiers() { 
        return $this->hasMany(Sentier::class);   
    }  
}
