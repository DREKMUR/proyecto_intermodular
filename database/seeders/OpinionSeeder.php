<?php

namespace Database\Seeders;

use App\Models\Opinion;
use Illuminate\Database\Seeder;

class OpinionSeeder extends Seeder
{
    public function run(): void
    {
        Opinion::truncate();

        $opinions = [
            ['product_id' => 1, 'user_id' => 2, 'user_name' => 'Derek Murillo', 'rating' => 5, 'title' => 'Espectacular',                       'opinion' => 'El mejor coche que he comprado nunca. La calidad es increíble y el rendimiento es brutal.'],
            ['product_id' => 1, 'user_id' => 3, 'user_name' => 'Laura García',  'rating' => 4, 'title' => 'Muy bueno',                         'opinion' => 'El diseño es impresionante. Le quito una estrella porque el consumo es un poco alto.'],
            ['product_id' => 1, 'user_id' => 5, 'user_name' => 'Anna Costa',    'rating' => 5, 'title' => 'Una pasada',                        'opinion' => 'Completamente recomendable. Hasta ahora el mejor coche que he tenido.'],
            ['product_id' => 3, 'user_id' => 4, 'user_name' => 'Marc López',    'rating' => 5, 'title' => 'Sueño cumplido',                    'opinion' => 'Siempre he querido un Corvette y esta es la mejor versión. Increíble.'],
            ['product_id' => 3, 'user_id' => 6, 'user_name' => 'Pau Roca',      'rating' => 4, 'title' => 'Buen coche',                        'opinion' => 'Muy buen estado general, aunque el precio es un poco elevado. Satisfecho con la compra.'],
            ['product_id' => 4, 'user_id' => 2, 'user_name' => 'Derek Murillo', 'rating' => 3, 'title' => 'Correcto',                          'opinion' => 'Cumple su función. No es espectacular pero es fiable y económico.'],
            ['product_id' => 4, 'user_id' => 5, 'user_name' => 'Anna Costa',    'rating' => 4, 'title' => 'Buena relación calidad-precio',       'opinion' => 'Perfecto para el día a día. Consumo bajo y mantenimiento sencillo.'],
            ['product_id' => 6, 'user_id' => 3, 'user_name' => 'Laura García',  'rating' => 5, 'title' => 'Leyenda viva',                      'opinion' => 'Uno de los mejores coches de su época. Conducirlo es una experiencia única.'],
            ['product_id' => 6, 'user_id' => 4, 'user_name' => 'Marc López',    'rating' => 5, 'title' => 'Increíble',                         'opinion' => 'El Evo VI es el sueño de cualquier fan de los rally. Totalmente recomendado.'],
            ['product_id' => 6, 'user_id' => 6, 'user_name' => 'Pau Roca',      'rating' => 4, 'title' => 'Muy contento',                      'opinion' => 'El coche está en muy buen estado. La tracción integral es impresionante.'],
            ['product_id' => 8, 'user_id' => 1, 'user_name' => 'Admin',         'rating' => 5, 'title' => 'Obra maestra',                      'opinion' => 'El Porsche 911 GT3 RS es simplemente perfecto. Cada detalle está cuidado al máximo.'],
            ['product_id' => 8, 'user_id' => 2, 'user_name' => 'Derek Murillo', 'rating' => 5, 'title' => 'El mejor deportivo',               'opinion' => 'Sensacional. Aceleración, sonido, diseño... Todo es de primera calidad.'],
            ['product_id' => 10, 'user_id' => 3, 'user_name' => 'Laura García', 'rating' => 4, 'title' => 'Gran compra',                      'opinion' => 'El Supra es una bestia. El motor es increíblemente potente. Muy satisfecha.'],
            ['product_id' => 10, 'user_id' => 5, 'user_name' => 'Anna Costa',   'rating' => 3, 'title' => 'Esperaba un poco más',              'opinion' => 'Buen coche pero el diseño interior podría ser mejor. La mecánica sí es buena.'],
            ['product_id' => 12, 'user_id' => 4, 'user_name' => 'Marc López',   'rating' => 5, 'title' => 'Auténtico lowrider',                'opinion' => 'El Fleetwood es todo un clásico. La suspensión hidráulica funciona perfectamente.'],
            ['product_id' => 12, 'user_id' => 6, 'user_name' => 'Pau Roca',     'rating' => 4, 'title' => 'Muy auténtico',                    'opinion' => 'El coche está muy bien conservado. Ideal para exhibiciones.'],
            ['product_id' => 5, 'user_id' => 1, 'user_name' => 'Admin',         'rating' => 4, 'title' => 'Legendario',                        'opinion' => 'El Type R EK9 es uno de los mejores compactos deportivos. Muy divertido de conducir.'],
            ['product_id' => 5, 'user_id' => 2, 'user_name' => 'Derek Murillo', 'rating' => 5, 'title' => 'Pura competición',                  'opinion' => 'El motor VTEC es una maravilla. Un coche puro y sin concesiones.'],
            ['product_id' => 2, 'user_id' => 5, 'user_name' => 'Anna Costa',    'rating' => 2, 'title' => 'No me ha gustado',                 'opinion' => 'El diseño es original pero la calidad de los materiales deja mucho que desear.'],
        ];

        foreach ($opinions as $opinion) {
            Opinion::create($opinion);
        }

        $this->command->info('Seeded ' . count($opinions) . ' opinions successfully!');
    }
}
