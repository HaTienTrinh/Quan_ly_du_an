<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->active()
            ->inStock()
            ->with('category')
            ->limit(6)
            ->get();

        return view('customers.home', compact('products'));
    }

    public function product()
    {
        $products = Product::query()
            ->active()
            ->inStock()
            ->with('category')
            ->orderByDesc('created_at')
            ->get();

        return view('customers.products', compact('products'));
    }

    public function show(Product $product)
    {
        // Kiểm tra sản phẩm có active không
        if (!$product->is_active) {
            abort(404);
        }

        $product->load('category', 'images');

        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::query()
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->limit(4)
            ->get();

        return view('customers.products.show', compact('product', 'relatedProducts'));
    }

    public function contact()
    {
        return view('customers.contact');
    }

    public function post()
    {
        $featuredPost = Post::query()
            ->published()
            ->with('author')
            ->inRandomOrder()
            ->first();

        $posts = Post::query()
            ->published()
            ->with('author')
            ->orderByDesc('published_at')
            ->get();

        return view('customers.posts', compact('featuredPost', 'posts'));
    }
}
