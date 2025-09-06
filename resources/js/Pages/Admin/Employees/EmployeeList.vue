<template>
  <div class="min-h-screen bg-gray-50 p-4">
    <!-- Filters -->
    <div class="bg-white shadow p-4 rounded-lg mb-6 flex flex-col md:flex-row gap-4 md:items-center">
      <input
        v-model="filters.name"
        @input="applyFilters"
        placeholder="Search by Name"
        class="input w-full md:w-auto"
      />
      <select v-model="filters.department_id" @change="applyFilters" class="input w-full md:w-auto">
        <option value="">All Departments</option>
        <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>
      <select v-model="filters.designation_id" @change="applyFilters" class="input w-full md:w-auto">
        <option value="">All Designations</option>
        <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>
    </div>

    <!-- Employee Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="emp in employees"
        :key="emp.id"
        class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition duration-300"
      >
        <h3 class="font-bold text-orange-600">{{ emp.first_name }} {{ emp.middle_name }} {{ emp.last_name }}</h3>
        <p class="text-sm text-gray-500">{{ emp.email }}</p>
        <p class="text-sm text-gray-600">{{ emp.department?.name }} - {{ emp.designation?.name }}</p>
        <div class="mt-3 flex gap-2">
          <button @click="openView(emp.id)" class="btn-view">View</button>
          <button @click="startEdit(emp)" class="btn-edit">Edit</button>
          <button @click="confirmDelete(emp.id)" class="btn-delete">Delete</button>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-auto">
        <div class="flex items-start justify-between">
          <h2 class="text-xl font-bold mb-2">Employee Details</h2>
          <button @click="closeView" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>

        <div v-if="loadingView" class="py-8 text-center">
          Loading...
        </div>

        <div v-else>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <h3 class="font-semibold">Name</h3>
              <p>{{ viewData.full_name || (viewData.first_name + ' ' + (viewData.middle_name ?? '') + ' ' + (viewData.last_name ?? '')) }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Email</h3>
              <p>{{ viewData.email }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Contact</h3>
              <p>{{ viewData.contact }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Emergency Contact</h3>
              <p>{{ viewData.emergency_contact }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Department</h3>
              <p>{{ viewData.department?.name }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Designation</h3>
              <p>{{ viewData.designation?.name }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Date of Birth</h3>
              <p>{{ formatDate(viewData.dob) }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Date of Joining</h3>
              <p>{{ formatDate(viewData.doj) }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Gender</h3>
              <p>{{ viewData.gender }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Address</h3>
              <p>{{ viewData.address }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Work Location</h3>
              <p>{{ viewData.work_location }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Pay Scale</h3>
              <p>{{ viewData.pay_scale }}</p>
            </div>

            <div>
              <h3 class="font-semibold">Marital Status</h3>
              <p>{{ viewData.marital_status }}</p>
            </div>

            <div v-if="viewData.zip">
              <h3 class="font-semibold">ZIP</h3>
              <p>{{ viewData.zip }}</p>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button @click="closeView" class="btn-cancel">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal (unchanged) -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Edit Employee</h2>
        <form @submit.prevent="saveEdit">
          <div class="grid grid-cols-2 gap-3">
            <input v-model="editForm.first_name" placeholder="First Name" class="input" required />
            <input v-model="editForm.middle_name" placeholder="Middle Name" class="input" />
            <input v-model="editForm.last_name" placeholder="Last Name" class="input" required />
            <input v-model="editForm.email" placeholder="Email" class="input" required />
            <input v-model="editForm.contact" placeholder="Contact" class="input" required />
            <input v-model="editForm.emergency_contact" placeholder="Emergency Contact" class="input" />
            <input v-model="editForm.gender" placeholder="Gender" class="input" />
            <input v-model="editForm.dob" type="date" class="input" />
            <input v-model="editForm.doj" type="date" class="input" />
            <input v-model="editForm.marital_status" placeholder="Marital Status" class="input" />
            <input v-model="editForm.address" placeholder="Address" class="input" />
            <input v-model="editForm.zip" placeholder="ZIP" class="input" />
            <input v-model="editForm.pay_scale" placeholder="Pay Scale" class="input" />
            <input v-model="editForm.work_location" placeholder="Work Location" class="input" />
            <select v-model="editForm.department_id" class="input">
              <option value="">Select Department</option>
              <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
            <select v-model="editForm.designation_id" class="input">
              <option value="">Select Designation</option>
              <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button type="submit" class="btn-save">Save</button>
            <button type="button" @click="showModal = false" class="btn-cancel">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  employees: Array,
  departments: Array,
  designations: Array,
  filters: Object,
});

const filters = reactive({ ...props.filters });
const showModal = ref(false);
const editForm = reactive({});
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
  Object.assign(editForm, emp);
};

const saveEdit = () => {
  router.post(route('admin.employees.manage.update', currentId), {
    ...editForm,
    _method: 'put'
  }, {
    onSuccess: () => {
      showModal.value = false;
    }
  });
};

const confirmDelete = (id) => {
  if (confirm("Are you sure you want to delete this employee?")) {
    router.post(route('admin.employees.manage.destroy', id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        console.log("Employee deleted!");
      },
      onError: (err) => {
        console.error("Failed to delete:", err);
      }
    });
  }
};

// ---------- View logic ----------
const openView = async (id) => {
  viewModal.value = true;
  loadingView.value = true;
  // reset previous data
  Object.keys(viewData).forEach(k => delete viewData[k]);

  try {
    const res = await axios.get(route('admin.employees.manage.show', id));
    if (res.data && res.data.success) {
      Object.assign(viewData, res.data.data || {});
    } else if (res.data) {
      Object.assign(viewData, res.data);
    }
  } catch (err) {
    console.error('Failed to load employee:', err);
    Object.assign(viewData, { first_name: 'Error', email: '', contact: '', department: {}, designation: {} });
  } finally {
    loadingView.value = false;
  }
};

const closeView = () => {
  viewModal.value = false;
  loadingView.value = false;
  Object.keys(viewData).forEach(k => delete viewData[k]);
};

const formatDate = (d) => {
  if (!d) return '';
  // simple yyyy-mm-dd -> dd-mm-yyyy
  const dt = (d || '').split('T')[0];
  const [y,m,day] = dt.split('-') || [];
  if (!y) return d;
  return `${day}-${m}-${y}`;
};
</script>

<style scoped>
.input {
  @apply border border-gray-300 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-orange-500;
}
.btn-view {
  @apply bg-green-500 text-white text-sm px-3 py-1 rounded hover:bg-green-600;
}
.btn-edit {
  @apply bg-blue-500 text-white text-sm px-3 py-1 rounded hover:bg-blue-600;
}
.btn-delete {
  @apply bg-red-500 text-white text-sm px-3 py-1 rounded hover:bg-red-600;
}
.btn-save {
  @apply bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600;
}
.btn-cancel {
  @apply bg-gray-300 px-4 py-2 rounded hover:bg-gray-400;
}
</style>
