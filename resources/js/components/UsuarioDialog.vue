<script setup lang="ts">
import { watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Props {
    open: boolean;
    usuario?: any;
}

const props = withDefaults(defineProps<Props>(), {
    open: false,
    usuario: null,
});

const emit = defineEmits<{
    success: [];
    close: [];
    'update:open': [value: boolean];
}>();

const isEditMode  = computed(() => !!props.usuario);
const dialogTitle = computed(() => isEditMode.value ? 'Editar Usuário' : 'Novo Usuário');

const form = useForm({
    name:            '',
    email:           '',
    cpf:             '',
    tipo_usuario_id: 2,
});

watch(() => props.usuario, (val) => {
    if (!val) { resetForm(); return; }
    form.name            = val.name            || '';
    form.email           = val.email           || '';
    form.cpf             = val.cpf             || '';
    form.tipo_usuario_id = val.tipo_usuario_id || 2;
}, { immediate: true, deep: true });

watch(() => props.open, (val) => {
    if (!val) resetForm();
});

function resetForm() {
    form.reset();
    form.clearErrors();
}

function formatCpf(e: Event) {
    let v = (e.target as HTMLInputElement).value.replace(/\D/g, '');
    if (v.length > 3)  v = v.slice(0, 3) + '.' + v.slice(3);
    if (v.length > 7)  v = v.slice(0, 7) + '.' + v.slice(7);
    if (v.length > 11) v = v.slice(0, 11) + '-' + v.slice(11);
    form.cpf = v.slice(0, 14);
}

function handleSave() {
    if (!/^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(form.cpf)) {
        form.setError('cpf', 'CPF inválido. Use o formato 000.000.000-00');
        return;
    }

    if (isEditMode.value) {
        form.put(route('cadastro-usuario.update', props.usuario.id), {
            onSuccess: () => emit('success'),
        });
    } else {
        form.post(route('cadastro-usuario.store'), {
            onSuccess: () => emit('success'),
        });
    }
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

            <!-- Erros -->
            <div v-if="Object.keys(form.errors).length > 0" class="rounded-md border border-red-300 bg-red-50 p-4">
                <p class="text-sm font-medium text-red-600 mb-2">Corrija os erros abaixo:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li v-for="(error, field) in form.errors" :key="field" class="text-sm text-red-600">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <div class="grid gap-4">
                <!-- Nome -->
                <div class="grid gap-2">
                    <Label>Nome *</Label>
                    <Input
                        v-model="form.name"
                        placeholder="Nome completo"
                        :class="form.errors.name ? 'border-red-500' : ''"
                    />
                </div>

                <!-- Email -->
                <div class="grid gap-2">
                    <Label>E-mail *</Label>
                    <Input
                        v-model="form.email"
                        type="email"
                        placeholder="email@exemplo.com"
                        :class="form.errors.email ? 'border-red-500' : ''"
                    />
                </div>

                <!-- CPF -->
                <div class="grid gap-2">
                    <Label>
                        CPF *
                        <span class="text-xs text-gray-400">(senha = primeiros 6 dígitos)</span>
                    </Label>
                    <Input
                        v-model="form.cpf"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        @input="formatCpf"
                        :class="form.errors.cpf ? 'border-red-500' : ''"
                    />
                </div>

                <!-- Tipo -->
                <div class="grid gap-2">
                    <Label>Tipo *</Label>
                    <select
                        v-model="form.tipo_usuario_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option :value="1">Coordenador</option>
                        <option :value="2">Voluntário</option>
                    </select>
                </div>
            </div>

            <DialogFooter class="gap-2 pt-2">
                <Button variant="secondary" @click="handleClose">Cancelar</Button>
                <Button @click="handleSave" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEditMode ? 'Salvar' : 'Criar') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>