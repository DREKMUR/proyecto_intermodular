<?php

namespace App\Enums;

enum CarStates: string
{
    case Available = 'Disponible';
    case Sold = 'Vendido';
    case Reserved = 'Reservado';
}
