<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SwaggerController extends Controller
{
    /**
     * Affiche la page de documentation Swagger UI
     */
    public function index()
    {
        return view('swagger.index');
    }

    /**
     * Retourne le fichier de documentation OpenAPI en JSON
     */
    public function json()
    {
        $path = storage_path('api-docs/swagger.json');
        
        if (!file_exists($path)) {
            return response()->json(['error' => 'Documentation not found'], 404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/json',
        ]);
    }
}
