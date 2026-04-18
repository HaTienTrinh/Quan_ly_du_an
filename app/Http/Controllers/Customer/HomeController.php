<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $reviews = ProductReview::query()
            ->with([
                'user:id,name,avatar',
                'product:id,name',
            ])
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->whereHas('user', function ($query) {
                $query->where('role', 'customer')
                    ->where('is_active', true);
            })
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->latest()
            ->take(8)
            ->get();

        return view('customers.home', compact('products', 'reviews'));
    }

    public function product(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $priceRange = (string) $request->input('price_range', '');
        $selectedCategories = collect($request->input('categories', []))
            ->filter(fn ($categoryId) => filled($categoryId) && is_numeric($categoryId))
            ->map(fn ($categoryId) => (int) $categoryId)
            ->unique()
            ->values()
            ->all();

        $categories = Category::query()
            ->active()
            ->whereHas('products', fn ($query) => $query->active()->inStock())
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($selectedCategories !== [], fn ($query) => $query->whereIn('category_id', $selectedCategories))
            ->when($priceRange !== '', function ($query) use ($priceRange) {
                match ($priceRange) {
                    'under_1000000' => $query->whereRaw('COALESCE(sale_price, price) < ?', [1000000]),
                    '1000000_2000000' => $query->whereRaw('COALESCE(sale_price, price) BETWEEN ? AND ?', [1000000, 2000000]),
                    'over_2000000' => $query->whereRaw('COALESCE(sale_price, price) > ?', [2000000]),
                    default => null,
                };
            })
            ->active()
            ->inStock()
            ->with('category')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('customers.products.index', compact(
            'products',
            'categories',
            'search',
            'priceRange',
            'selectedCategories'
        ));
    }

    public function show(Product $product)
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->load([
            'category',
            'colors',
            'images',
            'reviews.user',
        ]);

        $averageRating = round((float) $product->reviews()->avg('rating'), 1);
        $reviewCount = $product->reviews()->count();

        $canReview = false;
        $userReview = null;

        if (Auth::check() && Auth::user()->isCustomer()) {
            $user = Auth::user();

            $canReview = $user->orders()
                ->where('status', Order::STATUS_DELIVERED)
                ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
                ->exists();

            $userReview = ProductReview::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();
        }

        $relatedProducts = Product::query()
            ->active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->limit(4)
            ->get();

        return view('customers.products.show', compact(
            'product',
            'relatedProducts',
            'averageRating',
            'reviewCount',
            'canReview',
            'userReview'
        ));
    }

    public function contact()
    {
        $myContacts = Auth::check()
            ? \App\Models\Contact::where('user_id', Auth::id())
                ->latest()
                ->get()
            : collect();

        return view('customers.contact', compact('myContacts'));
    }

        public function post(Request $request)
    {
        $featuredPost = Post::query()
            ->published()
            ->with('author')
            ->inRandomOrder()
            ->first();

        $posts = Post::query()
            ->published()
            ->with('author')
            ->when($featuredPost, fn ($q) => $q->where('id', '!=', $featuredPost->id))
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('customers.posts.index', compact('featuredPost', 'posts'));
    }

    public function postShow(string $slug)
    {
        $post = Post::query()
            ->published()
            ->with(['author', 'comments.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        return view('customers.posts.show', compact('post', 'relatedPosts'));
    }
}
