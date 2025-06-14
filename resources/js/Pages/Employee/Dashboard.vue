<template>
  <div class="max-w-7xl mx-auto p-4">
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-orange-600 mb-2">
        Welcome, {{ auth?.user?.name || 'Employee' }} 👋
      </h1>
      <p class="text-gray-600">Here’s a quick summary of your activity.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <!-- Working Days -->
      <div class="bg-white shadow rounded-lg p-4 text-center hover:shadow-lg transition">
        <h2 class="text-xl font-semibold text-gray-700">🕒 Total Working Days</h2>
        <p class="text-2xl font-bold text-orange-500 mt-2">{{ workingDays }}</p>
      </div>

      <!-- Hours Worked -->
      <div class="bg-white shadow rounded-lg p-4 text-center hover:shadow-lg transition">
        <h2 class="text-xl font-semibold text-gray-700">⏱ Total Hours Worked</h2>
        <p class="text-2xl font-bold text-orange-500 mt-2">{{ totalHours }}h</p>
      </div>

      <!-- Remaining Leaves -->
      <div class="bg-white shadow rounded-lg p-4 text-center hover:shadow-lg transition">
        <h2 class="text-xl font-semibold text-gray-700">🌴 Remaining Leaves</h2>
        <p class="text-2xl font-bold text-orange-500 mt-2">{{ remainingLeaves }}</p>
      </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4">
      <button
        @click="goTo('employee.punches.index')"
        class="flex-1 bg-orange-500 text-white font-semibold py-3 rounded shadow hover:bg-orange-600 transition"
      >
        Punch In / Out
      </button>
      <button
        @click="goTo('employee.leave-applications.index')"
        class="flex-1 bg-orange-100 text-orange-600 font-semibold py-3 rounded shadow hover:bg-orange-200 transition"
      >
        Apply for Leave
      </button>
    </div>
  </div>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3'
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'

const page = usePage()
const auth = page.props.auth || {}
const workingDays = page.props.workingDays
const totalHours = page.props.totalHours
const remainingLeaves = page.props.remainingLeaves

defineOptions({ layout: EmployeeLayout })

const goTo = (routeName) => {
  router.get(route(routeName))
}
</script>
