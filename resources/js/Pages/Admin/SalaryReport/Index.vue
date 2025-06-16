<template>
  <AdminLayout>
    <div class="p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-orange-600">Salary Report</h1>
        <form :action="route('admin.salary-report.export')" method="GET">
          <input type="hidden" name="month" :value="month" />
          <input type="hidden" name="year" :value="year" />
          <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded shadow">Export to Excel</button>
        </form>
      </div>

      <table class="w-full bg-white shadow rounded-lg">
        <thead class="bg-orange-100">
          <tr>
            <th class="p-2 border">Name</th>
            <th class="p-2 border">Department</th>
            <th class="p-2 border">Designation</th>
            <th class="p-2 border">Office Days</th>
            <th class="p-2 border">Present Days</th>
            <th class="p-2 border">Approved Leaves</th>
            <th class="p-2 border">Leave Assigned</th>
            <th class="p-2 border">Leave Balance</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in report" :key="item.employee.id" class="hover:bg-orange-50">
            <td class="p-2 border">{{ item.employee.first_name }} {{ item.employee.last_name }}</td>
            <td class="p-2 border">{{ item.employee.department?.name || '-' }}</td>
            <td class="p-2 border">{{ item.employee.designation?.name || '-' }}</td>
            <td class="p-2 border">{{ officeDays }}</td>
            <td class="p-2 border">{{ item.present_days }}</td>
            <td class="p-2 border">{{ item.approved_leaves }}</td>
            <td class="p-2 border">{{ item.leave_assigned }}</td>
            <td class="p-2 border">{{ item.leave_balance }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route } from 'ziggy-js'

defineProps({
  report: Array,
  officeDays: Number,
  month: String,
  year: String
})
</script>
