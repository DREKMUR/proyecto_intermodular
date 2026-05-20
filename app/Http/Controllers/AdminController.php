<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Brand;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Enums\CarStates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'total_cars'     => Car::count(),
            'total_orders'   => Order::count(),
            'total_users'    => User::count(),
            'low_stock'      => Car::where('stock', '>', 0)->where('stock', '<', 5)->count(),
            'out_of_stock'   => Car::where('stock', '<=', 0)->count(),
            'total_revenue'  => Order::sum('total'),
        ]);
    }

    public function products(Request $request)
    {
        $query = Car::select('cars.*')
            ->selectRaw('COALESCE((SELECT SUM(quantity) FROM order_items WHERE product_id = cars.id), 0) as total_sold')
            ->with('brand:id,name');

        if ($request->search) {
            $query->where('cars.name', 'like', "%{$request->search}%");
        }

        $sortField = match ($request->sort) {
            'stock' => 'cars.stock',
            'price' => 'cars.price',
            'sold'  => 'total_sold',
            'name'  => 'cars.name',
            default => 'cars.name',
        };
        $sortDir = $request->dir === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortDir);

        return response()->json($query->paginate(15));
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'brand_id'        => 'required|exists:brands,id',
            'name'            => 'required|string|max:255',
            'model'           => 'required|string|max:255',
            'year'            => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price'           => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'category'        => 'required|string',
            'imageRoute'      => 'required|string',
            'specs'           => 'nullable|json',
            'discount'        => 'nullable|numeric|min:0|max:100',
        ]);

        $data['slug'] = Str::slug($data['name'] . '-' . $data['year']);
        $data['state'] = $data['stock'] > 0 ? CarStates::Available : CarStates::Sold;

        $car = Car::create($data);
        return response()->json($car, 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $data = $request->validate([
            'brand_id'        => 'sometimes|exists:brands,id',
            'name'            => 'sometimes|string|max:255',
            'model'           => 'sometimes|string|max:255',
            'year'            => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'price'           => 'sometimes|numeric|min:0',
            'stock'           => 'sometimes|integer|min:0',
            'category'        => 'sometimes|string',
            'imageRoute'      => 'sometimes|string',
            'specs'           => 'nullable',
            'discount'        => 'nullable|numeric|min:0|max:100',
            'state'           => 'sometimes|string',
        ]);

        if (isset($data['specs']) && is_array($data['specs'])) {
            $data['specs'] = json_encode($data['specs']);
        }

        if (isset($data['stock'])) {
            $data['state'] = $data['stock'] > 0 ? CarStates::Available : CarStates::Sold;
        }

        $car->update($data);
        return response()->json($car->fresh());
    }

    public function deleteProduct($id)
    {
        Car::findOrFail($id)->delete();
        return response()->json(['message' => 'Producto eliminado.']);
    }

    public function bulkDiscount(Request $request)
    {
        $request->validate(['discount' => 'required|numeric|min:0|max:100']);
        Car::query()->update(['discount' => $request->discount]);
        return response()->json(['message' => "Descuento del {$request->discount}% aplicado a todos los productos."]);
    }

    public function brands()
    {
        return response()->json(Brand::withCount('cars')->get());
    }

    public function storeBrand(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255|unique:brands',
            'logo'    => 'nullable|string',
            'country' => 'nullable|string|max:255',
        ]);
        return response()->json(Brand::create($data), 201);
    }

    public function updateBrand(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $data = $request->validate([
            'name'    => 'sometimes|string|max:255|unique:brands,name,' . $id,
            'logo'    => 'nullable|string',
            'country' => 'nullable|string|max:255',
        ]);
        $brand->update($data);
        return response()->json($brand);
    }

    public function deleteBrand($id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->cars()->exists()) {
            return response()->json(['message' => 'Hay coches asociados a esta marca. Reasígnelos primero.'], 422);
        }
        $brand->delete();
        return response()->json(['message' => 'Marca eliminada.']);
    }

    public function orders()
    {
        return response()->json(
            Order::with('user:id,name,email', 'items')
                ->orderBy('created_at', 'desc')
                ->paginate(15)
        );
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate(['status' => 'required|string|in:pending,completed,cancelled,shipped']);
        $order->update($data);
        return response()->json($order);
    }

    public function deleteOrder($id)
    {
        Order::findOrFail($id)->delete();
        return response()->json(['message' => 'Pedido eliminado.']);
    }

    public function users()
    {
        return response()->json(
            User::withCount('orders')->orderBy('created_at', 'desc')->paginate(15)
        );
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'is_admin' => 'sometimes|boolean',
        ]);
        $user->update($data);
        return response()->json($user);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'No puedes eliminarte a ti mismo.'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado.']);
    }

    public function salesData()
    {
        $sales = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->get();

        return response()->json($sales);
    }
}
