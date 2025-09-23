<script setup>
import { ref, computed, watch } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

const props = defineProps({
  records: Array,
  month: String,
  filters: Object,
  summary: Object
});

// Filter states
const selectedMonth = ref(props.filters?.month || '');
const startDate = ref(props.filters?.start_date || '');
const endDate = ref(props.filters?.end_date || '');
const selectedStatus = ref(props.filters?.status || '');
const searchQuery = ref('');

// UI states
const viewMode = ref('table'); // 'table' or 'calendar'
const showFilters = ref(false);

// Filter options
const statusOptions = [
  { value: '', label: 'All Status' },
  { value: 'on-time', label: 'On Time' },
  { value: 'late', label: 'Late' },
];

// Computed filtered records (for search)
const filteredRecords = computed(() => {
  if (!searchQuery.value) return props.records;
  
  const query = searchQuery.value.toLowerCase();
  return props.records.filter(record => 
    record.date.includes(query) || 
    record.day_of_week.toLowerCase().includes(query)
  );
});

// Apply filters
const applyFilters = () => {
  const params = {};
  
  if (startDate.value && endDate.value) {
    params.start_date = startDate.value;
    params.end_date = endDate.value;
  } else if (selectedMonth.value) {
    params.month = selectedMonth.value;
  }
  
  if (selectedStatus.value) {
    params.status = selectedStatus.value;
  }
  
  router.get(route('employee.attendance.index'), params, { 
    preserveState: true, 
    replace: true 
  });
};

// Reset filters
const resetFilters = () => {
  selectedMonth.value = '';
  startDate.value = '';
  endDate.value = '';
  selectedStatus.value = '';
  searchQuery.value = '';
  
  router.get(route('employee.attendance.index'), {}, { 
    preserveState: true, 
    replace: true 
  });
};

// Export data
const exportData = () => {
  const params = { month: selectedMonth.value };
  window.location.href = route('employee.attendance.export', params);
};

// Format functions
function formatTime(datetime) {
  if (!datetime) return '--';
  return new Date(datetime).toLocaleTimeString([], { 
    hour: '2-digit', 
    minute: '2-digit',
    hour12: true 
  });
}

function formatDuration(seconds) {
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  return `${h}h ${m}m`;
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', { 
    weekday: 'short', 
    month: 'short', 
    day: 'numeric' 
  });
}

function getStatusBadgeClass(status) {
  const classes = {
    'on-time': 'bg-green-100 text-green-800',
    'late': 'bg-yellow-100 text-yellow-800',
    'absent': 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
}

// Auto-apply month filter
watch(selectedMonth, (val) => {
  if (val && !startDate.value && !endDate.value) {
    applyFilters();
  }
});
</script>

<template>
  <EmployeeLayout>
    <Head title="My Attendance" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">My Attendance</h1>
            <p class="mt-1 text-sm text-gray-500">Track your attendance records and work hours</p>
          </div>
          
          <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <!-- View Mode Toggle -->
            <div class="inline-flex rounded-lg border border-gray-200 p-1">
              <button
                @click="viewMode = 'table'"
                :class="[
                  'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                  viewMode === 'table' 
                    ? 'bg-orange-500 text-white' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Table
              </button>
              <button
                @click="viewMode = 'calendar'"
                :class="[
                  'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                  viewMode === 'calendar' 
                    ? 'bg-orange-500 text-white' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Calendar
              </button>
            </div>
            
            <!-- Export Button -->
            <button
              @click="exportData"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Export
            </button>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Days</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ summary.total_days }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

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
                  <dt class="text-sm font-medium text-gray-500 truncate">On Time</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ summary.on_time_days }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

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
                  <dt class="text-sm font-medium text-gray-500 truncate">Late Days</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ summary.late_days }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Hours</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ summary.total_hours }}h</dd>
                </dl>
              </div>
            </div>
          </div>
        </div> -->
      </div>

      <!-- Filters Section -->
      <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
        <div class="px-6 py-4">
          <button
            @click="showFilters = !showFilters"
            class="flex items-center justify-between w-full text-left"
          >
            <h3 class="text-lg font-medium text-gray-900">Filters</h3>
            <svg
              :class="['w-5 h-5 text-gray-500 transition-transform', showFilters ? 'rotate-180' : '']"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
          >
            <div v-show="showFilters" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <!-- Month Filter -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                <input
                  type="month"
                  v-model="selectedMonth"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                />
              </div>

              <!-- Date Range Start -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input
                  type="date"
                  v-model="startDate"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                />
              </div>

              <!-- Date Range End -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input
                  type="date"
                  v-model="endDate"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                />
              </div>

              <!-- Status Filter -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                  v-model="selectedStatus"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                >
                  <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>
              </div>

              <!-- Search -->
              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Search by date or day..."
                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                />
              </div>

              <!-- Filter Actions -->
              <div class="flex items-end space-x-2 sm:col-span-2">
                <button
                  @click="applyFilters"
                  class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
                >
                  Apply Filters
                </button>
                <button
                  @click="resetFilters"
                  class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
                >
                  Reset
                </button>
              </div>
            </div>
          </transition>
        </div>
      </div>

      <!-- Table View -->
      <div v-if="viewMode === 'table'" class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Day
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  First In
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Last Out
                </th>
              
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="record in filteredRecords" :key="record.date" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ record.date }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ record.day_of_week }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatTime(record.first_in) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatTime(record.last_out) }}
                </td>
                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ record.total_hours }}h
                </td> -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      getStatusBadgeClass(record.status)
                    ]"
                  >
                    {{ record.status === 'on-time' ? 'On Time' : record.status.charAt(0).toUpperCase() + record.status.slice(1) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="!filteredRecords.length" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
          <p class="mt-1 text-sm text-gray-500">No records found for the selected period.</p>
        </div>
      </div>

      <!-- Calendar View (Simple implementation) -->
      <div v-else class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <div class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Calendar View</h3>
          <p class="mt-1 text-sm text-gray-500">Calendar view coming soon!</p>
        </div>
      </div>
    </div>
  </EmployeeLayout>
</template>

<style scoped>
/* Add any additional custom styles here */
</style>