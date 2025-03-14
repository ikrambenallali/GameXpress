<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $produits = Product::with('images')->get();

        return response()->json(
            [
                "status" => "success",
                "produits" => $produits
            ]
        );
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
            'status' => $request->stock > 0 ? 'disponible' : 'en rupture',
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
            'status' => 'success',
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
            'primary_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $product->update([
            'user_id' => Auth::id(),
            'name' => $validatedData['name'],
            'slug' => $validatedData['slug'] ?? Str::slug($validatedData['name']),
            'price' => $validatedData['price'],
            'stock' => $validatedData['stock'],
            'status' => $request->stock > 0 ? 'disponible' : 'en rupture',
            'category_id' => $validatedData['category_id'],
        ]);
        if ($request->hasFile('primary_image')) {
            if ($product->images()->where('is_primary', true)->exists()) {
                $oldPrimaryImage = $product->images()->where('is_primary', true)->first();
                Storage::disk('public')->delete($oldPrimaryImage->image_url);
                $oldPrimaryImage->delete();
            }

            $primaryImagePath = $request->file('primary_image')->store('products', 'public');
            $product->images()->create([
                'image_url' => $primaryImagePath,
                'is_primary' => true
            ]);
        }

        // additional images
        if ($request->hasFile('images')) {
            // Delete all existing images except the primary one
            $product->images()->where('is_primary', false)->delete();

            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                $product->images()->create([
                    'image_url' => $imagePath,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'produits' => $product->load('images')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }
        }

        $product->delete();
        return response()->json(['message' => 'product supprimer avec succès']);
    }
}
