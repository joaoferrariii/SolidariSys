<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Props {
    open: boolean;
    doador?: any;
}

const props = withDefaults(defineProps<Props>(), {
    open: false,
    doador: null,
});

const emit = defineEmits<{
    save: [data: any];
    close: [];
    'update:open': [value: boolean];
}>();

const formData = ref({
    nome: '', cpf: '', email: '', telefone: '',
});

const isEditMode  = computed(() => !!props.doador);
const dialogTitle = computed(() => isEditMode.value ? 'Editar Doador' : 'Novo Doador');

watch(() => props.doador, (val) => {
    if (!val) { resetForm(); return; }
    formData.value = {
        nome:     val.nome     || '',
        cpf:      val.cpf      || '',
        email:    val.email    || '',
        telefone: val.telefone || '',
    };
}, { immediate: true, deep: true });

watch(() => props.open, (val) => { if (!val) resetForm(); });

function resetForm() {
    formData.value = { nome: '', cpf: '', email: '', telefone: '' };
}

function handleSave() {
    if (!formData.value.nome) {
        alert('O nome é obrigatório.');
        return;
    }
    emit('save', { ...formData.value });
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
                    <Label style="color: #111827;">Nome *</Label>
                    <Input
                        v-model="formData.nome"
                        placeholder="Nome completo"
                        class="focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827;"
                    />
                </div>
                <div class="grid gap-2">
                    <Label style="color: #6B7280;">CPF</Label>
                    <Input
                        v-model="formData.cpf"
                        placeholder="000.000.000-00"
                        class="focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827;"
                    />
                </div>
                <div class="grid gap-2">
                    <Label style="color: #6B7280;">E-mail</Label>
                    <Input
                        v-model="formData.email"
                        type="email"
                        placeholder="email@exemplo.com"
                        class="focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827;"
                    />
                </div>
                <div class="grid gap-2">
                    <Label style="color: #6B7280;">Telefone</Label>
                    <Input
                        v-model="formData.telefone"
                        placeholder="(00) 00000-0000"
                        class="focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827;"
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