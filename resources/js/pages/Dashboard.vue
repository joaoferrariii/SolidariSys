<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
        backgroundColor: '#6366f1',
        borderRadius: 4,
    }],
};

const pieData = {
    labels: props.doadoresMaisAtivos.map(d => d.doador),
    datasets: [{
        data: props.doadoresMaisAtivos.map(d => d.total),
        backgroundColor: ['#6366f1', '#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe'],
    }],
};

const barOptions = {
    responsive: true,
    plugins: { legend: { display: false } },
};

const pieOptions = {
    responsive: true,
    plugins: { legend: { position: 'bottom' as const } },
};

function gerarPDF() {
    const doc = new jsPDF();
    const dataAtual = new Date().toLocaleDateString('pt-BR');

    // Título
    doc.setFontSize(18);
    doc.setTextColor(99, 102, 241);
    doc.text('SolidariSys — Relatório de Doações', 14, 20);

    // Data
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text(`Gerado em: ${dataAtual}`, 14, 28);

    // Linha separadora
    doc.setDrawColor(200);
    doc.line(14, 32, 196, 32);

    // Cards resumo
    doc.setFontSize(11);
    doc.setTextColor(50);
    doc.text(`Total de Doações: ${props.totalDoacoes}`, 14, 42);
    doc.text(`Total de Itens Doados: ${props.totalItens}`, 80, 42);
    doc.text(`Total de Doadores: ${props.totalDoadores}`, 150, 42);

    // Tabela de doações
    doc.setFontSize(13);
    doc.setTextColor(50);
    doc.text('Listagem de Doações', 14, 55);

    autoTable(doc, {
        startY: 60,
        head: [['Doador', 'Item', 'Quantidade', 'Data']],
        body: props.doacoes.map(d => [d.doador, d.item, d.quantidade, d.data]),
        headStyles: {
            fillColor: [99, 102, 241],
            textColor: 255,
            fontStyle: 'bold',
        },
        alternateRowStyles: {
            fillColor: [245, 245, 255],
        },
        styles: {
            fontSize: 10,
        },
    });

    // Itens mais doados
    const finalY = (doc as any).lastAutoTable.finalY + 10;
    doc.setFontSize(13);
    doc.text('Itens mais doados', 14, finalY);

    autoTable(doc, {
        startY: finalY + 5,
        head: [['Item', 'Total']],
        body: props.itensMaisDoados.map(i => [i.item, i.total]),
        headStyles: {
            fillColor: [99, 102, 241],
            textColor: 255,
            fontStyle: 'bold',
        },
        styles: { fontSize: 10 },
    });

    // Doadores mais ativos
    const finalY2 = (doc as any).lastAutoTable.finalY + 10;
    doc.setFontSize(13);
    doc.text('Doadores mais ativos', 14, finalY2);

    autoTable(doc, {
        startY: finalY2 + 5,
        head: [['Doador', 'Total de Doações']],
        body: props.doadoresMaisAtivos.map(d => [d.doador, d.total]),
        headStyles: {
            fillColor: [99, 102, 241],
            textColor: 255,
            fontStyle: 'bold',
        },
        styles: { fontSize: 10 },
    });

    doc.save(`relatorio-doacoes-${dataAtual.replace(/\//g, '-')}.pdf`);
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
                <button
                    @click="gerarPDF"
                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none"
                >
                    Gerar Relatório PDF
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Cards resumo -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="bg-white rounded-lg shadow-sm p-6 border">
                        <p class="text-sm text-gray-500">Total de Doações</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ totalDoacoes }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 border">
                        <p class="text-sm text-gray-500">Total de Itens Doados</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ totalItens }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 border">
                        <p class="text-sm text-gray-500">Total de Doadores</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ totalDoadores }}</p>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="bg-white rounded-lg shadow-sm p-6 border">
                        <h3 class="text-base font-semibold text-gray-700 mb-4">Itens mais doados</h3>
                        <Bar :data="barData" :options="barOptions" />
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 border">
                        <h3 class="text-base font-semibold text-gray-700 mb-4">Doadores mais ativos</h3>
                        <Pie :data="pieData" :options="pieOptions" />
                    </div>
                </div>

                <!-- Tabela últimas doações -->
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="p-6 border-b">
                        <h3 class="text-base font-semibold text-gray-700">Últimas doações</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-6 py-3 text-left">Doador</th>
                                    <th class="px-6 py-3 text-left">Item</th>
                                    <th class="px-6 py-3 text-left">Quantidade</th>
                                    <th class="px-6 py-3 text-left">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(d, i) in doacoes.slice(0, 10)" :key="i" class="hover:bg-gray-50">
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