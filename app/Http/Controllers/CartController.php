<?php

namespace App\Http\Controllers;

use Domain\Cart\Models\CartItem;
use Domain\Product\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index', [
            'items' => cart()->items()
        ]);
    }

    public function add(Product $product)
    {
        cart()->add(
            $product,
            request('quantity', 1),
            request('options', []),
        );

        flash()->info('Товар добавлен в корзину');

        return redirect()->intended(route('cart'));
    }

    public function quantity(CartItem $item)
    {
        cart()->quantity($item, request('quantity', 1));

        flash()->info('Количество товаров изменено');

        return redirect()->intended(route('cart'));
    }

    public function delete(CartItem $item)
    {
        cart()->delete($item);

        flash()->info('Удалено из корзины');

        return redirect()->intended(route('cart'));
    }

    public function truncate()
    {
        cart()->truncate();
        
        flash()->info('Корзина очищена');

        return redirect()->intended(route('cart'));
    }
}
