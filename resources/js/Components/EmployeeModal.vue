<template>
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">Employee Details</h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100"
          >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>

        <div class="px-6 py-4 overflow-y-auto max-h-[calc(90vh-8rem)]">
          <LoadingSpinner v-if="loading" />

          <div v-else-if="employee && employee.id" class="space-y-6">
            <!-- Employee Header -->
            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
              <div class="flex-shrink-0 h-16 w-16">
                <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                  <span class="text-xl font-medium text-white">
                    {{ getInitials(employee.first_name, employee.last_name) }}
                  </span>
                </div>
              </div>
              <div>
                <h4 class="text-xl font-semibold text-gray-900">{{ employee.full_name }}</h4>
                <p class="text-gray-600">{{ employee.employee_code || 'No Employee Code' }}</p>
                <div class="flex items-center space-x-4 mt-2">
                  <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    {{ employee.department?.name || 'No Department' }}
                  </span>
                  <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                    {{ employee.designation?.name || 'No Designation' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h5 class="text-lg font-medium text-gray-900 mb-3">Personal Information</h5>
                <div class="space-y-3">
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Email:</span>
                    <span class="text-gray-900">{{ employee.email }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Contact:</span>
                    <span class="text-gray-900">{{ employee.contact }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Emergency Contact:</span>
                    <span class="text-gray-900">{{ employee.emergency_contact || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Gender:</span>
                    <span class="text-gray-900 capitalize">{{ employee.gender || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Date of Birth:</span>
                    <span class="text-gray-900">{{ formatDate(employee.dob) }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Marital Status:</span>
                    <span class="text-gray-900 capitalize">{{ employee.marital_status || '-' }}</span>
                  </div>
                </div>
              </div>

              <div>
                <h5 class="text-lg font-medium text-gray-900 mb-3">Work Information</h5>
                <div class="space-y-3">
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Date of Joining:</span>
                    <span class="text-gray-900">{{ formatDate(employee.doj) }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Department:</span>
                    <span class="text-gray-900">{{ employee.department?.name || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Designation:</span>
                    <span class="text-gray-900">{{ employee.designation?.name || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Pay Scale:</span>
                    <span class="text-gray-900">{{ employee.pay_scale || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Work Location:</span>
                    <span class="text-gray-900">{{ employee.work_location || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="font-medium text-gray-600">Employee Since:</span>
                    <span class="text-gray-900">{{ calculateEmployeeTenure(employee.doj) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Address Information -->
            <div>
              <h5 class="text-lg font-medium text-gray-900 mb-3">Address Information</h5>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-900">{{ employee.address || 'No address provided' }}</p>
                <p v-if="employee.zip" class="text-gray-600 mt-1">ZIP: {{ employee.zip }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
          <div class="flex space-x-3">
            <Link
              v-if="canEdit && employee.id"
              :href="route('manager.employees.edit', employee.id)"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
              </svg>
              Edit Employee
            </Link>
          </div>
          <button
            @click="$emit('close')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import LoadingSpinner from '@/Components/Common/LoadingSpinner.vue';

export default {
  name: 'EmployeeModal',
  
  components: {
    Link,
    LoadingSpinner,
  },
  
  props: {
    show: {
      type: Boolean,
      default: false
    },
    employee: {
      type: Object,
      default: () => ({})
    },
    loading: {
      type: Boolean,
      default: false
    },
    canEdit: {
      type: Boolean,
      default: false
    }
  },
  
  emits: ['close'],
  
  methods: {
    getInitials(firstName, lastName) {
      const first = firstName ? firstName.charAt(0).toUpperCase() : '';
      const last = lastName ? lastName.charAt(0).toUpperCase() : '';
      return first + last;
    },
    
    formatDate(date) {
      if (!date) return '-';
      try {
        return new Date(date).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      } catch (e) {
        return date;
      }
    },
    
    calculateEmployeeTenure(doj) {
      if (!doj) return '-';
      try {
        const joinDate = new Date(doj);
        const now = new Date();
        const diffTime = Math.abs(now - joinDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 30) {
          return `${diffDays} days`;
        } else if (diffDays < 365) {
          const months = Math.floor(diffDays / 30);
          return `${months} month${months > 1 ? 's' : ''}`;
        } else {
          const years = Math.floor(diffDays / 365);
          const remainingMonths = Math.floor((diffDays % 365) / 30);
          return `${years} year${years > 1 ? 's' : ''}${remainingMonths > 0 ? ` ${remainingMonths} month${remainingMonths > 1 ? 's' : ''}` : ''}`;
        }
      } catch (e) {
        return '-';
      }
    }
  }
};
</script>

<style scoped>
/* Custom scrollbar for modal */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>