<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(5); 
        return view('dashboard', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'qty' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($request->all());

        return response()->json(['success' => 'Product added successfully.', 'product' => $product]);
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(['product' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'qty' => 'required|integer',
            'price' => 'required|numeric',
        ]);
    
        // Update the product with the new data
        $product->update($request->all());
    
        return response()->json([
            'success' => 'Product updated successfully.',
            'product' => $product
        ]);
    }
    

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['success' => 'Product deleted successfully.']);
    }
}
