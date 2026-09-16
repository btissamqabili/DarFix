<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $fillable = [
        'nom',
        'description',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }
}
