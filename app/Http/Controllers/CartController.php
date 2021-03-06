<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Darryldecode\Cart\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, int $id)
    {
        $prod = Product::find($id);
        $prod->quantity = $request->quantity;
        \Cart::session(auth()->id())->add(array(
            'id'              => $prod->id,
            'name'            => $prod->title,
            'price'           => $prod->price,
            'quantity'        => $prod->quantity,
            'associatedModel' => $prod,
        ));

        return back();
    }

    public function index()
    {
        $items = \Cart::session(auth()->id())->getContent()->sortBy('id');

        return view('private', compact('items'));
    }

    public function edit(Request $request, int $id)
    {
        \Cart::session(auth()->id())->update($id,[
            'quantity' => array(
            'relative' => false,
            'value'    => $request->quantity,)
            ]);

        return back();
    }

    public function remove(int $id)
    {
        \Cart::session(auth()->id())->remove($id);

        return back();
    }

    public function createOrder()
    {
        $items = \Cart::session(auth()->id())->getContent();

        if (empty($items->all())) {
            return redirect(route('cart'))->withErrors(['cart'=>'Нужно что-то положить в корзину!']);
        }

        $order = Order::create([
                'user_id'     => auth()->id(),
                'status'      => 'В обработке',
                'total_price' => \Cart::session(auth()->id())->getTotal(),
            ]);

        $orderId = $order->id;
        foreach ($items as $item) {
        OrderProduct::create([
            'order_id'         => $orderId,
            'product_id'       => $item->id,
            'product_price'    => $item->price,
            'product_quantity' => $item->quantity,
            ]);
        }
        \Cart::session(auth()->id())->clear();

        return back();
    }

}
