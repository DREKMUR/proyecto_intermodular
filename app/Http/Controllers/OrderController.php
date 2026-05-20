<?php

namespace App\Http\Controllers;

use App\Enums\CarStates;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function myOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product.brand'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'billing_address'  => 'required|string|max:255',
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|exists:cars,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($request) {
                $user = Auth::user();
                $subtotal = 0;
                $orderItemsData = [];

                foreach ($request->items as $cartItem) {
                    $car = Car::query()->lockForUpdate()->find($cartItem['id']);

                    if ($car->stock < $cartItem['quantity']) {
                        throw new \Exception("El vehículo '{$car->name}' no tiene suficiente stock disponible.");
                    }

                    $itemSubtotal = $car->price * $cartItem['quantity'];
                    $subtotal += $itemSubtotal;

                    $car->decrement('stock', $cartItem['quantity']);

                    if ($car->stock < 1) {
                        $car->update(['state' => CarStates::Sold]);
                    }

                    $orderItemsData[] = [
                        'product_id'     => $car->id,
                        'product_name'   => $car->name,
                        'quantity'       => $cartItem['quantity'],
                        'price'          => $car->price,
                        'has_to_comment' => true,
                    ];
                }

                $discountPercent = $request->input('discount_percent', 0);
                $discountAmount = $subtotal * ($discountPercent / 100);
                $shippingCost = 0.00;

                $total = ($subtotal - $discountAmount) + $shippingCost;

                $order = Order::create([
                    'user_id'          => $user->id,
                    'shipping_address' => $request->shipping_address,
                    'billing_address'  => $request->billing_address,
                    'subtotal'         => $subtotal,
                    'discount_percent' => $discountPercent,
                    'shipping_cost'    => $shippingCost,
                    'total'            => $total,
                    'status'           => 'completed',
                ]);

                $order->items()->createMany($orderItemsData);

                return $order;
            });

            return response()->json([
                'success'  => true,
                'message'  => 'Pedido procesado con éxito.',
                'order_id' => $order->id
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
