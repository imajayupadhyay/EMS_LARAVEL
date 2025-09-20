<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:text-4xl">
              Leave Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
              View and manage employee leave requests
            </p>
          </div>
          <div class="mt-4 flex md:mt-0 md:ml-4 gap-3">
            <button
              @click="toggleFilters"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
              </svg>
              {{ showFilters ? 'Hide Filters' : 'Show Filters' }}
            </button>
            <button
              @click="exportLeaves"
              :disabled="loading"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
              </svg>
              {{ loading ? 'Exporting...' : 'Export' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Leave Summary (when employee is selected) -->
      <div v-if="leaveSummary" class="mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Leave Summary - {{ leaveSummary.employee_name }}
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-blue-50 rounded-lg">
              <p class="text-sm text-blue-600">Total Assigned</p>
              <p class="text-2xl font-bold text-blue-700">{{ leaveSummary.total_assigned }}</p>
            </div>
            <div class="p-4 bg-orange-50 rounded-lg">
              <p class="text-sm text-orange-600">Total Used</p>
              <p class="text-2xl font-bold text-orange-700">{{ leaveSummary.total_used }}</p>
            </div>
            <div class="p-4 bg-green-50 rounded-lg">
              <p class="text-sm text-green-600">Balance</p>
              <p class="text-2xl font-bold text-green-700">{{ leaveSummary.total_balance }}</p>
            </div>
          </div>

          <!-- Leave Breakdown -->
          <div v-if="leaveSummary.leave_breakdown && leaveSummary.leave_breakdown.length > 0" class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Leave Type Breakdown</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              <div
                v-for="(breakdown, idx) in leaveSummary.leave_breakdown"
                :key="idx"
                class="p-3 bg-gray-50 rounded-lg"
              >
                <p class="text-sm font-medium text-gray-900">{{ breakdown.leave_type }}</p>
                <div class="mt-2 flex justify-between text-xs text-gray-600">
                  <span>Total: {{ breakdown.total }}</span>
                  <span>Used: {{ breakdown.used }}</span>
                  <span>Balance: {{ breakdown.balance }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Leaves -->
          <div v-if="leaveSummary.recent_leaves && leaveSummary.recent_leaves.length > 0">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Recent Leave History</h4>
            <div class="space-y-2">
              <div
                v-for="(leave, idx) in leaveSummary.recent_leaves"
                :key="idx"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">{{ leave.leave_type }}</p>
                  <p class="text-xs text-gray-500">{{ leave.start_date }} to {{ leave.end_date }} ({{ leave.days }} days)</p>
                </div>
                <span :class="getStatusClass(leave.status)" class="px-2 py-1 text-xs font-medium rounded">
                  {{ leave.status }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div v-if="showFilters" class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Employee Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
              <select
                v-model="filtersLocal.employee_id"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Employees</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.first_name }} {{ emp.last_name }}
                </option>
              </select>
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                v-model="filtersLocal.status"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <!-- Leave Type Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
              <select
                v-model="filtersLocal.leave_type_id"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Types</option>
                <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                  {{ type.name }}
                </option>
              </select>
            </div>

            <!-- From Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
              <input
                type="date"
                v-model="filtersLocal.from_date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <!-- To Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
              <input
                type="date"
                v-model="filtersLocal.to_date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Filter Actions -->
          <div class="flex gap-3 mt-4">
            <button
              @click="applyFilters"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Apply Filters
            </button>
            <button
              @click="clearFilters"
              class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
            >
              Clear All
            </button>
          </div>
        </div>
      </div>

      <!-- Leave Requests -->
      <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Requests</h3>
        
        <div v-if="leaves.data && leaves.data.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div
              v-for="leave in leaves.data"
              :key="leave.id"
              class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow"
            >
              <!-- Employee Info -->
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                    {{ getInitials(leave.employee) }}
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-900">{{ getEmployeeName(leave.employee) }}</h4>
                    <p class="text-sm text-gray-500">{{ getEmployeeDepartment(leave.employee) }}</p>
                  </div>
                </div>
                <span :class="getStatusClass(leave.status)" class="px-2 py-1 text-xs font-medium rounded">
                  {{ leave.status }}
                </span>
              </div>

              <!-- Leave Details -->
              <div class="space-y-2 mb-3">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  <span>{{ leave.leave_type?.name || 'N/A' }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span>{{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>{{ getDays(leave.start_date, leave.end_date) }} days</span>
                </div>
              </div>

              <!-- Reason -->
              <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ leave.reason || 'No reason provided' }}</p>

              <!-- Actions -->
              <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <button
                  @click="viewDetails(leave.id)"
                  class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                >
                  View Details
                </button>
                <div v-if="leave.status === 'pending'" class="flex gap-2">
                  <button
                    @click="updateLeaveStatus(leave.id, 'approved')"
                    class="px-3 py-1 text-sm text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors"
                  >
                    Approve
                  </button>
                  <button
                    @click="updateLeaveStatus(leave.id, 'rejected')"
                    class="px-3 py-1 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors"
                  >
                    Reject
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="leaves.links" class="flex justify-center">
            <nav class="flex gap-2">
              <template v-for="(link, index) in leaves.links" :key="index">
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <p class="text-gray-500 text-lg">No leave requests found</p>
          <p class="text-gray-400 text-sm mt-2">Try adjusting your filters</p>
        </div>
      </div>
    </div>

    <!-- Details Modal -->
    <Transition name="modal">
      <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" @click="closeDetailsModal">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
          
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.stop>
            <!-- Modal Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Leave Request Details</h3>
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

              <div v-else-if="detailsData" class="space-y-4">
                <!-- Employee Info -->
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h4 class="text-sm font-semibold text-gray-700 mb-2">Employee Information</h4>
                  <div class="space-y-1 text-sm">
                    <p><span class="font-medium">Name:</span> {{ detailsData.employee }}</p>
                    <p><span class="font-medium">Department:</span> {{ detailsData.department }}</p>
                    <p><span class="font-medium">Designation:</span> {{ detailsData.designation }}</p>
                  </div>
                </div>

                <!-- Leave Details -->
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h4 class="text-sm font-semibold text-gray-700 mb-2">Leave Details</h4>
                  <div class="space-y-1 text-sm">
                    <p><span class="font-medium">Leave Type:</span> {{ detailsData.leave_type }}</p>
                    <p><span class="font-medium">Start Date:</span> {{ detailsData.start_date }}</p>
                    <p><span class="font-medium">End Date:</span> {{ detailsData.end_date }}</p>
                    <p><span class="font-medium">Total Days:</span> {{ detailsData.days }}</p>
                    <p><span class="font-medium">Day Type:</span> {{ formatDayType(detailsData.day_type) }}</p>
                    <p><span class="font-medium">Status:</span> 
                      <span :class="getStatusClass(detailsData.status)" class="px-2 py-0.5 text-xs rounded ml-1">
                        {{ detailsData.status }}
                      </span>
                    </p>
                    <p><span class="font-medium">Applied Date:</span> {{ detailsData.applied_at }}</p>
                  </div>
                </div>

                <!-- Reason -->
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h4 class="text-sm font-semibold text-gray-700 mb-2">Reason</h4>
                  <p class="text-sm text-gray-600">{{ detailsData.reason || 'No reason provided' }}</p>
                </div>

                <!-- Actions for pending leaves -->
                <div v-if="detailsData.status === 'pending'" class="flex gap-3 pt-4">
                  <button
                    @click="updateLeaveStatusFromModal(detailsData.id, 'approved')"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                  >
                    Approve
                  </button>
                  <button
                    @click="updateLeaveStatusFromModal(detailsData.id, 'rejected')"
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                  >
                    Reject
                  </button>
                </div>
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
    leaves: {
      type: Object,
      default: () => ({ data: [], links: [] })
    },
    employees: {
      type: Array,
      default: () => []
    },
    leaveTypes: {
      type: Array,
      default: () => []
    },
    leaveSummary: {
      type: Object,
      default: null
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
        status: this.filters.status || '',
        leave_type_id: this.filters.leave_type_id || '',
        date: this.filters.date || '',
        from_date: this.filters.from_date || '',
        to_date: this.filters.to_date || '',
      },
      csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
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

      router.get(route('manager.leaves.index'), params, {
        preserveState: true,
        preserveScroll: true,
      });
    },

    clearFilters() {
      this.filtersLocal = {
        employee_id: '',
        status: '',
        leave_type_id: '',
        date: '',
        from_date: '',
        to_date: '',
      };
      
      router.get(route('manager.leaves.index'), this.filtersLocal, {
        preserveState: true,
        preserveScroll: true,
      });
    },

    exportLeaves() {
      this.loading = true;
      const params = new URLSearchParams();
      
      Object.keys(this.filtersLocal).forEach(key => {
        if (this.filtersLocal[key]) {
          params.append(key, this.filtersLocal[key]);
        }
      });

      window.location.href = route('manager.leaves.export') + '?' + params.toString();
      
      setTimeout(() => {
        this.loading = false;
      }, 1000);
    },

    async viewDetails(leaveId) {
      this.showDetailsModal = true;
      this.modalLoading = true;
      this.detailsData = null;

      try {
        const response = await fetch(route('manager.leaves.details', { leave: leaveId }));
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

    async updateLeaveStatus(leaveId, status) {
      if (!confirm(`Are you sure you want to ${status} this leave request?`)) {
        return;
      }

      try {
        await router.post(route('manager.leaves.update-status', { leave: leaveId }), 
          { status },
          {
            preserveState: false,
            preserveScroll: true,
          }
        );
      } catch (error) {
        console.error('Error updating leave status:', error);
      }
    },

    async updateLeaveStatusFromModal(leaveId, status) {
      this.closeDetailsModal();
      await this.updateLeaveStatus(leaveId, status);
    },

    changePage(url) {
      router.get(url, {}, {
        preserveState: true,
        preserveScroll: true,
      });
    },

    getInitials(employee) {
      if (!employee) return 'E';
      const name = this.getEmployeeName(employee);
      const parts = name.trim().split(' ');
      if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
      }
      return name[0].toUpperCase();
    },

    getEmployeeName(employee) {
      if (!employee) return 'Unknown';
      return `${employee.first_name || ''} ${employee.last_name || ''}`.trim();
    },

    getEmployeeDepartment(employee) {
      if (!employee || !employee.department) return 'No Department';
      return employee.department.name;
    },

    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-700',
        approved: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700',
      };
      return classes[status] || 'bg-gray-100 text-gray-700';
    },

    formatDate(date) {
      if (!date) return 'N/A';
      const d = new Date(date);
      return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    },

    formatDayType(type) {
      if (!type) return 'Full Day';
      return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    },

    getDays(startDate, endDate) {
      if (!startDate || !endDate) return 0;
      const start = new Date(startDate);
      const end = new Date(endDate);
      const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
      return diff + 1;
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

/* Line clamp */
.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

/* Animations */
.transition-shadow {
  transition: box-shadow 0.15s ease-in-out;
}

.transition-colors {
  transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>