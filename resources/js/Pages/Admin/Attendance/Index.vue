<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  attendance: Array,
  employees: Array,
  filters: Object,
  totalWorkingDays: Number,
})

const filters = reactive({ ...props.filters })

watch(filters, () => {
  router.get(route('admin.attendance.index'), filters, {
    preserveState: true,
    replace: true,
  })
})

const exportExcel = () => {
  window.open(route('admin.attendance.export', filters), '_blank')
}
</script>

<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-8 px-4">
      <h1 class="text-3xl font-bold text-orange-600 mb-6">Attendance Summary</h1>

      <!-- Flash -->
      <div v-if="$page.props.flash.success" class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 mb-4 rounded">
        {{ $page.props.flash.success }}
      </div>

      <!-- Filters -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <select v-model="filters.employee_id" class="form-select w-full rounded border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-orange-500">
          <option value="">All Employees</option>
          <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.first_name }} {{ e.last_name }}</option>
        </select>

        <input type="date" v-model="filters.date" class="form-input w-full rounded border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-orange-500" />

        <input type="month" v-model="filters.month" class="form-input w-full rounded border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-orange-500" />

        <button
          @click="exportExcel"
          class="bg-gradient-to-r from-orange-400 to-orange-600 text-white px-4 py-2 rounded font-semibold hover:opacity-90 shadow-sm transition"
        >
          Export to Excel
        </button>
      </div>

      <!-- Total Working Days -->
      <div class="bg-orange-50 border-l-4 border-orange-400 text-orange-700 px-4 py-2 mb-4 rounded shadow-sm">
        <strong>Total Working Days this Month:</strong> {{ totalWorkingDays }}
      </div>

      <!-- Attendance Table -->
      <div v-if="attendance.length" class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full text-sm text-left border border-gray-200">
          <thead class="bg-orange-50 text-orange-700 font-semibold">
            <tr>
              <th class="py-2 px-4 border-b">Employee</th>
              <th class="py-2 px-4 border-b">Date</th>
              <th class="py-2 px-4 border-b">Total Hours</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="record in attendance"
              :key="record.date + '-' + record.employee"
              class="hover:bg-orange-50 border-b"
            >
              <td class="py-2 px-4">{{ record.employee }}</td>
              <td class="py-2 px-4">{{ record.date }}</td>
              <td class="py-2 px-4">{{ record.hours }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="text-gray-500 text-center mt-6">No attendance records found.</div>
    </div>
  </AdminLayout>
</template>

<style scoped>
table {
  border-collapse: collapse;
}
th, td {
  white-space: nowrap;
}
</style>
