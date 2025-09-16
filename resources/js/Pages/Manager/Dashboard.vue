<template>
  <div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="p-4 bg-white rounded shadow">
        <div class="text-sm text-gray-500">Employees</div>
        <div class="text-2xl font-bold">{{ stats.employees }}</div>
      </div>
      <div class="p-4 bg-white rounded shadow">
        <div class="text-sm text-gray-500">Pending Leaves</div>
        <div class="text-2xl font-bold">{{ stats.pending_leaves }}</div>
      </div>
      <div class="p-4 bg-white rounded shadow">
        <div class="text-sm text-gray-500">Today Attendance</div>
        <div class="text-2xl font-bold">{{ stats.today_attendance }}</div>
      </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h3 class="font-semibold mb-2">Welcome, manager</h3>
      <p class="text-gray-600">This is your manager dashboard. Use the sidebar to navigate.</p>
    </div>
  </div>
</template>

<script>
import ManagerLayout from '@/Layouts/ManagerLayout.vue';

export default {
  name: 'ManagerDashboard',
  layout: ManagerLayout, // uses the ManagerLayout we added earlier
  props: {
    stats: {
      type: Object,
      default: () => ({ employees: 0, pending_leaves: 0, today_attendance: 0 })
    }
  }
};
</script>

<style scoped>
/* small page-level tweaks if required */
</style>
