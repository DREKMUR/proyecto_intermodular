<script setup lang="ts">
import Galleria from 'primevue/galleria';

const images = [
    {
        itemImageSrc: '/images/honda_civic.png',
        alt: 'Honda Civic',
        title: 'Honda Civic'
    },
    {
        itemImageSrc: '/images/lancer.png',
        alt: 'Mitsubishi Lancer Evolution',
        title: 'Mitsubishi Lancer Evolution'
    },
    {
        itemImageSrc: '/images/nose.png',
        alt: 'Nose',
        title: 'Nose'
    },
    {
        itemImageSrc: '/images/subaru.png',
        alt: 'Subaru',
        title: 'Subaru'
    },
    {
        itemImageSrc: '/images/coche_rojo.png',
        alt: 'Coche Rojo',
        title: 'Coche Rojo'
    },
    {
        itemImageSrc: '/images/carton.png',
        alt: 'Coche cartón',
        title: 'Coche cartón'
    },
    {
        itemImageSrc: '/images/porsche.png',
        alt: 'Porsche',
        title: 'Porsche'
    },
]

</script>

<template>
    <div class="flex gap-40 flex-col text-justify text-xl mx-40">
        <div class="flex gap-10 flex-col justify-center items-center">
            <h1 class="text-center text-5xl font-extrabold">
                ¡Bienvenido!
            </h1>

            <p>
                Somos <strong>DeMuFe®</strong>, una empresa fundada en el 2026 con
                la misión de ofrecer una experiencia de tuning unica en el mercado.
                Nuestro objetivo es conseguir hacer que coches comunes en las calles,
                pasen a ser unicos en el mundo.
            </p>

            <Galleria :value="images" containerStyle="max-width: 100vh" :showThumbnails="false" circular showItemNavigators showItemNavigatorsOnHover>
                <template #item="slotProps">
                    <img :src="slotProps.item.itemImageSrc" :alt="slotProps.item.alt"/>
                </template>
                <template #thumbnail="slotProps">
                    <img :src="slotProps.item.thumbnailImageSrc" :alt="slotProps.item.alt" />
                </template>
            </Galleria>
        </div>

        <div class="flex gap-10 flex-col">
            <h1 class="text-center text-5xl font-extrabold">
                    ¿Qué ofrecemos?
            </h1>

            <p>
                Convertimos vehículos de uso diario en piezas únicas, combinando diseño,
                rendimiento y personalidad. Nuestro trabajo va más allá del simple tuneo:
                reinterpretamos cada coche para llevarlo a un nivel superior, manteniendo
                su esencia pero potenciando cada detalle.
            </p>

            <div class="grid grid-cols-2 gap-4 w-full items-center">
                <div>
                    <strong>
                        - Diseño exterior personalizado
                    </strong>
                </div>
                <div class="my-4">
                    Kits aerodinámicos, llantas exclusivas y acabados premium que transforman
                    la presencia del vehículo.
                </div>

                <div>
                    <strong>
                    - Interiores a medida
                    </strong>
                </div>
                <div class="my-4">
                    Materiales de alta calidad, combinaciones únicas y un estilo que refleja
                    la identidad del conductor.
                </div>

                <div>
                    <strong>
                        - Mejoras de rendimiento
                    </strong>
                </div>
                <div class="my-4">
                    Ajustes de motor, suspensión y sonido para que el coche no solo luzca distinto,
                    sino que también se sienta distinto.
                </div>

                <div>
                    <strong>
                        - Proyectos totalmente personalizados
                    </strong>
                </div>
                <div class="my-4">
                    Cada vehículo se trabaja como una obra individual, adaptada a los gustos y
                    necesidades del cliente.
                </div>
            </div>

            <p>
                Nuestro objetivo es convertir un coche común en una pieza irrepetible, capaz de
                destacar por su diseño, potencia y carácter.
            </p>
        </div>
    </div>
</template>
