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
          <button @click="startEdit(emp)" class="btn-edit">Edit</button>
          <button @click="confirmDelete(emp.id)" class="btn-delete">Delete</button>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
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
    router.post(route('admin.employees.manage.destroy', id), {
      _method: 'delete'
    });
  }
};
</script>

<style scoped>
.input {
  @apply border border-gray-300 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-orange-500;
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
