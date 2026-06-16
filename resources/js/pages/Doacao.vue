<script setup lang="ts">
import DataGrid from '@/components/DataGrid.vue';
import DoacaoDialog from '@/components/DoacaoDialog.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    doacoes:  any[];
    doadores: any[];
    itens:    any[];
}>();

const columns = [
    { key: 'id',         label: 'ID',        sortable: true },
    { key: 'doador',     label: 'Doador',    sortable: true },
    { key: 'item',       label: 'Item',      sortable: true },
    { key: 'categoria',  label: 'Categoria', sortable: true },
    { key: 'quantidade', label: 'Qtd',       sortable: true },
    { key: 'data',       label: 'Data',      sortable: true },
];

const dialogOpen     = ref(false);
const doacaoEditando = ref<any>(null);

function handleAdd() {
    doacaoEditando.value = null;
    dialogOpen.value = true;
}

function handleEdit(row: any) {
    doacaoEditando.value = row;
    dialogOpen.value = true;
}

function handleDelete(row: any) {
    if (!confirm(`Deletar doação #${row.id}?`)) return;
    router.delete(route('doacao.destroy', row.id));
}

function handleSave(data: any) {
    if (doacaoEditando.value) {
        router.put(route('doacao.update', doacaoEditando.value.id), data, {
            onSuccess: () => {
                dialogOpen.value = false;
                doacaoEditando.value = null;
            },
        });
    } else {
        router.post(route('doacao.store'), data, {
            onSuccess: () => { dialogOpen.value = false; },
        });
    }
}
</script>

<template>
    <Head title="Doação" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight" style="color: #111827;">
                Doações
            </h2>
        </template>

        <div class="py-12" style="background-color: #F3F4F6;">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg" style="background-color: #FFFFFF;">
                    <div class="p-6">
                        <DataGrid
                            :columns="columns"
                            :data="doacoes"
                            :selectable="true"
                            :show-menubar="true"
                            :pagination="true"
                            :page-size="10"
                            :on-add="handleAdd"
                            :on-edit="handleEdit"
                            :on-delete="handleDelete"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <DoacaoDialog
        :open="dialogOpen"
        :doacao="doacaoEditando"
        :doadores="props.doadores"
        :itens="props.itens"
        @save="handleSave"
        @close="dialogOpen = false"
    />
</template>