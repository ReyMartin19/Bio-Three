<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Summaries {
  data: {
    record_date: string;
    userid: string;
    time_in?: string;
    time_out?: string;
    total_hours: number;
    is_late: boolean;
    late_minutes: number;
    status: string;
  }[];
  from: number;
  to: number;
  total: number;
  links: any[];
}

defineProps<{
  summaries: Summaries;
}>();
</script>

<template>
  <AppLayout title="Attendance Dashboard">
    <Head title="Attendance Dashboard" />

    <div class="py-12 min-h-[calc(100vh-4rem)]">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-foreground">
                Attendance Intelligence
            </h1>
            <div class="flex gap-3">
                <Link href="/work-arrangements" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                    Manage Work Arrangements
                </Link>
            </div>
        </div>

        <!-- Dashboard Grid (Stats) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- These are just mock stats for visual appeal in the dashboard -->
            <div class="bg-card rounded-xl border border-border shadow-sm p-6 overflow-hidden relative">
                <dt class="text-sm font-medium text-muted-foreground truncate">Total Employees Recorded</dt>
                <dd class="mt-2 text-3xl font-semibold text-card-foreground">240</dd>
                <div class="absolute bottom-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-400 to-emerald-600"></div>
            </div>
            <div class="bg-card rounded-xl border border-border shadow-sm p-6 overflow-hidden relative">
                <dt class="text-sm font-medium text-muted-foreground truncate">Punctual Today</dt>
                <dd class="mt-2 text-3xl font-semibold text-emerald-600 dark:text-emerald-400">92%</dd>
                <div class="absolute bottom-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-400 to-emerald-600"></div>
            </div>
            <div class="bg-card rounded-xl border border-border shadow-sm p-6 overflow-hidden relative">
                <dt class="text-sm font-medium text-muted-foreground truncate">Flagged Late</dt>
                <dd class="mt-2 text-3xl font-semibold text-amber-600 dark:text-amber-400">12</dd>
                <div class="absolute bottom-0 inset-x-0 h-1 bg-gradient-to-r from-amber-400 to-amber-600"></div>
            </div>
            <div class="bg-card rounded-xl border border-border shadow-sm p-6 overflow-hidden relative">
                <dt class="text-sm font-medium text-muted-foreground truncate">Absences</dt>
                <dd class="mt-2 text-3xl font-semibold text-rose-600 dark:text-rose-400">4</dd>
                <div class="absolute bottom-0 inset-x-0 h-1 bg-gradient-to-r from-rose-400 to-rose-600"></div>
            </div>
        </div>

        <!-- Main Data Table Container -->
        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-border bg-muted/40">
                <h2 class="text-lg font-semibold text-foreground">Recent Summaries</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-left text-sm whitespace-nowrap">
                    <thead class="bg-muted/50">
                        <tr class="text-muted-foreground">
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Date</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Employee ID</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Time In</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Time Out</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Total Hr</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Tardiness</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-card">
                        <tr v-for="item in summaries.data" :key="item.userid + '-' + item.record_date" class="hover:bg-muted/50 transition-colors duration-150">
                            <td class="px-6 py-4 font-medium text-foreground">{{ item.record_date }}</td>
                            <td class="px-6 py-4 text-muted-foreground">{{ item.userid }}</td>
                            <td class="px-6 py-4 font-mono text-emerald-600 dark:text-emerald-400">{{ item.time_in || '--:--' }}</td>
                            <td class="px-6 py-4 font-mono text-rose-600 dark:text-rose-400">{{ item.time_out || '--:--' }}</td>
                            <td class="px-6 py-4 font-medium text-foreground">{{ item.total_hours }}</td>
                            <td class="px-6 py-4">
                                <div v-if="item.is_late" class="flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-rose-500">
                                      <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-medium text-xs">{{ item.late_minutes }}m late</span>
                                </div>
                                <div v-else class="text-emerald-600 dark:text-emerald-400 font-medium text-xs">On time</div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400': item.status === 'Present',
                                    'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400': item.status === 'Absent' || item.status === 'Incomplete',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400': item.status === 'Late' || item.status === 'Undertime' || item.status === 'Late/Undertime'
                                }" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide">
                                    {{ item.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="summaries.data.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                                No attendance records found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-border bg-muted/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-sm text-muted-foreground">
                    Showing <span class="font-medium text-foreground">{{ summaries.from || 0 }}</span> to <span class="font-medium text-foreground">{{ summaries.to || 0 }}</span> of <span class="font-medium text-foreground">{{ summaries.total }}</span> entries
                </span>
                <div class="flex gap-2">
                    <template v-for="(link, index) in summaries.links" :key="index">
                        <Link v-if="link.url" :href="link.url" v-html="link.label" 
                            class="px-3 py-1 text-sm rounded-md transition-colors"
                            :class="link.active ? 'bg-primary text-primary-foreground font-medium' : 'bg-secondary text-secondary-foreground hover:bg-secondary/80'" />
                        <span v-else v-html="link.label" class="px-3 py-1 text-sm bg-secondary/50 text-muted-foreground rounded-md cursor-not-allowed"></span>
                    </template>
                </div>
            </div>
        </div>
        
      </div>
    </div>
  </AppLayout>
</template>
