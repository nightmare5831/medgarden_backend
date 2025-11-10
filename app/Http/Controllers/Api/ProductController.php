<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->isSeller()) {
            // Web App (Authenticated Seller) - Show their own products
            $products = Product::where('seller_id', $user->id)
                ->latest()
                ->paginate(20);
        } elseif ($user && $user->isAdmin()) {
            // Web App (Admin) - Show all products
            $products = Product::latest()->paginate(20);
        } else {
            // Android App (Public) - Show only approved & active products
            $products = Product::where('status', 'approved')
                ->where('is_active', true)
                ->latest()
                ->paginate(20);
        }

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isSeller()) {
            return response()->json(['error' => 'Only sellers can create products'], 403);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'gold_weight' => 'required|numeric|min:0.001',
            'gold_purity' => 'required|in:18k,22k,24k',
            'base_price' => 'required|numeric|min:0',
            'labor_cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'dimensions' => 'nullable|json',
            'model_3d_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'seller_id' => $user->id,
            ...$request->all(),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Product created successfully. Awaiting admin approval.',
            'product' => $product->load(['category', 'images']),
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $product = Product::with(['category', 'images', 'seller'])->findOrFail($id);

        if ($product->seller_id !== $user->id && !$product->isApproved() && !$user->isAdmin()) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        if ($product->seller_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($product->isApproved()) {
            return response()->json(['error' => 'Cannot edit approved products. Please contact admin.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'gold_weight' => 'sometimes|numeric|min:0.001',
            'gold_purity' => 'sometimes|in:18k,22k,24k',
            'base_price' => 'sometimes|numeric|min:0',
            'labor_cost' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'dimensions' => 'nullable|json',
            'model_3d_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update($request->all());

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->load(['category', 'images']),
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        if ($product->seller_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
