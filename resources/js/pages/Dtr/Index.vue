<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { FileText, Users, Building2, Calendar, FileDown, Search, CheckCircle2 } from 'lucide-vue-next';
import dtr from '@/routes/dtr';

const props = defineProps<{
    users: Array<{ Userid: string; Name: string; Deptid: number }>;
    departments: Array<{ Deptid: number; DeptName: string }>;
}>();

const activeTab = ref('single');
const searchQuery = ref('');
const currentDate = new Date();
const selectedMonth = ref(currentDate.getMonth() + 1);
const selectedYear = ref(currentDate.getFullYear());

const months = [
    { value: 1, label: 'January' }, { value: 2, label: 'February' }, { value: 3, label: 'March' },
    { value: 4, label: 'April' }, { value: 5, label: 'May' }, { value: 6, label: 'June' },
    { value: 7, label: 'July' }, { value: 8, label: 'August' }, { value: 9, label: 'September' },
    { value: 10, label: 'October' }, { value: 11, label: 'November' }, { value: 12, label: 'December' }
];

const years = Array.from({ length: 5 }, (_, i) => currentDate.getFullYear() - i);

const filteredUsers = computed(() => {
    if (!searchQuery.value) return props.users;
    return props.users.filter(user => 
        user.Name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.Userid.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const singleForm = useForm({
    user_id: '',
    month: selectedMonth.value,
    year: selectedYear.value
});

const bulkForm = useForm({
    dept_id: '',
    month: selectedMonth.value,
    year: selectedYear.value
});

const handleSingleExport = () => {
    if (!singleForm.user_id) return;
    
    // Wayfinder doesn't support direct download via router.get easily for blobs
    // So we use window.location or a direct link for downloads
    const url = dtr.export.url({
        query: {
            user_id: singleForm.user_id,
            month: selectedMonth.value,
            year: selectedYear.value
        }
    });
    window.open(url, '_blank');
};

const handleBulkExport = () => {
    if (!bulkForm.dept_id) return;
    
    const url = dtr.bulkExport.url({
        query: {
            dept_id: bulkForm.dept_id,
            month: selectedMonth.value,
            year: selectedYear.value
        }
    });
    window.open(url, '_blank');
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'DTR Generator', href: dtr.index() }
        ]
    }
});
</script>

