<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
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
            'product_size_id' => ['nullable', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::with('sizes')->find((int) $validated['product_id']);

        if (! $product) {
            return back()->with('error', 'Sản phẩm không tồn tại');
        }

        $productSize = $this->resolveProductSize($product, $validated['product_size_id'] ?? null);

        if ($product->sizes->isNotEmpty() && ! $productSize) {
            return back()->withErrors([
                'product_size_id' => 'Vui lòng chọn size trước khi thêm vào giỏ hàng.',
            ])->withInput();
        }

        $quantity = (int) $validated['quantity'];
        $cart = Session::get('cart', []);
        $itemKey = $this->buildCartItemKey($product->id, $productSize?->id);
        $currentQuantity = (int) ($cart[$itemKey]['quantity'] ?? 0);
        $nextQuantity = $currentQuantity + $quantity;

        // Kiểm tra stock theo size (nếu có chọn size) hoặc stock sản phẩm
        $availableStock = $productSize ? $productSize->stock : $product->stock;

        if ($nextQuantity > $availableStock) {
            return back()->with('error', 'Số lượng vượt quá tồn kho hiện có của size này.');
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] = $nextQuantity;
        } else {
            $cart[$itemKey] = [
                'item_key' => $itemKey,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_color_id' => $productSize?->id,
                'product_size_name' => $productSize?->name,
                'product_thumbnail' => $product->thumbnail,
                'unit_price' => $product->price,
                'quantity' => $quantity,
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
            $sizeId = $cart[$productId]['product_color_id'] ?? null;
            $availableStock = $product->stock;

            if ($sizeId) {
                $size = ProductSize::find($sizeId);
                if ($size) {
                    $availableStock = $size->stock;
                }
            }

            if ($quantity > $availableStock) {
                return back()->with('error', 'Số lượng vượt quá tồn kho hiện có của size này.');
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

    private function resolveProductSize(Product $product, mixed $productSizeId): ?ProductSize
    {
        if (! $productSizeId) {
            return null;
        }

        return $product->sizes->firstWhere('id', (int) $productSizeId);
    }

    private function buildCartItemKey(int $productId, ?int $productSizeId): string
    {
        return $productId . '-' . ($productSizeId ?? 0);
    }
}