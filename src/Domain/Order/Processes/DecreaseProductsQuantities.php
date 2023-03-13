<?php

namespace Domain\Order\Processes;

use Domain\Order\Contracts\OrderProcessContract;
use Domain\Order\Models\Order;

class DecreaseProductsQuantities implements OrderProcessContract
{
    public function handle(Order $order, $next)
    {
        foreach (cart()->items() as $item) {
            $item->product->decrement('quantity', $item->quantity);
        }
        return $next($order);
    }
}