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

    <div class="flex gap-16 md:gap-40 flex-col text-justify text-lg md:text-xl mx-6 sm:mx-16 lg:mx-40 my-10 md:my-12">

        <div class="flex gap-6 md:gap-10 flex-col justify-center items-center">
            <h1 class="text-center text-3xl md:text-5xl font-extrabold">
                Dónde Encontrarnos
            </h1>

            <p>
                ¡Ven a visitarnos! Te esperamos en nuestras instalaciones de Calafell. Si deseas realizar un proyecto a medida o ver nuestros acabados de tuning en persona, puedes encontrarnos directamente en la siguiente dirección o ponerte en contacto con nosotros.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4 w-full md:items-center">
                <div>
                    <strong>
                        - Dirección
                    </strong>
                </div>
                <div class="mb-4 md:my-4">
                    {{ direccion }}
                </div>

                <div>
                    <strong>
                        - Teléfono
                    </strong>
                </div>
                <div class="mb-4 md:my-4">
                    <a :href="`tel:${telefono}`" class="hover:underline font-semibold">{{ telefono }}</a>
                </div>

                <div>
                    <strong>
                        - Correo Electrónico
                    </strong>
                </div>
                <div class="mb-4 md:my-4">
                    <a :href="`mailto:${email}`" class="hover:underline  font-semibold">{{ email }}</a>
                </div>

                <div>
                    <strong>
                        - Redes Sociales
                    </strong>
                </div>
                <div class="mb-4 md:my-4 flex items-center gap-4">
                    <a href="https://www.instagram.com/derekkkmur/" target="_blank" rel="noopener noreferrer" class="hover:scale-110 transition-transform" title="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-slate-200">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                    <a href="https://www.tiktok.com/@drekmur" target="_blank" rel="noopener noreferrer" class="hover:scale-110 transition-transform" title="TikTok">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-slate-200">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="flex gap-6 md:gap-10 flex-col">
            <h1 class="text-center text-3xl md:text-5xl font-extrabold">
                Horarios y Mapa
            </h1>

            <p>
                Disponemos de un amplio horario comercial para adaptarnos a tus necesidades. Además, a continuación puedes ver nuestra ubicación exacta e interactuar con el mapa o abrirlo directamente en tu navegador para guiarte en el camino.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4 w-full md:items-center">
                <template v-for="(item, index) in horario" :key="index">
                    <div>
                        <strong>
                            - {{ item.dias }}
                        </strong>
                    </div>
                    <div class="mb-4 md:my-4">
                        {{ item.horas }}
                    </div>
                </template>
            </div>

            <div class="w-full flex flex-col items-center gap-6 mt-4">
                <div class="w-full h-80 md:h-137.5 overflow-hidden rounded-lg shadow-sm border border-gray-200">
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

                <p class="text-center w-full text-base md:text-lg">
                    <a :href="googleMapsUrl" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline font-bold">
                        Haz clic aquí para abrir en Google Maps
                    </a>
                </p>
            </div>
        </div>
    </div>
    <FooterBase />
</template>
