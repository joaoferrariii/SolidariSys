<script setup lang="ts">
import { MenubarRoot, MenubarMenu, MenubarTrigger } from 'reka-ui';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

// Props
interface Column {
    key: string;
    label: string;
    sortable?: boolean;
    width?: string;
    align?: 'left' | 'center' | 'right';
}

interface PaginationData {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
    from: number | null;
    to: number | null;
}

interface DataGridProps {
    columns: Column[];
    data: any[];
    editable?: boolean;
    selectable?: boolean;
    showMenubar?: boolean;
    pagination?: boolean | PaginationData; // ✅ Aceita objeto de paginação
    pageSize?: number;
    loading?: boolean;
    onEdit?: (row: any, column: string, value: any) => void;
    onDelete?: (row: any) => void;
    onAdd?: () => void;
    onPageChange?: (page: number) => void; // ✅ Callback para mudança de página
    onExport?: () => void;
    customActions?: Array<{
        label: string;
        icon?: string;
        onClick: (row: any) => void;
    }>;
}

const props = withDefaults(defineProps<DataGridProps>(), {
    editable: false,
    selectable: false,
    showMenubar: true,
    pagination: true,
    pageSize: 10,
    loading: false,
});

const emit = defineEmits<{
    'update:data': [data: any[]];
    'row-click': [row: any];
    'selection-change': [selectedRows: any[]];
    'page-change': [page: number]; // ✅ Emitir mudança de página
}>();

// State
const selectedRows = ref<Set<number>>(new Set());
const sortColumn = ref<string | null>(null);
const sortDirection = ref<'asc' | 'desc'>('asc');
const currentPage = ref(1);
const editingCell = ref<{ rowIndex: number; columnKey: string } | null>(null);
const editValue = ref('');
const visibleColumns = ref<Set<string>>(
    new Set(props.columns.map((c) => c.key)),
);

// ✅ Verificar se é paginação externa (do backend)
const isExternalPagination = computed(() => {
    return typeof props.pagination === 'object' && props.pagination !== null;
});

// ✅ Dados de paginação externa
const externalPaginationData = computed(() => {
    if (isExternalPagination.value) {
        return props.pagination as PaginationData;
    }
    return null;
});

// Computed
const sortedData = computed(() => {
    if (!sortColumn.value) return props.data;

    return [...props.data].sort((a, b) => {
        const aVal = a[sortColumn.value!];
        const bVal = b[sortColumn.value!];

        if (aVal === bVal) return 0;

        const comparison = aVal > bVal ? 1 : -1;
        return sortDirection.value === 'asc' ? comparison : -comparison;
    });
});

const paginatedData = computed(() => {
    // ✅ Se for paginação externa, retorna os dados direto
    if (isExternalPagination.value) {
        return props.data;
    }

    // Paginação interna
    if (!props.pagination) return sortedData.value;

    const start = (currentPage.value - 1) * props.pageSize;
    const end = start + props.pageSize;
    return sortedData.value.slice(start, end);
});

const totalPages = computed(() => {
    if (isExternalPagination.value) {
        return externalPaginationData.value?.last_page || 1;
    }

    if (!props.pagination) return 1;
    return Math.ceil(sortedData.value.length / props.pageSize);
});

const displayColumns = computed(() => {
    return props.columns.filter((col) => visibleColumns.value.has(col.key));
});

// ✅ Informações de paginação
const paginationInfo = computed(() => {
    if (isExternalPagination.value) {
        const data = externalPaginationData.value!;
        return {
            from: data.from || 0,
            to: data.to || 0,
            total: data.total,
            currentPage: data.current_page,
        };
    }

    return {
        from: (currentPage.value - 1) * props.pageSize + 1,
        to: Math.min(
            currentPage.value * props.pageSize,
            sortedData.value.length,
        ),
        total: sortedData.value.length,
        currentPage: currentPage.value,
    };
});

// Methods
function toggleSort(columnKey: string) {
    if (sortColumn.value === columnKey) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = columnKey;
        sortDirection.value = 'asc';
    }
}

function toggleRowSelection(index: number) {
    const globalIndex = isExternalPagination.value
        ? index
        : (currentPage.value - 1) * props.pageSize + index;
    const newSet = new Set(selectedRows.value);

    if (newSet.has(globalIndex)) {
        newSet.delete(globalIndex);
    } else {
        newSet.add(globalIndex);
    }

    selectedRows.value = newSet;
    emitSelection();
}

function emitSelection() {
    const selected = Array.from(selectedRows.value).map(
        (index) => props.data[index],
    );
    emit('selection-change', selected);
}

function handleRowClick(row: any, rowIndex: number) {
    emit('row-click', row);
    toggleRowSelection(rowIndex);
}

function handleEditSelected() {
    if (selectedRows.value.size !== 1) return;

    const selectedIndex = Array.from(selectedRows.value)[0];
    const row = props.data[selectedIndex];

    if (props.onEdit) {
        props.onEdit(row, '', '');
    }
}

function handleDeleteSelected() {
    if (selectedRows.value.size === 0) return;

    const rowsToDelete = Array.from(selectedRows.value).map(
        (index) => props.data[index],
    );

    rowsToDelete.forEach((row) => props.onDelete?.(row));

    selectedRows.value = new Set();
}

