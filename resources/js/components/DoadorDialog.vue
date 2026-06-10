<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Button } from '@/components/ui/button';
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
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>{{ dialogTitle }}</DialogTitle>
            </DialogHeader>
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label>Nome *</Label>
                    <Input v-model="formData.nome" placeholder="Nome completo" />
                </div>
                <div class="grid gap-2">
                    <Label>CPF</Label>
                    <Input v-model="formData.cpf" placeholder="000.000.000-00" />
                </div>
                <div class="grid gap-2">
                    <Label>E-mail</Label>
                    <Input v-model="formData.email" type="email" placeholder="email@exemplo.com" />
                </div>
                <div class="grid gap-2">
                    <Label>Telefone</Label>
                    <Input v-model="formData.telefone" placeholder="(00) 00000-0000" />
                </div>
            </div>
            <DialogFooter class="gap-2 pt-2">
                <Button variant="secondary" @click="handleClose">Cancelar</Button>
                <Button @click="handleSave">{{ isEditMode ? 'Salvar' : 'Criar' }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>