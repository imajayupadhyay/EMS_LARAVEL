<script setup>
import { ref, computed } from 'vue'
import { useForm, router, Head } from '@inertiajs/vue3'
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'

const props = defineProps({
  leaveTypes: Array,
  applications: Object,
  flash: Object,
  leaveBalances: Object // Added for showing balances
})

const editing = ref(null)
const showForm = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')

const form = useForm({
  leave_type_id: '',
  start_date: '',
  end_date: '',
  reason: '',
  day_type: 'full'
})

// Computed filtered applications
const filteredApplications = computed(() => {
  if (!props.applications?.data) return []
  
  let filtered = props.applications.data
  
  if (statusFilter.value) {
    filtered = filtered.filter(app => app.status === statusFilter.value)
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(app => 
      app.leave_type.name.toLowerCase().includes(query) ||
      app.reason?.toLowerCase().includes(query) ||
      app.status.toLowerCase().includes(query)
    )
  }
  
  return filtered
})

// Get leave balance for selected type
const selectedLeaveBalance = computed(() => {
  if (!form.leave_type_id || !props.leaveBalances) return null
  return props.leaveBalances[form.leave_type_id] || null
})

// Calculate days between dates
const calculateDays = computed(() => {
  if (!form.start_date || !form.end_date) return 0
  
  const start = new Date(form.start_date)
  const end = new Date(form.end_date)
  const diffTime = Math.abs(end - start)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
  
  return form.day_type === 'half' ? diffDays * 0.5 : diffDays
})

const submit = () => {
  if (editing.value) {
    form.post(route('employee.leave-applications.update', editing.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        editing.value = null
        showForm.value = false
      }
    })
  } else {
    form.post(route('employee.leave-applications.store'), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        showForm.value = false
      }
    })
  }
}

const edit = (app) => {
  form.leave_type_id = app.leave_type_id
  form.start_date = app.start_date
  form.end_date = app.end_date
  form.reason = app.reason
  form.day_type = app.day_type || 'full'
  editing.value = app
  showForm.value = true
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const deleteApp = (app) => {
  if (confirm('Are you sure you want to delete this leave request?')) {
    router.delete(route('employee.leave-applications.destroy', app.id), {
      preserveScroll: true
    })
  }
}

const cancelEdit = () => {
  form.reset()
  editing.value = null
  showForm.value = false
}

const getStatusBadgeClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'approved': 'bg-green-100 text-green-800 border-green-200',
    'rejected': 'bg-red-100 text-red-800 border-red-200',
  }
  return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

const isToday = (dateString) => {
  const today = new Date().toISOString().split('T')[0]
  return dateString.split('T')[0] === today
}
</script>

