<?php

namespace App\Http\Controllers;

use App\Enums\TicketStates;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index(){
        return Ticket::all();
    }

    public function store(Request $request){
        Ticket::create([
            'name' => $request->name,
            'email' => $request->email,
            'product_ref' => $request->productRef,
            'description' => $request->description,
            'user_id' => $request->id,
            'status' => TicketStates::Open,
        ]);

        return redirect()->back();
    }
}
