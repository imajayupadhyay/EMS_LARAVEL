<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl">
              Attendance Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
              Track and monitor employee attendance records
            </p>
          </div>
          <div class="mt-4 flex md:mt-0 md:ml-4 gap-3">
            <button
              @click="toggleFilters"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
              </svg>
              {{ showFilters ? 'Hide Filters' : 'Show Filters' }}
            </button>
            <button
              @click="exportAttendance"
              :disabled="loading"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
              </svg>
              {{ loading ? 'Exporting...' : 'Export' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div v-if="showFilters" class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Employee Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
              <select
                v-model="filtersLocal.employee_id"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
                <option value="">All Employees</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.first_name }} {{ emp.last_name }}
                </option>
              </select>
            </div>

            <!-- Department Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
              <select
                v-model="filtersLocal.department_id"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
                <option value="">All Departments</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>

            <!-- Designation Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
              <select
                v-model="filtersLocal.designation_id"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
                <option value="">All Designations</option>
                <option v-for="desig in designations" :key="desig.id" :value="desig.id">
                  {{ desig.name }}
                </option>
              </select>
            </div>

            <!-- Specific Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Specific Date</label>
              <input
                type="date"
                v-model="filtersLocal.date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>

            <!-- From Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
              <input
                type="date"
                v-model="filtersLocal.from_date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>

            <!-- To Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
              <input
                type="date"
                v-model="filtersLocal.to_date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>

            <!-- Month Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
              <input
                type="month"
                v-model="filtersLocal.month"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>
          </div>

          <!-- Filter Actions -->
          <div class="flex gap-3 mt-4">
            <button
              @click="applyFilters"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
              Apply Filters
            </button>
            <button
              @click="clearFilters"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
              Clear All
            </button>
          </div>
        </div>
      </div>

      <!-- Today's Attendance Section -->
      <div v-if="todayAttendance && todayAttendance.length > 0" class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Attendance</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="(record, idx) in todayAttendance"
            :key="idx"
            class="bg-white rounded-lg shadow-sm border border-l-4 border-l-blue-500 p-4 hover:shadow-md transition-shadow cursor-pointer"
          >
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                  {{ getInitials(record.employee) }}
                </div>
                <div>
                  <h4 class="font-semibold text-gray-900">{{ record.employee }}</h4>
                  <p class="text-sm text-gray-500">{{ record.designation }}</p>
                </div>
              </div>
              <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">Today</span>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="text-sm text-gray-600">{{ record.department }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-600">{{ record.date }}</span>
              </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
              <div class="flex items-center gap-4">
                <div>
                  <p class="text-xs text-gray-500">First In</p>
                  <p class="text-sm font-medium text-green-600">{{ record.first_in || '—' }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Last Out</p>
                  <p class="text-sm font-medium text-red-600">{{ record.last_out || '—' }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Total Hours</p>
                  <p class="text-sm font-medium text-blue-600">{{ record.hours || '—' }}</p>
                </div>
              </div>
              <button
                @click="viewDetails(record.employee_id, record.date)"
                class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
              >
                Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Previous/All Attendance Records -->
      <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          {{ todayAttendance && todayAttendance.length > 0 ? 'Previous Attendance' : 'Attendance Records' }}
        </h3>
        
        <div v-if="attendance.data && attendance.data.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div
              v-for="(record, idx) in attendance.data"
              :key="idx"
              class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer"
            >
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                    {{ getInitials(record.employee) }}
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900">{{ record.employee }}</h4>
                    <p class="text-sm text-gray-500">{{ record.designation }}</p>
                  </div>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  <span class="text-sm text-gray-600">{{ record.department }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span class="text-sm text-gray-600">{{ record.date }}</span>
                </div>
              </div>

              <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div class="flex items-center gap-4">
                  <div>
                    <p class="text-xs text-gray-500">First In</p>
                    <p class="text-sm font-medium text-green-600">{{ record.first_in || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Last Out</p>
                    <p class="text-sm font-medium text-red-600">{{ record.last_out || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Total Hours</p>
                    <p class="text-sm font-medium text-blue-600">{{ record.hours || '—' }}</p>
                  </div>
                </div>
                <button
                  @click="viewDetails(record.employee_id, record.date)"
                  class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                >
                  Details
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="attendance.links" class="flex justify-center">
            <nav class="flex gap-2">
              <template v-for="(link, index) in attendance.links" :key="index">
                <button
                  v-if="link.url"
                  @click="changePage(link.url)"
                  :class="[
                    'px-3 py-1 rounded-lg text-sm',
                    link.active
                      ? 'bg-blue-600 text-white'
                      : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
                  ]"
                  v-html="link.label"
                ></button>
                <span
                  v-else
                  :class="[
                    'px-3 py-1 rounded-lg text-sm',
                    'bg-gray-100 text-gray-400 cursor-not-allowed'
                  ]"
                  v-html="link.label"
                ></span>
              </template>
            </nav>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <p class="text-gray-500 text-lg">No attendance records found</p>
          <p class="text-gray-400 text-sm mt-2">Try adjusting your filters</p>
        </div>
      </div>
    </div>

    <!-- Details Modal -->
    <Transition name="modal">
      <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" @click="closeDetailsModal">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
          
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" @click.stop>
            <!-- Modal Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Attendance Details</h3>
                <button @click="closeDetailsModal" class="text-gray-400 hover:text-gray-600">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
              <div v-if="modalLoading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="text-gray-500 mt-4">Loading details...</p>
              </div>

              <div v-else-if="detailsData" class="space-y-6">
                <!-- Employee Info -->
                <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                  <div>
                    <p class="text-sm text-gray-500">Employee</p>
                    <p class="font-medium">{{ detailsData.employee }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Date</p>
                    <p class="font-medium">{{ detailsData.date }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Department</p>
                    <p class="font-medium">{{ detailsData.department }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">Designation</p>
                    <p class="font-medium">{{ detailsData.designation }}</p>
                  </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-3 gap-4">
                  <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-green-600">First In</p>
                    <p class="text-lg font-semibold text-green-700">{{ detailsData.first_in || '—' }}</p>
                  </div>
                  <div class="p-4 bg-red-50 rounded-lg">
                    <p class="text-sm text-red-600">Last Out</p>
                    <p class="text-lg font-semibold text-red-700">{{ detailsData.last_out || '—' }}</p>
                  </div>
                  <div class="p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-600">Total Hours</p>
                    <p class="text-lg font-semibold text-blue-700">{{ detailsData.hours || '—' }}</p>
                  </div>
                </div>

                <!-- Punch Intervals -->
                <div v-if="detailsData.intervals && detailsData.intervals.length > 0">
                  <h4 class="font-semibold text-gray-900 mb-3">Punch Intervals</h4>
                  <div class="space-y-2">
                    <div
                      v-for="(interval, idx) in detailsData.intervals"
                      :key="idx"
                      class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                    >
                      <div class="flex items-center gap-4">
                        <div>
                          <p class="text-xs text-gray-500">Punch In</p>
                          <p class="text-sm font-medium">{{ interval.punch_in }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <div>
                          <p class="text-xs text-gray-500">Punch Out</p>
                          <p class="text-sm font-medium">{{ interval.punch_out }}</p>
                        </div>
                      </div>
                      <div class="text-right">
                        <p class="text-xs text-gray-500">Duration</p>
                        <p class="text-sm font-medium text-blue-600">{{ interval.duration }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="text-center py-12">
                <p class="text-gray-500">No details available</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import ManagerLayout from '@/Layouts/ManagerLayout.vue';
import { router } from '@inertiajs/vue3';

export default {
  layout: ManagerLayout,

  props: {
    todayAttendance: {
      type: Array,
      default: () => []
    },
    attendance: {
      type: Object,
      default: () => ({ data: [], links: [] })
    },
    employees: {
      type: Array,
      default: () => []
    },
    departments: {
      type: Array,
      default: () => []
    },
    designations: {
      type: Array,
      default: () => []
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      showFilters: false,
      showDetailsModal: false,
      modalLoading: false,
      loading: false,
      detailsData: null,
      filtersLocal: {
        employee_id: this.filters.employee_id || '',
        department_id: this.filters.department_id || '',
        designation_id: this.filters.designation_id || '',
        date: this.filters.date || '',
        from_date: this.filters.from_date || '',
        to_date: this.filters.to_date || '',
        month: this.filters.month || new Date().toISOString().slice(0, 7),
      }
    };
  },

  methods: {
    toggleFilters() {
      this.showFilters = !this.showFilters;
    },

    applyFilters() {
      const params = {};
      Object.keys(this.filtersLocal).forEach(key => {
        if (this.filtersLocal[key]) {
          params[key] = this.filtersLocal[key];
        }
      });

      router.get(route('manager.attendance.index'), params, {
        preserveState: true,
        preserveScroll: true,
      });
    },

    clearFilters() {
      this.filtersLocal = {
        employee_id: '',
        department_id: '',
        designation_id: '',
        date: '',
        from_date: '',
        to_date: '',
        month: new Date().toISOString().slice(0, 7),
      };
      
      router.get(route('manager.attendance.index'), this.filtersLocal, {
        preserveState: true,
        preserveScroll: true,
      });
    },

    exportAttendance() {
      this.loading = true;
      const params = new URLSearchParams();
      
      Object.keys(this.filtersLocal).forEach(key => {
        if (this.filtersLocal[key]) {
          params.append(key, this.filtersLocal[key]);
        }
      });

      window.location.href = route('manager.attendance.export') + '?' + params.toString();
      
      setTimeout(() => {
        this.loading = false;
      }, 1000);
    },

    async viewDetails(employeeId, date) {
      this.showDetailsModal = true;
      this.modalLoading = true;
      this.detailsData = null;

      try {
        const response = await fetch(route('manager.attendance.details', { employeeId, date }));
        const data = await response.json();
        this.detailsData = data;
      } catch (error) {
        console.error('Error fetching details:', error);
      } finally {
        this.modalLoading = false;
      }
    },

    closeDetailsModal() {
      this.showDetailsModal = false;
      this.detailsData = null;
    },

    changePage(url) {
      router.get(url, {}, {
        preserveState: true,
        preserveScroll: true,
      });
    },

    getInitials(name) {
      if (!name) return 'E';
      const parts = name.trim().split(' ');
      if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
      }
      return name[0].toUpperCase();
    }
  }
};
</script>

<style scoped>
/* Modal transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Smooth animations */
.transition-shadow {
  transition: box-shadow 0.15s ease-in-out;
}

.transition-colors {
  transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
}

/* Loading animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>