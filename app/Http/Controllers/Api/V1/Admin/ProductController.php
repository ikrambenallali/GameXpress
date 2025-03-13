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
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'primary_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'category_id' => $request->category_id,
        ]);
        $primaryImagePath = $request->file('primary_image')->store('products', 'public');
        $product->images()->create([
            'image_url' => $primaryImagePath,
            'is_primary' => true
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                $product->images()->create([
                    'image_url' => $imagePath,
                ]);
            }
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('images')
        ], 201);
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'], 
            'price' => ['required', 'numeric', 'min:0'],

            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:disponible,en rupture'], 
            'category_id' => ['required', 'exists:categories,id'], 
        ]);
    
        $product->update([
            'user_id' => Auth::id(),
            'name' => $validatedData['name'],
            'slug' => $validatedData['slug'] ?? Str::slug($validatedData['name']), 
            'price' => $validatedData['price'],
            'stock' => $validatedData['stock'],
            'status' => $validatedData['status'],
            'category_id' => $validatedData['category_id'],
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
