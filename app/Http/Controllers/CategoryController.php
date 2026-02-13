<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Récupérer les catégories principales avec leurs enfants
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        Category::create($request->all());
        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès');
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        // Exclure la catégorie et tous ses descendants
        $forbiddenIds = [$category->id];
        foreach ($category->getAllDescendants() as $descendant) {
            $forbiddenIds[] = $descendant->id;
        }
        
        $categories = Category::whereNotIn('id', $forbiddenIds)->get();
        return view('categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value) {
                        // Vérifier que la catégorie n'essaie pas de se pointer à elle-même
                        if ($value == $category->id) {
                            $fail('Une catégorie ne peut pas être sa propre parente.');
                        }
                    
                        // Vérifier qu'on ne crée pas une boucle circulaire
                        $descendantIds = collect($category->getAllDescendants())->pluck('id')->toArray();
                        if (in_array($value, $descendantIds)) {
                            $fail('Vous ne pouvez pas assigner un enfant comme parent (boucle circulaire).');
                        }
                    }
                },
            ],
        ]);
        
        $category->update($request->only(['name', 'parent_id']));
        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès');
    }
}