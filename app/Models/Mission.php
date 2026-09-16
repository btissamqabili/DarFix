<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    use HasFactory;

    protected $casts = [
        'date_souhaitee' => 'date',
        'photos' => 'array',
    ];

    protected $fillable = [
        'client_id',
        'categorie_id',
        'titre',
        'description',
        'budget',
        'adresse',
        'date_souhaitee',
        'photos',
        'statut',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function offres(): HasMany
    {
        return $this->hasMany(Offre::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function prestations(): HasMany
    {
        return $this->hasMany(Prestation::class);
    }
}
