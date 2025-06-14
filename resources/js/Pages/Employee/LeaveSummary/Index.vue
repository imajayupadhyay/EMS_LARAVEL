<script setup>
import { Head } from '@inertiajs/vue3'
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'

const props = defineProps({
  assignments: {
    type: Array,
    default: () => []
  },
  applications: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <EmployeeLayout>
    <Head title="Leave Summary" />

    <div class="max-w-5xl mx-auto p-4">
      <h1 class="text-2xl font-bold text-orange-600 mb-6 flex items-center gap-2">📄 Leave Summary</h1>

      <!-- Leave Balance Containers -->
      <div v-if="assignments.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div
          v-for="assign in assignments"
          :key="assign.id"
          class="bg-white shadow rounded-lg p-4 border-l-4"
          :class="{
            'border-orange-500': assign.balance > 5,
            'border-yellow-400': assign.balance <= 5 && assign.balance > 2,
            'border-red-500': assign.balance <= 2
          }"
        >
          <div class="text-sm text-gray-500">{{ assign.leave_type.name }}</div>
          <div class="text-xl font-bold text-gray-800">
            {{ assign.balance }} / {{ assign.total_assigned }} days left
          </div>
        </div>
      </div>
      <div v-else class="text-gray-500 text-center mb-6">No leave balance data available.</div>

      <!-- Applications Table -->
      <div class="bg-white shadow rounded-lg">
        <h2 class="text-lg font-semibold text-gray-700 border-b px-4 py-2">Your Leave Applications</h2>
        <div v-if="applications.length">
          <table class="w-full text-sm">
            <thead class="bg-orange-50">
              <tr>
                <th class="text-left p-2">Type</th>
                <th class="text-left p-2">Date</th>
                <th class="text-left p-2">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="app in applications" :key="app.id" class="border-t hover:bg-orange-50">
                <td class="p-2">{{ app.leave_type.name }}</td>
                <td class="p-2">{{ app.start_date }} to {{ app.end_date }}</td>
                <td class="p-2">
                  <span :class="{
                    'text-yellow-500 font-medium': app.status === 'pending',
                    'text-green-600 font-medium': app.status === 'approved',
                    'text-red-500 font-medium': app.status === 'rejected'
                  }">{{ app.status.charAt(0).toUpperCase() + app.status.slice(1) }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-gray-500 text-center p-4">No leave applications found.</div>
      </div>
    </div>
  </EmployeeLayout>
</template>
