<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        $product = Product::find($productId);

        if (!$product) {
            return back()->with('error', 'Sản phẩm không tồn tại');
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_thumbnail' => $product->thumbnail,
                'unit_price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        $cart[$productId]['subtotal'] = $cart[$productId]['unit_price'] * $cart[$productId]['quantity'];

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
        } else {
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
                $cart[$productId]['subtotal'] = $cart[$productId]['unit_price'] * $quantity;
            }
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
}
