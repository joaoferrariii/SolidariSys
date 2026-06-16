<script setup lang="ts">
import DataGrid from '@/components/DataGrid.vue';
import DoadorDialog from '@/components/DoadorDialog.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{ doadores: any[] }>();

const columns = [
    { key: 'id',       label: 'ID',       sortable: true  },
    { key: 'nome',     label: 'Nome',     sortable: true  },
    { key: 'cpf',      label: 'CPF',      sortable: true  },
    { key: 'email',    label: 'E-mail',   sortable: true  },
    { key: 'telefone', label: 'Telefone', sortable: false },
];

const dialogOpen     = ref(false);
const doadorEditando = ref<any>(null);

function handleAdd()          { doadorEditando.value = null; dialogOpen.value = true; }
function handleEdit(row: any) { doadorEditando.value = row;  dialogOpen.value = true; }

function handleDelete(row: any) {
    if (!confirm(`Deletar doador "${row.nome}"?`)) return;
    router.delete(route('cadastro-doador.destroy', row.id));
}

function handleSave(data: any) {
    if (doadorEditando.value) {
        router.put(route('cadastro-doador.update', doadorEditando.value.id), data, {
            onSuccess: () => { dialogOpen.value = false; doadorEditando.value = null; },
        });
    } else {
        router.post(route('cadastro-doador.store'), data, {
            onSuccess: () => { dialogOpen.value = false; },
        });
    }
}
</script>

<template>
    <Head title="Doadores" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight" style="color: #111827;">
                Doadores
            </h2>
        </template>
        <div class="py-12" style="background-color: #F3F4F6;">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg" style="background-color: #FFFFFF;">
                    <div class="p-6">
                        <DataGrid
                            :columns="columns"
                            :data="doadores"
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
    <DoadorDialog
        :open="dialogOpen"
        :doador="doadorEditando"
        @save="handleSave"
        @close="dialogOpen = false"
    />
</template>