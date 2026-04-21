<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Users, Clock, ArrowRightLeft, Filter, X, FileDown } from 'lucide-vue-next';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { dashboard } from '@/routes';
import employees from '@/routes/employees';
import { onMounted, onUnmounted } from 'vue';

let interval: ReturnType<typeof setInterval>;

const props = defineProps<{
    checkinouts: any[];
    filters: {
        start_date?: string;
        end_date?: string;
    };
}>();

const activeTab = ref('logs');
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');

const tabs = [
    { id: 'logs', label: 'Check-In/Out Logs', icon: Clock },
];

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const handleFilter = () => {
    router.get(dashboard(), {
        start_date: startDate.value,
        end_date: endDate.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleReset = () => {
    startDate.value = '';
    endDate.value = '';
    router.get(dashboard(), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const getCheckTypeLabel = (type: number) => {
    const types: Record<number, string> = {
        0: 'Check In',
        1: 'Check Out',
        2: 'Break Out',
        3: 'Break In',
        4: 'Overtime In',
        5: 'Overtime Out',
    };
    return types[type] || `Type ${type}`;
};

const getCheckTypeClass = (type: number) => {
    if (type === 0 || type === 4) return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20';
    if (type === 1 || type === 5) return 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20';
    return 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20';
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
};

onMounted(() => {
    interval = setInterval(() => {
        router.reload({ only: ['checkinouts'] });
    }, 10000); // refresh every 10 seconds
});

onUnmounted(() => {
    clearInterval(interval);
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
        <!-- Stats Grid -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <Link :href="employees.index().url" class="relative aspect-video overflow-hidden rounded-2xl border border-sidebar-border/70 bg-sidebar p-6 shadow-sm transition-all hover:shadow-md hover:border-sidebar-border-hover dark:border-sidebar-border group">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-widest">Total Employees</span>
                    <span class="text-4xl font-black tracking-tight">View List</span>
                </div>
                <Users class="absolute bottom-4 right-4 h-12 w-12 text-foreground/5 transition-transform group-hover:scale-110" />
                <PlaceholderPattern class="absolute inset-0 -z-10 opacity-10" />
            </Link>
            <div class="relative overflow-hidden rounded-2xl border border-sidebar-border/70 bg-sidebar p-6 shadow-sm transition-all hover:shadow-md dark:border-sidebar-border">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-widest">Logged Events</span>
                    <span class="text-4xl font-black tracking-tight">{{ checkinouts.length }}</span>
                </div>
                <!-- <Clock class="absolute bottom-4 right-4 h-12 w-12 text-foreground/5" />
                <PlaceholderPattern class="absolute inset-0 -z-10 opacity-10" /> -->
            </div>
        </div>

        <!-- Main Content with Tabs -->
        <div class="flex flex-1 flex-col overflow-hidden rounded-2xl border border-sidebar-border/70 bg-sidebar shadow-sm dark:border-sidebar-border">
            <!-- Tab Headers -->
            <div class="flex items-center justify-between border-b border-sidebar-border/70 px-6 py-4">
                <div class="flex gap-1 rounded-xl bg-muted/60 p-1">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all',
                            activeTab === tab.id
                                ? 'bg-background text-foreground shadow-sm'
                                : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                        ]"
                    >
                        <component :is="tab.icon" class="h-4 w-4" />
                        {{ tab.label }}
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <div v-if="activeTab === 'logs'" class="mr-2 flex items-center gap-2 animate-in fade-in slide-in-from-right-2 duration-300">
                        <div class="flex items-center gap-1.5 rounded-lg border border-sidebar-border/50 bg-muted/40 px-2 py-1">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground/70">From</span>
                            <input
                                type="date"
                                v-model="startDate"
                                class="bg-transparent text-xs font-medium outline-none focus:ring-0"
                            />
                        </div>
                        <div class="flex items-center gap-1.5 rounded-lg border border-sidebar-border/50 bg-muted/40 px-2 py-1">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground/70">To</span>
                            <input
                                type="date"
                                v-model="endDate"
                                class="bg-transparent text-xs font-medium outline-none focus:ring-0"
                            />
                        </div>
                        <button
                            @click="handleFilter"
                            class="flex items-center gap-1.5 rounded-lg bg-foreground px-3 py-1.5 text-xs font-bold text-background transition-all hover:opacity-90 active:scale-95"
                        >
                            <Filter class="h-3 w-3" />
                            Filter
                        </button>
                        <button
                            v-if="startDate || endDate"
                            @click="handleReset"
                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-sidebar-border/70 text-muted-foreground transition-all hover:bg-muted hover:text-foreground active:scale-95"
                            title="Reset Filter"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>
                    <span class="text-xs font-medium text-muted-foreground tracking-tight">Showing {{ checkinouts.length }} results</span>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="flex-1 overflow-auto p-6">

                <!-- Checkinout Table -->
                <div v-if="activeTab === 'logs'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-separate border-spacing-0">
                            <thead>
                                <tr class="text-muted-foreground/80">
                                    <th class="pb-4 font-medium uppercase tracking-wider">Log ID</th>
                                    <th class="pb-4 font-medium uppercase tracking-wider">User</th>
                                    <th class="pb-4 font-medium uppercase tracking-wider">Check Time</th>
                                    <th class="pb-4 font-medium uppercase tracking-wider">Type</th>
                                    <th class="pb-4 font-medium uppercase tracking-wider text-center">Sensor</th>
                                    <th class="pb-4 font-medium uppercase tracking-wider text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in checkinouts" :key="log.Logid" class="group transition-colors hover:bg-muted/30">
                                    <td class="py-4 pr-4 align-top font-mono text-xs text-muted-foreground">{{ log.Logid }}</td>
                                    <td class="py-4 pr-4 align-top">
                                        <div class="flex flex-col">
                                            <span v-if="log.user" class="font-bold text-foreground">{{ log.user.Name }}</span>
                                            <span v-else class="text-muted-foreground italic">Unknown User</span>
                                            <span class="text-[10px] font-mono uppercase text-muted-foreground">{{ log.Userid }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 pr-4 align-top font-medium tracking-tight">
                                        {{ formatDate(log.CheckTime) }}
                                    </td>
                                    <td class="py-4 pr-4 align-top">
                                        <span :class="['inline-flex items-center rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-tight ring-1 ring-inset', getCheckTypeClass(log.CheckType)]">
                                            {{ getCheckTypeLabel(log.CheckType) }}
                                        </span>
                                    </td>
                                    <td class="py-4 pr-4 text-center align-top font-mono text-xs opacity-60">
                                        #{{ log.Sensorid }}
                                    </td>
                                    <td class="py-4 align-top text-right">
                                        <div class="flex justify-end gap-1.5 pt-1">
                                            <div v-if="log.Checked" title="Checked" class="h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></div>
                                            <div v-if="log.Exported" title="Exported" class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                                            <div v-if="log.OpenDoorFlag" title="Door Opened" class="h-2 w-2 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div>
                                            <div v-if="!log.Checked && !log.Exported && !log.OpenDoorFlag" class="h-2 w-2 rounded-full bg-muted"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="checkinouts.length === 0">
                                    <td colspan="6" class="py-20 text-center">
                                        <div class="flex flex-col items-center gap-2 opacity-40">
                                            <Clock class="h-12 w-12" />
                                            <span class="text-lg font-medium">No logs recorded yet</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
