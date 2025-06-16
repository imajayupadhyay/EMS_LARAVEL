<script setup>
import { ref, watch } from 'vue';
import { usePage, router, Head } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

const props = defineProps({
  records: Array,
  month: String
});

const selectedMonth = ref(props.month);

watch(selectedMonth, (val) => {
  router.get(route('employee.attendance.index'), { month: val }, { preserveState: true, replace: true });
});

function formatTime(datetime) {
  if (!datetime) return '--';
  return new Date(datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function formatDuration(seconds) {
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  return `${h.toString().padStart(2, '0')}h ${m.toString().padStart(2, '0')}m`;
}
</script>

<template>
  <EmployeeLayout>
    <Head title="My Attendance" />
    <div class="max-w-4xl mx-auto bg-white p-4 shadow rounded-lg">
      <h1 class="text-2xl font-bold text-orange-600 mb-4">Attendance</h1>

      <div class="mb-4">
        <input
          type="month"
          v-model="selectedMonth"
          class="border px-2 py-1 rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
        />
      </div>

      <table class="w-full text-sm border">
        <thead class="bg-orange-100">
          <tr>
            <th class="border px-2 py-1 text-left">Date</th>
            <th class="border px-2 py-1 text-left">First In</th>
            <th class="border px-2 py-1 text-left">Last Out</th>
            <th class="border px-2 py-1 text-left">Total Hours</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rec in props.records" :key="rec.date">
            <td class="border px-2 py-1">{{ rec.date }}</td>
            <td class="border px-2 py-1">{{ formatTime(rec.first_in) }}</td>
            <td class="border px-2 py-1">{{ formatTime(rec.last_out) }}</td>
            <td class="border px-2 py-1">{{ formatDuration(rec.total_seconds) }}</td>
          </tr>
        </tbody>
      </table>

      <div v-if="!props.records.length" class="text-center text-gray-500 mt-4">
        No records found for this month.
      </div>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
table {
  border-collapse: collapse;
}
</style>
