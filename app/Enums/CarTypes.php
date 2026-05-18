<?php

namespace App\Enums;

enum CarTypes: string
{
    case Sport = 'Deportivo';
    case ClassicSport = 'Deportivo clasico';
    case Coupe = 'Coupe';
    case Classic = 'Clasico';
    case Lowrider = 'Lowrider';
    case Compact = 'Compacto';
    case Suv = 'SUV';
}
