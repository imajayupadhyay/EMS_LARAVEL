<script setup>
import { ref, computed } from 'vue'
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
  },
  statistics: {
    type: Object,
    default: () => ({})
  }
})

// Filter states
const selectedYear = ref(new Date().getFullYear())
const selectedStatus = ref('')
const selectedLeaveType = ref('')
const viewMode = ref('overview') // 'overview', 'details', 'calendar'

// Computed filters
const filteredApplications = computed(() => {
  let filtered = props.applications

  if (selectedStatus.value) {
    filtered = filtered.filter(app => app.status === selectedStatus.value)
  }

  if (selectedLeaveType.value) {
    filtered = filtered.filter(app => app.leave_type_id == selectedLeaveType.value)
  }

  return filtered
})

// Calculate leave statistics
const leaveStats = computed(() => {
  const total = props.applications.length
  const approved = props.applications.filter(app => app.status === 'approved').length
  const pending = props.applications.filter(app => app.status === 'pending').length
  const rejected = props.applications.filter(app => app.status === 'rejected').length

  return { total, approved, pending, rejected }
})

// Get unique leave types for filter
const leaveTypes = computed(() => {
  const types = new Map()
  props.assignments.forEach(assignment => {
    types.set(assignment.leave_type.id, assignment.leave_type.name)
  })
  return Array.from(types, ([id, name]) => ({ id, name }))
})

// Helper functions
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

const getStatusColor = (status) => {
  const colors = {
    'approved': 'text-green-600 bg-green-50 border-green-200',
    'pending': 'text-yellow-600 bg-yellow-50 border-yellow-200',
    'rejected': 'text-red-600 bg-red-50 border-red-200',
  }
  return colors[status] || 'text-gray-600 bg-gray-50 border-gray-200'
}

const getBalanceColor = (balance, total) => {
  const percentage = (balance / total) * 100
  if (percentage > 50) return 'text-green-600'
  if (percentage > 20) return 'text-yellow-600'
  return 'text-red-600'
}

const getBalanceBarColor = (balance, total) => {
  const percentage = (balance / total) * 100
  if (percentage > 50) return 'bg-green-500'
  if (percentage > 20) return 'bg-yellow-500'
  return 'bg-red-500'
}

const calculateUsedPercentage = (balance, total) => {
  if (!total) return 0
  return Math.round(((total - balance) / total) * 100)
}
</script>

<template>
  <EmployeeLayout>
    <Head title="Leave Summary" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Leave Summary</h1>
            <p class="mt-1 text-sm text-gray-500">Track your leave balance and application history</p>
          </div>
          
          <div class="mt-4 sm:mt-0">
            <!-- View Mode Toggle -->
            <div class="inline-flex rounded-lg border border-gray-200 p-1">
              <button
                @click="viewMode = 'overview'"
                :class="[
                  'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                  viewMode === 'overview' 
                    ? 'bg-orange-500 text-white' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Overview
              </button>
              <button
                @click="viewMode = 'details'"
                :class="[
                  'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                  viewMode === 'details' 
                    ? 'bg-orange-500 text-white' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Total Applications -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Applications</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ leaveStats.total }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Approved -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ leaveStats.approved }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-yellow-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ leaveStats.pending }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Rejected -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-red-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ leaveStats.rejected }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Overview Mode -->
      <div v-if="viewMode === 'overview'">
        <!-- Leave Balance Cards -->
        <div class="mb-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Leave Balance</h2>
          
          <div v-if="assignments.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="assign in assignments"
              :key="assign.id"
              class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
              <!-- Header -->
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ assign.leave_type.name }}</h3>
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Balance Info -->
              <div class="mb-4">
                <div class="flex items-baseline justify-between mb-2">
                  <span class="text-3xl font-bold" :class="getBalanceColor(assign.balance, assign.total_assigned)">
                    {{ assign.balance }}
                  </span>
                  <span class="text-sm text-gray-500">
                    of {{ assign.total_assigned }} days
                  </span>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div 
                    :class="getBalanceBarColor(assign.balance, assign.total_assigned)"
                    class="h-2.5 rounded-full transition-all duration-300"
                    :style="{ width: `${(assign.balance / assign.total_assigned) * 100}%` }"
                  ></div>
                </div>
              </div>

              <!-- Stats -->
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <p class="text-gray-500">Used</p>
                  <p class="font-semibold text-gray-900">{{ assign.total_assigned - assign.balance }} days</p>
                </div>
                <div class="text-right">
                  <p class="text-gray-500">Percentage</p>
                  <p class="font-semibold text-gray-900">{{ calculateUsedPercentage(assign.balance, assign.total_assigned) }}% used</p>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="bg-white shadow-sm rounded-lg border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No leave assignments</h3>
            <p class="mt-1 text-sm text-gray-500">You haven't been assigned any leave types yet.</p>
          </div>
        </div>

        <!-- Recent Applications -->
        <div>
          <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Applications</h2>
          
          <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
              <div v-for="app in applications.slice(0, 5)" :key="app.id" class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <div class="flex items-center space-x-3">
                      <h4 class="text-sm font-medium text-gray-900">{{ app.leave_type.name }}</h4>
                      <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border', getStatusColor(app.status)]">
                        {{ app.status.charAt(0).toUpperCase() + app.status.slice(1) }}
                      </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ formatDate(app.start_date) }} - {{ formatDate(app.end_date) }}
                    </p>
                  </div>
                </div>
              </div>

              <div v-if="!applications.length" class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No applications</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't submitted any leave applications yet.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Details Mode -->
      <div v-if="viewMode === 'details'">
        <!-- Filters -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Leave Type Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
              <select v-model="selectedLeaveType" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                <option value="">All Types</option>
                <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                  {{ type.name }}
                </option>
              </select>
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select v-model="selectedStatus" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                <option value="">All Status</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <!-- Year Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
              <select v-model="selectedYear" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                <option :value="new Date().getFullYear()">{{ new Date().getFullYear() }}</option>
                <option :value="new Date().getFullYear() - 1">{{ new Date().getFullYear() - 1 }}</option>
                <option :value="new Date().getFullYear() - 2">{{ new Date().getFullYear() - 2 }}</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Applications Table -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Leave Type
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Start Date
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    End Date
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Duration
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Applied On
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="app in filteredApplications" :key="app.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ app.leave_type.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(app.start_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(app.end_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ app.day_type === 'half' ? 'Half Day' : 'Full Day' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusColor(app.status)]">
                      {{ app.status.charAt(0).toUpperCase() + app.status.slice(1) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(app.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-if="!filteredApplications.length" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No applications found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters.</p>
          </div>
        </div>
      </div>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
/* Add any custom styles here */
</style>