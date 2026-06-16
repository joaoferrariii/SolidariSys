<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Bar, Pie } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title, Tooltip, Legend,
    BarElement, CategoryScale, LinearScale,
    ArcElement,
} from 'chart.js';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const props = defineProps<{
    doacoes:            any[];
    totalDoacoes:       number;
    totalItens:         number;
    totalDoadores:      number;
    itensMaisDoados:    any[];
    doadoresMaisAtivos: any[];
}>();

const barData = {
    labels: props.itensMaisDoados.map(i => i.item),
    datasets: [{
        label: 'Quantidade doada',
        data: props.itensMaisDoados.map(i => i.total),
        backgroundColor: '#1B4332',
        borderRadius: 4,
    }],
};

const pieData = {
    labels: props.doadoresMaisAtivos.map(d => d.doador),
    datasets: [{
        data: props.doadoresMaisAtivos.map(d => d.total),
        backgroundColor: ['#1B4332', '#2D6A4F', '#52B788', '#95D5B2', '#D8F3DC'],
    }],
};

const barOptions = {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
        x: { ticks: { color: '#6B7280' } },
        y: { ticks: { color: '#6B7280' } },
    },
};

const pieOptions = {
    responsive: true,
    plugins: { legend: { position: 'bottom' as const, labels: { color: '#111827' } } },
};

function gerarPDF() {
    const doc = new jsPDF();
    const dataAtual = new Date().toLocaleDateString('pt-BR');

    doc.setFontSize(18);
    doc.setTextColor(27, 67, 50);
    doc.text('SolidariSys — Relatório de Doações', 14, 20);

    doc.setFontSize(10);
    doc.setTextColor(107, 114, 128);
    doc.text(`Gerado em: ${dataAtual}`, 14, 28);

    doc.setDrawColor(243, 244, 246);
    doc.line(14, 32, 196, 32);

    doc.setFontSize(11);
    doc.setTextColor(17, 24, 39);
    doc.text(`Total de Doações: ${props.totalDoacoes}`, 14, 42);
    doc.text(`Total de Itens Doados: ${props.totalItens}`, 80, 42);
    doc.text(`Total de Doadores: ${props.totalDoadores}`, 150, 42);

    doc.setFontSize(13);
    doc.setTextColor(17, 24, 39);
    doc.text('Listagem de Doações', 14, 55);

    autoTable(doc, {
        startY: 60,
        head: [['Doador', 'Item', 'Quantidade', 'Data']],
        body: props.doacoes.map(d => [d.doador, d.item, d.quantidade, d.data]),
        headStyles: { fillColor: [27, 67, 50], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [243, 244, 246] },
        styles: { fontSize: 10, textColor: [17, 24, 39] },
    });

    const finalY = (doc as any).lastAutoTable.finalY + 10;
    doc.setFontSize(13);
    doc.text('Itens mais doados', 14, finalY);

    autoTable(doc, {
        startY: finalY + 5,
        head: [['Item', 'Total']],
        body: props.itensMaisDoados.map(i => [i.item, i.total]),
        headStyles: { fillColor: [27, 67, 50], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [243, 244, 246] },
        styles: { fontSize: 10, textColor: [17, 24, 39] },
    });

    const finalY2 = (doc as any).lastAutoTable.finalY + 10;
    doc.setFontSize(13);
    doc.text('Doadores mais ativos', 14, finalY2);

    autoTable(doc, {
        startY: finalY2 + 5,
        head: [['Doador', 'Total de Doações']],
        body: props.doadoresMaisAtivos.map(d => [d.doador, d.total]),
        headStyles: { fillColor: [27, 67, 50], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [243, 244, 246] },
        styles: { fontSize: 10, textColor: [17, 24, 39] },
    });

    doc.save(`relatorio-doacoes-${dataAtual.replace(/\//g, '-')}.pdf`);
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight" style="color: #111827;">
                    Dashboard
                </h2>
                <button
                    @click="gerarPDF"
                    class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium text-white transition-colors"
                    style="background-color: #1B4332;"
                    onmouseover="this.style.backgroundColor='#2D6A4F'"
                    onmouseout="this.style.backgroundColor='#1B4332'"
                >
                    Gerar Relatório PDF
                </button>
            </div>
        </template>

        <div class="py-12" style="background-color: #F3F4F6;">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

             
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border p-6" style="background-color: #FFFFFF;">
                        <p class="text-sm" style="color: #6B7280;">Total de Doações</p>
                        <p class="text-3xl font-bold mt-1" style="color: #1B4332;">{{ totalDoacoes }}</p>
                    </div>
                    <div class="rounded-lg border p-6" style="background-color: #FFFFFF;">
                        <p class="text-sm" style="color: #6B7280;">Total de Itens Doados</p>
                        <p class="text-3xl font-bold mt-1" style="color: #1B4332;">{{ totalItens }}</p>
                    </div>
                    <div class="rounded-lg border p-6" style="background-color: #FFFFFF;">
                        <p class="text-sm" style="color: #6B7280;">Total de Doadores</p>
                        <p class="text-3xl font-bold mt-1" style="color: #1B4332;">{{ totalDoadores }}</p>
                    </div>
                </div>

       
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-lg border p-6" style="background-color: #FFFFFF;">
                        <h3 class="text-base font-semibold mb-4" style="color: #111827;">Itens mais doados</h3>
                        <Bar :data="barData" :options="barOptions" />
                    </div>
                    <div class="rounded-lg border p-6" style="background-color: #FFFFFF;">
                        <h3 class="text-base font-semibold mb-4" style="color: #111827;">Doadores mais ativos</h3>
                        <Pie :data="pieData" :options="pieOptions" />
                    </div>
                </div>


                <div class="rounded-lg border" style="background-color: #FFFFFF;">
                    <div class="p-6 border-b">
                        <h3 class="text-base font-semibold" style="color: #111827;">Últimas doações</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead style="background-color: #F3F4F6;">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium" style="color: #6B7280;">Doador</th>
                                    <th class="px-6 py-3 text-left font-medium" style="color: #6B7280;">Item</th>
                                    <th class="px-6 py-3 text-left font-medium" style="color: #6B7280;">Quantidade</th>
                                    <th class="px-6 py-3 text-left font-medium" style="color: #6B7280;">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color: #F3F4F6;">
                                <tr
                                    v-for="(d, i) in doacoes.slice(0, 10)"
                                    :key="i"
                                    class="transition-colors"
                                    style="color: #111827;"
                                    onmouseover="this.style.backgroundColor='#F3F4F6'"
                                    onmouseout="this.style.backgroundColor=''"
                                >
                                    <td class="px-6 py-3">{{ d.doador }}</td>
                                    <td class="px-6 py-3">{{ d.item }}</td>
                                    <td class="px-6 py-3">{{ d.quantidade }}</td>
                                    <td class="px-6 py-3">{{ d.data }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>