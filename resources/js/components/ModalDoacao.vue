<script setup lang="ts">
import type { DateValue } from '@internationalized/date';
import {
    getLocalTimeZone,
    parseDate,
} from '@internationalized/date';
import { ChevronDownIcon, Check, ChevronsUpDown } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

// ==================== TYPES ====================
interface Tutor {
    id: number;
    nome: string;
}

interface Pet {
    id: number;
    nome: string;
    id_tutor: number;
}

interface Servico {
    id: number;
    nome: string;
    preco: number;
}

// ==================== PROPS ====================
interface Props {
    open: boolean;
    agendamento?: any;
    tutores: Tutor[];
    pets: Pet[];
    servicos: Servico[];
}

const props = withDefaults(defineProps<Props>(), {
    open: false,
    agendamento: null,
    tutores: () => [],
    pets: () => [],
    servicos: () => [],
});

// ==================== EMITS ====================
const emit = defineEmits<{
    save: [agendamento: any];
    close: [];
    'update:open': [value: boolean];
}>();

// ==================== STATE ====================
const date = ref<DateValue | null>(null);
const datePickerOpen = ref(false);
const tutorOpen = ref(false);
const petOpen = ref(false);
const servicoOpen = ref(false);

const formData = ref({

    pet_id: null as number | null,
    servico_id: null as number | null,
    data: '',
    hora: '',
});

// ==================== COMPUTED ====================
const isEditMode = computed(() => !!props.agendamento);

const dialogTitle = computed(() =>
    isEditMode.value ? 'Editar Agendamento' : 'Novo Agendamento',
);

const selectedTutor = computed(() =>
    props.tutores.find((t) => t.id === formData.value.tutor_id),
);

const selectedPet = computed(() =>
    props.pets.find((p) => p.id === formData.value.pet_id),
);

const selectedServico = computed(() =>
    props.servicos.find((s) => s.id === formData.value.servico_id),
);

const filteredPets = computed(() => {
    if (!formData.value.tutor_id) return props.pets;
    return props.pets.filter((p) => p.id_tutor === formData.value.tutor_id);
});

// ==================== WATCHERS ====================
watch(
    () => props.agendamento,
    (newVal) => {
        if (!newVal) {
            resetForm();
            return;
        }

        const tutorId = newVal.tutor_id || newVal.tutor?.id || null;
        const petId = newVal.pet_id || newVal.pet?.id || null;
        const servicoId = newVal.servico_id || newVal.servico?.id || null;
        const dataValue = newVal.data || newVal.data_formatada || '';
        const horaValue = newVal.hora || newVal.hora_formatada || '';

        formData.value = {
            tutor_id: tutorId,
            pet_id: petId,
            servico_id: servicoId,
            data: dataValue,
            hora: horaValue,
        };

        if (dataValue) {
            try {
                const dateStr = dataValue.substring(0, 10);
                date.value = parseDate(dateStr);
            } catch (err) {
                console.error('Erro ao parsear data:', err, dataValue);
                date.value = null;
            }
        }
    },
    { immediate: true, deep: true },
);

watch(
    () => props.open,
    (newVal) => {
        if (!newVal) resetForm();
    },
);

watch(
    () => formData.value.tutor_id,
    () => {
        if (!filteredPets.value.some((p) => p.id === formData.value.pet_id)) {
            formData.value.pet_id = null;
        }
    },
);

// ==================== METHODS ====================
function resetForm() {
    formData.value = {
        tutor_id: null,
        pet_id: null,
        servico_id: null,
        data: '',
        hora: '',
    };
    date.value = null;
}

function handleSave() {
    if (
        !formData.value.tutor_id ||
        !formData.value.pet_id ||
        !formData.value.servico_id ||
        !date.value ||
        !formData.value.hora
    ) {
        alert('Por favor, preencha todos os campos obrigatórios');
        return;
    }

    emit('save', {
        tutor_id: formData.value.tutor_id,
        pet_id: formData.value.pet_id,
        servico_id: formData.value.servico_id,
        data: date.value.toString(),
        hora: formData.value.hora,
    });
}

