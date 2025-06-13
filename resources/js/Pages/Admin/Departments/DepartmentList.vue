<template>
  <AdminLayout>
    <div class="max-w-4xl mx-auto p-6">
      <h2 class="text-2xl font-bold text-orange-600 mb-6">Manage Departments</h2>

      <!-- Add Department -->
      <form @submit.prevent="addDepartment" class="flex flex-col md:flex-row gap-3 mb-6">
        <input
          v-model="form.name"
          type="text"
          placeholder="New Department Name"
          class="form-input flex-1"
        />
        <button
          type="submit"
          class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600"
        >
          Add
        </button>
      </form>

      <!-- Department Table -->
      <div class="bg-white shadow rounded overflow-x-auto">
        <table class="w-full table-auto text-sm">
          <thead class="bg-orange-100 text-orange-700">
            <tr>
              <th class="p-3 text-left">ID</th>
              <th class="p-3 text-left">Name</th>
              <th class="p-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="dept in departments"
              :key="dept.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="p-3">{{ dept.id }}</td>
              <td class="p-3">
                <span v-if="!editing[dept.id]">{{ dept.name }}</span>
                <input
                  v-else
                  v-model="editValues[dept.id]"
                  type="text"
                  class="form-input w-full"
                />
              </td>
              <td class="p-3 text-right space-x-2">
                <button
                  v-if="!editing[dept.id]"
                  @click="startEdit(dept)"
                  class="text-blue-600 hover:underline"
                >
                  Edit
                </button>
                <button
                  v-else
                  @click="updateDepartment(dept.id)"
                  class="text-green-600 hover:underline"
                >
                  Save
                </button>
                <button
                  @click="deleteDepartment(dept.id)"
                  class="text-red-600 hover:underline"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="departments.length === 0">
              <td colspan="3" class="p-3 text-center text-gray-500">No departments found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  departments: Array,
})

const form = useForm({ name: '' })
const editing = ref({})
const editValues = ref({})

// Add New
const addDepartment = () => {
  form.post(route('admin.departments.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

// Start Editing
const startEdit = (dept) => {
  editing.value[dept.id] = true
  editValues.value[dept.id] = dept.name
}

// Update Department
const updateDepartment = (id) => {
  const updateForm = useForm({
    name: editValues.value[id],
  })

  updateForm.put(route('admin.departments.update', { department: id }), {
    preserveScroll: true,
    onSuccess: () => {
      editing.value[id] = false
    },
  })

  
updateForm.put(route('admin.departments.update', { department: id }), {
    preserveScroll: true,
    onSuccess: () => {
      editing.value[id] = false
    },
  })
}

// Delete
const deleteDepartment = (id) => {
  if (confirm('Are you sure you want to delete this department?')) {
    router.delete(route('admin.departments.destroy', id), {
      preserveScroll: true,
    })
  }
}
</script>

<style scoped>
.form-input {
  @apply border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-500;
}
</style>
