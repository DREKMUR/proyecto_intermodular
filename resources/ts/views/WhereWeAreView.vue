<script setup lang="ts">
import { ref } from 'vue'
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";

const direccion = ref('Carrer Jaume Pallarès, 0, 43820 Calafell, Tarragona')
const telefono = ref('+34 622 39 11 28')
const email = ref('dmurillo@inscamidemar.cat')
const horario = ref([
    { dias: 'Lunes a Viernes', horas: '9:00 - 14:00 y 16:00 - 19:30' },
    { dias: 'Sábados', horas: '10:00 - 13:30' },
    { dias: 'Domingos y Festivos', horas: 'Cerrado' }
])

const googleMapsUrl = 'https://maps.google.com/?q=Carrer+Jaume+Pallar%C3%A8s,+0,+43820+Calafell,+Tarragona,+Espanya&ftid=0x12a38b85dcb1f51d:0x3d2894be7b096216'

const iframeSrc = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2999.0434320703875!2d1.5714312!3d41.1916327!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12a38b85dcb1f51d%3A0x3d2894be7b096216!2sCarrer%20Jaume%20Pallar%C3%A8s%2C%200%2C%2043820%20Calafell%2C%20Tarragona!5e0!3m2!1ses!2ses!4v1716053000000!5m2!1ses!2ses'
</script>

<template>
    <HeaderBase />

    <div class="flex gap-40 flex-col text-justify text-xl mx-40 my-12">

        <div class="flex gap-10 flex-col justify-center items-center">
            <h1 class="text-center text-5xl font-extrabold">
                Dónde Encontrarnos
            </h1>

            <p>
                ¡Ven a visitarnos! Te esperamos en nuestras instalaciones de Calafell. Si deseas realizar un proyecto a medida o ver nuestros acabados de tuning en persona, puedes encontrarnos directamente en la siguiente dirección o ponerte en contacto con nosotros.
            </p>

            <div class="grid grid-cols-2 gap-4 w-full items-center">
                <div>
                    <strong>
                        - Dirección
                    </strong>
                </div>
                <div class="my-4">
                    {{ direccion }}
                </div>

                <div>
                    <strong>
                        - Teléfono
                    </strong>
                </div>
                <div class="my-4">
                    <a :href="`tel:${telefono}`" class="hover:underline font-semibold">{{ telefono }}</a>
                </div>

                <div>
                    <strong>
                        - Correo Electrónico
                    </strong>
                </div>
                <div class="my-4">
                    <a :href="`mailto:${email}`" class="hover:underline  font-semibold">{{ email }}</a>
                </div>
            </div>
        </div>

        <div class="flex gap-10 flex-col">
            <h1 class="text-center text-5xl font-extrabold">
                Horarios y Mapa
            </h1>

            <p>
                Disponemos de un amplio horario comercial para adaptarnos a tus necesidades. Además, a continuación puedes ver nuestra ubicación exacta e interactuar con el mapa o abrirlo directamente en tu navegador para guiarte en el camino.
            </p>

            <div class="grid grid-cols-2 gap-4 w-full items-center">
                <template v-for="(item, index) in horario" :key="index">
                    <div>
                        <strong>
                            - {{ item.dias }}
                        </strong>
                    </div>
                    <div class="my-4">
                        {{ item.horas }}
                    </div>
                </template>
            </div>

            <div class="w-full flex flex-col items-center gap-6 mt-4">
                <div class="w-full h-137.5 overflow-hidden rounded-lg shadow-sm border border-gray-200">
                    <iframe
                        :src="iframeSrc"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        :allowfullscreen="true"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>

                <p class="text-center w-full">
                    ¿Prefieres usar el GPS de tu móvil?
                    <a :href="googleMapsUrl" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline font-bold">
                        Haz clic aquí para abrir en Google Maps
                    </a>.
                </p>
            </div>
        </div>
    </div>
    <FooterBase />
</template>
