<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offre extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id',
        'prestataire_id',
        'prix_propose',
        'message',
        'delai_execution',
        'statut',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prestataire_id');
    }

    public function prestation(): HasOne
    {
        return $this->hasOne(Prestation::class);
    }
}
