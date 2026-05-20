<?php

namespace App\Http\Controllers;

use App\Enums\TicketStates;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return response()->json(
                Ticket::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->orderBy('created_at', 'desc')
                    ->get()
            );
        }

        return response()->json([], 200);
    }

    public function store(Request $request)
    {
        $ticket = Ticket::create([
            'user_id'     => $request->user()?->id ?? $request->id,
            'name'        => $request->name,
            'email'       => $request->email,
            'product_ref' => $request->productRef,
            'description' => $request->description,
            'status'      => TicketStates::Open,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket creado correctamente',
            'ticket'  => $ticket,
        ], 201);
    }

    public function adminIndex(Request $request)
    {
        $query = Ticket::with('user:id,name,email')->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate(15));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|integer|in:1,2']);

        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del ticket actualizado',
        ]);
    }
}
