<script setup>
import { ref } from 'vue'
import { useForm, router, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  leaveTypes: Object,
  flash: Object
})

const form = useForm({
  name: '',
  allowed_days: ''
})

const editing = ref(null)

const submit = () => {
  if (editing.value) {
    form.post(route('admin.leave-types.update', editing.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        editing.value = null
      }
    })
  } else {
    form.post(route('admin.leave-types.store'), {
      preserveScroll: true,
      onSuccess: () => form.reset()
    })
  }
}

const edit = (type) => {
  form.name = type.name
  form.allowed_days = type.allowed_days
  editing.value = type
}

const deleteType = (type) => {
  if (confirm(`Delete ${type.name}?`)) {
    router.post(route('admin.leave-types.destroy', type.id), {
      preserveScroll: true
    })
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Manage Leave Types" />
    <div class="max-w-5xl mx-auto p-4">
      <h1 class="text-3xl font-extrabold text-orange-600 mb-6">Leave Types Management</h1>

      <div v-if="props.flash?.success" class="bg-green-100 text-green-700 px-4 py-2 rounded-lg shadow mb-6 animate-fade-in">
        {{ props.flash.success }}
      </div>

      <!-- Form -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-orange-100">
        <h2 class="font-semibold text-lg text-gray-700 mb-4">
          {{ editing ? '✏️ Edit Leave Type' : '➕ Add Leave Type' }}
        </h2>
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <input 
              type="text" 
              v-model="form.name" 
              placeholder="Leave Type Name" 
              class="border border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
          </div>
          <div>
            <input 
              type="number" 
              v-model="form.allowed_days" 
              placeholder="Allowed Days" 
              class="border border-gray-300 rounded-lg w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
            <div v-if="form.errors.allowed_days" class="text-red-600 text-sm mt-1">{{ form.errors.allowed_days }}</div>
          </div>
          <div class="md:col-span-2">
            <button 
              type="submit" 
              class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2 rounded-lg shadow transition"
            >
              {{ editing ? 'Update Leave Type' : 'Add Leave Type' }}
            </button>
          </div>
        </form>
      </div>

      <!-- List -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-orange-100">
        <table class="w-full text-sm">
          <thead class="bg-orange-50 text-gray-700">
            <tr>
              <th class="text-left p-3">Leave Type</th>
              <th class="text-left p-3">Allowed Days</th>
              <th class="text-left p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="type in props.leaveTypes.data" 
              :key="type.id" 
              class="border-t hover:bg-orange-50 transition"
            >
              <td class="p-3 font-medium text-gray-800">{{ type.name }}</td>
              <td class="p-3">{{ type.allowed_days }}</td>
              <td class="p-3 space-x-2">
                <button 
                  @click="edit(type)" 
                  class="px-2 py-1 text-blue-600 hover:text-blue-800 bg-blue-50 rounded"
                >
                  Edit
                </button>
                <button 
                  @click="deleteType(type)" 
                  class="px-2 py-1 text-red-600 hover:text-red-800 bg-red-50 rounded"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex justify-between items-center p-3 bg-orange-50">
          <button 
            v-if="props.leaveTypes.prev_page_url"
            @click="router.get(props.leaveTypes.prev_page_url, {}, { preserveScroll: true })"
            class="px-4 py-1 bg-orange-500 text-white rounded shadow hover:bg-orange-600"
          >
            Previous
          </button>
          <button 
            v-if="props.leaveTypes.next_page_url"
            @click="router.get(props.leaveTypes.next_page_url, {}, { preserveScroll: true })"
            class="px-4 py-1 bg-orange-500 text-white rounded shadow hover:bg-orange-600"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fade-in {
  animation: fade-in 0.5s ease;
}
</style>