<template>
    <Head title="DTR Generator" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col gap-2">
            <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                <FileText class="h-8 w-8 text-primary" />
                DTR Generator
            </h1>
            <p class="text-muted-foreground">Generate Daily Time Records for individual employees or entire departments.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-12">
            <!-- Settings Card -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <div class="rounded-2xl border border-sidebar-border/70 bg-sidebar p-6 shadow-sm dark:border-sidebar-border">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-muted-foreground flex items-center gap-2 mb-6">
                        <Calendar class="h-4 w-4" />
                        Reporing Period
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold uppercase opacity-60">Month</label>
                            <select 
                                v-model="selectedMonth" 
                                class="w-full rounded-xl border-sidebar-border/50 bg-muted/30 px-3 py-2 text-sm outline-none transition-all focus:ring-2 focus:ring-primary/20"
                            >
                                <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold uppercase opacity-60">Year</label>
                            <select 
                                v-model="selectedYear" 
                                class="w-full rounded-xl border-sidebar-border/50 bg-muted/30 px-3 py-2 text-sm outline-none transition-all focus:ring-2 focus:ring-primary/20"
                            >
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-sidebar-border/70 bg-primary/5 p-6 shadow-sm animate-pulse dark:border-sidebar-border" v-if="!singleForm.user_id && activeTab === 'single'">
                    <div class="flex items-start gap-4">
                        <div class="rounded-full bg-primary/20 p-2">
                            <CheckCircle2 class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <h3 class="font-bold text-primary">Quick Tip</h3>
                            <p class="text-sm text-primary/80">Select an employee from the list to enable the export button.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selection Card -->
            <div class="lg:col-span-8">
                <div class="flex h-full flex-col overflow-hidden rounded-3xl border border-sidebar-border/70 bg-sidebar shadow-sm dark:border-sidebar-border">
                    <!-- Tab Headers -->
                    <div class="flex items-center justify-between border-b border-sidebar-border/70 px-6 py-4">
                        <div class="flex gap-1 rounded-xl bg-muted/60 p-1">
                            <button
                                @click="activeTab = 'single'"
                                :class="[
                                    'flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all',
                                    activeTab === 'single' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                ]"
                            >
                                <Users class="h-4 w-4" />
                                Single Employee
                            </button>
                            <button
                                @click="activeTab = 'bulk'"
                                :class="[
                                    'flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all',
                                    activeTab === 'bulk' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                ]"
                            >
                                <Building2 class="h-4 w-4" />
                                Bulk Department
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="flex-1 overflow-hidden p-6">
                        <!-- Single Employee Section -->
                        <div v-if="activeTab === 'single'" class="flex h-full flex-col gap-4 animate-in fade-in slide-in-from-bottom-2 duration-300">
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground opacity-50" />
                                <input 
                                    v-model="searchQuery"
                                    type="text" 
                                    placeholder="Search by name or ID..."
                                    class="w-full rounded-xl border-sidebar-border/50 bg-muted/30 pl-10 pr-4 py-2 text-sm outline-none transition-all focus:ring-2 focus:ring-primary/20"
                                />
                            </div>

                            <div class="flex-1 overflow-auto rounded-xl border border-sidebar-border/50 bg-muted/10">
                                <div class="grid grid-cols-1 gap-1 p-2">
                                    <button 
                                        v-for="user in filteredUsers" 
                                        :key="user.Userid"
                                        @click="singleForm.user_id = user.Userid"
                                        :class="[
                                            'flex items-center justify-between rounded-lg px-4 py-3 text-left transition-all',
                                            singleForm.user_id === user.Userid 
                                                ? 'bg-primary text-primary-foreground' 
                                                : 'hover:bg-muted/50'
                                        ]"
                                    >
                                        <div class="flex flex-col">
                                            <span class="font-bold">{{ user.Name }}</span>
                                            <span :class="['text-xs opacity-60', singleForm.user_id === user.Userid ? 'text-primary-foreground' : 'text-muted-foreground']">ID: {{ user.Userid }}</span>
                                        </div>
                                        <CheckCircle2 v-if="singleForm.user_id === user.Userid" class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            <button 
                                @click="handleSingleExport"
                                :disabled="!singleForm.user_id"
                                class="flex items-center justify-center gap-2 rounded-xl bg-foreground px-6 py-4 font-bold text-background transition-all hover:opacity-90 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
                            >
                                <FileDown class="h-5 w-5" />
                                Generate PDF Report
                            </button>
                        </div>

                        <!-- Bulk Department Section -->
                        <div v-if="activeTab === 'bulk'" class="flex h-full flex-col gap-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-muted-foreground">Select Department</label>
                                <div class="grid grid-cols-1 gap-2">
                                    <button 
                                        v-for="dept in departments" 
                                        :key="dept.Deptid"
                                        @click="bulkForm.dept_id = dept.Deptid.toString()"
                                        :class="[
                                            'flex items-center justify-between rounded-xl border px-6 py-4 text-left transition-all',
                                            bulkForm.dept_id === dept.Deptid.toString()
                                                ? 'border-primary bg-primary/5 text-primary'
                                                : 'border-sidebar-border hover:border-sidebar-border-hover'
                                        ]"
                                    >
                                        <span class="font-bold">{{ dept.DeptName }}</span>
                                        <CheckCircle2 v-if="bulkForm.dept_id === dept.Deptid.toString()" class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <button 
                                    @click="handleBulkExport"
                                    :disabled="!bulkForm.dept_id"
                                    class="w-full flex items-center justify-center gap-2 rounded-xl bg-foreground px-6 py-4 font-bold text-background transition-all hover:opacity-90 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    <FileDown class="h-5 w-5" />
                                    Generate Bulk DTRs
                                </button>
                                <p class="mt-4 text-center text-xs text-muted-foreground">
                                    This will generate a single PDF containing DTRs for all employees in the selected department.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
