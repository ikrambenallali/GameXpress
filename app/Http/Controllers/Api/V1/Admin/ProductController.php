<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;    



class ProductController extends Controller
{
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'category_id' => $request->category_id,
        ]);
        return response()->json($product);
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
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'category_id' => $request->category_id,
        ]);
    
        $product->update([
           'user_id' => Auth::id(),
            'name' => $validatedData['name'],
            'slug' => $validatedData['slug'],
        ]);
    
        return response()->json(['message' => 'product mise à jour avec succès']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'product supprimer avec succès']);
    }
}
