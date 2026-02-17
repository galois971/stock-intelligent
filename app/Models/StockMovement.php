<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'subtype',
        'quantity',
        'motif',
        'reference',
        'supplier',
        'customer',
        'movement_date',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
    ];

    /**
     * Types de mouvement (entrées)
     */
    const SUBTYPE_ENTRY = [
        'achat' => 'Achat',
        'retour' => 'Retour',
        'correction' => 'Correction',
    ];

    /**
     * Types de mouvement (sorties)
     */
    const SUBTYPE_EXIT = [
        'vente' => 'Vente',
        'perte' => 'Perte',
        'casse' => 'Casse',
        'expiration' => 'Expiration',
    ];

    /**
     * Relation vers le produit
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation vers l'utilisateur qui a effectué le mouvement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère le libellé du sous-type
     */
    public function getSubtypeLabelAttribute(): ?string
    {
        if (!$this->subtype) {
            return null;
        }

        $allSubtypes = array_merge(self::SUBTYPE_ENTRY, self::SUBTYPE_EXIT);
        return $allSubtypes[$this->subtype] ?? $this->subtype;
    }

    /**
     * Vérifie si le mouvement est une entrée
     */
    public function isEntry(): bool
    {
        return $this->type === 'entry';
    }

    /**
     * Vérifie si le mouvement est une sortie
     */
    public function isExit(): bool
    {
        return $this->type === 'exit';
    }
}
