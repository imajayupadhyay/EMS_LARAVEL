<script setup>
import { reactive, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import { route } from 'ziggy-js'

defineOptions({ layout: ManagerLayout })

const props = defineProps({
  employees: Array,
  shifts: Array,
  departments: Array,
  designations: Array,
  filters: Object,
})

const filters = reactive({ ...props.filters })
const showModal = ref(false)
const selectedEmployee = ref(null)

const form = useForm({
  shift_id: null,
})

const applyFilters = () => {
  router.get(route('manager.employees.shifts.index'), filters, {
    preserveScroll: true,
    preserveState: true,
  })
}

const openAssignModal = (employee) => {
  selectedEmployee.value = employee
  form.shift_id = employee.shift_id || null
  showModal.value = true
}

const assignShift = () => {
  form.post(route('manager.employees.assign-shift', selectedEmployee.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showModal.value = false
      form.reset()
    },
  })
}
</script>

<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Employee Shift Assignment</h1>
      <p class="text-gray-600">Manage and assign shifts to employees</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
          <select 
            v-model="filters.department_id" 
            @change="applyFilters" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Departments</option>
            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
          <select 
            v-model="filters.shift_id" 
            @change="applyFilters" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Shifts</option>
            <option v-for="s in shifts" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Employee Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="emp in employees"
        :key="emp.id"
        class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 overflow-hidden"
      >
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
          <h3 class="text-lg font-semibold text-white">
            {{ emp.first_name }} {{ emp.middle_name }} {{ emp.last_name }}
          </h3>
          <p class="text-blue-100 text-sm">{{ emp.email }}</p>
        </div>

        <!-- Card Body -->
        <div class="p-4 space-y-3">
          <div class="flex items-center text-sm">
            <span class="text-gray-500 w-28">Department:</span>
            <span class="text-gray-900 font-medium">{{ emp.department?.name || 'N/A' }}</span>
          </div>
          <div class="flex items-center text-sm">
            <span class="text-gray-500 w-28">Designation:</span>
            <span class="text-gray-900 font-medium">{{ emp.designation?.name || 'N/A' }}</span>
          </div>
          <div class="flex items-center text-sm">
            <span class="text-gray-500 w-28">Current Shift:</span>
            <span 
              class="font-medium"
              :class="emp.shift ? 'text-green-600' : 'text-red-600'"
            >
              {{ emp.shift?.name || 'Not Assigned' }}
            </span>
          </div>
          <div v-if="emp.shift" class="flex items-center text-sm">
            <span class="text-gray-500 w-28">Timing:</span>
            <span class="text-gray-600">
              {{ emp.shift.time_from?.substring(0, 5) }} - {{ emp.shift.time_to?.substring(0, 5) }}
            </span>
          </div>
        </div>

        <!-- Card Actions -->
        <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
          <button 
            @click="openAssignModal(emp)" 
            class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium text-sm"
          >
            {{ emp.shift ? 'Change Shift' : 'Assign Shift' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!employees || employees.length === 0" class="text-center py-12">
      <div class="text-gray-400 text-6xl mb-4">👥</div>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">No employees found</h3>
      <p class="text-gray-600">Try adjusting your filters</p>
    </div>

    <!-- Assign Shift Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
      <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex items-center justify-between">
          <h2 class="text-xl font-bold text-white">Assign Shift</h2>
          <button @click="showModal = false" class="text-white hover:text-gray-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="assignShift" class="p-6">
          <div class="mb-4">
            <p class="text-gray-700 mb-4">
              Assign shift to: <span class="font-semibold">{{ selectedEmployee?.first_name }} {{ selectedEmployee?.last_name }}</span>
            </p>
            
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Shift</label>
            <select 
              v-model="form.shift_id" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option :value="null">No Shift (Remove Assignment)</option>
              <option v-for="shift in shifts" :key="shift.id" :value="shift.id">
                {{ shift.name }} ({{ shift.time_from?.substring(0, 5) }} - {{ shift.time_to?.substring(0, 5) }})
              </option>
            </select>
          </div>

          <!-- Modal Footer -->
          <div class="flex justify-end gap-3">
            <button 
              type="button" 
              @click="showModal = false" 
              class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium disabled:opacity-50"
            >
              {{ form.processing ? 'Saving...' : 'Assign Shift' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>