<template>
  <EmployeeLayout>
    <Head title="Leave Applications" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Leave Applications</h1>
            <p class="mt-1 text-sm text-gray-500">Apply for leave and manage your requests</p>
          </div>
          
          <div class="mt-4 sm:mt-0">
            <button
              @click="showForm = !showForm"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              {{ showForm ? 'Cancel' : 'Apply for Leave' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
      >
        <div v-if="flash.success" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          {{ flash.success }}
        </div>
      </transition>

      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
      >
        <div v-if="flash.error" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
          <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          {{ flash.error }}
        </div>
      </transition>

      <!-- Leave Balances Summary -->
      <div v-if="leaveBalances && Object.keys(leaveBalances).length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div v-for="(balance, typeId) in leaveBalances" :key="typeId" class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-500">{{ balance.name }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ balance.balance || 0 }}</p>
              </div>
              <div class="flex-shrink-0">
                <div class="h-12 w-12 rounded-full bg-orange-100 flex items-center justify-center">
                  <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">of {{ balance.total_assigned || 0 }} days</p>
          </div>
        </div>
      </div>

      <!-- Application Form -->
      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform -translate-y-4"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform -translate-y-4"
      >
        <div v-if="showForm" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mb-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              {{ editing ? 'Edit Leave Application' : 'New Leave Application' }}
            </h3>
            <button @click="cancelEdit" class="text-gray-400 hover:text-gray-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <!-- Leave Type -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Leave Type <span class="text-red-500">*</span>
                </label>
                <select v-model="form.leave_type_id" class="form-input" required>
                  <option value="">Select Leave Type</option>
                  <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
                <p v-if="selectedLeaveBalance" class="mt-1 text-xs text-gray-500">
                  Available: {{ selectedLeaveBalance.balance }} days
                </p>
                <p v-if="form.errors.leave_type_id" class="mt-1 text-sm text-red-600">{{ form.errors.leave_type_id }}</p>
              </div>

              <!-- Day Type -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Leave Duration <span class="text-red-500">*</span>
                </label>
                <select v-model="form.day_type" class="form-input" required>
                  <option value="full">Full Day</option>
                  <option value="half">Half Day</option>
                </select>
                <p v-if="form.errors.day_type" class="mt-1 text-sm text-red-600">{{ form.errors.day_type }}</p>
              </div>

              <!-- Start Date -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Start Date <span class="text-red-500">*</span>
                </label>
                <input 
                  type="date" 
                  v-model="form.start_date" 
                  class="form-input" 
                  required
                  :min="new Date().toISOString().split('T')[0]"
                />
                <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">{{ form.errors.start_date }}</p>
              </div>

              <!-- End Date -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  End Date <span class="text-red-500">*</span>
                </label>
                <input 
                  type="date" 
                  v-model="form.end_date" 
                  class="form-input" 
                  required
                  :min="form.start_date || new Date().toISOString().split('T')[0]"
                />
                <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">{{ form.errors.end_date }}</p>
              </div>
            </div>

            <!-- Days Calculation -->
            <div v-if="calculateDays > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
              <p class="text-sm text-blue-800">
                <span class="font-medium">Total Days:</span> {{ calculateDays }} day{{ calculateDays !== 1 ? 's' : '' }}
              </p>
            </div>

            <!-- Reason -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Reason <span class="text-red-500">*</span>
              </label>
              <textarea 
                v-model="form.reason" 
                class="form-input" 
                rows="4"
                placeholder="Please provide a reason for your leave..."
                required
                maxlength="500"
              ></textarea>
              <div class="flex justify-between mt-1">
                <p v-if="form.errors.reason" class="text-sm text-red-600">{{ form.errors.reason }}</p>
                <p class="text-xs text-gray-500">{{ form.reason?.length || 0 }}/500</p>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-3">
              <button
                type="button"
                @click="cancelEdit"
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ editing ? 'Update Application' : 'Submit Application' }}
              </button>
            </div>
          </form>
        </div>
      </transition>

      <!-- Filters & Search -->
      <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <div class="relative">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Search by type, reason, or status..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Status Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status Filter</label>
            <select v-model="statusFilter" class="form-input">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Applications List -->
      <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Leave Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Dates
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Duration
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="app in filteredApplications" :key="app.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                      <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ app.leave_type.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ formatDate(app.start_date) }}</div>
                  <div class="text-sm text-gray-500">to {{ formatDate(app.end_date) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ app.day_type === 'half' ? 'Half Day' : 'Full Day' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusBadgeClass(app.status)]">
                    {{ app.status.charAt(0).toUpperCase() + app.status.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-3">
                    <button
                      v-if="isToday(app.created_at)"
                      @click="edit(app)"
                      class="text-orange-600 hover:text-orange-900"
                      title="Edit"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button
                      v-if="isToday(app.created_at)"
                      @click="deleteApp(app)"
                      class="text-red-600 hover:text-red-900"
                      title="Delete"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                    <span v-if="!isToday(app.created_at)" class="text-gray-400 text-xs">
                      No actions
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div class="sm:hidden">
          <div v-for="app in filteredApplications" :key="app.id" class="border-b border-gray-200 p-4 hover:bg-gray-50">
            <div class="flex items-center justify-between mb-2">
              <h4 class="text-sm font-medium text-gray-900">{{ app.leave_type.name }}</h4>
              <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border', getStatusBadgeClass(app.status)]">
                {{ app.status }}
              </span>
            </div>
            <div class="space-y-1 text-sm text-gray-500">
              <p>{{ formatDate(app.start_date) }} - {{ formatDate(app.end_date) }}</p>
              <p>{{ app.day_type === 'half' ? 'Half Day' : 'Full Day' }}</p>
            </div>
            <div v-if="isToday(app.created_at)" class="mt-3 flex space-x-3">
              <button @click="edit(app)" class="text-orange-600 hover:text-orange-900 text-sm font-medium">
                Edit
              </button>
              <button @click="deleteApp(app)" class="text-red-600 hover:text-red-900 text-sm font-medium">
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!filteredApplications.length" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No leave applications</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by applying for leave.</p>
          <div class="mt-6">
            <button
              @click="showForm = true"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Apply for Leave
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="applications.data?.length > 0" class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                v-if="applications.prev_page_url"
                @click="router.get(applications.prev_page_url, {}, { preserveScroll: true })"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Previous
              </button>
              <button
                v-if="applications.next_page_url"
                @click="router.get(applications.next_page_url, {}, { preserveScroll: true })"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Next
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ applications.from || 0 }}</span>
                  to
                  <span class="font-medium">{{ applications.to || 0 }}</span>
                  of
                  <span class="font-medium">{{ applications.total || 0 }}</span>
                  results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <button
                    v-if="applications.prev_page_url"
                    @click="router.get(applications.prev_page_url, {}, { preserveScroll: true })"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  >
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                  </button>
                  <button
                    v-if="applications.next_page_url"
                    @click="router.get(applications.next_page_url, {}, { preserveScroll: true })"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  >
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
.form-input {
  @apply block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm;
}

/* Custom animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.5s ease-out;
}

/* Loading spinner animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>