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
        return view('customers.home');
    }

    public function product()
    {
        $products = Product::all();
        return view('customers.products', compact('products'));
    }

    public function contact()
    {
        return view('customers.contact');
    }

    public function post()
    {
        $featuredPost = [
            'title' => 'Top 10 mẫu giày thể thao hot nhất 2026',
            'category' => 'Xu hướng',
            'date' => '21/01/2026',
            'author' => 'Nguyễn Văn A',
            'desc' => 'Khám phá những mẫu giày thể thao được yêu thích nhất trong năm nay với thiết kế độc đáo và công nghệ tiên tiến...',
            'image' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?q=80&w=2070&auto=format&fit=crop'
        ];
        $posts = Post::all();
        return view('customers.posts', compact('featuredPost', 'posts'));
    }
}
