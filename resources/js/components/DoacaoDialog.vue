<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Doador { id: number; nome: string; }
interface Item   { id: number; nome_item: string; }

interface Props {
    open: boolean;
    doacao?: any;
    doadores: Doador[];
    itens: Item[];
}

const props = withDefaults(defineProps<Props>(), {
    open: false,
    doacao: null,
    doadores: () => [],
    itens: () => [],
});

const emit = defineEmits<{
    save: [data: any];
    close: [];
    'update:open': [value: boolean];
}>();

const formData = ref({
    doador_id:  null as number | null,
    item_id:    null as number | null,
    quantidade: 1,
    data:       '',
});

const isEditMode  = computed(() => !!props.doacao);
const dialogTitle = computed(() => isEditMode.value ? 'Editar Doação' : 'Nova Doação');

watch(
    () => [props.open, props.doacao],
    ([isOpen, val]) => {
        if (!isOpen) return;
        if (!val) { resetForm(); return; }
        formData.value = {
            doador_id:  val.doador_id,
            item_id:    val.item_id,
            quantidade: val.quantidade,
            data:       String(val.data).substring(0, 10),
        };
    },
    { immediate: true, deep: true }
);

function resetForm() {
    formData.value = { doador_id: null, item_id: null, quantidade: 1, data: '' };
}

function handleSave() {
    if (!formData.value.doador_id || !formData.value.item_id || !formData.value.data) {
        alert('Preencha todos os campos obrigatórios.');
        return;
    }
    emit('save', {
        doador_id:  formData.value.doador_id,
        item_id:    formData.value.item_id,
        quantidade: formData.value.quantidade,
        data:       formData.value.data,
    });
}

function handleClose() {
    emit('close');
    emit('update:open', false);
}
</script>

<template>
    <Dialog :open="open" @update:open="handleClose">
        <DialogContent class="max-w-md" style="background-color: #FFFFFF;">
            <DialogHeader>
                <DialogTitle style="color: #111827;">{{ dialogTitle }}</DialogTitle>
            </DialogHeader>

            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label style="color: #111827;">Doador *</Label>
                    <select
                        v-model="formData.doador_id"
                        class="flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827; background-color: #FFFFFF;"
                    >
                        <option :value="null" disabled style="color: #6B7280;">Selecione o doador...</option>
                        <option v-for="d in doadores" :key="d.id" :value="d.id">{{ d.nome }}</option>
                    </select>
                </div>

                <div class="grid gap-2">
                    <Label style="color: #111827;">Item *</Label>
                    <select
                        v-model="formData.item_id"
                        class="flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827; background-color: #FFFFFF;"
                    >
                        <option :value="null" disabled style="color: #6B7280;">Selecione o item...</option>
                        <option v-for="i in itens" :key="i.id" :value="i.id">{{ i.nome_item }}</option>
                    </select>
                </div>

                <div class="grid gap-2">
                    <Label style="color: #111827;">Quantidade *</Label>
                    <Input
                        v-model="formData.quantidade"
                        type="number"
                        min="1"
                        class="focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827;"
                    />
                </div>

                <div class="grid gap-2">
                    <Label style="color: #111827;">Data *</Label>
                    <input
                        v-model="formData.data"
                        type="date"
                        class="flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827; background-color: #FFFFFF;"
                    />
                </div>
            </div>

            <DialogFooter class="gap-2 pt-2">
                <button
                    @click="handleClose"
                    class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium transition-colors"
                    style="background-color: #F3F4F6; color: #111827;"
                    onmouseover="this.style.backgroundColor='#E5E7EB'"
                    onmouseout="this.style.backgroundColor='#F3F4F6'"
                >
                    Cancelar
                </button>
                <button
                    @click="handleSave"
                    class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-white transition-colors"
                    style="background-color: #1B4332;"
                    onmouseover="this.style.backgroundColor='#2D6A4F'"
                    onmouseout="this.style.backgroundColor='#1B4332'"
                >
                    {{ isEditMode ? 'Salvar' : 'Criar' }}
                </button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>