<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-8 px-4">
      <h1 class="text-2xl font-bold text-orange-600 mb-6">Attendance Summary</h1>

      <!-- Flash Success -->
      <div v-if="$page.props.flash.success" class="bg-green-100 text-green-700 px-4 py-2 mb-4 rounded">
        {{ $page.props.flash.success }}
      </div>

      <!-- Filters -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <select v-model="filters.employee_id" class="form-input w-full rounded border px-3 py-2">
          <option value="">All Employees</option>
          <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }}</option>
        </select>

        <input type="date" v-model="filters.date" class="form-input w-full rounded border px-3 py-2" />

        <input type="month" v-model="filters.month" class="form-input w-full rounded border px-3 py-2" />

        <button
          @click="exportExcel"
          class="bg-gradient-to-r from-orange-400 to-orange-600 text-white px-4 py-2 rounded font-semibold hover:opacity-90 transition"
        >
          Export to Excel
        </button>
      </div>

      <!-- Attendance Table -->
      <div v-if="attendance.length" class="overflow-x-auto shadow border rounded-lg">
        <table class="min-w-full text-left text-sm">
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
              :key="record.date + '-' + record.user"
              class="border-b hover:bg-orange-50"
            >
              <td class="py-2 px-4">{{ record.user }}</td>
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

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  attendance: Array,
  employees: Array,
  filters: Object,
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
