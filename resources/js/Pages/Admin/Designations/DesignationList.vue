<template>
  <div class="overflow-x-auto bg-white shadow rounded mt-6">
    <table class="w-full text-sm">
      <thead class="bg-orange-100 text-orange-700">
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
            <div v-if="!editing[designation.id]">{{ designation.name }}</div>
            <input
              v-else
              v-model="editValues[designation.id].name"
              class="form-input w-full"
            />
          </td>
          <td class="p-3">
            <div v-if="!editing[designation.id]">
              {{ designation.department?.name || '—' }}
            </div>
            <select
              v-else
              v-model="editValues[designation.id].department_id"
              class="form-input w-full"
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
    <div v-if="!designations.length" class="text-gray-500 mt-4 p-4 text-center">
      No designations found.
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  designations: Array,
  departments: Array,
})

const editing = ref({})
const editValues = ref({})

// Start Editing
const startEdit = (designation) => {
  editing.value[designation.id] = true
  editValues.value[designation.id] = {
    name: designation.name,
    department_id: designation.department_id,
  }
}

// Update Designation with method spoofing
const updateDesignation = (id) => {
  const form = useForm({
    ...editValues.value[id],
    _method: 'put',
  })

  form.post(route('admin.designations.update', id), {
    preserveScroll: true,
    onSuccess: () => {
      editing.value[id] = false
    },
  })
}

// Delete
const deleteDesignation = (id) => {
  if (confirm('Are you sure you want to delete this designation?')) {
    router.delete(route('admin.designations.destroy', id), {
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
