<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('q')) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->q.'%')
                      ->orWhere('slug', 'like', '%'.$request->q.'%');
            });
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'sale_price'  => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock'       => ['required', 'integer', 'min:0'],
            'thumbnail'   => ['nullable', 'image', 'max:4096'],
            'colors'      => ['nullable', 'array'],
            'colors.*.name' => ['nullable', 'string', 'max:255'],
            'colors.*.hex_code' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        $product = Product::create($data);
        $this->syncColors($product, $request->input('colors', []));

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Đã thêm sản phẩm thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product->load('colors');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'sale_price'  => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock'       => ['required', 'integer', 'min:0'],
            'thumbnail'   => ['nullable', 'image', 'max:4096'],
            'colors'      => ['nullable', 'array'],
            'colors.*.name' => ['nullable', 'string', 'max:255'],
            'colors.*.hex_code' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail && !Str::startsWith($product->thumbnail, ['http://', 'https://'])) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        $product->update($data);
        $this->syncColors($product, $request->input('colors', []));

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Đã cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Đã chuyển sản phẩm vào thùng rác.');
    }

    public function trashed(Request $request)
    {
        $query = Product::onlyTrashed()->with('category');

        if ($request->filled('q')) {
            $search = $request->string('q')->trim();
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                      ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        $products = $query->latest('deleted_at')->paginate(15)->withQueryString();

        return view('admin.products.trashed', compact('products'));
    }

    public function restore(int $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()
            ->route('admin.products.trashed')
            ->with('success', 'Đã khôi phục sản phẩm «'.$product->name.'».');
    }

    public function forceDestroy(int $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        if ($product->thumbnail && !Str::startsWith($product->thumbnail, ['http://', 'https://'])) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        $product->forceDelete();

        return redirect()
            ->route('admin.products.trashed')
            ->with('success', 'Đã xóa vĩnh viễn sản phẩm «'.$product->name.'».');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'colors']);

        return view('admin.products.show', compact('product'));
    }

    private function syncColors(Product $product, array $colors): void
    {
        $normalizedColors = collect($colors)
            ->map(function ($color) {
                $name = trim((string) ($color['name'] ?? ''));
                $hexCode = strtoupper(trim((string) ($color['hex_code'] ?? '')));

                if ($name === '') {
                    return null;
                }

                if ($hexCode !== '' && ! Str::startsWith($hexCode, '#')) {
                    $hexCode = '#' . $hexCode;
                }

                return [
                    'name' => $name,
                    'hex_code' => $hexCode !== '' ? $hexCode : null,
                ];
            })
            ->filter()
            ->values()
            ->all();

        $product->colors()->delete();

        if ($normalizedColors !== []) {
            $product->colors()->createMany($normalizedColors);
        }
    }
}
