<script setup lang="ts">
import DataGrid from '@/components/DataGrid.vue';
import UsuarioDialog from '@/components/UsuarioDialog.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps<{ usuarios: any[] }>();
const page = usePage();

const columns = [
    { key: 'id',    label: 'ID',    sortable: true  },
    { key: 'name',  label: 'Nome',  sortable: true  },
    { key: 'email', label: 'Email', sortable: true  },
    { key: 'cpf',   label: 'CPF',   sortable: true  },
    { key: 'tipo',  label: 'Tipo',  sortable: false },
];

const dialogOpen      = ref(false);
const usuarioEditando = ref<any>(null);

watch(() => page.props.errors, (errors) => {
    if (errors && Object.keys(errors).length > 0) {
        dialogOpen.value = true;
    }
});

function handleAdd()          { usuarioEditando.value = null; dialogOpen.value = true; }
function handleEdit(row: any) { usuarioEditando.value = row;  dialogOpen.value = true; }

function handleDelete(row: any) {
    if (!confirm(`Deletar usuário "${row.name}"?`)) return;
    router.delete(route('cadastro-usuario.destroy', row.id));
}

function handleSave(data: any) {
    if (usuarioEditando.value) {
        router.put(route('cadastro-usuario.update', usuarioEditando.value.id), data, {
            onSuccess: () => { dialogOpen.value = false; usuarioEditando.value = null; },
            onError:   () => { dialogOpen.value = true; },
        });
    } else {
        router.post(route('cadastro-usuario.store'), data, {
            onSuccess: () => { dialogOpen.value = false; },
            onError:   () => { dialogOpen.value = true; },
        });
    }
}
</script>

<template>
    <Head title="Usuários" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight" style="color: #111827;">
                Usuários
            </h2>
        </template>
        <div class="py-12" style="background-color: #F3F4F6;">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg" style="background-color: #FFFFFF;">
                    <div class="p-6">
                        <DataGrid
                            :columns="columns"
                            :data="usuarios"
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
    <UsuarioDialog
        :open="dialogOpen"
        :usuario="usuarioEditando"
        @save="handleSave"
        @close="dialogOpen = false"
    />
</template>