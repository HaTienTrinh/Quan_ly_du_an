<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Xem giỏ hàng
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        $itemCount = 0;

        foreach ($cart as $item) {
            $total += $item['subtotal'];
            $itemCount += $item['quantity'];
        }

        return view('customers.cart.index', [
            'cart' => $cart,
            'total' => $total,
            'itemCount' => $itemCount,
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'product_color_id' => ['nullable', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::with('colors')->find((int) $validated['product_id']);

        if (! $product) {
            return back()->with('error', 'Sản phẩm không tồn tại');
        }

        $productColor = $this->resolveProductColor($product, $validated['product_color_id'] ?? null);

        if ($product->colors->filter(fn($c) => $c->size)->isNotEmpty() && ! $productColor) {
            return back()->withErrors([
                'product_color_id' => 'Vui lòng chọn size trước khi thêm vào giỏ hàng.',
            ])->withInput();
        }

        $quantity = (int) $validated['quantity'];
        $cart = Session::get('cart', []);
        $itemKey = $this->buildCartItemKey($product->id, $productColor?->id);
        $currentQuantity = (int) ($cart[$itemKey]['quantity'] ?? 0);
        $nextQuantity = $currentQuantity + $quantity;

        if ($nextQuantity > $product->stock) {
            return back()->with('error', 'Số lượng vượt quá tồn kho hiện có của sản phẩm.');
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] = $nextQuantity;
        } else {
            $cart[$itemKey] = [
                'item_key'           => $itemKey,
                'product_id'         => $product->id,
                'product_name'       => $product->name,
                'product_color_id'   => $productColor?->id,
                'product_color_name' => $productColor?->name,
                'product_color_hex'  => $productColor?->hex_code,
                'product_size'       => $productColor?->size,
                'product_thumbnail'  => $product->thumbnail,
                'unit_price'         => $product->price,
                'quantity'           => $quantity,
            ];
        }

        $cart[$itemKey]['subtotal'] = $cart[$itemKey]['unit_price'] * $cart[$itemKey]['quantity'];

        Session::put('cart', $cart);

        return back()->with('success', 'Thêm sản phẩm vào giỏ hàng thành công');
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function update(Request $request, $productId)
    {
        $quantity = (int) $request->input('quantity', 1);
        $cart = Session::get('cart', []);

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } elseif (isset($cart[$productId])) {
            $product = Product::find($cart[$productId]['product_id']);

            if ($product && $quantity > $product->stock) {
                return back()->with('error', 'Số lượng vượt quá tồn kho hiện có của sản phẩm.');
            }

            $cart[$productId]['quantity'] = $quantity;
            $cart[$productId]['subtotal'] = $cart[$productId]['unit_price'] * $quantity;
        }

        Session::put('cart', $cart);

        return back()->with('success', 'Cập nhật giỏ hàng thành công');
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove($productId)
    {
        $cart = Session::get('cart', []);

        unset($cart[$productId]);

        Session::put('cart', $cart);

        return back()->with('success', 'Xóa sản phẩm khỏi giỏ hàng thành công');
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        Session::forget('cart');

        return back()->with('success', 'Giỏ hàng đã được xóa trống');
    }

    private function resolveProductColor(Product $product, mixed $productColorId): ?ProductColor
    {
        if (! $productColorId) {
            return null;
        }

        return $product->colors->firstWhere('id', (int) $productColorId);
    }

    private function buildCartItemKey(int $productId, ?int $productColorId): string
    {
        return $productId . '-' . ($productColorId ?? 0);
    }
}