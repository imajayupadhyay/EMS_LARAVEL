<script setup>
import { ref, computed, watch } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue'

const props = defineProps({
  holidays: Object,
  filters: Object,
  types: Array,
  currentMonth: String,
  upcomingCount: Number,
  stats: Object
})

// Filter states
const type = ref(props.filters?.type || '')
const month = ref(props.filters?.month || '')
const searchQuery = ref('')
const viewMode = ref('grid') // 'grid', 'list', 'calendar'

// Computed filtered holidays
const filteredHolidays = computed(() => {
  if (!props.holidays?.data) return []
  
  let filtered = props.holidays.data
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(holiday => 
      holiday.name.toLowerCase().includes(query) ||
      holiday.type?.toLowerCase().includes(query)
    )
  }
  
  return filtered
})

// Apply filters
const applyFilters = () => {
  router.get(route('employee.holidays.index'), {
    type: type.value,
    month: month.value
  }, {
    preserveState: true,
    replace: true
  })
}

// Reset filters
const resetFilters = () => {
  type.value = ''
  month.value = ''
  searchQuery.value = ''
  applyFilters()
}

// Helper functions
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getShortDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

const getDayName = (date) => {
  return new Date(date).toLocaleDateString('en-US', { weekday: 'long' })
}

const getMonthName = (date) => {
  return new Date(date).toLocaleDateString('en-US', { month: 'long' })
}

const isUpcoming = (date) => {
  return new Date(date) >= new Date()
}

const isPast = (date) => {
  return new Date(date) < new Date()
}

const isToday = (date) => {
  const today = new Date().toDateString()
  return new Date(date).toDateString() === today
}

const getTypeBadgeClass = (type) => {
  const classes = {
    'public': 'bg-green-100 text-green-800 border-green-200',
    'national': 'bg-blue-100 text-blue-800 border-blue-200',
    'optional': 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'regional': 'bg-purple-100 text-purple-800 border-purple-200',
  }
  return classes[type?.toLowerCase()] || 'bg-gray-100 text-gray-800 border-gray-200'
}

const getTypeIcon = (type) => {
  const icons = {
    'public': '🎉',
    'national': '🏛️',
    'optional': '📅',
    'regional': '🌍',
  }
  return icons[type?.toLowerCase()] || '📌'
}

const daysUntil = (date) => {
  const today = new Date()
  const holidayDate = new Date(date)
  const diffTime = holidayDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays
}

// Month options
const monthOptions = [
  { value: '', label: 'All Months' },
  { value: '1', label: 'January' },
  { value: '2', label: 'February' },
  { value: '3', label: 'March' },
  { value: '4', label: 'April' },
  { value: '5', label: 'May' },
  { value: '6', label: 'June' },
  { value: '7', label: 'July' },
  { value: '8', label: 'August' },
  { value: '9', label: 'September' },
  { value: '10', label: 'October' },
  { value: '11', label: 'November' },
  { value: '12', label: 'December' },
]

// Auto-apply filters when changed
watch([type, month], () => {
  applyFilters()
})
</script>

