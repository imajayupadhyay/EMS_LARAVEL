<template>
  <AdminLayout>
    <div class="max-w-6xl mx-auto p-6">
      <h2 class="text-2xl font-bold text-orange-600 mb-4">Manage Designations</h2>

      <!-- Flash Message -->
      <div
        v-if="$page.props.flash.success"
        class="bg-green-100 text-green-800 px-4 py-2 mb-4 rounded"
      >
        {{ $page.props.flash.success }}
      </div>

      <!-- Add Form -->
      <form @submit.prevent="addDesignation" class="flex flex-col md:flex-row gap-2 mb-6">
        <input
          v-model="form.name"
          type="text"
          placeholder="Designation name"
          class="form-input flex-1"
        />
        <select v-model="form.department_id" class="form-input flex-1">
          <option disabled value="">Select Department</option>
          <option v-for="dept in departments" :key="dept.id" :value="dept.id">
            {{ dept.name }}
          </option>
        </select>
        <button class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
          Add
        </button>
      </form>

      <!-- Table -->
      <div class="overflow-x-auto bg-white shadow rounded">
        <table class="w-full text-sm">
          <thead class="bg-orange-100">
            <tr>
              <th class="p-3 text-left">ID</th>
              <th class="p-3 text-left">Designation</th>
              <th class="p-3 text-left">Department</th>
              <th class="p-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="designation in designations"
              :key="designation.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="p-3">{{ designation.id }}</td>
              <td class="p-3">
                <span v-if="!editing[designation.id]">{{ designation.name }}</span>
                <input
                  v-else
                  v-model="editValues[designation.id].name"
                  class="form-input w-full"
                />
              </td>
              <td class="p-3">
                <span v-if="!editing[designation.id]">
                  {{ designation.department?.name || '—' }}
                </span>
                <select
                  v-else
                  v-model="editValues[designation.id].department_id"
                  class="form-input"
                >
                  <option disabled value="">Select Department</option>
                  <option
                    v-for="dept in departments"
                    :key="dept.id"
                    :value="dept.id"
                  >
                    {{ dept.name }}
                  </option>
                </select>
              </td>
              <td class="p-3 text-right space-x-2">
                <button
                  v-if="!editing[designation.id]"
                  @click="startEdit(designation)"
                  class="text-blue-600 hover:underline"
                >
                  Edit
                </button>
                <button
                  v-else
                  @click="updateDesignation(designation.id)"
                  class="text-green-600 hover:underline"
                >
                  Save
                </button>
                <button
                  @click="deleteDesignation(designation.id)"
                  class="text-red-600 hover:underline"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!designations.length" class="text-gray-500 mt-4 p-4">No designations found.</div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  designations: Array,
  departments: Array,
})

const form = useForm({
  name: '',
  department_id: '',
})

const editing = ref({})
const editValues = ref({})

const addDesignation = () => {
  form.post(route('admin.designations.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

const startEdit = (designation) => {
  editing.value[designation.id] = true
  editValues.value[designation.id] = {
    name: designation.name,
    department_id: designation.department_id,
  }
}

const updateDesignation = (id) => {
  const updateForm = useForm({
    ...editValues.value[id],
    _method: 'put',
  })

  updateForm.post(route('admin.designations.update', id), {
    preserveScroll: true,
    onSuccess: () => (editing.value[id] = false),
  })
}

const deleteDesignation = (id) => {
  if (confirm('Are you sure you want to delete this designation?')) {
    router.post(route('admin.designations.destroy', id), {
      _method: 'delete',
      preserveScroll: true,
    })
  }
}
</script>

<style scoped>
.form-input {
  @apply border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-500 w-full;
}
</style>
