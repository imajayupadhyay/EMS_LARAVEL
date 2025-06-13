<template>
  <div>
    <!-- Filters -->
    <div class="flex flex-wrap gap-4 mb-6">
      <input v-model="filters.name" type="text" placeholder="Search Name" class="form-input" />
      <select v-model="filters.department_id" class="form-input">
        <option value="">All Departments</option>
        <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
      </select>
      <select v-model="filters.designation_id" class="form-input">
        <option value="">All Designations</option>
        <option v-for="des in designations" :key="des.id" :value="des.id">{{ des.name }}</option>
      </select>
      <button class="bg-orange-500 text-white px-4 py-2 rounded" @click="applyFilters">Filter</button>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded overflow-x-auto">
      <table class="w-full table-auto text-sm">
        <thead class="bg-orange-100 text-orange-700">
          <tr>
            <th class="p-3">ID</th>
            <th class="p-3">Name</th>
            <th class="p-3">Email</th>
            <th class="p-3">Department</th>
            <th class="p-3">Designation</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="emp in employees" :key="emp.id" class="border-t">
            <td class="p-3">{{ emp.id }}</td>
            <td class="p-3">
              <span v-if="!editing[emp.id]">{{ emp.name }}</span>
              <input v-else v-model="editValues[emp.id].name" class="form-input" />
            </td>
            <td class="p-3">
              <span v-if="!editing[emp.id]">{{ emp.email }}</span>
              <input v-else v-model="editValues[emp.id].email" class="form-input" />
            </td>
            <td class="p-3">
              <span v-if="!editing[emp.id]">{{ emp.department?.name }}</span>
              <select v-else v-model="editValues[emp.id].department_id" class="form-input">
                <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </td>
            <td class="p-3">
              <span v-if="!editing[emp.id]">{{ emp.designation?.name }}</span>
              <select v-else v-model="editValues[emp.id].designation_id" class="form-input">
                <option v-for="d in designations" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </td>
            <td class="p-3 text-right space-x-2">
              <button
                v-if="!editing[emp.id]"
                @click="startEdit(emp)"
                class="text-blue-600 hover:underline"
              >
                Edit
              </button>
              <button
                v-else
                @click="updateEmployee(emp.id)"
                class="text-green-600 hover:underline"
              >
                Save
              </button>
              <button
                @click="deleteEmployee(emp.id)"
                class="text-red-600 hover:underline"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="!employees.length">
            <td colspan="6" class="text-center text-gray-500 p-4">No employees found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'

const props = defineProps({
  employees: Array,
  departments: Array,
  designations: Array,
})

const editing = ref({})
const editValues = ref({})

const filters = reactive({
  name: '',
  department_id: '',
  designation_id: '',
})

const applyFilters = () => {
  router.get(route('admin.employees.manage'), filters, { preserveScroll: true, preserveState: true })
}

const startEdit = (emp) => {
  editing.value[emp.id] = true
  editValues.value[emp.id] = {
    name: emp.name,
    email: emp.email,
    department_id: emp.department_id,
    designation_id: emp.designation_id,
  }
}

const updateEmployee = (id) => {
  const updateForm = useForm(editValues.value[id])
  updateForm.post(route('admin.employees.manage.update', id), {
    preserveScroll: true,
    _method: 'put',
    onSuccess: () => (editing.value[id] = false),
  })
}

const deleteEmployee = (id) => {
  if (confirm('Are you sure you want to delete this employee?')) {
    router.post(route('admin.employees.manage.destroy', id), {
      _method: 'delete',
      preserveScroll: true,
    })
  }
}
</script>

<style scoped>
.form-input {
  @apply border border-gray-300 px-3 py-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-orange-500;
}
</style>
