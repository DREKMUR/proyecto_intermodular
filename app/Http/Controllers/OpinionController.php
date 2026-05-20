<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Opinion;
use Illuminate\Http\Request;

class OpinionController extends Controller
{
    public function getOpinions(int $idProducte)
    {
        $product = Car::find($idProducte);
        if (!$product) {
            return response()->json([
                'error' => [
                    'code'      => '404',
                    'message'   => 'No se han encontrado opiniones para el producto especificado.',
                    'reason'    => "El identificador del producto (idProducte: $idProducte) no existe.",
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                ]
            ], 404);
        }

        $opinions = Opinion::where('product_id', $idProducte)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $ratings = [
            $opinions->where('rating', 1)->count(),
            $opinions->where('rating', 2)->count(),
            $opinions->where('rating', 3)->count(),
            $opinions->where('rating', 4)->count(),
            $opinions->where('rating', 5)->count(),
        ];

        return response()->json([
            'idProducte'    => (int) $idProducte,
            'date'          => now()->format('Y-m-d H:i:s'),
            'totalOpinions' => $opinions->count(),
            'ratings'       => $ratings,
            'opinions'      => $opinions->map(fn($o) => [
                'opinionId' => (string) $o->id,
                'idUser'    => $o->user_id,
                'user'      => $o->user_name,
                'timeStamp' => $o->created_at->format('Y-m-d H:i:s'),
                'rating'    => (int) $o->rating,
                'title'     => $o->title,
                'opinion'   => $o->opinion,
            ]),
        ]);
    }

    public function sendOpinion(Request $request)
    {
        $validator = validator($request->all(), [
            'idProduct' => 'required|string',
            'rating'    => 'required|integer|between:1,5',
            'title'     => 'required|string',
            'text'      => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $reason = null;
            if ($errors->has('idProduct')) $reason = "El campo 'idProduct' es obligatorio.";
            elseif ($errors->has('text')) $reason = "El campo 'text' es obligatorio.";
            elseif ($errors->has('title')) $reason = "El campo 'title' es obligatorio.";
            elseif ($errors->has('rating')) $reason = "El campo 'rating' es obligatorio y debe estar entre 1 y 5 ambos incluidos.";

            return response()->json([
                'error' => [
                    'code'      => '400',
                    'message'   => 'La petición contiene errores de formato o falta algún campo obligatorio.',
                    'reason'    => $reason,
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                ]
            ], 400);
        }

        $productId = (int) $request->idProduct;
        $product = Car::find($productId);
        if (!$product) {
            return response()->json([
                'error' => [
                    'code'      => '404',
                    'message'   => 'No se han encontrado opiniones para el producto especificado.',
                    'reason'    => "El identificador del producto (idProducte: $productId) no existe.",
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                ]
            ], 404);
        }

        $opinion = Opinion::create([
            'product_id' => $productId,
            'user_id'    => $request->idUser ?? auth()->id(),
            'user_name'  => $request->user ?? auth()->user()?->name,
            'rating'     => (int) $request->rating,
            'title'      => $request->title,
            'opinion'    => $request->text,
        ]);

        if ($request->order_item_id) {
            \App\Models\OrderItem::where('id', $request->order_item_id)
                ->where('has_to_comment', true)
                ->update(['has_to_comment' => false]);
        }

        return response()->json([
            'message'   => 'Opinión añadida correctamente',
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function getRating()
    {
        $products = Car::select('cars.id', 'cars.name')
            ->selectRaw('COUNT(opinions.id) as total_opinions')
            ->selectRaw('COALESCE(AVG(opinions.rating), 0) as avg_rating')
            ->leftJoin('opinions', 'cars.id', '=', 'opinions.product_id')
            ->groupBy('cars.id', 'cars.name')
            ->havingRaw('COUNT(opinions.id) >= 1')
            ->orderByDesc('avg_rating')
            ->take(10)
            ->get();

        return response()->json($products->map(function ($p) {
            $opinions = Opinion::where('product_id', $p->id)->get();
            return [
                'idProducte'    => (int) $p->id,
                'date'          => now()->format('Y-m-d H:i:s'),
                'totalOpinions' => (int) $p->total_opinions,
                'ratings'       => [
                    $opinions->where('rating', 1)->count(),
                    $opinions->where('rating', 2)->count(),
                    $opinions->where('rating', 3)->count(),
                    $opinions->where('rating', 4)->count(),
                    $opinions->where('rating', 5)->count(),
                ],
            ];
        }));
    }

    public function getAllOpinions()
    {
        $products = Car::with('opinions')->get();

        return response()->json($products->map(function ($p) {
            $opinions = $p->opinions;
            return [
                'idProducte'    => (int) $p->id,
                'date'          => now()->format('Y-m-d H:i:s'),
                'totalOpinions' => $opinions->count(),
                'ratings'       => [
                    $opinions->where('rating', 1)->count(),
                    $opinions->where('rating', 2)->count(),
                    $opinions->where('rating', 3)->count(),
                    $opinions->where('rating', 4)->count(),
                    $opinions->where('rating', 5)->count(),
                ],
                'opinions'      => $opinions->map(fn($o) => [
                    'opinionId' => (string) $o->id,
                    'idUser'    => $o->user_id,
                    'user'      => $o->user_name,
                    'timeStamp' => $o->created_at->format('Y-m-d H:i:s'),
                    'rating'    => (int) $o->rating,
                    'title'     => $o->title,
                    'opinion'   => $o->opinion,
                ]),
            ];
        }));
    }

    public function checkPendingOpinion(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['has_pending' => false]);
        }

        $pendingItems = \App\Models\OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('has_to_comment', true)->with('product:id,name')->get();

        return response()->json([
            'has_pending' => $pendingItems->isNotEmpty(),
            'items'       => $pendingItems->map(fn($i) => [
                'order_item_id' => $i->id,
                'product_id'    => $i->product_id,
                'product_name'  => $i->product_name,
            ]),
        ]);
    }

    public function dismissPendingOpinion(Request $request)
    {
        $request->validate(['order_item_id' => 'required|exists:order_items,id']);

        \App\Models\OrderItem::where('id', $request->order_item_id)
            ->whereHas('order', function ($q) {
                $q->where('user_id', auth()->id());
            })->update(['has_to_comment' => false]);

        return response()->json(['message' => 'Dismissed']);
    }
}
