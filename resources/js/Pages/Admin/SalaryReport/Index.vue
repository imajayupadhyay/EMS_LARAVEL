<template>
  <AdminLayout>
    <div class="p-4">
      <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-4">
        <h1 class="text-2xl font-bold text-orange-600">💼 Salary Report</h1>
        <div class="flex flex-col md:flex-row gap-2">
          <input
            type="month"
            :value="`${year}-${month.padStart(2, '0')}`"
            @change="e => changeMonthYear(e.target.value)"
            class="border rounded px-2 py-1 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
          />
          <select v-model="selectedEmployee" @change="applyFilters"
            class="border rounded px-2 py-1 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            <option value="">All Employees</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.name }}
            </option>
          </select>
          <form :action="route('admin.salary-report.export')" method="GET">
            <input type="hidden" name="month" :value="month" />
            <input type="hidden" name="year" :value="year" />
            <button type="submit"
              class="bg-orange-600 text-white px-4 py-2 rounded shadow hover:bg-orange-700 transition">
              Export to Excel
            </button>
          </form>
        </div>
      </div>

      <table class="w-full bg-white shadow rounded-lg text-sm">
        <thead class="bg-orange-100 text-gray-700">
          <tr>
            <th class="p-3 border">Name</th>
            <th class="p-3 border">Department</th>
            <th class="p-3 border">Designation</th>
            <th class="p-3 border">Office Days</th>
            <th class="p-3 border">Present Days</th>
            <th class="p-3 border">Approved Leaves</th>
            <th class="p-3 border">Leave Assigned</th>
            <th class="p-3 border">Leave Balance</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in report" :key="item.employee.id"
            class="hover:bg-orange-50 transition shadow-sm">
            <td class="p-3 border">{{ item.employee.first_name }} {{ item.employee.last_name }}</td>
            <td class="p-3 border">{{ item.employee.department?.name || '-' }}</td>
            <td class="p-3 border">{{ item.employee.designation?.name || '-' }}</td>
            <td class="p-3 border">{{ officeDays }}</td>
            <td class="p-3 border">{{ item.present_days }}</td>
            <td class="p-3 border">{{ item.approved_leaves }}</td>
            <td class="p-3 border">{{ item.leave_assigned }}</td>
            <td class="p-3 border">{{ item.leave_balance }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route } from 'ziggy-js'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  report: Array,
  officeDays: Number,
  month: String,
  year: String,
  employees: Array,
  selectedEmployee: String
})

const selectedEmployee = ref(props.selectedEmployee || '')

const changeMonthYear = (value) => {
  const [yr, mo] = value.split('-')
  router.get(route('admin.salary-report.index'), {
    month: mo,
    year: yr,
    employee_id: selectedEmployee.value
  }, { preserveScroll: true })
}

const applyFilters = () => {
  router.get(route('admin.salary-report.index'), {
    month: props.month,
    year: props.year,
    employee_id: selectedEmployee.value
  }, { preserveScroll: true })
}
</script>

<style scoped>
table {
  border-collapse: collapse;
}
th {
  text-align: left;
}
</style>
