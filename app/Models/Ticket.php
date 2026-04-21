<?php
namespace App\Models;

use App\Enums\TicketStates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'product_ref',
        'description',
        'status'
    ];

    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'product_ref' => 'string',
        'status' => TicketStates::class,
    ];
}
