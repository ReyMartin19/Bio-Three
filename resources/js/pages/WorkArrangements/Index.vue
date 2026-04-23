<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface UserInfo {
  Userid: string;
  Name: string;
}

interface Arrangement {
  arrangement_id: number;
  userid: string;
  userinfo?: UserInfo;
  arrangement_type: string;
  schclassid?: number;
  covered_period_start?: string;
  covered_period_end?: string;
  preferred_wfh_days?: string;
  status: string;
  created_at: string;
}

interface PaginationData {
  data: Arrangement[];
  from: number;
  to: number;
  total: number;
  links: any[];
}

const props = defineProps<{
  arrangements: PaginationData;
}>();
</script>

<template>
  <AppLayout title="Work Arrangements">
    <Head title="Work Arrangements" />

    <div class="py-12 min-h-[calc(100vh-4rem)]">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    Work Arrangements
                </h1>
                <p class="text-muted-foreground mt-2">Manage employee schedules, flexitime limits, and WFH permissions.</p>
            </div>
            <div class="flex gap-3">
                <Link href="/attendance-dashboard" class="inline-flex items-center px-4 py-2 border border-border text-sm font-medium rounded-md shadow-sm text-foreground bg-card hover:bg-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                    ← Back to Summaries
                </Link>
                <Link href="/work-arrangements/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-primary-foreground bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all">
                    Add Arrangement
                </Link>
            </div>
        </div>

        <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-left text-sm whitespace-nowrap">
                    <thead class="bg-muted/50">
                        <tr class="text-muted-foreground">
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Employee</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Type</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Sch / Days</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Period</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs">Status</th>
                            <th scope="col" class="px-6 py-4 font-semibold uppercase tracking-wider text-xs text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-card">
                        <tr v-for="item in arrangements.data" :key="item.arrangement_id" class="hover:bg-muted/50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="font-medium text-foreground">{{ item.userinfo?.Name || 'Unknown Employee' }}</div>
                                <div class="text-xs text-muted-foreground">ID: {{ item.userid }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ item.arrangement_type }}
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                <span v-if="item.arrangement_type === 'Fixed Flexi'">Slot {{ item.schclassid }}</span>
                                <span v-else-if="item.arrangement_type === 'WFH'">{{ item.preferred_wfh_days }}</span>
                                <span v-else>N/A</span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground font-mono text-xs">
                                {{ item.covered_period_start || '*' }} → {{ item.covered_period_end || '*' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400': item.status === 'Approved',
                                    'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400': item.status === 'Denied',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400': item.status === 'Pending'
                                }" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="`/work-arrangements/${item.arrangement_id}/edit`" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm transition-colors">
                                    Edit
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="arrangements.data.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                                No work arrangements found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-border bg-muted/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-sm text-muted-foreground">
                    Showing <span class="font-medium text-foreground">{{ arrangements.from || 0 }}</span> to <span class="font-medium text-foreground">{{ arrangements.to || 0 }}</span> of <span class="font-medium text-foreground">{{ arrangements.total }}</span> entries
                </span>
                <div class="flex gap-2">
                    <template v-for="(link, index) in arrangements.links" :key="index">
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
