<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Building2, Plus, Edit2, Trash2, Search, X } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import departmentRoutes from '@/routes/departments';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

const props = defineProps<{
    departments: any[];
}>();

const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);
const searchQuery = ref('');

const form = useForm({
    DeptName: '',
    SupDeptid: 0,
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Departments',
                href: departmentRoutes.index().url,
            },
        ],
    },
});

const openCreateModal = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (dept: any) => {
    isEditing.value = true;
    editingId.value = dept.Deptid;
    form.DeptName = dept.DeptName;
    form.SupDeptid = dept.SupDeptid;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submitForm = () => {
    if (isEditing.value && editingId.value) {
        form.put(departmentRoutes.update(editingId.value).url, {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(departmentRoutes.store().url, {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteDepartment = (dept: any) => {
    if (confirm(`Are you sure you want to delete the department "${dept.DeptName}"?`)) {
        router.delete(departmentRoutes.destroy(dept.Deptid).url);
    }
};

const filteredDepartments = () => {
    if (!searchQuery.value) return props.departments;
    const query = searchQuery.value.toLowerCase();
    return props.departments.filter(d => 
        d.DeptName.toLowerCase().includes(query) || 
        d.Deptid.toString().includes(query)
    );
};
</script>

<template>
    <Head title="Departments" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-black tracking-tight flex items-center gap-2">
                    <Building2 class="h-6 w-6 text-muted-foreground" />
                    Departments
                </h1>
                <p class="text-sm text-muted-foreground">Manage organization departments and hierarchy.</p>
            </div>
            <Button @click="openCreateModal" class="gap-2 shadow-sm transition-all hover:scale-105">
                <Plus class="h-4 w-4" />
                Add Department
            </Button>
        </div>

        <!-- Filter & Stats Section -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-4">
            <div class="relative overflow-hidden rounded-2xl border border-sidebar-border/70 bg-sidebar p-6 shadow-sm dark:border-sidebar-border md:col-span-3">
                <div class="flex items-center gap-4">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input 
                            v-model="searchQuery" 
                            placeholder="Search departments by name or ID..." 
                            class="pl-9 bg-muted/40 border-none focus-visible:ring-1 focus-visible:ring-foreground/20"
                        />
                    </div>
                </div>
                <PlaceholderPattern class="absolute inset-0 -z-10 opacity-5" />
            </div>
            <div class="relative overflow-hidden rounded-2xl border border-sidebar-border/70 bg-sidebar p-6 shadow-sm dark:border-sidebar-border">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-widest">Total</span>
                    <span class="text-3xl font-black tracking-tight">{{ departments.length }}</span>
                </div>
                <PlaceholderPattern class="absolute inset-0 -z-10 opacity-10" />
            </div>
        </div>

        <!-- Main Content Table -->
        <div class="flex-1 overflow-hidden rounded-2xl border border-sidebar-border/70 bg-sidebar shadow-sm dark:border-sidebar-border">
            <div class="overflow-auto p-6 h-full">
                <table class="w-full text-left text-sm border-separate border-spacing-0">
                    <thead>
                        <tr class="text-muted-foreground/80">
                            <th class="pb-4 font-medium uppercase tracking-wider">ID</th>
                            <th class="pb-4 font-medium uppercase tracking-wider">Department Name</th>
                            <th class="pb-4 font-medium uppercase tracking-wider">Parent ID</th>
                            <th class="pb-4 font-medium uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="dept in filteredDepartments()" :key="dept.Deptid" class="group transition-colors hover:bg-muted/30">
                            <td class="py-4 pr-4 align-middle font-mono text-xs text-muted-foreground">#{{ dept.Deptid }}</td>
                            <td class="py-4 pr-4 align-middle">
                                <span class="font-bold text-foreground">{{ dept.DeptName }}</span>
                            </td>
                            <td class="py-4 pr-4 align-middle">
                                <span v-if="dept.SupDeptid > 0" class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-tight ring-1 ring-inset ring-foreground/10 bg-muted/50">
                                    ID: {{ dept.SupDeptid }}
                                </span>
                                <span v-else class="text-muted-foreground/50 text-xs">-</span>
                            </td>
                            <td class="py-4 align-middle text-right">
                                <div class="flex justify-end gap-2">
                                    <Button 
                                        variant="ghost" 
                                        size="icon" 
                                        @click="openEditModal(dept)"
                                        class="h-8 w-8 text-muted-foreground hover:text-foreground hover:bg-muted/50 transition-all"
                                    >
                                        <Edit2 class="h-4 w-4" />
                                    </Button>
                                    <Button 
                                        variant="ghost" 
                                        size="icon" 
                                        @click="deleteDepartment(dept)"
                                        class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-all"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredDepartments().length === 0">
                            <td colspan="4" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-40">
                                    <Building2 class="h-12 w-12" />
                                    <span class="text-lg font-medium">No departments found</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Dialog :open="isModalOpen" @update:open="val => !val && closeModal()">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Edit Department' : 'Add Department' }}</DialogTitle>
                    <DialogDescription>
                        {{ isEditing ? 'Update the department details below.' : 'Fill in the details for the new department.' }}
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label htmlFor="DeptName">Department Name</Label>
                        <Input 
                            id="DeptName" 
                            v-model="form.DeptName" 
                            placeholder="e.g. Information Technology" 
                            :class="{'border-destructive': form.errors.DeptName}"
                        />
                        <p v-if="form.errors.DeptName" class="text-xs text-destructive font-medium">{{ form.errors.DeptName }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label htmlFor="SupDeptid">Parent Department ID</Label>
                        <Input 
                            id="SupDeptid" 
                            type="number" 
                            v-model="form.SupDeptid" 
                            placeholder="0 for none" 
                        />
                        <p v-if="form.errors.SupDeptid" class="text-xs text-destructive font-medium">{{ form.errors.SupDeptid }}</p>
                    </div>
                    <DialogFooter class="mt-4">
                        <Button type="button" variant="outline" @click="closeModal">Cancel</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ isEditing ? 'Update' : 'Create' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
