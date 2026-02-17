<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'parent_id'];

    /**
     * Relation vers les produits de cette catégorie
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relation vers la catégorie parente (pour la hiérarchie)
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relation vers les catégories enfants (sous-catégories)
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Vérifie si la catégorie a des enfants
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Récupère tous les descendants (enfants, petits-enfants, etc.)
     */
    public function getAllDescendants()
    {
        $descendants = [];
        foreach ($this->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $child->getAllDescendants());
        }
        return $descendants;
    }

    /**
     * Récupère le chemin complet de la catégorie (avec parents)
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }
}