<template>
  <EmployeeLayout>
    <Head title="Company Holidays" />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Company Holidays</h1>
            <p class="mt-1 text-sm text-gray-500">View all company holidays and plan your time off</p>
          </div>
          
          <div class="mt-4 sm:mt-0">
            <!-- View Mode Toggle -->
            <div class="inline-flex rounded-lg border border-gray-200 p-1">
              <button
                @click="viewMode = 'grid'"
                :class="[
                  'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                  viewMode === 'grid' 
                    ? 'bg-orange-500 text-white' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
              </button>
              <button
                @click="viewMode = 'list'"
                :class="[
                  'px-3 py-1.5 rounded-md text-sm font-medium transition-colors',
                  viewMode === 'list' 
                    ? 'bg-orange-500 text-white' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div v-if="stats" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Total Holidays -->
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
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Holidays</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Upcoming -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Upcoming</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ upcomingCount || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- This Month -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ stats.this_month || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Next Holiday -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-orange-500 text-white">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Next Holiday</dt>
                  <dd class="text-lg font-bold text-gray-900">
                    {{ stats.next_holiday_days ? `In ${stats.next_holiday_days} days` : 'N/A' }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <div class="relative">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Search holidays..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Type Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Holiday Type</label>
            <select v-model="type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
              <option value="">All Types</option>
              <option v-for="t in types" :key="t" :value="t">
                {{ t.charAt(0).toUpperCase() + t.slice(1) }}
              </option>
            </select>
          </div>

          <!-- Month Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
            <select v-model="month" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
              <option v-for="option in monthOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>

        <!-- Filter Actions -->
        <div class="mt-4 flex justify-end space-x-3">
          <button
            @click="resetFilters"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Grid View -->
      <div v-if="viewMode === 'grid'" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="holiday in filteredHolidays"
          :key="holiday.id"
          :class="[
            'bg-white rounded-lg shadow-sm border-2 p-6 hover:shadow-md transition-shadow',
            isToday(holiday.date) ? 'border-orange-500 ring-2 ring-orange-200' : 'border-gray-200',
            isPast(holiday.date) ? 'opacity-75' : ''
          ]"
        >
          <!-- Badge -->
          <div class="flex items-center justify-between mb-4">
            <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getTypeBadgeClass(holiday.type)]">
              {{ getTypeIcon(holiday.type) }} {{ holiday.type?.toUpperCase() }}
            </span>
            <span v-if="isUpcoming(holiday.date) && !isToday(holiday.date)" class="text-xs font-medium text-orange-600">
              In {{ daysUntil(holiday.date) }} days
            </span>
            <span v-if="isToday(holiday.date)" class="text-xs font-medium text-orange-600 animate-pulse">
              TODAY! 🎉
            </span>
          </div>

          <!-- Holiday Info -->
          <div class="text-center">
            <div class="text-4xl font-bold text-gray-900 mb-2">
              {{ getShortDate(holiday.date) }}
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ holiday.name }}</h3>
            <p class="text-sm text-gray-500">{{ getDayName(holiday.date) }}</p>
          </div>

          <!-- Description (if available) -->
          <div v-if="holiday.description" class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">{{ holiday.description }}</p>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-if="viewMode === 'list'" class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Holiday Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Day
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="holiday in filteredHolidays" 
                :key="holiday.id"
                :class="[
                  'hover:bg-gray-50',
                  isToday(holiday.date) ? 'bg-orange-50' : '',
                  isPast(holiday.date) ? 'opacity-60' : ''
                ]"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ formatDate(holiday.date).split(',')[0] }}</div>
                  <div class="text-sm text-gray-500">{{ getShortDate(holiday.date) }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ holiday.name }}</div>
                  <div v-if="holiday.description" class="text-sm text-gray-500">{{ holiday.description }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ getDayName(holiday.date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getTypeBadgeClass(holiday.type)]">
                    {{ holiday.type?.charAt(0).toUpperCase() + holiday.type?.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="isToday(holiday.date)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                    Today
                  </span>
                  <span v-else-if="isUpcoming(holiday.date)" class="text-sm text-green-600">
                    Upcoming ({{ daysUntil(holiday.date) }} days)
                  </span>
                  <span v-else class="text-sm text-gray-400">
                    Past
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="!filteredHolidays.length" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No holidays found</h3>
          <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="holidays.data?.length > 0 && (holidays.next_page_url || holidays.prev_page_url)" class="mt-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              v-if="holidays.prev_page_url"
              @click="router.get(holidays.prev_page_url)"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Previous
            </button>
            <button
              v-if="holidays.next_page_url"
              @click="router.get(holidays.next_page_url)"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Next
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ holidays.from || 0 }}</span>
                to
                <span class="font-medium">{{ holidays.to || 0 }}</span>
                of
                <span class="font-medium">{{ holidays.total || 0 }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  v-if="holidays.prev_page_url"
                  @click="router.get(holidays.prev_page_url)"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  <span class="sr-only">Previous</span>
                  <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </button>
                <button
                  v-if="holidays.next_page_url"
                  @click="router.get(holidays.next_page_url)"
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
  </EmployeeLayout>
</template>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>