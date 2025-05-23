<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(
            [
                "status" => "success",
                "categories" => $categories
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);    
        return response()->json([
            'message' => 'Catégorie créée avec succès', // Remplace "status"
            'category' => $category // Remplace "categories"
        ], 200); 
     }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
        ]);
    
        $category->update([
           'user_id' => Auth::id(),
            'name' => $validatedData['name'],
            'slug' => $validatedData['slug'],
        ]);
    
        return response()->json(
            [
                'message' => 'Catégorie créée avec succès', // Remplace "status"
                "categories" => $category
            ]
        );    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(
            [
                'message' => 'category deleted successfully', // Remplace "status"
                "categories" => $category
            ]
        );   
     }
     
     
}
