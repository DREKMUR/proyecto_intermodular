<script setup lang="ts">
import { ref } from 'vue'
import HeaderBase from "@/components/HeaderBase.vue"
import FooterBase from "@/components/FooterBase.vue";

const activeFaq = ref<number | null>(null)

const toggleFaq = (index: number) => {
    activeFaq.value = activeFaq.value === index ? null : index
}

const pasosGuia = [
    {
        titulo: '1. Explora el Catálogo',
        descripcion: 'Navega por nuestra sección de catálogo para ver los proyectos base disponibles, kits de carrocería y piezas de rendimiento que ofrecemos para cada plataforma.'
    },
    {
        titulo: '2. Configura tu Ticket',
        descripcion: 'Si eres usuario registrado, puedes iniciar una solicitud de diseño o soporte abriendo un ticket personalizado con los detalles de tu vehículo.'
    },
    {
        titulo: '3. Recibe tu Presupuesto',
        descripcion: 'Nuestro equipo de diseñadores e ingenieros de DeMuFe® revisará tu propuesta y se pondrá en contacto contigo para cerrar los detalles del tuning.'
    }
]

const faqs = [
    {
        pregunta: '¿Hacéis homologaciones de las piezas e instalaciones?',
        respuesta: 'Sí, absolutamente todo. En DeMuFe® nos encargamos de que cualquier reforma (kits aerodinámicos, suspensiones, cambios de llantas o escape) salga de nuestras instalaciones con el proyecto técnico listo para pasar la ITV sin problemas.'
    },
    {
        pregunta: '¿Cuánto tiempo suele tardar un proyecto de tuning completo?',
        respuesta: 'Depende de la complejidad. Un cambio estético exterior básico (llantas, muelles y vinilado) suele tardar entre 3 y 5 días laborables. Proyectos integrales que requieren ensanche de carrocería y potenciación mecánica pueden llevar de 2 a 4 semanas.'
    },
    {
        pregunta: '¿Tengo que llevar el coche obligatoriamente a Calafell?',
        respuesta: 'Para las modificaciones mecánicas y de carrocería complejas, el coche debe recibir el tratamiento en nuestro taller principal. No obstante, realizamos envíos de accesorios y piezas de tuning listas para montar a toda España.'
    },
    {
        pregunta: '¿Cuáles son los métodos de pago aceptados y ofrecéis financiación?',
        respuesta: 'Aceptamos tarjetas de crédito/débito, PayPal y plataformas de pago móvil. Para proyectos de gran envergadura, disponemos de opciones de financiación a medida de hasta 12 meses directamente en nuestra oficina.'
    }
]
</script>

<template>
    <HeaderBase />

    <div class="flex gap-16 md:gap-40 flex-col text-justify text-lg md:text-xl mx-6 sm:mx-16 lg:mx-40 my-10 md:my-12">

        <div class="flex gap-6 md:gap-10 flex-col justify-center items-center">
            <h1 class="text-center text-3xl md:text-5xl font-extrabold">
                Guía de la Web
            </h1>

            <p>
                Bienvenido a la plataforma digital de <strong>DeMuFe®</strong>. Hemos diseñado este espacio para que transformar tu vehículo sea un proceso intuitivo, transparente y completamente personalizado. A continuación, te mostramos cómo funciona nuestra interfaz para sacar el máximo partido a tu experiencia.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4 w-full md:items-center">
                <template v-for="(paso, index) in pasosGuia" :key="index">
                    <div>
                        <strong>
                            {{ paso.titulo }}
                        </strong>
                    </div>
                    <div class="mb-4 md:my-4">
                        {{ paso.descripcion }}
                    </div>
                </template>
            </div>
        </div>

        <div class="flex gap-6 md:gap-10 flex-col">
            <h1 class="text-center text-3xl md:text-5xl font-extrabold">
                Preguntas Frecuentes
            </h1>

            <p class="mb-4">
                Antes de iniciar tu proyecto, es normal que te surjan dudas sobre los procesos, las homologaciones o los tiempos de entrega. Aquí tienes las respuestas a las consultas más habituales de nuestros clientes:
            </p>

            <div class="w-full space-y-4">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="border-b border-gray-200 pb-4 transition-all"
                >
                    <button
                        @click="toggleFaq(index)"
                        class="w-full flex justify-between items-center text-left font-bold text-xl md:text-2xl py-2 hover:text-secondary-hover hover:cursor-pointer transition-colors focus:outline-none"
                    >
                        <span>- {{ faq.pregunta }}</span>
                        <span class="text-xl ml-4 transform transition-transform duration-200 shrink-0" :class="{ '-rotate-30': activeFaq === index }">
                            {{ activeFaq === index ? '▲' : '▼' }}
                        </span>
                    </button>

                    <div
                        v-show="activeFaq === index"
                        class="mt-3 text-slate-300 pl-4 border-l-2 border-slate-400 transition-all duration-300 ease-in-out"
                    >
                        <p class="leading-relaxed">
                            {{ faq.respuesta }}
                        </p>
                    </div>
                </div>
            </div>

            <p class="mt-8">
                ¿Sigues teniendo dudas sobre tu coche o tienes una idea tan loca que no aparece aquí? No te preocupes. Pásate por nuestra sección de
                <RouterLink :to="{ name: 'contactUs' }" class="text-blue-600 hover:underline font-bold">Contáctanos</RouterLink>
                o ven a vernos directamente a nuestro taller mecánico en Calafell.
            </p>
        </div>

    </div>

    <FooterBase />
</template>
