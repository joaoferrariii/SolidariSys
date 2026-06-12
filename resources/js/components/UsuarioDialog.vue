<script setup lang="ts">
import { watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
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
    save: [data: any];
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
        <DialogContent class="max-w-md" style="background-color: #FFFFFF;">
            <DialogHeader>
                <DialogTitle style="color: #111827;">{{ dialogTitle }}</DialogTitle>
            </DialogHeader>

       
            <div
                v-if="Object.keys(form.errors).length > 0"
                class="rounded-md border p-4"
                style="border-color: #EF4444; background-color: #FEF2F2;"
            >
                <p class="text-sm font-medium mb-2" style="color: #EF4444;">Corrija os erros abaixo:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li v-for="(error, field) in form.errors" :key="field" class="text-sm" style="color: #EF4444;">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <div class="grid gap-4">
     
                <div class="grid gap-2">
                    <Label style="color: #111827;">Nome *</Label>
                    <Input
                        v-model="form.name"
                        placeholder="Nome completo"
                        class="focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        :style="form.errors.name ? 'border-color: #EF4444;' : 'border-color: #D1D5DB;'"
                    />
                </div>

  
                <div class="grid gap-2">
                    <Label style="color: #111827;">E-mail *</Label>
                    <Input
                        v-model="form.email"
                        type="email"
                        placeholder="email@exemplo.com"
                        class="focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        :style="form.errors.email ? 'border-color: #EF4444;' : 'border-color: #D1D5DB;'"
                    />
                </div>

        
                <div class="grid gap-2">
                    <Label style="color: #111827;">
                        CPF *
                        <span class="text-xs" style="color: #6B7280;">(senha = primeiros 6 dígitos)</span>
                    </Label>
                    <Input
                        v-model="form.cpf"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        @input="formatCpf"
                        class="focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        :style="form.errors.cpf ? 'border-color: #EF4444;' : 'border-color: #D1D5DB;'"
                    />
                </div>

   
                <div class="grid gap-2">
                    <Label style="color: #111827;">Tipo *</Label>
                    <select
                        v-model="form.tipo_usuario_id"
                        class="flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]"
                        style="border-color: #D1D5DB; color: #111827; background-color: #FFFFFF;"
                    >
                        <option :value="1">Coordenador</option>
                        <option :value="2">Voluntário</option>
                    </select>
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
                    :disabled="form.processing"
                    class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-white transition-colors"
                    style="background-color: #1B4332;"
                    onmouseover="this.style.backgroundColor='#2D6A4F'"
                    onmouseout="this.style.backgroundColor='#1B4332'"
                >
                    {{ form.processing ? 'Salvando...' : (isEditMode ? 'Salvar' : 'Criar') }}
                </button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>