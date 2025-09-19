<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header Section -->
    <!-- <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Employee Management</h1>
    </div> -->

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search by Name</label>
          <input
            v-model="filters.name"
            @input="applyFilters"
            placeholder="Enter employee name..."
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
          <select 
            v-model="filters.department_id" 
            @change="applyFilters" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
          >
            <option value="">All Departments</option>
            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Designation</label>
          <select 
            v-model="filters.designation_id" 
            @change="applyFilters" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
          >
            <option value="">All Designations</option>
            <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Employee Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="emp in employees"
        :key="emp.id"
        class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 overflow-hidden"
      >
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-4">
          <h3 class="text-lg font-semibold text-white">
            {{ emp.first_name }} {{ emp.middle_name }} {{ emp.last_name }}
          </h3>
          <p class="text-orange-100 text-sm">{{ emp.email }}</p>
        </div>

        <!-- Card Body -->
        <div class="p-4 space-y-3">
          <div class="flex items-center text-sm">
            <span class="text-gray-500 w-24">Department:</span>
            <span class="text-gray-900 font-medium">{{ emp.department?.name || 'N/A' }}</span>
          </div>
          <div class="flex items-center text-sm">
            <span class="text-gray-500 w-24">Designation:</span>
            <span class="text-gray-900 font-medium">{{ emp.designation?.name || 'N/A' }}</span>
          </div>
          <div class="flex items-center text-sm">
            <span class="text-gray-500 w-24">Shift:</span>
            <span class="text-gray-900 font-medium">{{ emp.shift?.name || 'Not Assigned' }}</span>
          </div>
          <div v-if="emp.shift" class="flex items-center text-sm">
            <span class="text-gray-500 w-24">Timing:</span>
            <span class="text-gray-600">
              {{ emp.shift.time_from?.substring(0, 5) }} - {{ emp.shift.time_to?.substring(0, 5) }}
            </span>
          </div>
        </div>

        <!-- Card Actions -->
        <div class="border-t border-gray-100 px-4 py-3 bg-gray-50 flex gap-2">
          <button 
            @click="openView(emp.id)" 
            class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-medium text-sm"
          >
            View
          </button>
          <button 
            @click="startEdit(emp)" 
            class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium text-sm"
          >
            Edit
          </button>
          <button 
            @click="confirmDelete(emp.id)" 
            class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium text-sm"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!employees || employees.length === 0" class="text-center py-12">
      <div class="text-gray-400 text-6xl mb-4">👥</div>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">No employees found</h3>
      <p class="text-gray-600">Try adjusting your filters or add a new employee</p>
    </div>

    <!-- View Modal -->
    <div v-if="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
      <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex items-center justify-between">
          <h2 class="text-2xl font-bold text-white">Employee Details</h2>
          <button @click="closeView" class="text-white hover:text-gray-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
          <div v-if="loadingView" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Full Name</label>
              <p class="text-lg font-semibold text-gray-900">{{ viewData.full_name }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Email</label>
              <p class="text-gray-900">{{ viewData.email }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Contact</label>
              <p class="text-gray-900">{{ viewData.contact }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Emergency Contact</label>
              <p class="text-gray-900">{{ viewData.emergency_contact || 'N/A' }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Department</label>
              <p class="text-gray-900">{{ viewData.department?.name || 'N/A' }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Designation</label>
              <p class="text-gray-900">{{ viewData.designation?.name || 'N/A' }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Shift</label>
              <p class="text-gray-900">{{ viewData.shift?.name || 'Not Assigned' }}</p>
              <p v-if="viewData.shift" class="text-sm text-gray-500">
                {{ viewData.shift.time_from?.substring(0, 5) }} - {{ viewData.shift.time_to?.substring(0, 5) }}
              </p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Date of Birth</label>
              <p class="text-gray-900">{{ formatDate(viewData.dob) }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Date of Joining</label>
              <p class="text-gray-900">{{ formatDate(viewData.doj) }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Gender</label>
              <p class="text-gray-900">{{ viewData.gender || 'N/A' }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Marital Status</label>
              <p class="text-gray-900">{{ viewData.marital_status || 'N/A' }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Work Location</label>
              <p class="text-gray-900">{{ viewData.work_location || 'N/A' }}</p>
            </div>

            <div class="md:col-span-2 space-y-1">
              <label class="text-sm font-medium text-gray-500">Address</label>
              <p class="text-gray-900">{{ viewData.address || 'N/A' }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">ZIP Code</label>
              <p class="text-gray-900">{{ viewData.zip || 'N/A' }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Pay Scale</label>
              <p class="text-gray-900">{{ viewData.pay_scale || 'N/A' }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Monthly Salary</label>
              <p class="text-lg font-semibold text-orange-600">
                {{ formatMoney(viewData.monthly_salary) }} {{ viewData.salary_currency || 'INR' }}
              </p>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500">Salary Type</label>
              <p class="text-gray-900 capitalize">{{ viewData.salary_type || 'monthly' }}</p>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end">
          <button 
            @click="closeView" 
            class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
      <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 flex items-center justify-between">
          <h2 class="text-2xl font-bold text-white">Edit Employee</h2>
          <button @click="showModal = false" class="text-white hover:text-gray-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="saveEdit" class="p-6 overflow-y-auto max-h-[calc(90vh-160px)]">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
              <input v-model="editForm.first_name" required class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
              <input v-model="editForm.middle_name" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
              <input v-model="editForm.last_name" required class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
              <input v-model="editForm.email" type="email" required class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Contact *</label>
              <input v-model="editForm.contact" required class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label>
              <input v-model="editForm.emergency_contact" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
              <select v-model="editForm.gender" class="input-field">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
              <input v-model="editForm.dob" type="date" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date of Joining</label>
              <input v-model="editForm.doj" type="date" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Marital Status</label>
              <select v-model="editForm.marital_status" class="input-field">
                <option value="">Select Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Divorced">Divorced</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
              <input v-model="editForm.address" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
              <input v-model="editForm.zip" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Pay Scale</label>
              <input v-model="editForm.pay_scale" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Work Location</label>
              <input v-model="editForm.work_location" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
              <select v-model="editForm.department_id" required class="input-field">
                <option value="">Select Department</option>
                <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Designation *</label>
              <select v-model="editForm.designation_id" required class="input-field">
                <option value="">Select Designation</option>
                <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </div>
            
            <!-- FIXED: Shift Dropdown with proper v-if to ensure shifts prop exists -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
              <select v-model="editForm.shift_id" class="input-field">
                <option :value="null">Select Shift (Optional)</option>
                <option v-for="shift in (shifts || [])" :key="shift.id" :value="shift.id">
                  {{ shift.name }} ({{ shift.time_from?.substring(0, 5) || '00:00' }} - {{ shift.time_to?.substring(0, 5) || '00:00' }})
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Salary</label>
              <input v-model.number="editForm.monthly_salary" type="number" step="0.01" min="0" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
              <input v-model="editForm.salary_currency" placeholder="INR" maxlength="3" class="input-field" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Salary Type</label>
              <select v-model="editForm.salary_type" class="input-field">
                <option value="monthly">Monthly</option>
                <option value="daily">Daily</option>
                <option value="hourly">Hourly</option>
              </select>
            </div>
          </div>
        </form>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end gap-3">
          <button 
            type="button" 
            @click="showModal = false" 
            class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium"
          >
            Cancel
          </button>
          <button 
            @click="saveEdit"
            class="px-6 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors font-medium"
          >
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { route } from 'ziggy-js';

const props = defineProps({
  employees: Array,
  departments: Array,
  designations: Array,
  shifts: Array,
  filters: Object,
});

const filters = reactive({ ...props.filters });
const showModal = ref(false);
const editForm = reactive({
  first_name: '',
  middle_name: '',
  last_name: '',
  email: '',
  contact: '',
  emergency_contact: '',
  gender: '',
  dob: '',
  doj: '',
  marital_status: '',
  address: '',
  zip: '',
  pay_scale: '',
  work_location: '',
  department_id: '',
  designation_id: '',
  shift_id: null,
  monthly_salary: null,
  salary_currency: 'INR',
  salary_type: 'monthly',
});
let currentId = null;

const viewModal = ref(false);
const viewData = reactive({});
const loadingView = ref(false);

const applyFilters = () => {
  router.get(route('admin.employees.manage'), filters, {
    preserveScroll: true,
    preserveState: true,
  });
};

const startEdit = (emp) => {
  showModal.value = true;
  currentId = emp.id;

  editForm.first_name = emp.first_name ?? '';
  editForm.middle_name = emp.middle_name ?? '';
  editForm.last_name = emp.last_name ?? '';
  editForm.email = emp.email ?? '';
  editForm.contact = emp.contact ?? '';
  editForm.emergency_contact = emp.emergency_contact ?? '';
  editForm.gender = emp.gender ?? '';
  editForm.dob = emp.dob ?? '';
  editForm.doj = emp.doj ?? '';
  editForm.marital_status = emp.marital_status ?? '';
  editForm.address = emp.address ?? '';
  editForm.zip = emp.zip ?? '';
  editForm.pay_scale = emp.pay_scale ?? '';
  editForm.work_location = emp.work_location ?? '';
  editForm.department_id = emp.department_id ?? emp.department?.id ?? '';
  editForm.designation_id = emp.designation_id ?? emp.designation?.id ?? '';
  // FIXED: Properly handle shift_id conversion to number or null
  editForm.shift_id = emp.shift_id ? Number(emp.shift_id) : (emp.shift?.id ? Number(emp.shift.id) : null);
  editForm.monthly_salary = emp.monthly_salary !== undefined && emp.monthly_salary !== null ? Number(emp.monthly_salary) : null;
  editForm.salary_currency = emp.salary_currency ?? 'INR';
  editForm.salary_type = emp.salary_type ?? 'monthly';
};

const saveEdit = () => {
  const payload = {
    ...editForm,
    _method: 'put'
  };

  router.post(route('admin.employees.manage.update', currentId), payload, {
    onSuccess: () => {
      showModal.value = false;
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
    }
  });
};

const confirmDelete = (id) => {
  if (confirm("Are you sure you want to delete this employee?")) {
    router.post(route('admin.employees.manage.destroy', id), {}, {
      preserveScroll: true,
    });
  }
};

const openView = async (id) => {
  viewModal.value = true;
  loadingView.value = true;
  Object.keys(viewData).forEach(k => delete viewData[k]);

  try {
    const res = await axios.get(route('admin.employees.manage.show', id));
    if (res.data && res.data.success) {
      Object.assign(viewData, res.data.data || {});
    }
  } catch (err) {
    console.error('Failed to load employee:', err);
  } finally {
    loadingView.value = false;
  }
};

const closeView = () => {
  viewModal.value = false;
  Object.keys(viewData).forEach(k => delete viewData[k]);
};

const formatDate = (d) => {
  if (!d) return 'N/A';
  const dt = (d || '').split('T')[0];
  const [y,m,day] = dt.split('-') || [];
  if (!y) return d;
  return `${day}-${m}-${y}`;
};

const formatMoney = (value) => {
  if (value === null || value === undefined || value === '') return '0.00';
  const n = Number(value) || 0;
  return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>

<style scoped>
.input-field {
  @apply w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all;
}
</style>