function handleClose() {
    emit('close');
    emit('update:open', false);
}

function handleCancel() {
    resetForm();
    handleClose();
}
</script>

<template>
    <Dialog :open="open" @update:open="handleClose">
        <DialogContent class="max-w-md">
            <DialogHeader class="space-y-3">
                <DialogTitle>{{ dialogTitle }}</DialogTitle>
            </DialogHeader>

            <div class="grid gap-4">
                <!-- ==================== TUTOR COMBOBOX ==================== -->
                <div class="grid gap-3">
                    <Label for="tutor-combobox">Tutor *</Label>
                    <Popover v-model:open="tutorOpen">
                        <PopoverTrigger as-child>
                            <Button
                                id="tutor-combobox"
                                variant="outline"
                                role="combobox"
                                :aria-expanded="tutorOpen"
                                class="justify-between font-normal"
                            >
                                {{
                                    selectedTutor
                                        ? selectedTutor.nome
                                        : 'Selecione o tutor...'
                                }}
                                <ChevronsUpDown
                                    class="ml-2 h-4 w-4 shrink-0 opacity-50"
                                />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-[400px] p-0">
                            <Command>
                                <CommandInput placeholder="Buscar tutor..." />
                                <CommandList>
                                    <CommandEmpty
                                        >Nenhum tutor encontrado.</CommandEmpty
                                    >
                                    <CommandGroup>
                                        <CommandItem
                                            v-for="tutor in tutores"
                                            :key="tutor.id"
                                            :value="tutor.nome"
                                            @select="
                                                () => {
                                                    formData.tutor_id =
                                                        tutor.id;
                                                    tutorOpen = false;
                                                }
                                            "
                                        >
                                            <Check
                                                :class="
                                                    cn(
                                                        'mr-2 h-4 w-4',
                                                        formData.tutor_id ===
                                                            tutor.id
                                                            ? 'opacity-100'
                                                            : 'opacity-0',
                                                    )
                                                "
                                            />
                                            {{ tutor.nome }}
                                        </CommandItem>
                                    </CommandGroup>
                                </CommandList>
                            </Command>
                        </PopoverContent>
                    </Popover>
                </div>

                <!-- ==================== PET COMBOBOX ==================== -->
                <div class="grid gap-3">
                    <Label for="pet-combobox">Pet *</Label>
                    <Popover v-model:open="petOpen">
                        <PopoverTrigger as-child>
                            <Button
                                id="pet-combobox"
                                variant="outline"
                                role="combobox"
                                :aria-expanded="petOpen"
                                :disabled="!formData.tutor_id"
                                class="justify-between font-normal"
                            >
                                {{
                                    selectedPet
                                        ? selectedPet.nome
                                        : formData.tutor_id
                                          ? 'Selecione o pet...'
                                          : 'Selecione primeiro o tutor'
                                }}
                                <ChevronsUpDown
                                    class="ml-2 h-4 w-4 shrink-0 opacity-50"
                                />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-[400px] p-0">
                            <Command>
                                <CommandInput placeholder="Buscar pet..." />
                                <CommandList>
                                    <CommandEmpty
                                        >Nenhum pet encontrado.</CommandEmpty
                                    >
                                    <CommandGroup>
                                        <CommandItem
                                            v-for="pet in filteredPets"
                                            :key="pet.id"
                                            :value="pet.nome"
                                            @select="
                                                () => {
                                                    formData.pet_id = pet.id;
                                                    petOpen = false;
                                                }
                                            "
                                        >
                                            <Check
                                                :class="
                                                    cn(
                                                        'mr-2 h-4 w-4',
                                                        formData.pet_id ===
                                                            pet.id
                                                            ? 'opacity-100'
                                                            : 'opacity-0',
                                                    )
                                                "
                                            />
                                            {{ pet.nome }}
                                        </CommandItem>
                                    </CommandGroup>
                                </CommandList>
                            </Command>
                        </PopoverContent>
                    </Popover>
                </div>

                <!-- ==================== SERVIÇO COMBOBOX ==================== -->
                <div class="grid gap-3">
                    <Label for="servico-combobox">Serviço *</Label>
                    <Popover v-model:open="servicoOpen">
                        <PopoverTrigger as-child>
                            <Button
                                id="servico-combobox"
                                variant="outline"
                                role="combobox"
                                :aria-expanded="servicoOpen"
                                class="justify-between font-normal"
                            >
                                {{
                                    selectedServico
                                        ? `${selectedServico.nome} - R$ ${selectedServico.preco.toFixed(2)}`
                                        : 'Selecione o serviço...'
                                }}
                                <ChevronsUpDown
                                    class="ml-2 h-4 w-4 shrink-0 opacity-50"
                                />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-[400px] p-0">
                            <Command>
                                <CommandInput placeholder="Buscar serviço..." />
                                <CommandList>
                                    <CommandEmpty
                                        >Nenhum serviço
                                        encontrado.</CommandEmpty
                                    >
                                    <CommandGroup>
                                        <CommandItem
                                            v-for="servico in servicos"
                                            :key="servico.id"
                                            :value="servico.nome"
                                            @select="
                                                () => {
                                                    formData.servico_id =
                                                        servico.id;
                                                    servicoOpen = false;
                                                }
                                            "
                                        >
                                            <Check
                                                :class="
                                                    cn(
                                                        'mr-2 h-4 w-4',
                                                        formData.servico_id ===
                                                            servico.id
                                                            ? 'opacity-100'
                                                            : 'opacity-0',
                                                    )
                                                "
                                            />
                                            <div
                                                class="flex w-full justify-between"
                                            >
                                                <span>{{ servico.nome }}</span>
                                                <span
                                                    class="text-muted-foreground"
                                                >
                                                    R$
                                                    {{
                                                        servico.preco.toFixed(2)
                                                    }}
                                                </span>
                                            </div>
                                        </CommandItem>
                                    </CommandGroup>
                                </CommandList>
                            </Command>
                        </PopoverContent>
                    </Popover>
                </div>

                <!-- ==================== DATA E HORA ==================== -->
                <div class="flex gap-3">
                    <!-- Data -->
                    <div class="flex w-1/2 flex-col gap-3">
                        <Label for="date-picker" class="px-1">Data *</Label>
                        <Popover v-model:open="datePickerOpen">
                            <PopoverTrigger as-child>
                                <Button
                                    id="date-picker"
                                    variant="outline"
                                    class="justify-between font-normal"
                                >
                                    {{
                                        date
                                            ? date
                                                  .toDate(getLocalTimeZone())
                                                  .toLocaleDateString('pt-BR')
                                            : 'Escolha uma data'
                                    }}
                                    <ChevronDownIcon class="h-4 w-4" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent
                                class="w-auto overflow-hidden p-0"
                                align="start"
                            >
                                <Calendar
                                    :model-value="date"
                                    @update:model-value="
                                        (value) => (date = value)
                                    "
                                />
                            </PopoverContent>
                        </Popover>
                    </div>

                    <!-- Hora -->
                    <div class="flex w-1/2 flex-col gap-3">
                        <Label for="time-picker" class="px-1">Horário *</Label>
                        <Input
                            id="time-picker"
                            v-model="formData.hora"
                            type="time"
                            step="900"
                            placeholder="10:30"
                            class="appearance-none bg-background [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none"
                        />
                    </div>
                </div>
            </div>

            <DialogFooter class="gap-2">
                <Button variant="secondary" @click="handleCancel">
                    Cancelar
                </Button>

                <Button @click="handleSave">
                    {{ isEditMode ? 'Salvar' : 'Criar' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>