<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Users, FileDown } from 'lucide-vue-next';
import { dashboard } from '@/routes';

defineProps<{
    employees: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Employees',
                href: '#',
            },
        ],
    },
});
</script>

<template>
    <Head title="Employees" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <Users class="h-6 w-6 text-muted-foreground" />
                <h1 class="text-2xl font-black tracking-tight">Employees</h1>
            </div>
            <span class="text-xs font-medium text-muted-foreground tracking-tight">
                Showing {{ employees.length }} employees
            </span>
        </div>

        <div class="flex-1 overflow-hidden rounded-2xl border border-sidebar-border/70 bg-sidebar shadow-sm dark:border-sidebar-border">
            <div class="h-full overflow-auto p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-separate border-spacing-0">
                        <thead>
                            <tr class="text-muted-foreground/80">
                                <th class="pb-4 font-medium uppercase tracking-wider">Employee</th>
                                <th class="pb-4 font-medium uppercase tracking-wider">Code</th>
                                <th class="pb-4 font-medium uppercase tracking-wider text-center">Sex</th>
                                <th class="pb-4 font-medium uppercase tracking-wider">Department</th>
                                <th class="pb-4 font-medium uppercase tracking-wider">Duty</th>
                                <th class="pb-4 font-medium uppercase tracking-wider">Contact</th>
                                <th class="pb-4 font-medium uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in employees" :key="user.Userid" class="group transition-colors hover:bg-muted/30">
                                <td class="py-4 pr-4 align-top">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-foreground">{{ user.Name }}</span>
                                        <span class="text-[10px] font-mono uppercase text-muted-foreground">{{ user.Userid }}</span>
                                    </div>
                                </td>
                                <td class="py-4 pr-4 align-top font-mono text-xs">{{ user.UserCode }}</td>
                                <td class="py-4 pr-4 text-center align-top">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-muted text-[10px] font-bold">
                                        {{ user.Sex }}
                                    </span>
                                </td>
                                <td class="py-4 pr-4 align-top">
                                    <span v-if="user.dept" class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-tight ring-1 ring-inset ring-foreground/10 bg-muted/50">
                                        {{ user.dept.DeptName }}
                                    </span>
                                    <span v-else class="text-muted-foreground italic text-xs">Unassigned</span>
                                </td>
                                <td class="py-4 pr-4 align-top text-muted-foreground leading-relaxed">{{ user.Duty || '-' }}</td>
                                <td class="py-4 align-top">
                                    <div class="flex flex-col text-xs">
                                        <span class="text-foreground/80 font-medium">{{ user.Telephone || '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 align-top text-right">
                                    <a 
                                        :href="'/dtr/export?user_id=' + user.Userid" 
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-sidebar-border/70 px-3 py-1.5 text-xs font-bold text-muted-foreground transition-all hover:bg-muted hover:text-foreground active:scale-95 transition-all"
                                        title="Export Monthly DTR"
                                    >
                                        <FileDown class="h-3 w-3" />
                                        Export
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="employees.length === 0">
                                <td colspan="7" class="py-20 text-center">
                                    <div class="flex flex-col items-center gap-2 opacity-40">
                                        <Users class="h-12 w-12" />
                                        <span class="text-lg font-medium">No employees found</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
