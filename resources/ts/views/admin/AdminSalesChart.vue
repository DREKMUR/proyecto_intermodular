<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import HeaderBase from "@/components/HeaderBase.vue";
import FooterBase from "@/components/FooterBase.vue";
import AdminNav from "@/components/admin/AdminNav.vue";
import Skeleton from '@volt/Skeleton.vue';

interface SaleItem {
    product_id: number;
    product_name: string;
    total_sold: number;
}

const salesData = ref<SaleItem[]>([]);
const loading = ref(true);
const canvasRef = ref<HTMLCanvasElement | null>(null);

const colors = ['#0061ad', '#d10c3c', '#f59e0b', '#10b981', '#8b5cf6', '#06b6d4', '#f97316', '#ec4899', '#14b8a6', '#6366f1'];

function drawGraphic(canvas: HTMLCanvasElement, ctx: CanvasRenderingContext2D, vals: number[], labels: string[], title: string, colors: string[]) {
    const ml = 35;
    const mb = 55;
    const mt = 30;

    const graphicHeight = canvas.height - mt - mb;
    const graphicWidth = canvas.width - ml;
    const spaceBetweenColumns = graphicWidth / vals.length;

    let valMax = Math.max(...vals, 1);
    valMax = Math.ceil((valMax + 1) / 5) * 5;

    const totalLines = 5;

    ctx.textAlign = "right";
    ctx.font = "bold 14px Arial";
    ctx.fillStyle = "rgba(255,255,255,0.5)";

    for (let i = 0; i < totalLines; i++) {
        const valor = Math.round(i * (valMax / (totalLines - 1)));
        const lineHeight = (valor / valMax) * graphicHeight;
        const spaceBetweenLines = canvas.height - mb - lineHeight;

        ctx.strokeStyle = "rgba(255,255,255,0.1)";
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(ml, spaceBetweenLines);
        ctx.lineTo(canvas.width, spaceBetweenLines);
        ctx.stroke();

        ctx.fillText(String(valor), ml - 5, spaceBetweenLines + 5);
    }

    const margen = 5;
    const barWidth = spaceBetweenColumns - 5 * margen;

    for (let i = 0; i < vals.length; i++) {
        const valor = vals[i] ?? 0;
        const barHeight = (valor / valMax) * graphicHeight;
        const x = ml + i * spaceBetweenColumns + margen;
        const y = canvas.height - mb - barHeight;

        ctx.fillStyle = colors[i % colors.length] ?? '#0061ad';
        ctx.fillRect(x, y, barWidth, barHeight);

        ctx.lineWidth = 2;
        ctx.strokeStyle = "rgba(0,0,0,0.3)";
        ctx.strokeRect(x, y, barWidth, barHeight);

        ctx.fillStyle = "rgba(255,255,255,0.9)";
        ctx.font = "bold 14px Arial";
        ctx.textAlign = "center";
        ctx.fillText(String(valor), x + barWidth / 2, y - 8);

        ctx.fillStyle = "rgba(255,255,255,0.6)";
        ctx.font = "12px Arial";
        const lbl = labels[i] ?? '';
        const label = lbl.length > 14 ? lbl.slice(0, 12) + '...' : lbl;
        ctx.save();
        ctx.translate(x + barWidth / 2, canvas.height - mb + 10);
        ctx.rotate(-Math.PI / 6);
        ctx.textAlign = "right";
        ctx.fillText(label, 0, 0);
        ctx.restore();
    }

    ctx.fillStyle = "rgba(255,255,255,0.7)";
    ctx.font = "bold 14px Arial";
    ctx.textAlign = "center";
    ctx.fillText(title, canvas.width / 2, canvas.height - 10);
}

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/admin/sales');
        salesData.value = data;
    } catch (e) { console.error(e); }
    finally {
        loading.value = false;
        await nextTick();
        const canvas = canvasRef.value;
        if (!canvas || salesData.value.length === 0) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const vals = salesData.value.map(s => s.total_sold);
        const labels = salesData.value.map(s => s.product_name);
        drawGraphic(canvas, ctx, vals, labels, 'Ventas por producto', colors);
    }
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-linear-to-tl from-black to-primary text-white">
        <HeaderBase />
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <AdminNav />

            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2"><i class="pi pi-chart-bar"></i> Ventas por Producto</h2>

            <Skeleton v-if="loading" width="100%" height="400px" />

            <div v-else-if="salesData.length === 0" class="bg-black/30 rounded-2xl border border-white/5 p-12 text-center">
                <i class="pi pi-chart-bar text-5xl opacity-20 mb-4 block"></i>
                <p class="opacity-50">No hay datos de ventas todavía.</p>
            </div>

            <div v-else class="flex justify-center">
                <canvas
                    ref="canvasRef"
                    width="600"
                    height="500"
                    class="border-3 border-white/20 rounded-b-2xl rounded-tr-2xl"
                />
            </div>
        </main>
        <FooterBase />
    </div>
</template>
