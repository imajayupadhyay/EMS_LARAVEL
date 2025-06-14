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
</script>

<template>
  <EmployeeLayout>
    <Head title="My Attendance" />
    <div class="max-w-4xl mx-auto bg-white p-4 shadow rounded-lg">
      <h1 class="text-2xl font-bold text-orange-600 mb-4">Attendance</h1>
      <div class="mb-4">
        <input type="month" v-model="selectedMonth" class="border px-2 py-1 rounded" />
      </div>
      <table class="w-full text-sm border">
        <thead class="bg-orange-100">
          <tr>
            <th class="border px-2 py-1">Date</th>
            <th class="border px-2 py-1">First In</th>
            <th class="border px-2 py-1">Last Out</th>
            <th class="border px-2 py-1">Total Hours</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rec in props.records" :key="rec.date">
            <td class="border px-2 py-1">{{ rec.date }}</td>
            <td class="border px-2 py-1">{{ rec.first_in }}</td>
            <td class="border px-2 py-1">{{ rec.last_out }}</td>
            <td class="border px-2 py-1">{{ rec.total_hours }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
table {
  border-collapse: collapse;
}
</style>