// ✅ Funções de navegação de página
function nextPage() {
    if (isExternalPagination.value) {
        const nextPageNum = externalPaginationData.value!.current_page + 1;
        if (nextPageNum <= totalPages.value) {
            if (props.onPageChange) {
                props.onPageChange(nextPageNum);
            }
        }
    } else {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
        }
    }
}

function prevPage() {
    if (isExternalPagination.value) {
        const prevPageNum = externalPaginationData.value!.current_page - 1;
        if (prevPageNum >= 1) {
            if (props.onPageChange) {
                props.onPageChange(prevPageNum);
            }
        }
    } else {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    }
}

function isRowSelected(rowIndex: number) {
    const globalIndex = isExternalPagination.value
        ? rowIndex
        : (currentPage.value - 1) * props.pageSize + rowIndex;

    return selectedRows.value.has(globalIndex);
}

// Watch
watch(
    () => props.data,
    () => {
        selectedRows.value = new Set();
        if (!isExternalPagination.value) {
            currentPage.value = 1;
        }
    },
);
</script>

<template>
    <div class="data-grid-container">
        <!-- Menubar -->
        <MenubarRoot v-if="showMenubar" class="mb-4 rounded-md border">
            <MenubarMenu>
                <MenubarTrigger
                    @click="onAdd"
                    v-if="onAdd"
                    class="cursor-pointer px-4 py-2 hover:bg-gray-100 hover:text-black"
                >
                    Novo
                </MenubarTrigger>
                <MenubarTrigger
                    @click="handleEditSelected"
                    :disabled="selectedRows.size !== 1"
                    :class="[
                        'cursor-pointer px-4 py-2',
                        selectedRows.size !== 1
                            ? 'cursor-not-allowed opacity-50'
                            : 'hover:bg-gray-100 hover:text-black',
                    ]"
                >
                    Editar
                </MenubarTrigger>
                <MenubarTrigger
                    @click="handleDeleteSelected"
                    :disabled="selectedRows.size === 0"
                    :class="[
                        'cursor-pointer px-4 py-2',
                        selectedRows.size === 0
                            ? 'cursor-not-allowed opacity-50'
                            : 'hover:bg-gray-100 hover:text-black',
                    ]"
                >
                    Deletar
                </MenubarTrigger>
            </MenubarMenu>
        </MenubarRoot>

        <!-- Table -->
        <div class="overflow-hidden rounded-md border">
            <div class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead
                                v-for="column in displayColumns"
                                :key="column.key"
                                :class="[
                                    column.sortable
                                        ? 'cursor-pointer hover:bg-gray-50 hover:text-black'
                                        : '',
                                    column.width ? `w-[${column.width}]` : '',
                                    `text-${column.align || 'left'}`,
                                ]"
                                @click="
                                    column.sortable
                                        ? toggleSort(column.key)
                                        : null
                                "
                            >
                                <div class="flex items-center gap-2">
                                    {{ column.label }}
                                    <span
                                        v-if="
                                            column.sortable &&
                                            sortColumn === column.key
                                        "
                                    >
                                        {{
                                            sortDirection === 'asc' ? '↑' : '↓'
                                        }}
                                    </span>
                                </div>
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody v-if="!loading">
                        <TableRow
                            v-for="(row, rowIndex) in paginatedData"
                            :key="rowIndex"
                            @click="handleRowClick(row, rowIndex)"
                            :class="[
                                'cursor-pointer',
                                isRowSelected(rowIndex)
                                    ? 'row-selected'
                                    : 'hover:bg-gray-50 hover:text-black',
                            ]"
                        >
                            <TableCell
                                v-for="column in displayColumns"
                                :key="column.key"
                            >
                                {{ row[column.key] }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                    <TableBody v-else>
                        <TableRow>
                            <TableCell
                                :colspan="displayColumns.length"
                                class="py-8 text-center"
                            >
                                Carregando...
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- ✅ Paginação -->
            <div
                v-if="pagination"
                class="flex items-center justify-between border-t px-4 py-3"
            >
                <div class="text-sm text-gray-600">
                    Mostrando {{ paginationInfo.from }} a
                    {{ paginationInfo.to }} de
                    {{ paginationInfo.total }} registros
                </div>
                <div class="flex gap-2">
                    <Button
                        @click="prevPage"
                        :disabled="paginationInfo.currentPage === 1"
                        size="sm"
                        variant="outline"
                    >
                        Anterior
                    </Button>
                    <span class="flex items-center px-2 text-sm">
                        Página {{ paginationInfo.currentPage }} de
                        {{ totalPages }}
                    </span>
                    <Button
                        @click="nextPage"
                        :disabled="paginationInfo.currentPage === totalPages"
                        size="sm"
                        variant="outline"
                    >
                        Próxima
                    </Button>
                </div>
            </div>
        </div>

        <div v-if="selectedRows.size > 0" class="mt-2 text-sm text-gray-600">
            {{ selectedRows.size }} linha(s) selecionada(s)
        </div>
    </div>
</template>

<style scoped>
.data-grid-container {
    width: 100%;
}

.row-selected {
    background-color: #c6c6c6;
    color: #000000;
}

.row-selected:hover {
    background-color: #6d6d71;
}
</style>