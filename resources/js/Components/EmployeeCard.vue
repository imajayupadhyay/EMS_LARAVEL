<template>
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
    <div class="p-4">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
          <input
            type="checkbox"
            :checked="selected"
            @change="$emit('toggle-selection', employee.id, $event.target.checked)"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <div class="flex-shrink-0 h-10 w-10">
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
              <span class="text-sm font-medium text-white">
                {{ getInitials(employee.first_name, employee.last_name) }}
              </span>
            </div>
          </div>
          <div>
            <h3 class="text-lg font-medium text-gray-900">
              {{ employee.first_name }} {{ employee.last_name }}
            </h3>
            <p class="text-sm text-gray-500">{{ employee.employee_code || 'No Code' }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-2 text-sm text-gray-700 mb-4">
        <div><span class="font-medium">Email:</span> {{ employee.email }}</div>
        <div><span class="font-medium">Contact:</span> {{ employee.contact }}</div>
        <div><span class="font-medium">Department:</span> {{ employee.department?.name || '-' }}</div>
        <div><span class="font-medium">Designation:</span> {{ employee.designation?.name || '-' }}</div>
        <div><span class="font-medium">Joined:</span> {{ formatDate(employee.doj) }}</div>
      </div>

      <div class="flex justify-between items-center pt-3 border-t border-gray-100">
        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
          {{ employee.department?.name || 'No Department' }}
        </span>
        <div class="flex items-center space-x-3">
          <button
            @click="$emit('view', employee.id)"
            class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-50"
            title="View Details"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
              <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
            </svg>
          </button>
          <Link
            v-if="canEdit"
            :href="route('manager.employees.edit', employee.id)"
            class="text-indigo-600 hover:text-indigo-800 p-2 rounded-full hover:bg-indigo-50"
            title="Edit Employee"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
            </svg>
          </Link>
          <button
            v-if="canDelete"
            @click="$emit('confirm-delete', employee)"
            class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50"
            title="Delete Employee"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  name: 'EmployeeCard',
  
  components: {
    Link,
  },
  
  props: {
    employee: {
      type: Object,
      required: true
    },
    selected: {
      type: Boolean,
      default: false
    },
    canEdit: {
      type: Boolean,
      default: false
    },
    canDelete: {
      type: Boolean,
      default: false
    }
  },
  
  emits: ['toggle-selection', 'view', 'confirm-delete'],
  
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
          month: 'short',
          day: 'numeric'
        });
      } catch (e) {
        return date;
      }
    }
  }
};
</script>