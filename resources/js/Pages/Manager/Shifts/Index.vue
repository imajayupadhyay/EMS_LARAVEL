<script setup>
import { ref } from 'vue'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: ManagerLayout })

const props = defineProps({
  shifts: Array,
})
</script>

<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Shift Overview</h1>
      <p class="text-gray-600">View all available shifts and their timings</p>
    </div>

    <!-- Shifts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="shift in shifts"
        :key="shift.id"
        class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 overflow-hidden"
      >
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
          <h3 class="text-lg font-semibold text-white">{{ shift.name }}</h3>
          <p class="text-blue-100 text-sm">{{ shift.employees_count || 0 }} employees assigned</p>
        </div>

        <!-- Card Body -->
        <div class="p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 mb-1">Start Time</p>
              <p class="text-2xl font-bold text-gray-900">{{ shift.time_from?.substring(0, 5) }}</p>
            </div>
            <div class="text-gray-300 text-2xl">→</div>
            <div>
              <p class="text-sm text-gray-500 mb-1">End Time</p>
              <p class="text-2xl font-bold text-gray-900">{{ shift.time_to?.substring(0, 5) }}</p>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-600">
              <span class="font-semibold">Duration:</span>
              {{ calculateDuration(shift.time_from, shift.time_to) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!shifts || shifts.length === 0" class="text-center py-12">
      <div class="text-gray-400 text-6xl mb-4">⏰</div>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">No shifts available</h3>
      <p class="text-gray-600">Contact administrator to create shifts</p>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    calculateDuration(start, end) {
      if (!start || !end) return 'N/A'
      
      const startTime = start.substring(0, 5).split(':')
      const endTime = end.substring(0, 5).split(':')
      
      let startMinutes = parseInt(startTime[0]) * 60 + parseInt(startTime[1])
      let endMinutes = parseInt(endTime[0]) * 60 + parseInt(endTime[1])
      
      if (endMinutes < startMinutes) {
        endMinutes += 24 * 60 // Add 24 hours if shift crosses midnight
      }
      
      const totalMinutes = endMinutes - startMinutes
      const hours = Math.floor(totalMinutes / 60)
      const minutes = totalMinutes % 60
      
      return `${hours}h ${minutes}m`
    }
  }
}
</script>